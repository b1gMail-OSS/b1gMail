/* b1gMail – Web Push client (admin area) */
'use strict';

var bmPush = (function () {
	function installRoot() {
		var cfg = (typeof bmSessionConfig !== 'undefined') ? bmSessionConfig : null;
		var base = (cfg && cfg.apiBase) ? String(cfg.apiBase) : '';
		if (!base)
			return '';
		return base.replace(/\/?$/, '/');
	}

	function apiUrl(action) {
		var url = 'push-api.php?action=' + encodeURIComponent(action);
		if (typeof bmSessionAppendUrl === 'function') {
			return bmSessionAppendUrl(url);
		}
		var base = installRoot();
		if (base)
			url = base + url;
		if (typeof currentSID !== 'undefined' && currentSID && url.indexOf('sid=') === -1) {
			url += '&sid=' + encodeURIComponent(currentSID);
		}
		return url;
	}

	function csrfToken() {
		if (typeof bmCsrfToken !== 'undefined' && bmCsrfToken) {
			return bmCsrfToken;
		}
		if (typeof bmSessionConfig !== 'undefined' && bmSessionConfig && bmSessionConfig.csrfToken) {
			return bmSessionConfig.csrfToken;
		}
		return '';
	}

	function fetchJson(url, options) {
		options = options || {};
		options.credentials = 'same-origin';
		options.headers = options.headers || {};
		if (!options.headers['Content-Type']) {
			options.headers['Content-Type'] = 'application/json';
		}
		if ((options.method || 'GET').toUpperCase() === 'POST') {
			var token = csrfToken();
			if (token) {
				options.headers['X-CSRF-TOKEN'] = token;
			}
			options.headers['Accept'] = 'application/json';
		}
		return fetch(url, options).then(function (r) {
			return r.json();
		});
	}

	function urlBase64ToUint8Array(base64String) {
		var padding = '='.repeat((4 - (base64String.length % 4)) % 4);
		var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
		var raw = window.atob(base64);
		var arr = new Uint8Array(raw.length);
		for (var i = 0; i < raw.length; ++i) {
			arr[i] = raw.charCodeAt(i);
		}
		return arr;
	}

	function expectedSwUrl() {
		var base = installRoot();
		if (base) {
			try {
				return new URL('sw.js', base).href;
			} catch (e) {
				return base + 'sw.js';
			}
		}
		try {
			return new URL('./sw.js', window.location.href).href;
		} catch (e) {
			return '';
		}
	}

	function registerServiceWorker() {
		if (!('serviceWorker' in navigator)) {
			return Promise.reject(new Error('no_sw'));
		}
		var base = installRoot();
		var path = base ? (base + 'sw.js') : './sw.js';
		var expected = expectedSwUrl();

		var ready = Promise.resolve();
		if (navigator.serviceWorker.getRegistrations) {
			ready = navigator.serviceWorker.getRegistrations().then(function (regs) {
				return Promise.all(regs.map(function (reg) {
					var worker = reg.active || reg.waiting || reg.installing;
					var url = worker && worker.scriptURL ? worker.scriptURL : '';
					if (expected && url && url !== expected)
						return reg.unregister();
					return Promise.resolve();
				}));
			});
		}

		return ready.then(function () {
			return navigator.serviceWorker.register(path, { scope: base || './' });
		});
	}

	function getVapidKey() {
		return fetchJson(apiUrl('vapidPublicKey')).then(function (data) {
			if (!data.ok || !data.publicKey) {
				throw new Error('no_vapid');
			}
			return data.publicKey;
		});
	}

	function subscribe() {
		return registerServiceWorker()
			.then(function () {
				return getVapidKey();
			})
			.then(function (publicKey) {
				return Notification.requestPermission().then(function (perm) {
					if (perm !== 'granted') {
						throw new Error('denied');
					}
					return navigator.serviceWorker.ready.then(function (reg) {
						return reg.pushManager.subscribe({
							userVisibleOnly: true,
							applicationServerKey: urlBase64ToUint8Array(publicKey),
						});
					});
				});
			})
			.then(function (subscription) {
				return fetchJson(apiUrl('subscribe'), {
					method: 'POST',
					body: JSON.stringify({ subscription: subscription.toJSON() }),
				});
			});
	}

	function unsubscribe() {
		return navigator.serviceWorker.ready
			.then(function (reg) {
				return reg.pushManager.getSubscription();
			})
			.then(function (sub) {
				if (!sub) {
					return { ok: true };
				}
				var endpoint = sub.endpoint;
				return sub.unsubscribe().then(function () {
					return fetchJson(apiUrl('unsubscribe'), {
						method: 'POST',
						body: JSON.stringify({ endpoint: endpoint }),
					});
				});
			});
	}

	function isSupported() {
		return (
			'serviceWorker' in navigator &&
			'PushManager' in window &&
			'Notification' in window
		);
	}

	function initAutoRegister() {
		if (!isSupported() || typeof bmPushEnabled === 'undefined' || !bmPushEnabled) {
			return;
		}
		registerServiceWorker().catch(function () {});
	}

	function syncServerSubscription() {
		if (!isSupported() || typeof bmPushEnabled === 'undefined' || !bmPushEnabled) {
			return Promise.resolve();
		}
		return fetchJson(apiUrl('status'))
			.then(function (status) {
				if (!status || !status.ok || !status.prefsEnabled) {
					return null;
				}
				return navigator.serviceWorker.ready.then(function (reg) {
					return reg.pushManager.getSubscription();
				});
			})
			.then(function (sub) {
				if (!sub) {
					return null;
				}
				return fetchJson(apiUrl('subscribe'), {
					method: 'POST',
					body: JSON.stringify({ subscription: sub.toJSON() }),
				});
			})
			.catch(function () {});
	}

	function initServiceWorkerSync() {
		if (!isSupported() || typeof bmPushEnabled === 'undefined' || !bmPushEnabled) {
			return;
		}
		registerServiceWorker()
			.then(function () {
				return syncServerSubscription();
			})
			.catch(function () {});

		navigator.serviceWorker.addEventListener('controllerchange', function () {
			syncServerSubscription();
		});
	}

	function init() {
		initAutoRegister();
		initServiceWorkerSync();
	}

	return {
		isSupported: isSupported,
		subscribe: subscribe,
		unsubscribe: unsubscribe,
		registerServiceWorker: registerServiceWorker,
		syncServerSubscription: syncServerSubscription,
		init: init,
		initAutoRegister: initAutoRegister,
	};
})();

function bmPushInitClient() {
	if (typeof bmPush !== 'undefined') {
		bmPush.init();
	}
}

if (typeof document !== 'undefined') {
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', bmPushInitClient);
	} else {
		bmPushInitClient();
	}
}
