/*
 * b1gMail – Webdisk inline preview (gallery + PDF.js)
 */

var _wdPreview = {
	files: [],
	galleryFiles: [],
	index: -1,
	modal: null,
	pdfDoc: null,
	pdfPage: 1,
	pdfRenderTask: null,
	pdfLoadToken: 0,
	pdfRenderToken: 0,
	pdfCache: {},
	pdfResizeTimer: null,
	pdfSizeRetryTimer: null,
	textLoadToken: 0,
	textDirty: false,
	textOriginal: '',
	markdownViewMode: 'split',
	stageResizeObserver: null,
	keyHandler: null,
	initialized: false,
	pdfWorkerReady: false
};

function webdiskPreviewHasBootstrapModal()
{
	return(typeof bootstrap !== 'undefined' && bootstrap.Modal && typeof bootstrap.Modal.getOrCreateInstance === 'function');
}

function webdiskPreviewEnsureBackdrop()
{
	var bd = EBID('wdPreviewBackdrop');

	if(bd)
		return(bd);

	bd = document.createElement('div');
	bd.id = 'wdPreviewBackdrop';
	bd.className = 'modal-backdrop fade in bm-wd-preview-backdrop';
	bd.addEventListener('click', function() {
		webdiskPreviewClose();
	});
	document.body.appendChild(bd);

	return(bd);
}

function webdiskPreviewRemoveBackdrop()
{
	var bd = EBID('wdPreviewBackdrop');

	if(bd && bd.parentNode)
		bd.parentNode.removeChild(bd);
}

function webdiskPreviewModalShow(modal, onShownOnce)
{
	if(webdiskPreviewHasBootstrapModal())
	{
		_wdPreview.modal = bootstrap.Modal.getOrCreateInstance(modal, {
			backdrop: true,
			keyboard: true,
			focus: true
		});
		if(onShownOnce)
			modal.addEventListener('shown.bs.modal', onShownOnce, { once: true });
		_wdPreview.modal.show();
		return;
	}

	webdiskPreviewEnsureBackdrop();
	modal.style.display = 'block';
	modal.classList.add('show', 'in');
	modal.setAttribute('aria-hidden', 'false');
	document.body.classList.add('modal-open', 'bm-wd-preview-body-lock');
	if(onShownOnce)
		window.setTimeout(onShownOnce, 0);
}

function webdiskPreviewModalHideLegacy()
{
	var modal = EBID('wdPreviewModal');

	if(!modal || (!modal.classList.contains('show') && !modal.classList.contains('in')))
		return(false);

	modal.classList.remove('show', 'in');
	modal.style.display = 'none';
	modal.setAttribute('aria-hidden', 'true');
	document.body.classList.remove('modal-open', 'bm-wd-preview-body-lock');
	webdiskPreviewRemoveBackdrop();
	webdiskPreviewOnClose();

	return(true);
}

function webdiskPreviewFileUrl(fileOrId, inline)
{
	var file = (typeof fileOrId === 'object' && fileOrId !== null) ? fileOrId : null;
	var id = file ? file.id : fileOrId;

	if(file && file.isMail && typeof mailAttachmentDownloadUrl === 'function')
		return(mailAttachmentDownloadUrl(file.mailId, file.attachment, inline));

	if(!file && typeof webdiskPreviewGetItemMeta === 'function')
		file = webdiskPreviewGetItemMeta(id);

	if(file && file.isMail && typeof mailAttachmentDownloadUrl === 'function')
		return(mailAttachmentDownloadUrl(file.mailId, file.attachment, inline));

	return 'webdisk.php?action=downloadFile&id=' + id + (inline ? '&view=true' : '') ;
}

function webdiskPreviewEscapeHtml(str)
{
	return (str || '')
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;');
}

