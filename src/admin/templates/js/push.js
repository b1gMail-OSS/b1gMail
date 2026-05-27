/* b1gMail – Web Push client (admin area) */
'use strict';

var bmPush = (function () {
	var swPath = './sw.js';
	var apiPath = 'push-api.php';

	function apiUrl(action) {
		var url = apiPath + '?action=' + encodeURIComponent(action);
		if (typeof currentSID !== 'undefined' && currentSID) {
			url += '&sid=' + encodeURIComponent(currentSID);
		}
		return url;
	}

	function fetchJson(url, options) {
		options = options || {};
		options.credentials = 'same-origin';
		if (!options.headers) {
			options.headers = { 'Content-Type': 'application/json' };
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

	function registerServiceWorker() {
		if (!('serviceWorker' in navigator)) {
			return Promise.reject(new Error('no_sw'));
		}
		return navigator.serviceWorker.register(swPath, { scope: './' });
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

	function init() {
		initAutoRegister();
	}

	return {
		isSupported: isSupported,
		subscribe: subscribe,
		unsubscribe: unsubscribe,
		registerServiceWorker: registerServiceWorker,
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
