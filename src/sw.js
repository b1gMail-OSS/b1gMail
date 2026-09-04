/* b1gMail – Service Worker (customer area, Web Push + PWA) v5 */
'use strict';

self.addEventListener('install', function (event) {
	event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', function (event) {
	event.waitUntil(
		caches.keys().then(function (keys) {
			return Promise.all(keys.map(function (key) {
				return caches.delete(key);
			}));
		}).then(function () {
			return self.clients.claim();
		})
	);
});

function parsePushData(pushEvent) {
	if (!pushEvent.data) {
		return Promise.resolve({});
	}
	try {
		var parsed = pushEvent.data.json();
		if (parsed && typeof parsed.then === 'function') {
			return parsed.catch(function () {
				return {};
			});
		}
		return Promise.resolve(parsed);
	} catch (e) {
		return Promise.resolve({});
	}
}

function notificationTag(data) {
	var tag = String((data && data.tag) || (data && data.type) || 'b1gmail');
	/* Per-category tags from server (bm-push-core-mail, …) – do not append time */
	if (tag.indexOf('bm-push-') === 0) {
		return tag;
	}
	return tag + '-' + Date.now();
}

function buildNotification(data) {
	data = data || {};
	var title = String(data.title || 'b1gMail');
	var body = String(data.body || '');
	if (!body.trim()) {
		body = 'Neue Benachrichtigung';
	}
	var options = {
		body: body,
		tag: notificationTag(data),
		renotify: true,
		silent: false,
		data: { url: data.url || './' },
	};
	if (data.icon) {
		try {
			options.icon = new URL(String(data.icon), self.location.href).href;
		} catch (e) {}
	}
	return { title: title, options: options };
}

function showNotificationSafe(title, options) {
	return self.registration.showNotification(title, options).catch(function () {
		return self.registration.showNotification(title, {
			body: options.body,
			tag: options.tag,
			renotify: true,
			data: options.data,
		});
	});
}

function getSidFromUrl(url) {
	try {
		return new URL(url).searchParams.get('sid') || '';
	} catch (e) {
		return '';
	}
}

/** Use SW origin (e.g. beta.mail.town), not ACP selfurl; reuse sid from open tab if needed */
function resolveNotificationUrl(rawUrl, sidFromClient) {
	var base = self.registration.scope || self.location.href;
	var path = rawUrl || './';
	try {
		var parsed = new URL(path, base);
		var swOrigin = new URL(base).origin;
		if (parsed.origin !== swOrigin && /^https?:\/\//i.test(path)) {
			parsed = new URL(parsed.pathname + parsed.search + parsed.hash, base);
		}
		if (sidFromClient && !parsed.searchParams.get('sid')) {
			parsed.searchParams.set('sid', sidFromClient);
		}
		return parsed.href;
	} catch (e) {
		return new URL('./', base).href;
	}
}

function findSidFromClients(clientList) {
	for (var i = 0; i < clientList.length; i++) {
		var sid = getSidFromUrl(clientList[i].url);
		if (sid) {
			return sid;
		}
	}
	return '';
}

function clientInScope(client) {
	try {
		return client.url && client.url.indexOf(new URL(self.registration.scope).origin) === 0;
	} catch (e) {
		return !!client.url;
	}
}

self.addEventListener('push', function (event) {
	event.waitUntil(
		parsePushData(event).then(function (data) {
			var built = buildNotification(data);
			return showNotificationSafe(built.title, built.options);
		})
	);
});

self.addEventListener('notificationclick', function (event) {
	event.notification.close();
	var rawUrl = (event.notification.data && event.notification.data.url) ? event.notification.data.url : './';
	event.waitUntil(
		clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
			var sid = findSidFromClients(clientList) || getSidFromUrl(rawUrl);
			var url = resolveNotificationUrl(rawUrl, sid);
			for (var i = 0; i < clientList.length; i++) {
				var client = clientList[i];
				if (clientInScope(client) && 'focus' in client) {
					if ('navigate' in client) {
						return client.focus().then(function () {
							return client.navigate(url);
						});
					}
					return client.focus();
				}
			}
			if (clients.openWindow) {
				return clients.openWindow(url);
			}
		})
	);
});

self.addEventListener('pushsubscriptionchange', function (event) {
	event.waitUntil(
		clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
			for (var i = 0; i < clientList.length; i++) {
				clientList[i].postMessage({ type: 'bmPushResubscribe' });
			}
		})
	);
});