function webdiskPreviewRenderMarkdownToHtml(md)
{
	var lines = (md || '').replace(/\r\n/g, '\n').split('\n');
	var html = [];
	var inCode = false;
	var listMode = null;

	function closeList()
	{
		if(listMode === 'ul')
			html.push('</ul>');
		else if(listMode === 'ol')
			html.push('</ol>');
		listMode = null;
	}

	function inlineFmt(text)
	{
		var out = webdiskPreviewEscapeHtml(text);
		out = out.replace(/`([^`]+)`/g, '<code>$1</code>');
		out = out.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
		out = out.replace(/\*([^*]+)\*/g, '<em>$1</em>');
		out = out.replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noopener">$1</a>');
		return out;
	}

	for(var i = 0; i < lines.length; i++)
	{
		var line = lines[i];
		var trimmed = line.trim();
		var m;

		if(trimmed.indexOf('```') === 0)
		{
			closeList();
			if(!inCode)
			{
				html.push('<pre><code>');
				inCode = true;
			}
			else
			{
				html.push('</code></pre>');
				inCode = false;
			}
			continue;
		}

		if(inCode)
		{
			html.push(webdiskPreviewEscapeHtml(line));
			continue;
		}

		if(trimmed === '')
		{
			closeList();
			continue;
		}

		m = trimmed.match(/^(#{1,6})\s+(.*)$/);
		if(m)
		{
			closeList();
			html.push('<h' + m[1].length + '>' + inlineFmt(m[2]) + '</h' + m[1].length + '>');
			continue;
		}

		m = trimmed.match(/^>\s?(.*)$/);
		if(m)
		{
			closeList();
			html.push('<blockquote>' + inlineFmt(m[1]) + '</blockquote>');
			continue;
		}

		m = trimmed.match(/^[-*]\s+(.*)$/);
		if(m)
		{
			if(listMode !== 'ul')
			{
				closeList();
				html.push('<ul>');
				listMode = 'ul';
			}
			html.push('<li>' + inlineFmt(m[1]) + '</li>');
			continue;
		}

		m = trimmed.match(/^\d+\.\s+(.*)$/);
		if(m)
		{
			if(listMode !== 'ol')
			{
				closeList();
				html.push('<ol>');
				listMode = 'ol';
			}
			html.push('<li>' + inlineFmt(m[1]) + '</li>');
			continue;
		}

		closeList();
		html.push('<p>' + inlineFmt(trimmed) + '</p>');
	}

	closeList();
	if(inCode)
		html.push('</code></pre>');

	return html.join('\n');
}

function webdiskPreviewSetMarkdownViewMode(mode)
{
	var modal = EBID('wdPreviewModal');
	var editor = EBID('wdPreviewEditor');
	var preview = EBID('wdPreviewMarkdownPreview');
	var btnEdit = EBID('wdPreviewMdModeEdit');
	var btnSplit = EBID('wdPreviewMdModeSplit');
	var btnPreview = EBID('wdPreviewMdModePreview');
	var normalized = (mode === 'edit' || mode === 'preview') ? mode : 'split';

	_wdPreview.markdownViewMode = normalized;

	if(!modal || !editor || !preview)
		return;

	modal.classList.toggle('bm-wd-preview-md-edit', normalized === 'edit');
	modal.classList.toggle('bm-wd-preview-md-split', normalized === 'split');
	modal.classList.toggle('bm-wd-preview-md-preview', normalized === 'preview');

	if(btnEdit)
		btnEdit.classList.toggle('active', normalized === 'edit');
	if(btnSplit)
		btnSplit.classList.toggle('active', normalized === 'split');
	if(btnPreview)
		btnPreview.classList.toggle('active', normalized === 'preview');
}

function webdiskPreviewToggleMarkdownPreview(isMarkdown)
{
	var modal = EBID('wdPreviewModal');
	var preview = EBID('wdPreviewMarkdownPreview');
	var controls = EBID('wdPreviewMarkdownModes');

	if(modal)
		modal.classList.toggle('bm-wd-preview-markdown', !!isMarkdown);
	if(preview)
		preview.style.display = isMarkdown ? 'block' : 'none';
	if(controls)
		controls.style.display = isMarkdown ? 'inline-flex' : 'none';
	if(isMarkdown)
		webdiskPreviewSetMarkdownViewMode(_wdPreview.markdownViewMode);
}

function webdiskPreviewUpdateMarkdownPreview()
{
	var file = _wdPreview.files[_wdPreview.index];
	var preview = EBID('wdPreviewMarkdownPreview');
	var editor = EBID('wdPreviewEditor');

	if(!preview || !editor)
		return;

	if(!file || !file.isMarkdown)
	{
		preview.innerHTML = '';
		return;
	}

	preview.innerHTML = webdiskPreviewRenderMarkdownToHtml(editor.value);
}

function webdiskPreviewDownloadUrl(fileOrId)
{
	var file = (typeof fileOrId === 'object' && fileOrId !== null) ? fileOrId : null;
	var id = file ? file.id : fileOrId;

	if(file && file.isMail && typeof mailAttachmentDownloadUrl === 'function')
		return(mailAttachmentDownloadUrl(file.mailId, file.attachment, false));

	if(!file && typeof webdiskPreviewGetItemMeta === 'function')
		file = webdiskPreviewGetItemMeta(id);

	if(file && file.isMail && typeof mailAttachmentDownloadUrl === 'function')
		return(mailAttachmentDownloadUrl(file.mailId, file.attachment, false));

	return 'webdisk.php?action=downloadFile&id=' + id ;
}

function webdiskPreviewConfirmClose()
{
	if(_wdPreview.textDirty)
		return(confirm((typeof lang !== 'undefined' && lang['wd_text_unsaved'])
			? lang['wd_text_unsaved']
			: 'Ungespeicherte Änderungen verwerfen?'));

	return(true);
}

function webdiskPreviewClose()
{
	if(_wdPreview.modal && webdiskPreviewHasBootstrapModal())
	{
		try { _wdPreview.modal.hide(); } catch(exHide) {}
		return;
	}

	if(webdiskPreviewModalHideLegacy())
		return;

	var modalEl = EBID('wdPreviewModal');
	if(modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal)
	{
		var inst = bootstrap.Modal.getInstance(modalEl);
		if(inst)
			inst.hide();
	}
}

function webdiskEnsurePreviewModalInBody()
{
	var modals = document.querySelectorAll('#wdPreviewModal');
	var modal, i, inst;

	if(modals.length > 1)
	{
		for(i = modals.length - 1; i >= 1; i--)
		{
			inst = (typeof bootstrap !== 'undefined') ? bootstrap.Modal.getInstance(modals[i]) : null;
			if(inst)
			{
				try { inst.dispose(); } catch(exDispose) {}
			}
			if(modals[i].parentNode)
				modals[i].parentNode.removeChild(modals[i]);
		}
	}

	modal = EBID('wdPreviewModal');
	if(!modal)
		return(null);

	if(modal.parentNode !== document.body)
		document.body.appendChild(modal);

	return(modal);
}

function webdiskPreviewLoadDataFromDom()
{
	var manifestEl = EBID('webdiskPreviewManifest');
	var itemsEl = EBID('webdiskPreviewItems');

	window.webdiskPreviewFiles = [];
	window.webdiskPreviewItems = {};

	if(manifestEl)
	{
		try
		{
			window.webdiskPreviewFiles = JSON.parse(manifestEl.textContent || '[]');
		}
		catch(ex)
		{
			window.webdiskPreviewFiles = [];
		}
	}

	if(itemsEl)
	{
		try
		{
			window.webdiskPreviewItems = JSON.parse(itemsEl.textContent || '{}');
		}
		catch(ex2)
		{
			window.webdiskPreviewItems = {};
		}
	}

	_wdPreview.galleryFiles = window.webdiskPreviewFiles || [];
}

function webdiskPreviewGetGalleryFiles()
{
	if(_wdPreview.galleryFiles && _wdPreview.galleryFiles.length)
		return(_wdPreview.galleryFiles);

	return(window.webdiskPreviewFiles || []);
}

function webdiskPreviewGetItemMeta(fileId)
{
	var key = String(fileId);
	var items = window.webdiskPreviewItems || {};
	var meta, i, gallery;

	if(items[key])
		return(items[key]);

	gallery = webdiskPreviewGetGalleryFiles();
	for(i = 0; i < gallery.length; i++)
	{
		if(gallery[i].id == fileId)
			return(gallery[i]);
	}

	return(null);
}

function webdiskReloadPreviewFromDom()
{
	if(_wdPreview.modal)
	{
		try { _wdPreview.modal.hide(); } catch(exHide) {}
	}

	webdiskPreviewLoadDataFromDom();
	_wdPreview.files = _wdPreview.galleryFiles;
	_wdPreview.initialized = false;
	_wdPreview.modal = null;

	if(EBID('wdPreviewModal'))
		webdiskInitPreview();
}

function webdiskInitPreview()
{
	var modal;

	if(_wdPreview.initialized)
		return;

	modal = webdiskEnsurePreviewModalInBody();
	if(!modal)
		return;

	if(!_wdPreview.galleryFiles || !_wdPreview.galleryFiles.length)
		webdiskPreviewLoadDataFromDom();

	_wdPreview.galleryFiles = window.webdiskPreviewFiles || _wdPreview.galleryFiles || [];
	_wdPreview.files = _wdPreview.galleryFiles;
	_wdPreview.modal = webdiskPreviewHasBootstrapModal()
		? bootstrap.Modal.getOrCreateInstance(modal, {
			backdrop: true,
			keyboard: true,
			focus: true
		})
		: null;

	if(!modal.dataset.wdPreviewBound)
	{
		modal.dataset.wdPreviewBound = '1';
		if(webdiskPreviewHasBootstrapModal())
		{
			modal.addEventListener('hide.bs.modal', function(e)
			{
				if(_wdPreview.textDirty && !webdiskPreviewConfirmClose())
				{
					e.preventDefault();
					e.stopPropagation();
				}
			});
			modal.addEventListener('hidden.bs.modal', webdiskPreviewOnClose);
		}

		var closeBtn = EBID('wdPreviewClose') || modal.querySelector('.modal-header .btn-close');
		if(closeBtn && !closeBtn.dataset.wdPreviewCloseBound)
		{
			closeBtn.dataset.wdPreviewCloseBound = '1';
			closeBtn.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				if(!webdiskPreviewHasBootstrapModal() && _wdPreview.textDirty && !webdiskPreviewConfirmClose())
					return;
				webdiskPreviewClose();
			});
		}

		EBID('wdPreviewPrev').addEventListener('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			webdiskPreviewNav(-1);
		});
		EBID('wdPreviewNext').addEventListener('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			webdiskPreviewNav(1);
		});
		EBID('wdPreviewPdfPrevPage').addEventListener('click', function(e) {
			e.preventDefault();
			webdiskPreviewPdfPage(-1);
		});
		EBID('wdPreviewPdfNextPage').addEventListener('click', function(e) {
			e.preventDefault();
			webdiskPreviewPdfPage(1);
		});

		var saveBtn = EBID('wdPreviewSave');
		if(saveBtn && !saveBtn.dataset.wdPreviewSaveBound)
		{
			saveBtn.dataset.wdPreviewSaveBound = '1';
			saveBtn.addEventListener('click', function(e) {
				e.preventDefault();
				webdiskPreviewSaveText();
			});
		}

		var editor = EBID('wdPreviewEditor');
		if(editor && !editor.dataset.wdPreviewEditorBound)
		{
			editor.dataset.wdPreviewEditorBound = '1';
			editor.addEventListener('input', function()
			{
				_wdPreview.textDirty = (editor.value !== _wdPreview.textOriginal);
				var status = EBID('wdPreviewTextStatus');
				if(status && _wdPreview.textDirty)
					status.textContent = '';
				webdiskPreviewUpdateMarkdownPreview();
			});
		}

		var mdModeButtons = [
			{ id: 'wdPreviewMdModeEdit', mode: 'edit' },
			{ id: 'wdPreviewMdModeSplit', mode: 'split' },
			{ id: 'wdPreviewMdModePreview', mode: 'preview' }
		];
		for(var mb = 0; mb < mdModeButtons.length; mb++)
		{
			var mdBtn = EBID(mdModeButtons[mb].id);
			if(mdBtn && !mdBtn.dataset.wdPreviewMdModeBound)
			{
				mdBtn.dataset.wdPreviewMdModeBound = '1';
				mdBtn.addEventListener('click', (function(mode) {
					return function(e) {
						e.preventDefault();
						webdiskPreviewSetMarkdownViewMode(mode);
					};
				})(mdModeButtons[mb].mode));
			}
		}

		if(webdiskPreviewHasBootstrapModal())
			modal.addEventListener('shown.bs.modal', webdiskPreviewOnModalShown);

		if(typeof ResizeObserver !== 'undefined' && EBID('wdPreviewStage'))
		{
			_wdPreview.stageResizeObserver = new ResizeObserver(function()
			{
				if((!modal.classList.contains('show') && !modal.classList.contains('in')) || !_wdPreview.pdfDoc)
					return;
				if(_wdPreview.pdfResizeTimer)
					clearTimeout(_wdPreview.pdfResizeTimer);
				_wdPreview.pdfResizeTimer = setTimeout(function()
				{
					webdiskPreviewRenderPdfPage();
				}, 80);
			});
			_wdPreview.stageResizeObserver.observe(EBID('wdPreviewStage'));
		}
	}

	_wdPreview.initialized = true;
}

