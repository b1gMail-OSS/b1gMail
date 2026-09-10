/* b1gMail – Web Push client (customer area) */
'use strict';

var bmPush = (function () {
	var suppressResubscribe = false;
	var suppressResubscribeTimer = null;
	var subscribeGate = Promise.resolve();
	var lastSubscribeAt = 0;

	function beginSuppressResubscribe(ms) {
		suppressResubscribe = true;
		if (suppressResubscribeTimer) {
			clearTimeout(suppressResubscribeTimer);
		}
		suppressResubscribeTimer = setTimeout(function () {
			suppressResubscribe = false;
			suppressResubscribeTimer = null;
		}, ms || 2500);
	}

	function delay(ms) {
		return new Promise(function (resolve) {
			setTimeout(resolve, Math.max(0, ms || 0));
		});
	}

	function waitForController(timeoutMs) {
		if (!('serviceWorker' in navigator)) {
			return Promise.resolve();
		}
		if (navigator.serviceWorker.controller) {
			return Promise.resolve();
		}
		return new Promise(function (resolve) {
			var done = false;
			var timer = setTimeout(function () {
				if (!done) {
					done = true;
					resolve();
				}
			}, timeoutMs || 3000);
			function onChange() {
				if (done) {
					return;
				}
				done = true;
				clearTimeout(timer);
				navigator.serviceWorker.removeEventListener('controllerchange', onChange);
				resolve();
			}
			navigator.serviceWorker.addEventListener('controllerchange', onChange);
		});
	}

	/**
	 * FCM/Mozilla often reject or drop the first push if sent immediately after subscribe.
	 */
	function settleAfterSubscribe(minAgeMs) {
		minAgeMs = typeof minAgeMs === 'number' ? minAgeMs : 1500;
		return waitForController(3000).then(function () {
			if (!lastSubscribeAt) {
				return delay(400);
			}
			var remain = minAgeMs - (Date.now() - lastSubscribeAt);
			return delay(remain);
		});
	}

	function waitUntilReady(minAgeMs) {
		return subscribeGate.then(function () {
			return settleAfterSubscribe(minAgeMs);
		});
	}

	function installRoot() {
		var cfg = (typeof bmSessionConfig !== 'undefined') ? bmSessionConfig : null;
		var base = (cfg && cfg.apiBase) ? String(cfg.apiBase) : '';
		if (!base)
			return '';
		return base.replace(/\/?$/, '/');
	}

	function apiUrl(action) {
		var url = 'push-api.php?action=' + encodeURIComponent(action);
		if (typeof bmAppendSession === 'function') {
			return bmAppendSession(url);
		}
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

	function isOurServiceWorker(scriptURL) {
		try {
			return /\/sw\.js$/i.test(new URL(scriptURL, window.location.href).pathname);
		} catch (e) {
			return /sw\.js/i.test(String(scriptURL || ''));
		}
	}

	function registerServiceWorker() {
		if (!('serviceWorker' in navigator)) {
			return Promise.reject(new Error('no_sw'));
		}
		var base = installRoot();
		var path = base ? (base + 'sw.js') : './sw.js';

		var ready = Promise.resolve();
		if (navigator.serviceWorker.getRegistrations) {
			ready = navigator.serviceWorker.getRegistrations().then(function (regs) {
				return Promise.all(regs.map(function (reg) {
					var worker = reg.active || reg.waiting || reg.installing;
					var url = worker && worker.scriptURL ? worker.scriptURL : '';
					// Never unregister our own sw.js – that destroys the PushSubscription (FCM 410).
					// Only remove clearly foreign workers.
					if (url && !isOurServiceWorker(url)) {
						return reg.unregister();
					}
					return Promise.resolve();
				}));
			});
		}

		return ready.then(function () {
			return navigator.serviceWorker.register(path, {
				scope: base || './',
				updateViaCache: 'none',
			});
		});
	}

	/**
	 * Never wait on navigator.serviceWorker.ready – it can hang forever if install fails.
	 */
	function waitForPushReady(reg, timeoutMs) {
		timeoutMs = timeoutMs || 12000;
		if (reg && reg.active) {
			return Promise.resolve(reg);
		}
		if (!reg) {
			return Promise.reject(new Error('no_registration'));
		}

		return new Promise(function (resolve, reject) {
			var done = false;
			var timer = setTimeout(function () {
				finishErr(new Error('sw_timeout'));
			}, timeoutMs);
			var poll = setInterval(function () {
				if (reg.active) {
					finishOk();
				}
			}, 200);

			function cleanup() {
				clearTimeout(timer);
				clearInterval(poll);
			}

			function finishOk() {
				if (done || !reg.active) {
					return;
				}
				done = true;
				cleanup();
				resolve(reg);
			}

			function finishErr(err) {
				if (done) {
					return;
				}
				done = true;
				cleanup();
				reject(err);
			}

			function watch(worker) {
				if (!worker) {
					return;
				}
				worker.addEventListener('statechange', function () {
					if (worker.state === 'activated') {
						finishOk();
					} else if (worker.state === 'redundant') {
						finishErr(new Error('sw_redundant'));
					}
				});
			}

			watch(reg.installing);
			watch(reg.waiting);
			if (reg.active) {
				finishOk();
			}
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

	/**
	 * Reuse existing PushSubscription when VAPID key matches.
	 * Unsubscribing a live subscription makes its endpoint return HTTP 410.
	 */
	function applicationServerKeyMatches(subscription, keyBytes) {
		if (!subscription || !subscription.options || !subscription.options.applicationServerKey) {
			return false;
		}
		var existing = new Uint8Array(subscription.options.applicationServerKey);
		if (existing.length !== keyBytes.length) {
			return false;
		}
		for (var i = 0; i < existing.length; i++) {
			if (existing[i] !== keyBytes[i]) {
				return false;
			}
		}
		return true;
	}

	function subscribeFresh(reg, publicKey) {
		var keyBytes = urlBase64ToUint8Array(publicKey);
		return reg.pushManager.getSubscription().then(function (existing) {
			if (existing && applicationServerKeyMatches(existing, keyBytes)) {
				return existing;
			}
			var chain = Promise.resolve();
			if (existing) {
				beginSuppressResubscribe(4000);
				chain = existing.unsubscribe().catch(function () {
					return false;
				});
			}
			return chain.then(function () {
				return reg.pushManager.subscribe({
					userVisibleOnly: true,
					applicationServerKey: keyBytes,
				});
			});
		});
	}

	function subscribe(types) {
		// Permission first while the click gesture is still "fresh".
		var op = Promise.resolve()
			.then(function () {
				if (typeof Notification === 'undefined') {
					throw new Error('no_notification');
				}
				return Notification.requestPermission();
			})
			.then(function (perm) {
				if (perm !== 'granted') {
					throw new Error('denied');
				}
				return registerServiceWorker();
			})
			.then(function (reg) {
				return waitForPushReady(reg).then(function (readyReg) {
					return getVapidKey().then(function (publicKey) {
						return subscribeFresh(readyReg, publicKey);
					});
				});
			})
			.then(function (subscription) {
				if (!subscription) {
					throw new Error('no_subscription');
				}
				return fetchJson(apiUrl('subscribe'), {
					method: 'POST',
					body: JSON.stringify({
						subscription: subscription.toJSON(),
						types: types || null,
						replace: true,
					}),
				}).then(function (data) {
					if (data && data.csrfError) {
						throw new Error('csrf');
					}
					if (!data || !data.ok) {
						throw new Error((data && data.error) ? data.error : 'subscribe_failed');
					}
					lastSubscribeAt = Date.now();
					return settleAfterSubscribe(1500).then(function () {
						return data;
					});
				});
			});

		// Let overlapping Test clicks wait for this activation to finish settling.
		subscribeGate = op.then(function () {}, function () {});
		return op;
	}

	function getPushRegistration() {
		if (!('serviceWorker' in navigator)) {
			return Promise.resolve(null);
		}
		if (navigator.serviceWorker.getRegistration) {
			return navigator.serviceWorker.getRegistration().then(function (reg) {
				if (reg) {
					return reg;
				}
				if (!navigator.serviceWorker.getRegistrations) {
					return null;
				}
				return navigator.serviceWorker.getRegistrations().then(function (regs) {
					return regs && regs.length ? regs[0] : null;
				});
			});
		}
		return Promise.resolve(null);
	}

	function unsubscribe() {
		// Intentional disable – do not auto-resubscribe via pushsubscriptionchange.
		beginSuppressResubscribe(4000);
		var endpoint = '';

		return getPushRegistration()
			.then(function (reg) {
				if (!reg || !reg.pushManager) {
					return null;
				}
				return reg.pushManager.getSubscription();
			})
			.then(function (sub) {
				if (!sub) {
					return null;
				}
				endpoint = sub.endpoint || '';
				return sub.unsubscribe().catch(function () {
					return false;
				});
			})
			.then(function () {
				// Always tell the server – also clears prefs when browser had no sub.
				return fetchJson(apiUrl('unsubscribe'), {
					method: 'POST',
					body: JSON.stringify({ endpoint: endpoint }),
				});
			})
			.catch(function (err) {
				// Still try to disable server-side prefs if browser path failed.
				return fetchJson(apiUrl('unsubscribe'), {
					method: 'POST',
					body: JSON.stringify({ endpoint: endpoint }),
				}).then(function (r) {
					return r;
				}, function () {
					throw err;
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
		return getPushRegistration()
			.then(function (reg) {
				if (!reg || !reg.pushManager) {
					return null;
				}
				return reg.pushManager.getSubscription();
			})
			.then(function (sub) {
				return !!sub;
			})
			.catch(function () {
				return false;
			});
	}

	function getStatus() {
		return fetchJson(apiUrl('status')).catch(function () {
			return { ok: false };
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
		if (suppressResubscribe) {
			return Promise.resolve();
		}
		return fetchJson(apiUrl('status'))
			.then(function (status) {
				if (!status || !status.ok || !status.prefsEnabled) {
					return null;
				}
				return getPushRegistration().then(function (reg) {
					if (!reg || !reg.pushManager) {
						return null;
					}
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
			if (!event.data || event.data.type !== 'bmPushResubscribe') {
				return;
			}
			// Only restore after browser key rotation – never after user disable / key refresh.
			if (suppressResubscribe) {
				return;
			}
			fetchJson(apiUrl('status'))
				.then(function (status) {
					if (!status || !status.ok || !status.prefsEnabled) {
						return null;
					}
					return subscribe();
				})
				.catch(function () {});
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
		getStatus: getStatus,
		waitUntilReady: waitUntilReady,
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
