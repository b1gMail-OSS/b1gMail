/*
 * b1gMail – Tabler theme: Bootstrap modal layer (replaces legacy overlay UI)
 * Keeps openOverlay / hideOverlay / overlayDocument API for existing dialogs.
 */

var olCounter = 0, olOverlays = {};

function __getLastOLID()
{
	var lastOLID = -1, olID, n;
	for(olID in olOverlays)
		if(olOverlays[olID] != null)
		{
			n = parseInt(olID, 10);
			if(n > lastOLID)
				lastOLID = n;
		}
	return lastOLID;
}

function __getPLastOLID()
{
	var ids = [], olID;
	for(olID in olOverlays)
		if(olOverlays[olID] != null)
			ids.push(parseInt(olID, 10));
	ids.sort(function(a, b) { return a - b; });
	if(ids.length < 2)
		return -1;
	return ids[ids.length - 2];
}

function __getOLCount()
{
	var i = 0, olID;
	for(olID in olOverlays)
		if(olOverlays[olID] != null)
			i++;
	return i;
}

function __bmModalSizeClass(w)
{
	if(w >= 960)
		return 'modal-xl';
	if(w >= 720)
		return 'modal-lg';
	if(w > 0 && w < 480)
		return 'modal-sm';
	return '';
}

function __bmModalEscapeHtml(text)
{
	if(text == null)
		return '';
	return String(text)
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;')
		.replace(/"/g, '&quot;');
}

function __bmModalIframeHeight(h)
{
	var windowH = (typeof getDocumentMetrics === 'function')
		? getDocumentMetrics('windowH')
		: (window.innerHeight || 600);
	var maxBodyH = Math.floor(windowH * 0.85) - 56,
		minBodyH = 280;

	if(maxBodyH < minBodyH)
		maxBodyH = minBodyH;

	if(!h || h <= 0)
		return Math.min(Math.max(minBodyH, 360), maxBodyH);

	/* Kompakte Dialoge (z. B. Anlage hinzufügen, inkl. modal-footer) */
	if(h < minBodyH)
		return Math.min(Math.max(280, h), maxBodyH);

	return Math.max(minBodyH, Math.min(h, maxBodyH));
}

function __bmModalCleanup(id)
{
	var entry = olOverlays[id];
	if(!entry)
		return;

	if(entry.bsModal)
	{
		try {
			entry.bsModal.dispose();
		} catch(e) {}
	}

	if(entry.modalEl && entry.modalEl.parentNode)
		entry.modalEl.parentNode.removeChild(entry.modalEl);

	olOverlays[id] = null;
}

function hideOverlay()
{
	var lastOLID = __getLastOLID();
	if(lastOLID < 0 || !olOverlays[lastOLID])
		return;

	try {
		olOverlays[lastOLID].bsModal.hide();
	} catch(e) {
		__bmModalCleanup(lastOLID);
	}
}

function overlayDocument()
{
	if(__getOLCount() <= 1)
		return window;

	var olID = __getPLastOLID();
	if(olID < 0)
		return window;

	var f = window.frames['__olFrame_' + olID];
	if(f)
		return f;

	var el = document.getElementById('__olFrame_' + olID);
	if(el && el.contentWindow)
		return el.contentWindow;

	return window;
}

function openOverlay(url, name, w, h, clean, noClickHide)
{
	if(typeof(top) != 'undefined' && top != window && typeof(top.openOverlay) == 'function')
		return top.openOverlay(url, name, w, h, clean, noClickHide);

	if(typeof bootstrap === 'undefined' || !bootstrap.Modal)
	{
		if(typeof Overlay !== 'undefined')
		{
			var legacy = new Overlay(noClickHide);
			legacy.setSize(w, h);
			legacy.setCaption(name);
			legacy.setPage(url, clean);
			legacy.show();
			return legacy;
		}
		return null;
	}

	var id = olCounter++,
		closeLabel = (typeof lang != 'undefined' && lang['close']) ? lang['close'] : 'Close',
		sizeClass = __bmModalSizeClass(w || 0),
		iframeH = __bmModalIframeHeight(h),
		dialogMaxW = (w > 0 && typeof getDocumentMetrics === 'function')
			? Math.min(w, getDocumentMetrics('windowW') - 24)
			: 0,
		dialogClass = 'modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down bm-app-modal-dialog' + (sizeClass ? ' ' + sizeClass : ''),
		modalEl, dialogEl, contentEl, headerEl, titleEl, closeBtn, bodyEl, iframeEl, entry, bsModal;

	modalEl = document.createElement('div');
	modalEl.className = 'modal modal-blur fade bm-app-modal';
	modalEl.id = '__bmModal_' + id;
	modalEl.setAttribute('tabindex', '-1');
	modalEl.setAttribute('role', 'dialog');
	modalEl.setAttribute('aria-modal', 'true');

	dialogEl = document.createElement('div');
	dialogEl.className = dialogClass;
	if(dialogMaxW > 0)
	{
		dialogEl.style.maxWidth = dialogMaxW + 'px';
		dialogEl.style.marginLeft = 'auto';
		dialogEl.style.marginRight = 'auto';
	}

	contentEl = document.createElement('div');
	contentEl.className = 'modal-content';

	headerEl = document.createElement('div');
	headerEl.className = 'modal-header';

	titleEl = document.createElement('h5');
	titleEl.className = 'modal-title';
	titleEl.innerHTML = __bmModalEscapeHtml(name);

	closeBtn = document.createElement('button');
	closeBtn.type = 'button';
	closeBtn.className = 'btn-close';
	closeBtn.setAttribute('aria-label', closeLabel);
	closeBtn.addEventListener('click', function(e) {
		e.preventDefault();
		e.stopPropagation();
		hideOverlay();
	});

	headerEl.appendChild(titleEl);
	headerEl.appendChild(closeBtn);

	bodyEl = document.createElement('div');
	bodyEl.className = 'modal-body p-0';

	iframeEl = document.createElement('iframe');
	iframeEl.className = 'bm-app-modal-frame';
	iframeEl.name = '__olFrame_' + id;
	iframeEl.id = '__olFrame_' + id;
	iframeEl.src = 'about:blank';
	iframeEl.setAttribute('frameborder', '0');
	iframeEl.setAttribute('scrolling', 'auto');
	iframeEl.style.height = iframeH + 'px';
	iframeEl.style.minHeight = Math.min(iframeH, 280) + 'px';

	bodyEl.appendChild(iframeEl);
	contentEl.appendChild(headerEl);
	contentEl.appendChild(bodyEl);
	dialogEl.appendChild(contentEl);
	modalEl.appendChild(dialogEl);
	document.body.appendChild(modalEl);

	bsModal = bootstrap.Modal.getOrCreateInstance(modalEl, {
		backdrop: noClickHide ? 'static' : true,
		keyboard: !noClickHide,
		focus: true
	});

	modalEl.addEventListener('hidden.bs.modal', function() {
		__bmModalCleanup(id);
	});

	entry = {
		id: id,
		modalEl: modalEl,
		bsModal: bsModal,
		iframe: iframeEl
	};
	olOverlays[id] = entry;

	bsModal.show();
	iframeEl.src = url;

	return entry;
}

function setOverlayTitle(title, subtitle)
{
	var lastOLID = __getLastOLID(),
		entry, headerEl, titleWrap, titleEl, subEl;

	if(lastOLID < 0 || !(entry = olOverlays[lastOLID]) || !entry.modalEl)
		return;

	headerEl = entry.modalEl.querySelector('.modal-header');
	if(!headerEl)
		return;

	titleWrap = headerEl.querySelector('.bm-modal-title-wrap');
	if(!titleWrap)
	{
		titleEl = headerEl.querySelector('.modal-title');
		if(!titleEl)
			return;

		titleWrap = document.createElement('div');
		titleWrap.className = 'bm-modal-title-wrap me-auto';
		titleEl.parentNode.insertBefore(titleWrap, titleEl);
		titleWrap.appendChild(titleEl);
	}

	titleEl = titleWrap.querySelector('.modal-title');
	if(titleEl && title != null)
		titleEl.textContent = title;

	subEl = titleWrap.querySelector('.bm-modal-title-sub');
	if(subtitle)
	{
		if(!subEl)
		{
			subEl = document.createElement('div');
			subEl.className = 'bm-modal-title-sub text-secondary';
			titleWrap.appendChild(subEl);
		}
		subEl.textContent = subtitle;
	}
	else if(subEl)
		subEl.parentNode.removeChild(subEl);
}

window.openOverlay = openOverlay;
window.hideOverlay = hideOverlay;
window.overlayDocument = overlayDocument;
window.setOverlayTitle = setOverlayTitle;