function webdiskPreviewOnModalShown()
{
	if(_wdPreview.pdfDoc)
		webdiskPreviewRenderPdfPage();
}

function webdiskPreviewFindIndex(fileId)
{
	var files = _wdPreview.files.length ? _wdPreview.files : (window.webdiskPreviewFiles || []);

	for(var i = 0; i < files.length; i++)
	{
		if(files[i].id == fileId)
			return(i);
	}

	return(-1);
}

function webdiskOpenPreview(fileId)
{
	if(typeof bmForceHideDnDOverlay === 'function')
		bmForceHideDnDOverlay();

	webdiskEnsurePreviewModalInBody();
	webdiskInitPreview();

	var meta;

	_wdPreview.files = webdiskPreviewGetGalleryFiles();
	var idx = webdiskPreviewFindIndex(fileId);

	if(idx < 0)
	{
		meta = webdiskPreviewGetItemMeta(fileId);
		if(!meta)
		{
			window.open(webdiskPreviewFileUrl(fileId, true), '_blank');
			return;
		}
		_wdPreview.files = [meta];
		_wdPreview.index = 0;
	}
	else
	{
		_wdPreview.index = idx;
	}

	var modal = EBID('wdPreviewModal');
	var startRender = function()
	{
		webdiskPreviewRender();
	};

	if(modal && (modal.classList.contains('show') || modal.classList.contains('in')))
		startRender();
	else if(modal)
		webdiskPreviewModalShow(modal, startRender);
	else
		startRender();

	webdiskPreviewBindKeys();
}

