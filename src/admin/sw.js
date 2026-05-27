/* b1gMail – Service Worker (admin area, Web Push + PWA) v2 */
'use strict';

self.addEventListener('install', function (event) {
	event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', function (event) {
	event.waitUntil(self.clients.claim());
});

self.addEventListener('push', function (event) {
	function buildOptions(data) {
		data = data || {};
		var title = data.title || 'b1gMail Admin';
		var body = data.body || '';
		if (!body || !String(body).trim()) {
			body = 'Neue Benachrichtigung';
		}
		var tag = (data.tag || data.type || 'b1gmail-admin') + '-' + Date.now();
		var options = {
			body: body,
			tag: tag,
			renotify: true,
			silent: false,
			data: { url: data.url || './welcome.php' },
		};
		if (data.icon) {
			try {
				options.icon = new URL(data.icon, self.location.href).href;
			} catch (e) {}
		}
		return { title: title, options: options };
	}

	function showWithFallback(title, options) {
		return self.registration.showNotification(title, options).catch(function () {
			return self.registration.showNotification(title, {
				body: options.body,
				tag: options.tag,
				renotify: true,
				data: options.data,
			});
		});
	}

	var data = {};
	if (event.data) {
		try {
			data = event.data.json();
		} catch (e) {
			try {
				data = { body: event.data.text() };
			} catch (e2) {
				data = {};
			}
		}
	}

	var built = buildOptions(data);
	event.waitUntil(showWithFallback(built.title, built.options));
});

self.addEventListener('notificationclick', function (event) {
	event.notification.close();
	var url = (event.notification.data && event.notification.data.url) ? event.notification.data.url : './welcome.php';
	event.waitUntil(
		clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
			for (var i = 0; i < clientList.length; i++) {
				var client = clientList[i];
				if (client.url && 'focus' in client) {
					client.navigate(url);
					return client.focus();
				}
			}
			if (clients.openWindow) {
				return clients.openWindow(url);
			}
		})
	);
});
