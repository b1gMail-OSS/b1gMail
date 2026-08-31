(function() {
	if(typeof bmSessionConfig === 'undefined')
		return;

	var cfg = bmSessionConfig;
	var warnShown = false;
	var lockedShown = false;

	function csrfTokenValue() {
		if(typeof bmCsrfToken !== 'undefined' && bmCsrfToken)
			return bmCsrfToken;
		if(cfg.csrfToken)
			return cfg.csrfToken;
		var inp = document.querySelector('#bmSessionLockOverlayForm input[name="csrf_token"]')
			|| document.querySelector('form input[name="csrf_token"]');
		return inp ? inp.value : '';
	}

	function csrfAppendBody(body) {
		var token = csrfTokenValue();
		body = body || '';
		if(!token || body.indexOf('csrf_token=') !== -1)
			return body;
		return body + (body ? '&' : '') + 'csrf_token=' + encodeURIComponent(token);
	}

	function csrfInjectForms() {
		var token = csrfTokenValue();
		if(!token)
			return;
		document.querySelectorAll('form').forEach(function(form) {
			if((form.getAttribute('method') || '').toLowerCase() !== 'post')
				return;
			if(form.querySelector('input[name="csrf_token"]'))
				return;
			var inp = document.createElement('input');
			inp.type = 'hidden';
			inp.name = 'csrf_token';
			inp.value = token;
			form.appendChild(inp);
		});
	}

	function appendSid(url) {
		if(!url)
			return url;
		if(!/^(https?:)?\/\//i.test(url) && url.indexOf('/') !== 0) {
			var base = cfg.apiBase || '';
			if(base !== '')
				url = base.replace(/\/?$/, '/') + url.replace(/^\.\//, '');
		}
		/* Match PHP SessionUrlSidEnabled(): only with urlCompat and without cookie mode */
		if(!cfg.urlCompat || cfg.cookieMode)
			return url;
		if(url.indexOf('sid=') !== -1)
			return url;
		if(!cfg.sid)
			return url;
		return url + (url.indexOf('?') !== -1 ? '&' : '?') + 'sid=' + encodeURIComponent(cfg.sid);
	}

	window.bmSessionAppendUrl = appendSid;

	function apiUrl(action) {
		var base = cfg.apiBase || '';
		var path = cfg.apiUrl || 'start.php';
		var url = (base !== '' ? base : '') + path;
		return appendSid(url + (url.indexOf('?') !== -1 ? '&' : '?') + 'action=' + encodeURIComponent(action));
	}

	function showWarnModal() {
		warnShown = true;
		var el = document.getElementById('bmSessionWarnModal');
		if(!el)
			return;
		el.setAttribute('aria-hidden', 'false');
		if(typeof bootstrap !== 'undefined' && bootstrap.Modal)
			bootstrap.Modal.getOrCreateInstance(el).show();
		else
		{
			el.classList.add('show');
			el.style.display = 'block';
		}
	}

	function hideWarnModal() {
		warnShown = false;
		var el = document.getElementById('bmSessionWarnModal');
		if(!el)
			return;
		if(typeof bootstrap !== 'undefined' && bootstrap.Modal)
		{
			var modal = bootstrap.Modal.getInstance(el) || bootstrap.Modal.getOrCreateInstance(el);
			modal.hide();
		}
		else
		{
			el.classList.remove('show');
			el.style.display = 'none';
			el.setAttribute('aria-hidden', 'true');
		}
	}

	function doSessionKeepAlive() {
		return sessionFetch('sessionKeepAlive', { method: 'POST', body: '' }).then(function(result) {
			if(result.data && result.data.ok)
			{
				hideWarnModal();
				pollStatus();
			}
		}).catch(function() {});
	}

	function showLockOverlay() {
		lockedShown = true;
		hideWarnModal();
		if(typeof hideHeaderPopup === 'function')
			hideHeaderPopup('userMenu');
		var el = document.getElementById('bmSessionLockOverlay');
		if(el)
		{
			el.classList.remove('d-none');
			el.setAttribute('aria-hidden', 'false');
		}
		document.body.classList.add('bm-session-locked');
		var pw = document.getElementById('bmSessionLockOverlayPassword');
		if(pw)
			setTimeout(function() { pw.focus(); }, 100);
	}

	function hideLockOverlay() {
		lockedShown = false;
		var el = document.getElementById('bmSessionLockOverlay');
		if(el)
		{
			el.classList.add('d-none');
			el.setAttribute('aria-hidden', 'true');
		}
		document.body.classList.remove('bm-session-locked');
	}

	function handleSessionPayload(data, status) {
		if(data && data.sessionExpired)
		{
			window.top.location.href = appendSid(data.redirect || cfg.loginUrl || 'index.php?action=login&expired=1');
			return true;
		}
		if((data && data.locked) || (status === 401 && data && !data.sessionExpired))
		{
			showLockOverlay();
			return true;
		}
		return false;
	}

	window.bmSessionHandleResponse = function(xhr) {
		if(!xhr || xhr.readyState !== 4)
			return false;
		if(xhr.status === 403)
		{
			try
			{
				var csrfData = JSON.parse(xhr.responseText);
				if(csrfData && csrfData.csrfError)
				{
					if(typeof bmHandleCsrfFailure === 'function')
						bmHandleCsrfFailure(csrfData);
					else
						window.location.reload();
					return true;
				}
			}
			catch(e) {}
		}
		if(xhr.status === 401)
		{
			try
			{
				return handleSessionPayload(JSON.parse(xhr.responseText), 401);
			}
			catch(e)
			{
				showLockOverlay();
				return true;
			}
		}
		return false;
	};

	function sessionFetch(action, options) {
		options = options || {};
		var fetchOpts = {
			method: options.method || 'GET',
			credentials: 'same-origin',
			headers: {
				'Accept': 'application/json',
				'X-Requested-With': 'XMLHttpRequest'
			}
		};
		if(options.body !== undefined)
		{
			fetchOpts.method = 'POST';
			fetchOpts.headers['Content-Type'] = 'application/x-www-form-urlencoded';
			fetchOpts.body = csrfAppendBody(options.body);
		}
		else if((options.method || 'GET').toUpperCase() === 'POST')
		{
			fetchOpts.method = 'POST';
			fetchOpts.headers['Content-Type'] = 'application/x-www-form-urlencoded';
			fetchOpts.body = csrfAppendBody('');
		}
		return fetch(apiUrl(action), fetchOpts).then(function(res) {
			return res.text().then(function(text) {
				var data = null;
				try
				{
					data = JSON.parse(text);
				}
				catch(e)
				{
					data = { ok: false, parseError: true };
				}
				return { res: res, data: data };
			});
		});
	}

	function pollStatus() {
		sessionFetch('sessionStatus').then(function(result) {
			if(!result.data)
				return;
			if(result.res.status === 401 && handleSessionPayload(result.data, 401))
				return;
			if(result.data.sessionExpired || (result.res.status === 401 && result.data.sessionExpired))
			{
				handleSessionPayload(result.data, result.res.status);
				return;
			}
			if(result.data.locked)
			{
				showLockOverlay();
				return;
			}
			if(result.data.warn)
				showWarnModal();
			else if(!lockedShown)
				hideWarnModal();
		}).catch(function() {});
	}

	function applyCsrfTokenFromResponse(data) {
		if(!data || !data.csrfToken)
			return;
		if(typeof bmCsrfToken !== 'undefined')
			bmCsrfToken = data.csrfToken;
		cfg.csrfToken = data.csrfToken;
		document.querySelectorAll('input[name="csrf_token"]').forEach(function(inp) {
			inp.value = data.csrfToken;
		});
	}

	function confirmLockedThen(fn) {
		return sessionFetch('sessionStatus').then(function(statusResult) {
			if(statusResult.data && statusResult.data.locked)
			{
				showLockOverlay();
				return;
			}
			if(typeof fn === 'function')
				fn();
		}).catch(function() {
			if(typeof fn === 'function')
				fn();
		});
	}

	window.bmSessionLockNow = function() {
		showLockOverlay();
		csrfInjectForms();

		sessionFetch('sessionLockNow', { method: 'POST', body: '' }).then(function(result) {
			if(result.data)
				applyCsrfTokenFromResponse(result.data);
			if(result.data && (result.data.ok || result.data.locked))
				return;
			return confirmLockedThen(function() {
				hideLockOverlay();
				if(result.data && result.data.csrfError)
					window.location.reload();
				else if(result.data && result.data.sessionExpired && result.data.redirect)
					window.top.location.href = appendSid(result.data.redirect);
			});
		}).catch(function() {
			confirmLockedThen(function() {
				hideLockOverlay();
			});
		});
	};

	if(typeof MakeXMLRequest === 'function')
	{
		var origMakeXMLRequest = MakeXMLRequest;
		MakeXMLRequest = function(url, callback, param) {
			url = appendSid(url);
			return origMakeXMLRequest(url, function(http, p) {
				if(http.readyState === 4 && bmSessionHandleResponse(http))
					return;
				if(callback)
					callback(http, p);
			}, param);
		};
	}

	function initSessionMonitor() {
		csrfInjectForms();

		var lockMenu = document.getElementById('bmSessionLockMenu');
		if(lockMenu)
		{
			lockMenu.addEventListener('click', function(e) {
				e.preventDefault();
				if(typeof hideHeaderPopup === 'function')
					hideHeaderPopup('userMenu');
				window.bmSessionLockNow();
			});
		}

		var keepAliveBtn = document.getElementById('bmSessionKeepAlive');
		if(keepAliveBtn)
		{
			keepAliveBtn.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				doSessionKeepAlive();
			});
		}

		var warnModal = document.getElementById('bmSessionWarnModal');
		if(warnModal)
		{
			warnModal.addEventListener('mousedown', function(e) {
				e.stopPropagation();
			});
			warnModal.addEventListener('keydown', function(e) {
				e.stopPropagation();
			});
		}

		var unlockForm = document.getElementById('bmSessionLockOverlayForm');
		if(unlockForm)
		{
			unlockForm.addEventListener('submit', function(e) {
				e.preventDefault();
				var pw = document.getElementById('bmSessionLockOverlayPassword');
				var err = document.getElementById('bmSessionLockOverlayError');
				if(!pw || pw.value === '')
					return;

				err.classList.add('d-none');
				var body = csrfAppendBody('password=' + encodeURIComponent(pw.value));

				sessionFetch('sessionUnlock', { method: 'POST', body: body }).then(function(result) {
					if(result.data)
						applyCsrfTokenFromResponse(result.data);
					if(result.res.ok && result.data && result.data.ok)
					{
						hideLockOverlay();
						pw.value = '';
						pollStatus();
						return;
					}
				if(result.data && result.data.csrfError)
				{
					if(typeof bmCsrfErrorAlertHtml === 'function')
						err.innerHTML = bmCsrfErrorAlertHtml(result.data);
					else
						err.textContent = result.data.error || cfg.csrfError || '';
					err.classList.remove('d-none');
					return;
				}
					err.textContent = (result.data && result.data.error) ? result.data.error : (cfg.unlockError || '');
					err.classList.remove('d-none');
					pw.value = '';
					pw.focus();
				}).catch(function() {
					err.textContent = cfg.unlockError || '';
					err.classList.remove('d-none');
				});
			});
		}

		['mousedown', 'keydown', 'scroll', 'touchstart'].forEach(function(eventName) {
			document.addEventListener(eventName, function(e) {
				if(!warnShown)
					return;
				if(e.target && e.target.closest && e.target.closest('#bmSessionWarnModal'))
					return;
				doSessionKeepAlive();
			}, { passive: true });
		});

		var pollMs = 30000;
		if(cfg.idleTimeout > 0)
			pollMs = Math.max(15000, Math.min(60000, cfg.idleTimeout * 60 * 1000 / 2));

		setInterval(pollStatus, pollMs);
		pollStatus();

		if(document.body && document.body.classList.contains('bm-session-locked'))
			showLockOverlay();
	}

	if(document.readyState === 'loading')
		document.addEventListener('DOMContentLoaded', initSessionMonitor);
	else
		initSessionMonitor();
})();