function webdiskPreviewBindKeys()
{
	if(_wdPreview.keyHandler)
		return;

	_wdPreview.keyHandler = function(e)
	{
		if(!EBID('wdPreviewModal').classList.contains('show') && !EBID('wdPreviewModal').classList.contains('in'))
			return;

		if(e.target && e.target.type && (e.target.type === 'text' || e.target.type === 'textarea'))
			return;

		if((e.ctrlKey || e.metaKey) && (e.keyCode === 83 || e.key === 's'))
		{
			if(EBID('wdPreviewEditor') && EBID('wdPreviewEditor').style.display !== 'none')
			{
				webdiskPreviewSaveText();
				e.preventDefault();
			}
			return;
		}

		if(e.keyCode === 37)
		{
			webdiskPreviewNav(-1);
			e.preventDefault();
		}
		else if(e.keyCode === 39)
		{
			webdiskPreviewNav(1);
			e.preventDefault();
		}
	};

	document.addEventListener('keydown', _wdPreview.keyHandler);
}

function webdiskPreviewUnbindKeys()
{
	if(_wdPreview.keyHandler)
	{
		document.removeEventListener('keydown', _wdPreview.keyHandler);
		_wdPreview.keyHandler = null;
	}
}

function webdiskPreviewSetLayoutMode(mode)
{
	var modal = EBID('wdPreviewModal');
	var galleryPane = EBID('wdPreviewGalleryPane');
	var editorPane = EBID('wdPreviewEditorPane');
	var isText = (mode === 'text');

	if(modal)
		modal.classList.toggle('bm-wd-preview-mode-text', isText);

	if(galleryPane)
		galleryPane.style.display = isText ? 'none' : '';

	if(editorPane)
		editorPane.style.display = isText ? 'flex' : 'none';

	if(EBID('wdPreviewPrev'))
		EBID('wdPreviewPrev').style.display = isText ? 'none' : '';

	if(EBID('wdPreviewNext'))
		EBID('wdPreviewNext').style.display = isText ? 'none' : '';

	if(EBID('wdPreviewCounter'))
		EBID('wdPreviewCounter').style.display = isText ? 'none' : '';
}

