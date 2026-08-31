/* b1gMail – Web Push client (customer area) */
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

	function subscribe(types) {
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
					body: JSON.stringify({
						subscription: subscription.toJSON(),
						types: types || null,
					}),
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
		/* SW + Manifest: immer registrieren (PWA-Install), nicht nur bei bestehendem Abo */
		registerServiceWorker().catch(function () {});
	}

	function hasSubscription() {
		if (!isSupported()) {
			return Promise.resolve(false);
		}
		return navigator.serviceWorker.ready
			.then(function (reg) {
				return reg.pushManager.getSubscription();
			})
			.then(function (sub) {
				return !!sub;
			})
			.catch(function () {
				return false;
			});
	}

	function dismissPromptPermanent() {
		var banners = document.querySelectorAll('.bm-push-prompt');
		banners.forEach(function (banner) {
			banner.style.display = 'none';
		});
		try {
			localStorage.setItem('bmPushPromptDismiss', '1');
		} catch (e) {}
		fetchJson(apiUrl('dismissPrompt'), {
			method: 'POST',
			body: '{}',
		}).catch(function () {});
	}

	function initPromptBanner() {
		if (!isSupported() || typeof bmPushEnabled === 'undefined' || !bmPushEnabled) {
			return;
		}
		var banners = document.querySelectorAll('.bm-push-prompt');
		if (!banners.length) {
			return;
		}
		if (typeof bmPushPromptDismissed !== 'undefined' && bmPushPromptDismissed) {
			banners.forEach(function (banner) {
				banner.style.display = 'none';
			});
			return;
		}
		try {
			if (localStorage.getItem('bmPushPromptDismiss') === '1') {
				banners.forEach(function (banner) {
					banner.style.display = 'none';
				});
				return;
			}
		} catch (e) {}

		hasSubscription().then(function (subscribed) {
			if (subscribed) {
				banners.forEach(function (banner) {
					banner.style.display = 'none';
				});
				return;
			}
			banners.forEach(function (banner) {
				banner.style.display = '';
			});
		});

		banners.forEach(function (banner) {
			var enableBtn = banner.querySelector('.bm-push-prompt-enable');
			var declineBtn = banner.querySelector('.bm-push-prompt-decline');
			var dismissBtn = banner.querySelector('.bm-push-prompt-dismiss');
			if (enableBtn) {
				enableBtn.addEventListener('click', function () {
					subscribe().then(function (r) {
						if (r && r.ok) {
							banners.forEach(function (b) {
								b.style.display = 'none';
							});
						}
					}).catch(function () {});
				});
			}
			if (declineBtn) {
				declineBtn.addEventListener('click', dismissPromptPermanent);
			}
			if (dismissBtn) {
				dismissBtn.addEventListener('click', dismissPromptPermanent);
			}
		});
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

		navigator.serviceWorker.addEventListener('message', function (event) {
			if (event.data && event.data.type === 'bmPushResubscribe') {
				subscribe().catch(function () {});
			}
		});
	}

	function init() {
		initAutoRegister();
		initServiceWorkerSync();
		initPromptBanner();
	}

	return {
		isSupported: isSupported,
		subscribe: subscribe,
		unsubscribe: unsubscribe,
		registerServiceWorker: registerServiceWorker,
		initAutoRegister: initAutoRegister,
		init: init,
		hasSubscription: hasSubscription,
		syncServerSubscription: syncServerSubscription,
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
