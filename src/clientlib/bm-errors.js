/*
 * b1gMail – unified error / CSRF feedback (Tabler-style alerts)
 */
(function(global) {
	'use strict';

	function bmEscapeHtml(text)
	{
		if(text == null || text === '')
			return '';

		var div = document.createElement('div');
		div.appendChild(document.createTextNode(String(text)));
		return div.innerHTML;
	}

	function bmGetLangPack()
	{
		if(typeof global.lang_admin !== 'undefined' && global.lang_admin)
			return global.lang_admin;
		if(typeof global.lang_user !== 'undefined' && global.lang_user)
			return global.lang_user;
		return {};
	}

	/**
	 * @param {object} [data] Optional API payload (title, text, error, reload)
	 * @returns {{title:string,text:string,reload:string}}
	 */
	function bmGetCsrfErrorParts(data)
	{
		data = data || {};
		var cfg = (typeof global.bmSessionConfig !== 'undefined' && global.bmSessionConfig)
			? global.bmSessionConfig
			: {};
		var lang = bmGetLangPack();

		return {
			title: data.title || cfg.csrfErrorTitle || lang.csrf_error_title || '',
			text: data.text || data.error || cfg.csrfErrorText || cfg.csrfError
				|| lang.csrf_error_text || lang.csrf_error || '',
			reload: data.reload || cfg.csrfErrorReload || lang.csrf_error_reload || 'Reload page'
		};
	}

	/**
	 * HTML for inline alert boxes (session lock overlay, login form, etc.)
	 *
	 * @param {object} [data]
	 * @returns {string}
	 */
	function bmCsrfErrorAlertHtml(data)
	{
		var p = bmGetCsrfErrorParts(data);
		var html = '<div class="d-flex align-items-start gap-2">'
			+ '<div class="flex-shrink-0 pt-1"><i class="ti ti-shield-x alert-icon icon" aria-hidden="true"></i></div>'
			+ '<div class="flex-grow-1 min-w-0" style="overflow-wrap:anywhere;word-wrap:break-word">';

		if(p.title)
			html += '<div class="fw-semibold mb-1">' + bmEscapeHtml(p.title) + '</div>';

		html += '<div>' + bmEscapeHtml(p.text).replace(/\n/g, '<br>') + '</div>'
			+ '<div class="mt-2"><button type="button" class="btn btn-primary w-100" style="min-height:2.75rem" onclick="location.reload()">'
			+ bmEscapeHtml(p.reload) + '</button></div>'
			+ '</div></div>';

		return html;
	}

	/**
	 * @param {object} [data]
	 * @param {string} [containerId]
	 */
	function bmShowCsrfError(data, containerId)
	{
		if(typeof global.bmShowPageAlert === 'function')
		{
			global.bmShowPageAlert(bmCsrfErrorAlertHtml(data), 'danger', containerId, true);
			return;
		}

		var p = bmGetCsrfErrorParts(data);
		global.alert((p.title ? p.title + '\n\n' : '') + p.text);
	}

	/**
	 * @param {object} [data]
	 * @param {object} [options] containerId, showAlert, reload
	 */
	function bmHandleCsrfFailure(data, options)
	{
		options = options || {};

		if(options.containerId || options.showAlert)
		{
			bmShowCsrfError(data, options.containerId);
			return;
		}

		if(options.reload !== false)
			global.location.reload();
	}

	function bmDomId(id)
	{
		if(typeof global.EBID === 'function')
			return global.EBID(id);
		return global.document ? global.document.getElementById(id) : null;
	}

	if(typeof global.bmShowPageAlert !== 'function')
	{
		global.bmShowPageAlert = function(message, type, containerId, isHtml)
		{
			type = type || 'danger';
			containerId = containerId || 'bmPageAlert';
			var container = bmDomId(containerId);

			if(!container)
			{
				var anchor = bmDomId('mainContentArea') || bmDomId('mainContent')
					|| (global.document ? global.document.querySelector('.page-body .card-body') : null);
				if(!anchor)
				{
					global.alert(isHtml ? String(message).replace(/<[^>]+>/g, '') : message);
					return;
				}

				container = global.document.createElement('div');
				container.id = containerId;
				container.setAttribute('role', 'alert');
				container.setAttribute('aria-live', 'polite');
				anchor.insertBefore(container, anchor.firstChild);
			}

			var iconClass = 'ti-info-circle';
			if(type === 'success')
				iconClass = 'ti-check';
			else if(type === 'danger')
				iconClass = 'ti-alert-circle';
			else if(type === 'warning')
				iconClass = 'ti-alert-triangle';

			var html = isHtml
				? String(message)
				: bmEscapeHtml(String(message)).replace(/\n/g, '<br>');

			container.className = 'alert alert-' + type + ' alert-dismissible bm-page-alert mb-3';
			container.innerHTML =
				'<div class="d-flex">'
				+ '<div><i class="ti ' + iconClass + ' alert-icon icon" aria-hidden="true"></i></div>'
				+ '<div>' + html + '</div>'
				+ '</div>'
				+ '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
			container.classList.remove('d-none');
			container.style.display = '';

			if(typeof container.scrollIntoView === 'function')
				container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
		};
	}

	global.bmGetCsrfErrorParts = bmGetCsrfErrorParts;
	global.bmCsrfErrorAlertHtml = bmCsrfErrorAlertHtml;
	global.bmShowCsrfError = bmShowCsrfError;
	global.bmHandleCsrfFailure = bmHandleCsrfFailure;
})(typeof window !== 'undefined' ? window : this);