function webdiskPreviewOnClose()
{
	webdiskPreviewSetLayoutMode('gallery');
	webdiskPreviewToggleMarkdownPreview(false);
	webdiskPreviewSetMarkdownViewMode('split');

	if(_wdPreview.pdfSizeRetryTimer)
	{
		clearTimeout(_wdPreview.pdfSizeRetryTimer);
		_wdPreview.pdfSizeRetryTimer = null;
	}
	if(_wdPreview.pdfResizeTimer)
	{
		clearTimeout(_wdPreview.pdfResizeTimer);
		_wdPreview.pdfResizeTimer = null;
	}

	webdiskPreviewDestroyPdf(true);
	webdiskPreviewUnbindKeys();
	_wdPreview.textDirty = false;
	_wdPreview.textOriginal = '';
	webdiskPreviewHideStages();
}

function webdiskPreviewNav(delta)
{
	if(_wdPreview.files.length < 2)
		return;

	if(_wdPreview.textDirty && !webdiskPreviewConfirmClose())
		return;

	_wdPreview.index += delta;
	if(_wdPreview.index < 0)
		_wdPreview.index = _wdPreview.files.length - 1;
	if(_wdPreview.index >= _wdPreview.files.length)
		_wdPreview.index = 0;

	webdiskPreviewRender();
}

function webdiskPreviewHideStages()
{
	EBID('wdPreviewLoading').style.display = 'none';
	EBID('wdPreviewImage').style.display = 'none';
	EBID('wdPreviewPdfCanvas').style.display = 'none';
	EBID('wdPreviewText').style.display = 'none';
	if(EBID('wdPreviewVideo'))
	{
		EBID('wdPreviewVideo').pause();
		EBID('wdPreviewVideo').removeAttribute('src');
		EBID('wdPreviewVideo').load();
		EBID('wdPreviewVideo').style.display = 'none';
	}
	if(EBID('wdPreviewAudio'))
	{
		EBID('wdPreviewAudio').pause();
		EBID('wdPreviewAudio').removeAttribute('src');
		EBID('wdPreviewAudio').load();
		EBID('wdPreviewAudio').style.display = 'none';
	}
	if(EBID('wdPreviewEditor'))
		EBID('wdPreviewEditor').style.display = 'none';
	if(EBID('wdPreviewMarkdownPreview'))
		EBID('wdPreviewMarkdownPreview').style.display = 'none';
	if(EBID('wdPreviewEditorLoading'))
		EBID('wdPreviewEditorLoading').style.display = 'none';
	EBID('wdPreviewPdfToolbar').style.display = 'none';
	if(EBID('wdPreviewTextToolbar'))
		EBID('wdPreviewTextToolbar').style.display = 'none';
}

function webdiskPreviewShowLoading(textMode)
{
	webdiskPreviewHideStages();

	if(textMode && EBID('wdPreviewEditorLoading'))
		EBID('wdPreviewEditorLoading').style.display = 'flex';
	else
		EBID('wdPreviewLoading').style.display = '';
}

function webdiskPreviewEnsurePdfWorker()
{
	if(_wdPreview.pdfWorkerReady || typeof pdfjsLib === 'undefined')
		return;

	pdfjsLib.GlobalWorkerOptions.workerSrc = 'clientlib/pdfjs/pdf.worker.min.js';
	_wdPreview.pdfWorkerReady = true;
}

function webdiskPreviewDestroyPdf(clearCache)
{
	var id, cached;

	_wdPreview.pdfLoadToken++;

	if(_wdPreview.pdfRenderTask && _wdPreview.pdfRenderTask.cancel)
	{
		try { _wdPreview.pdfRenderTask.cancel(); } catch(ex) {}
	}
	_wdPreview.pdfRenderTask = null;
	_wdPreview.pdfDoc = null;
	_wdPreview.pdfPage = 1;

	if(!clearCache)
		return;

	for(id in _wdPreview.pdfCache)
	{
		if(!_wdPreview.pdfCache.hasOwnProperty(id))
			continue;
		cached = _wdPreview.pdfCache[id];
		if(cached && cached.destroy)
		{
			try { cached.destroy(); } catch(ex3) {}
		}
	}
	_wdPreview.pdfCache = {};
}

function webdiskPreviewRender()
{
	var file = _wdPreview.files[_wdPreview.index];
	var multi = _wdPreview.files.length > 1;
	var isText = !!(file.isEditableText || file.isText);

	EBID('wdPreviewTitle').textContent = file.title;
	EBID('wdPreviewCounter').textContent = (_wdPreview.index + 1) + ' / ' + _wdPreview.files.length;
	EBID('wdPreviewDownload').href = webdiskPreviewDownloadUrl(file);
	webdiskPreviewToggleMarkdownPreview(false);

	if(isText)
		webdiskPreviewSetLayoutMode('text');
	else
	{
		webdiskPreviewSetLayoutMode('gallery');
		if(EBID('wdPreviewPrev'))
			EBID('wdPreviewPrev').style.display = multi ? '' : 'none';
		if(EBID('wdPreviewNext'))
			EBID('wdPreviewNext').style.display = multi ? '' : 'none';
		if(EBID('wdPreviewCounter'))
			EBID('wdPreviewCounter').style.display = '';
	}

	webdiskPreviewShowLoading(isText);
	webdiskPreviewDestroyPdf(false);

	if(isText)
		webdiskPreviewRenderEditableText(file);
	else if(file.isPdf)
		webdiskPreviewRenderPdf(file);
	else if(file.isImage)
		webdiskPreviewRenderImage(file);
	else if(file.isVideo)
		webdiskPreviewRenderVideo(file);
	else if(file.isAudio)
		webdiskPreviewRenderAudio(file);
	else
		webdiskPreviewRenderText(file);
}

function webdiskPreviewRenderEditableText(file)
{
	var editor = EBID('wdPreviewEditor');
	var toolbar = EBID('wdPreviewTextToolbar');
	var status = EBID('wdPreviewTextStatus');
	var loadToken = ++_wdPreview.textLoadToken;

	if(!editor)
	{
		webdiskPreviewRenderText(file);
		return;
	}

	_wdPreview.textDirty = false;
	_wdPreview.textOriginal = '';
	webdiskPreviewToggleMarkdownPreview(!!file.isMarkdown);
	if(status)
		status.textContent = '';

	MakeXMLRequest(bmAppendSession('webdisk.php?action=getFileText&id=' + encodeURIComponent(file.id) ), function(e)
	{
		if(e.readyState != 4 || loadToken !== _wdPreview.textLoadToken)
			return;

		var data;
		try
		{
			data = JSON.parse(e.responseText || '{}');
		}
		catch(exParse)
		{
			webdiskPreviewSetLayoutMode('gallery');
			webdiskPreviewRenderText(file);
			return;
		}

		if(!data.ok)
		{
			if(data.error === 'toolarge')
			{
				webdiskPreviewSetLayoutMode('gallery');
				webdiskPreviewRenderText(file);
				return;
			}

			if(EBID('wdPreviewEditorLoading'))
				EBID('wdPreviewEditorLoading').style.display = 'none';
			if(status)
			{
				if(data.error === 'binary')
					status.textContent = (typeof lang !== 'undefined' && lang['wd_text_binary']) ? lang['wd_text_binary'] : 'Binary file';
				else
					status.textContent = (typeof lang !== 'undefined' && lang['wd_text_save_fail']) ? lang['wd_text_save_fail'] : 'Error';
			}
			if(toolbar)
				toolbar.style.display = 'flex';
			return;
		}

		editor.value = data.content;
		_wdPreview.textOriginal = data.content;
		if(EBID('wdPreviewEditorLoading'))
			EBID('wdPreviewEditorLoading').style.display = 'none';
		editor.style.display = 'block';
		webdiskPreviewUpdateMarkdownPreview();
		if(toolbar)
			toolbar.style.display = 'flex';
	});
}

function webdiskPreviewSaveText()
{
	var file = _wdPreview.files[_wdPreview.index];
	var editor = EBID('wdPreviewEditor');
	var status = EBID('wdPreviewTextStatus');
	var saveBtn = EBID('wdPreviewSave');
	var xhr;

	if(!file || !(file.isEditableText || file.isText) || !editor)
		return;

	if(saveBtn)
		saveBtn.disabled = true;
	if(status)
		status.textContent = '';

	xhr = GetXMLHTTP();
	if(!xhr)
	{
		if(saveBtn)
			saveBtn.disabled = false;
		return;
	}

	xhr.open('POST', 'webdisk.php?action=saveFileText&id=' + encodeURIComponent(file.id) , true);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
	xhr.onreadystatechange = function()
	{
		var data;

		if(xhr.readyState != 4)
			return;

		if(saveBtn)
			saveBtn.disabled = false;

		try
		{
			data = JSON.parse(xhr.responseText || '{}');
		}
		catch(exParse)
		{
			data = { ok: false };
		}

		if(data.ok)
		{
			_wdPreview.textOriginal = editor.value;
			_wdPreview.textDirty = false;
			if(status)
				status.textContent = (typeof lang !== 'undefined' && lang['wd_text_saved']) ? lang['wd_text_saved'] : 'Saved';
		}
		else if(status)
			status.textContent = data.message || ((typeof lang !== 'undefined' && lang['wd_text_save_fail']) ? lang['wd_text_save_fail'] : 'Error');
	};
	xhr.send('content=' + encodeURIComponent(editor.value));
}

function webdiskPreviewRenderImage(file)
{
	var img = EBID('wdPreviewImage');

	img.onload = function()
	{
		EBID('wdPreviewLoading').style.display = 'none';
		img.style.display = '';
	};
	img.onerror = function()
	{
		webdiskPreviewRenderText(file);
	};
	img.style.display = 'none';
	img.src = webdiskPreviewFileUrl(file, true);
}

function webdiskPreviewRenderText(file)
{
	var frame = EBID('wdPreviewText');

	frame.onload = function()
	{
		EBID('wdPreviewLoading').style.display = 'none';
		frame.style.display = '';
	};
	frame.style.display = 'none';
	frame.title = file.title;
	frame.src = webdiskPreviewFileUrl(file, true);
}

function webdiskPreviewRenderVideo(file)
{
	var video = EBID('wdPreviewVideo');

	if(!video)
	{
		webdiskPreviewRenderText(file);
		return;
	}

	video.onloadedmetadata = function()
	{
		EBID('wdPreviewLoading').style.display = 'none';
		video.style.display = '';
	};
	video.onerror = function()
	{
		webdiskPreviewRenderText(file);
	};
	video.style.display = 'none';
	video.preload = 'metadata';
	video.src = webdiskPreviewFileUrl(file, true);
	video.load();
}

function webdiskPreviewRenderAudio(file)
{
	var audio = EBID('wdPreviewAudio');

	if(!audio)
	{
		webdiskPreviewRenderText(file);
		return;
	}

	audio.onloadedmetadata = function()
	{
		EBID('wdPreviewLoading').style.display = 'none';
		audio.style.display = '';
	};
	audio.onerror = function()
	{
		webdiskPreviewRenderText(file);
	};
	audio.style.display = 'none';
	audio.preload = 'metadata';
	audio.src = webdiskPreviewFileUrl(file, true);
	audio.load();
}

function webdiskPreviewRenderPdf(file)
{
	var loadToken, cached;

	if(typeof pdfjsLib === 'undefined')
	{
		webdiskPreviewRenderText(file);
		return;
	}

	webdiskPreviewEnsurePdfWorker();

	cached = _wdPreview.pdfCache[file.id];
	if(cached)
	{
		_wdPreview.pdfDoc = cached;
		_wdPreview.pdfPage = 1;
		EBID('wdPreviewPdfToolbar').style.display = 'flex';
		webdiskPreviewRenderPdfPage();
		return;
	}

	loadToken = ++_wdPreview.pdfLoadToken;

	pdfjsLib.getDocument({
		url: webdiskPreviewFileUrl(file, true),
		disableAutoFetch: true,
		disableStream: false,
		rangeChunkSize: 65536
	}).promise.then(function(pdf)
	{
		if(loadToken !== _wdPreview.pdfLoadToken)
		{
			try { pdf.destroy(); } catch(exDiscard) {}
			return;
		}

		_wdPreview.pdfCache[file.id] = pdf;
		_wdPreview.pdfDoc = pdf;
		_wdPreview.pdfPage = 1;
		EBID('wdPreviewPdfToolbar').style.display = 'flex';
		webdiskPreviewRenderPdfPage();
	}).catch(function()
	{
		if(loadToken !== _wdPreview.pdfLoadToken)
			return;
		webdiskPreviewRenderText(file);
	});
}

function webdiskPreviewPdfPage(delta)
{
	if(!_wdPreview.pdfDoc)
		return;

	if(typeof delta !== 'undefined')
		_wdPreview.pdfPage = Math.min(Math.max(1, _wdPreview.pdfPage + delta), _wdPreview.pdfDoc.numPages);

	webdiskPreviewRenderPdfPage();
}

function webdiskPreviewGetStageMaxSize()
{
	var stage = EBID('wdPreviewStage');
	var body, maxW, maxH;

	if(!stage)
		return({ w: 320, h: 480 });

	maxW = stage.clientWidth - 32;
	maxH = stage.clientHeight - 32;

	if(maxW < 200 || maxH < 200)
	{
		body = stage.closest('.bm-wd-preview-body');
		if(body)
		{
			maxW = Math.max(maxW, body.clientWidth - 80);
			maxH = Math.max(maxH, body.clientHeight - 48);
		}
	}

	if(maxW < 200 || maxH < 200)
		return(null);

	return({
		w: Math.max(320, maxW),
		h: Math.max(240, maxH)
	});
}

function webdiskPreviewRenderPdfPage()
{
	var canvas, pageNo, stageSize, renderToken;

	if(!_wdPreview.pdfDoc)
		return;

	canvas = EBID('wdPreviewPdfCanvas');
	pageNo = _wdPreview.pdfPage;

	stageSize = webdiskPreviewGetStageMaxSize();
	if(!stageSize)
	{
		if(_wdPreview.pdfSizeRetryTimer)
			clearTimeout(_wdPreview.pdfSizeRetryTimer);
		_wdPreview.pdfSizeRetryTimer = setTimeout(function()
		{
			_wdPreview.pdfSizeRetryTimer = null;
			webdiskPreviewRenderPdfPage();
		}, 50);
		return;
	}

	renderToken = ++_wdPreview.pdfRenderToken;

	EBID('wdPreviewPdfPageLabel').textContent = pageNo + ' / ' + _wdPreview.pdfDoc.numPages;
	EBID('wdPreviewPdfPrevPage').disabled = (pageNo <= 1);
	EBID('wdPreviewPdfNextPage').disabled = (pageNo >= _wdPreview.pdfDoc.numPages);

	_wdPreview.pdfDoc.getPage(pageNo).then(function(page)
	{
		if(renderToken !== _wdPreview.pdfRenderToken)
			return;

		if(_wdPreview.pdfRenderTask && _wdPreview.pdfRenderTask.cancel)
		{
			try { _wdPreview.pdfRenderTask.cancel(); } catch(ex) {}
		}

		var viewport = page.getViewport({ scale: 1 });
		var maxW = stageSize.w;
		var maxH = stageSize.h;
		var scale = Math.min(maxW / viewport.width, maxH / viewport.height, 2.5);

		viewport = page.getViewport({ scale: scale });
		canvas.width = viewport.width;
		canvas.height = viewport.height;
		canvas.style.display = '';

		_wdPreview.pdfRenderTask = page.render({
			canvasContext: canvas.getContext('2d'),
			viewport: viewport
		});

		return _wdPreview.pdfRenderTask.promise;
	}).then(function()
	{
		if(renderToken !== _wdPreview.pdfRenderToken)
			return;
		EBID('wdPreviewLoading').style.display = 'none';
	}).catch(function(err)
	{
		if(err && err.name === 'RenderingCancelledException')
			return;
	});
}

function webdiskTryOpenPreviewOnDblClick(type, id)
{
	if(type != 2 || typeof webdiskOpenPreview !== 'function')
		return(false);

	if(!webdiskPreviewGetItemMeta(id))
		return(false);

	webdiskOpenPreview(id);
	return(true);
}
