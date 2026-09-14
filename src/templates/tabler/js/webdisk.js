/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */

var currentWebdiskLink = '';
var currentWebdiskFolderID = -1;
var _lastSelectedWebdiskID = 0;
var _lastSelectedWebdiskType = -1;
var _wdSel;

function initWebdiskFolderTree()
{
	if(!EBID('folderList'))
		return;

	EBID('folderList').innerHTML = (typeof bmWebdiskFolderTreesHtml == 'function') ? bmWebdiskFolderTreesHtml() : webdisk_d;
	enableWebdiskDragTargets();
	if(typeof webdiskFolderRowActions !== 'undefined' && webdiskFolderRowActions)
		attachWebdiskFolderRowActions(webdiskFolderRowActions);
	else
		attachWebdiskFolderShareActions(typeof webdiskFolderShareActions !== 'undefined' ? webdiskFolderShareActions : {});

	webdiskOpenTreeToFolder(currentWebdiskFolderID);
}

var webdiskShareTitleWebdisk = '';
var webdiskShareTitleFolder = '';

function attachWebdiskFolderShareActions(map, iconClass, webdiskTitle, folderTitle)
{
	if(webdiskTitle)
		webdiskShareTitleWebdisk = webdiskTitle;
	if(folderTitle)
		webdiskShareTitleFolder = folderTitle;
	if(!map)
		return;
	var root = EBID('folderList');
	if(!root)
		return;
	var links = root.querySelectorAll('a.node, a.nodeSel');
	for(var i=0; i<links.length; i++)
	{
		var href = links[i].getAttribute('href') || '';
		var m = href.match(/switchWebdiskFolder\((-?\d+)\)/);
		if(!m)
			continue;
		var id = parseInt(m[1], 10);
		if(!map[id])
			continue;
		var kind = map[id];
		var a = document.createElement('a');
		a.href = '#';
		a.className = 'bm-folder-tree-share';
		a.title = kind == 'webdisk' ? webdiskShareTitleWebdisk : webdiskShareTitleFolder;
		a.setAttribute('aria-label', a.title);
		a.innerHTML = '<i class="ti ti-share" aria-hidden="true"></i>';
		a.onclick = (function(shareKind, shareId)
		{
			return function(e)
			{
				if(e)
				{
					if(e.preventDefault)
						e.preventDefault();
					if(e.stopPropagation)
						e.stopPropagation();
				}
				var url = shareKind == 'webdisk'
					? bmAppendSession('webdisk.php?action=shareWebdisk')
					: bmAppendSession('webdisk.php?action=share&id=' + shareId);
				var title = shareKind == 'webdisk' ? webdiskShareTitleWebdisk : webdiskShareTitleFolder;
				var height = shareKind == 'webdisk'
					? Math.min(420, Math.max(320, window.innerHeight - 120))
					: Math.min(820, Math.max(520, window.innerHeight - 80));
				openOverlay(url, title, 640, height, true);
				return false;
			};
		})(kind, id);
		links[i].parentNode.appendChild(a);
	}
}

var webdiskRenamePromptTitle = 'Rename';
var webdiskDeleteConfirmMessage = 'Really delete?';
var webdiskEditTitle = 'Edit';
var webdiskDeleteTitle = 'Delete';

/**
 * Rendert eine Button-Group (Edit/Share/Delete) rechts am Ordner-Baum-Eintrag,
 * analog zur Kalender-Sidebar (.bm-organizer-sidebar-cal-actions).
 *
 * map: { <folderId>: { share: bool, rename: bool, del: bool, parent: int, title: string } }
 */
function attachWebdiskFolderRowActions(map)
{
	if(!map)
		return;
	var root = EBID('folderList');
	if(!root)
		return;
	var links = root.querySelectorAll('a.node, a.nodeSel');
	for(var i=0; i<links.length; i++)
	{
		var href = links[i].getAttribute('href') || '';
		var m = href.match(/switchWebdiskFolder\((-?\d+)\)/);
		if(!m)
			continue;
		var id = parseInt(m[1], 10);
		var cfg = map[id];
		if(!cfg)
			continue;
		if(!cfg.share && !cfg.rename && !cfg.del)
			continue;

		// Bestehende Actions-Gruppe entfernen (falls Tree neu gerendert wurde)
		var parent = links[i].parentNode;
		var oldGroup = parent.querySelector('.bm-folder-tree-actions');
		if(oldGroup)
			parent.removeChild(oldGroup);

		var group = document.createElement('span');
		group.className = 'btn-group btn-group-sm bm-folder-tree-actions';
		group.setAttribute('role', 'group');
		group.setAttribute('aria-label', webdiskEditTitle);
		// Klick im Wrapper darf nicht den Ordner umschalten
		group.onclick = function(e) { if(e && e.stopPropagation) e.stopPropagation(); };

		if(cfg.rename)
			group.appendChild(_wdMakeRowAction('ti-pencil', webdiskEditTitle,
				(function(fid, title)
				{
					return function(e)
					{
						if(e){ if(e.preventDefault) e.preventDefault(); if(e.stopPropagation) e.stopPropagation(); }
						webdiskRenameFolderNode(fid, title);
						return false;
					};
				})(id, cfg.title || '')));

		if(cfg.share)
			group.appendChild(_wdMakeRowAction('ti-share', webdiskShareTitleFolder,
				(function(fid)
				{
					return function(e)
					{
						if(e){ if(e.preventDefault) e.preventDefault(); if(e.stopPropagation) e.stopPropagation(); }
						var url = bmAppendSession('webdisk.php?action=share&id=' + fid);
						var h = Math.min(820, Math.max(520, window.innerHeight - 80));
						openOverlay(url, webdiskShareTitleFolder, 640, h, true);
						return false;
					};
				})(id)));

		if(cfg.del)
			group.appendChild(_wdMakeRowAction('ti-trash', webdiskDeleteTitle,
				(function(fid, pid)
				{
					return function(e)
					{
						if(e){ if(e.preventDefault) e.preventDefault(); if(e.stopPropagation) e.stopPropagation(); }
						if(!confirm(webdiskDeleteConfirmMessage))
							return false;
						document.location.href = bmAppendCsrf(bmAppendSession(
							'webdisk.php?action=deleteItem&type=1&folder=' + pid + '&id=' + fid));
						return false;
					};
				})(id, cfg.parent || 0)));

		parent.appendChild(group);
	}
}

function _wdMakeRowAction(iconClass, title, onClick)
{
	var a = document.createElement('a');
	a.href = '#';
	a.className = 'btn btn-icon';
	a.title = title;
	a.setAttribute('aria-label', title);
	a.innerHTML = '<i class="ti ' + iconClass + '" aria-hidden="true"></i>';
	a.onclick = onClick;
	return a;
}

function webdiskRenameFolderNode(id, oldTitle)
{
	var newName = prompt(webdiskRenamePromptTitle, oldTitle || '');
	if(newName == null)
		return;
	newName = ('' + newName).replace(/^\s+|\s+$/g, '');
	if(newName == '' || newName == oldTitle)
		return;
	var url = bmAppendCsrf(bmAppendSession(
		'webdisk.php?action=renameItem&type=1&id=' + id
		+ '&name=' + encodeURIComponent(newName)));
	MakeXMLRequest(url, function(e) {
		if(e.readyState == 4)
			document.location.href = bmAppendSession('webdisk.php?folder=' + currentWebdiskFolderID);
	});
}

function initWDSel()
{
	var viewType, container, tagName;

	if(EBID('wdContentTable'))
	{
		container = EBID('wdContentTable');
		viewType = 'table';
		tagName = 'tr';
	}
	else
	{
		container = EBID('wdContentDiv');
		viewType = 'icons';
		tagName = 'a';
	}

	var sel = new selecTable(container, tagName, true);
	sel.cbGetItemID = function(element)
	{
		return(element.id.substr(4));
	}
	sel.cbRowFilter = function(element)
	{
		return(element.id.substr(0, 4) == 'wli_');
	}
	sel.cbSelectSingleItem = function(element)
	{
		var itemID = this.getItemID(element).split('_');
		showWebdiskItemDetails(itemID[0], itemID[1]);
	}
	sel.cbSelectionChanged = function()
	{
		selectedWebdiskCountChanged(this.sel.length);
	}
	sel.cbItemContextMenu = function(element, event)
	{
		return(false);
	}
	sel.cbItemDragStart = function(element, event)
	{
		var dragImg = document.createElement('img');

		if(this.sel.length > 1)
		{
			dragImg.src = tplDir + 'images/li/drag_wditems.png';
		}
		else if(this.sel.length == 1)
		{
			var itemID = this.getItemID(this.sel[0]).split('_');
			if(itemID[0] == 2)
				dragImg.src = tplDir + 'images/li/drag_wdfile.png';
			else
				dragImg.src = tplDir + 'images/li/drag_wdfolder.png';
		}

		dragImg.width = 32;
		dragImg.height = 32;

		transferSelectedWebdiskItems();
		var wdItemStr = EBID('selectedWebdiskItems').value;

		event.dataTransfer.setData('wditems', wdItemStr);
		event.dataTransfer.setDragImage(dragImg, -10, -10);

		return(true);
	}
	sel.cbItemDoubleClick = function(element)
	{
		var itemID = this.getItemID(element).split('_');

		if(itemID[0] == 1)
			switchWebdiskFolder(itemID[1]);
		else if(!webdiskTryOpenPreviewOnDblClick(parseInt(itemID[0], 10), parseInt(itemID[1], 10)))
			document.location.href = bmAppendSession('webdisk.php?action=downloadFile&id='+itemID[1]);
	}
	sel.cbStyleRow = function(element, selected)
	{
		if(!selected)
		{
			if(element.tagName.toUpperCase() == 'TR')
				element.className = element.className.replace(' selected', '');
			else
				element.className = element.className.replace('Selected', '');
		}
		else
		{
			if(element.tagName.toUpperCase() == 'TR')
				element.className += ' selected';
			else
				element.className = element.className.replace('webdiskItem', 'webdiskItemSelected');
		}
	}
	sel.init();
	_wdSel = sel;
}

function registerWebdiskFolderHotkeyHandler()
{
	window.onkeydown = function(e)
	{
		var accelKey = accelKeyPressed(e);

		if(e.shiftKey || e.altKey)
			return(true);

		if(e.target.type && (e.target.type == "text" || e.target.type == "textarea"))
			return(true);

		if(accelKey)
		{
			switch(e.keyCode)
			{
			case 65: // a
				if(EBID('allChecker'))
					EBID('allChecker').click();
				else
					_wdSel.selectAll();
				return(false);

			case 67: // c
				if(currentID > 0)
					webdiskClipboardAction('copy');
				return(false);

			case 68: // d
				EBID('wdMassAction').value = 'download';
				transferSelectedWebdiskItems();
				document.forms.f1.submit();
				return(false);

			case 78: // n
				EBID('folderName').focus();
				return(false);

			case 85: // u
				if(EBID('fileCount').value <= 1)
					EBID('fileCount').value = 5;
				EBID('fileCountForm').submit();
				return(false);

			case 86: // v
				if(EBID('pasteLink'))
					document.location.href = EBID('pasteLink').href;
				return(false);

			case 88: // x
				if(currentID > 0)
					webdiskClipboardAction('cut');
				return(false);
			}
		}
		else
		{
			switch(e.keyCode)
			{
			case 46: // del
				if(confirm(lang['realdel']))
				{
					EBID('wdMassAction').value = 'delete';
					transferSelectedWebdiskItems();
					document.forms.f1.submit();
				}
				return(false);
			}
		}
	}
}
function webdiskMouseDown(event, type, id)
{
	return(true);
}
function webdiskMassDownload()
{
	EBID('wdMassAction').value = 'download';
	transferSelectedWebdiskItems();
	document.forms.f1.submit();
}

function webdiskDownloadCurrent()
{
	if(_wdSel && _wdSel.sel.length > 1)
	{
		webdiskMassDownload();
		return;
	}

	if(_wdSel && _wdSel.sel.length == 1)
	{
		var itemID = _wdSel.getIDList()[0].split('_');
		if(itemID[0] == '1')
		{
			webdiskMassDownload();
			return;
		}
		if(itemID[0] == '2')
		{
			document.location.href = bmAppendSession('webdisk.php?action=downloadFile&id=' + itemID[1] );
			return;
		}
	}

	if(currentType == 1 && currentID > 0)
		webdiskMassDownload();
	else if(currentType == 2 && currentID > 0)
		document.location.href = bmAppendSession('webdisk.php?action=downloadFile&id=' + currentID );
}

function showWebdiskItemDetails(type, id)
{
	_lastSelectedWebdiskID = type;
	_lastSelectedWebdiskType = id;
	var _requestTimeWDFolder = currentWebdiskFolderID;
	var _requestType = type;
	var _requestID = id;

	MakeXMLRequest('webdisk.php?action=itemInfo&type='+escape(type)+'&id='+escape(id), function(e)
			{
				if(e.readyState == 4)
				{
					if(_requestTimeWDFolder != currentWebdiskFolderID)
						return;
					if(_wdSel && _wdSel.sel.length > 1)
						return;
					if(_wdSel && _wdSel.sel.length == 1)
					{
						var singleID = _wdSel.getIDList()[0].split('_');
						if(singleID[0] != String(_requestType) || singleID[1] != String(_requestID))
							return;
					}
					if(e.responseXML)
					{
						var x = e.responseXML;
						var title = x.getElementsByTagName('title').item(0).childNodes.item(0).nodeValue;
						var shortTitle = x.getElementsByTagName('shortTitle').item(0).childNodes.item(0).nodeValue;
						var size = x.getElementsByTagName('size').item(0).childNodes.item(0).nodeValue;
						var created = x.getElementsByTagName('created').item(0).childNodes.item(0).nodeValue;
						var ext = x.getElementsByTagName('ext').item(0).childNodes.item(0).nodeValue;
						var share = x.getElementsByTagName('share').item(0).childNodes.item(0).nodeValue == '1';
						var viewable = x.getElementsByTagName('viewable').item(0).childNodes.item(0).nodeValue == '1';
						var uploaderNode = x.getElementsByTagName('uploader').item(0);
						var uploader = (uploaderNode && uploaderNode.childNodes.length)
							? uploaderNode.childNodes.item(0).nodeValue : '';

						webdiskShowInfo(
							type,
							title,
							shortTitle,
							size,
							ext,
							created,
							id,
							share,
							viewable,
							uploader);
					}
				}
			});
}
function selectedWebdiskCountChanged(no)
{
	if(no <= 1)
	{
		if(EBID('webdiskMultiActions'))
			EBID('webdiskMultiActions').style.display = 'none';
		return;
	}

	_lastSelectedWebdiskID = 0;
	_lastSelectedWebdiskType = 0;

	// reset links
	if(EBID('wdCutLink')) EBID('wdCutLink').className = '';
	if(EBID('wdCopyLink')) EBID('wdCopyLink').className = '';
	if(EBID('wdCutLink2')) EBID('wdCutLink2').className = '';
	if(EBID('wdCopyLink2')) EBID('wdCopyLink2').className = '';

	// details
	EBID('webdiskDetailInfoNote').style.display = 'none';
	EBID('webdiskDetailInfo').style.display = '';
	var wdExt = EBID('wdExt');
	if(wdExt)
		wdExt.src = tplDir + 'images/li/drag_wditems.png';
	EBID('wdTitle').innerHTML = no + ' ' + lang['items'];
	EBID('wdSize').innerHTML = '…';
	EBID('wdDate').innerHTML = '-';
	EBID('wdShared').style.display = 'none';
	webdiskSetUploader('');

	// actions
	EBID('webdiskDetailActionsNote').style.display = 'none';
	if(EBID('webdiskDetailActions'))
		EBID('webdiskDetailActions').style.display = 'none';

	// folder
	EBID('webdiskDetailFolderActions').style.display = 'none';
	if(EBID('wdStopShareLink'))
		EBID('wdStopShareLink').style.display = 'none';
	if(EBID('wdStopFileShareLink'))
		EBID('wdStopFileShareLink').style.display = 'none';

	// file
	EBID('webdiskDetailFileActions').style.display = 'none';
	EBID('webdiskDetailFileActionsView').style.display = 'none';

	// zip
	if(EBID('webdiskDetailZIPActions'))
		EBID('webdiskDetailZIPActions').style.display = 'none';

	// multiple
	EBID('webdiskMultiActions').style.display = '';

	transferSelectedWebdiskItems();
	var items = EBID('selectedWebdiskItems') ? EBID('selectedWebdiskItems').value : '';
	if(!items)
		return;

	var _requestFolder = currentWebdiskFolderID;
	var _requestCount = no;

	MakeXMLRequest('webdisk.php?action=selectionInfo&items=' + encodeURIComponent(items) , function(e)
	{
		if(e.readyState != 4)
			return;
		if(!_wdSel || _wdSel.sel.length != _requestCount || currentWebdiskFolderID != _requestFolder)
			return;
		if(!e.responseXML)
			return;

		var x = e.responseXML;
		var totalSize = parseInt(x.getElementsByTagName('totalSize').item(0).childNodes.item(0).nodeValue, 10);
		var fileCount = parseInt(x.getElementsByTagName('fileCount').item(0).childNodes.item(0).nodeValue, 10);
		var folderCount = parseInt(x.getElementsByTagName('folderCount').item(0).childNodes.item(0).nodeValue, 10);

		if(x.getElementsByTagName('sizeFormatted').length > 0)
			EBID('wdSize').innerHTML = x.getElementsByTagName('sizeFormatted').item(0).childNodes.item(0).nodeValue;
		else if(!isNaN(totalSize))
			EBID('wdSize').innerHTML = webdiskFormatBytes(totalSize);

		if(lang['wd_selection_detail'] && (fileCount > 0 || folderCount > 0))
		{
			var detail = lang['wd_selection_detail']
				.replace('%d', _requestCount)
				.replace('%f', fileCount)
				.replace('%o', folderCount);
			EBID('wdTitle').innerHTML = detail;
		}
	});
}
function transferSelectedWebdiskItems()
{
	var f = EBID('selectedWebdiskItems'), i;

	if(f)
	{
		f.value = '';

		var IDs = _wdSel.getIDList();
		for(i=0; i<IDs.length; i++)
		{
			var itemID = IDs[i].split('_');
			f.value += itemID[0] + ',' + itemID[1] + ';';
		}

		if(f.value.length > 0)
			f.value = f.value.substr(0, f.value.length-1);
	}
}
function webdiskFormatUploadAlertMessage(response, fileName)
{
	if(!response)
		return('');

	if(fileName && response.indexOf(fileName) < 0)
		return('<div class="fw-semibold mb-1">' + bmEscapeHtml(fileName) + '</div><div>' + bmEscapeHtml(response) + '</div>');

	return(bmEscapeHtml(response).replace(/\n/g, '<br>'));
}

function webdiskShowUploadAlert(messageOrHtml, type, isHtml)
{
	if(typeof bmShowPageAlert !== 'function')
	{
		alert(isHtml ? stripTags(messageOrHtml) : messageOrHtml);
		return;
	}

	var container = EBID('webdiskPageAlert');
	if(!container)
	{
		bmShowPageAlert(isHtml ? stripTags(messageOrHtml) : messageOrHtml, type, 'webdiskPageAlert');
		return;
	}

	type = type || 'danger';
	var iconClass = (type === 'success') ? 'ti-check' : 'ti-alert-circle';

	container.className = 'alert alert-' + type + ' alert-dismissible bm-webdisk-alert';
	container.innerHTML =
		'<div class="d-flex">'
		+ '<div><i class="ti ' + iconClass + ' alert-icon icon" aria-hidden="true"></i></div>'
		+ '<div class="bm-webdisk-alert-body">' + (isHtml ? messageOrHtml : bmEscapeHtml(String(messageOrHtml)).replace(/\n/g, '<br>')) + '</div>'
		+ '</div>'
		+ '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
	container.classList.remove('d-none');
	container.style.display = '';

	var wrap = EBID('wdAlerts');
	if(wrap && typeof wrap.scrollIntoView === 'function')
		wrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function webdiskDnDFileDone(response, fileName)
{
	if(response === '1')
		return;

	if(!response)
		response = (typeof lang !== 'undefined' && lang['internalerror']) ? lang['internalerror'] : 'Error';

	webdiskShowUploadAlert(webdiskFormatUploadAlertMessage(response, fileName), 'danger', true);
}

function webdiskFormatBytes(bytes)
{
	if(bytes < 1024)
		return(bytes + ' B');
	if(bytes < 1024 * 1024)
		return(Math.round(bytes / 1024 * 100) / 100 + ' KB');
	if(bytes < 1024 * 1024 * 1024)
		return(Math.round(bytes / 1024 / 1024 * 100) / 100 + ' MB');

	return(Math.round(bytes / 1024 / 1024 / 1024 * 100) / 100 + ' GB');
}

var _webdiskUploadModal = null;
var _webdiskUploadModalDnDInited = false;

function webdiskResetUploadModalState()
{
	if(_webdiskUploadModal)
	{
		try { _webdiskUploadModal.dispose(); } catch(exDispose) {}
	}
	_webdiskUploadModal = null;
	_webdiskUploadModalDnDInited = false;
}

function webdiskIsFileForbidden(fileName, mimeType)
{
	var rules = window.webdiskUploadRules, i, val;

	if(!rules)
		return(false);

	fileName = (fileName || '').toLowerCase();
	mimeType = (mimeType || '').toLowerCase();

	if(rules.extensions && rules.extensions.length)
	{
		for(i = 0; i < rules.extensions.length; i++)
		{
			val = String(rules.extensions[i]).toLowerCase();
			if(!val)
				continue;
			if(val.charAt(val.length - 1) === '*')
			{
				if(fileName.indexOf(val.substring(0, val.length - 1)) !== -1)
					return(true);
			}
			else if(val.length > 1 && fileName.length >= val.length && fileName.substr(fileName.length - val.length) === val)
				return(true);
		}
	}

	if(rules.mimetypes && rules.mimetypes.length)
	{
		for(i = 0; i < rules.mimetypes.length; i++)
		{
			val = String(rules.mimetypes[i]).toLowerCase();
			if(!val)
				continue;
			if(val === mimeType)
				return(true);
			if(val.charAt(val.length - 1) === '*' && mimeType.indexOf(val.substring(0, val.length - 1)) === 0)
				return(true);
		}
	}

	return(false);
}

function webdiskValidateUploadFile(file)
{
	var maxBytes = (typeof window.webdiskMaxUploadBytes !== 'undefined') ? window.webdiskMaxUploadBytes : 0;
	var errors = [];

	if(!file || !file.name)
		return(errors);

	if(webdiskIsFileForbidden(file.name, file.type))
		errors.push(((typeof lang !== 'undefined' && lang['wd_fileforbidden']) ? lang['wd_fileforbidden'] : 'File type not allowed') + ': ' + file.name);

	if(maxBytes > 0 && file.size > maxBytes)
	{
		var limitText = (typeof lang !== 'undefined' && lang['wd_filetoolarge'])
			? lang['wd_filetoolarge'].replace('%s', webdiskFormatBytes(maxBytes))
			: ('Max. ' + webdiskFormatBytes(maxBytes));
		errors.push(file.name + ': ' + limitText);
	}

	return(errors);
}

function webdiskEnsureUploadModalInBody()
{
	var modals = document.querySelectorAll('#webdiskUploadModal');
	var modal, i;

	if(modals.length > 1)
	{
		for(i = 0; i < modals.length; i++)
		{
			if(modals[i].parentNode !== document.body)
				modals[i].parentNode.removeChild(modals[i]);
		}
	}

	modal = EBID('webdiskUploadModal');
	if(!modal)
		return(null);

	if(modal.parentNode !== document.body)
		document.body.appendChild(modal);

	return(modal);
}

function webdiskCollectUploadModalFiles()
{
	var input = EBID('webdiskUploadFiles');
	var files = [], j;

	if(!input || !input.files)
		return(files);

	for(j = 0; j < input.files.length; j++)
		files.push(input.files[j]);

	return(files);
}

function webdiskValidateUploadModalForm()
{
	var files = webdiskCollectUploadModalFiles(), allErrors = [], i, errs;

	if(files.length <= 0)
	{
		webdiskShowUploadAlert((typeof lang !== 'undefined' && lang['wd_upload_nofiles']) ? lang['wd_upload_nofiles'] : 'Please select at least one file.', 'warning', false);
		return(false);
	}

	for(i = 0; i < files.length; i++)
	{
		errs = webdiskValidateUploadFile(files[i]);
		if(errs.length)
			allErrors = allErrors.concat(errs);
	}

	if(allErrors.length)
	{
		webdiskShowUploadAlert(allErrors.join('\n'), 'danger', false);
		return(false);
	}

	return(true);
}

function webdiskSyncUploadModalTargetFolder()
{
	var form = EBID('webdiskUploadForm');

	if(!form)
		return;

	var folderId = (typeof currentWebdiskFolderID !== 'undefined' && currentWebdiskFolderID != -1)
		? currentWebdiskFolderID
		: 0;

	form.action = 'webdisk.php?folder=' + folderId ;
}

function webdiskCloseUploadModal()
{
	if(_webdiskUploadModal)
	{
		try { _webdiskUploadModal.hide(); } catch(exHide) {}
	}
	else
	{
		var el = EBID('webdiskUploadModal');

		if(el && typeof bootstrap !== 'undefined')
		{
			var inst = bootstrap.Modal.getInstance(el);

			if(inst)
			{
				try { inst.hide(); } catch(exHide2) {}
			}
		}
	}

	window.setTimeout(webdiskCleanupModalBackdrops, 50);
}

function webdiskBindUploadModalClose(modalEl)
{
	var closeBtn = EBID('webdiskUploadClose') || (modalEl && modalEl.querySelector('.modal-header .btn-close'));
	var cancelBtn = modalEl && modalEl.querySelector('.modal-footer [data-bs-dismiss="modal"]');

	function onCloseClick(e)
	{
		if(e)
		{
			e.preventDefault();
			e.stopPropagation();
		}

		webdiskCloseUploadModal();
	}

	if(closeBtn && !closeBtn.dataset.wdUploadCloseBound)
	{
		closeBtn.dataset.wdUploadCloseBound = '1';
		closeBtn.addEventListener('click', onCloseClick);
	}

	if(cancelBtn && !cancelBtn.dataset.wdUploadCancelBound)
	{
		cancelBtn.dataset.wdUploadCancelBound = '1';
		cancelBtn.addEventListener('click', onCloseClick);
	}
}

function webdiskSubmitUploadModal()
{
	webdiskSyncUploadModalTargetFolder();
	return(webdiskValidateUploadModalForm());
}

function webdiskUploadFilesSelected(input)
{
	var list = EBID('webdiskUploadFilesList');

	if(!list || !input || !input.files)
		return;

	if(input.files.length === 0)
	{
		list.textContent = '';
		return;
	}

	var names = [], i;
	for(i = 0; i < input.files.length; i++)
		names.push(input.files[i].name);

	list.textContent = names.join(', ');
}

function webdiskResetUploadModalForm()
{
	var form = EBID('webdiskUploadForm');
	if(form)
		form.reset();

	var fileList = EBID('webdiskUploadFilesList');
	if(fileList)
		fileList.textContent = '';

	var zone = EBID('wdUploadModalDnD');
	if(zone)
	{
		zone.className = zone.className.replace(/bm-dnd-upload-active/g, '').replace(/\s+/g, ' ').trim();
		var prog = zone.querySelector('.bm-dnd-upload-progress');
		if(prog && prog.parentNode)
			prog.parentNode.removeChild(prog);
	}
}

function webdiskCleanupModalBackdrops()
{
	if(typeof bmForceHideDnDOverlay === 'function')
		bmForceHideDnDOverlay();

	var modal = EBID('webdiskUploadModal');
	if(modal && modal.classList.contains('show'))
		return;

	document.body.classList.remove('modal-open');
	document.body.style.removeProperty('overflow');
	document.body.style.removeProperty('padding-right');

	var backdrops = document.querySelectorAll('.modal-backdrop');
	for(var i = 0; i < backdrops.length; i++)
	{
		if(backdrops[i].parentNode)
			backdrops[i].parentNode.removeChild(backdrops[i]);
	}
}

function webdiskOpenUploadModal()
{
	var el = webdiskEnsureUploadModalInBody();

	if(!el || typeof bootstrap === 'undefined')
		return;

	webdiskSyncUploadModalTargetFolder();

	if(!_webdiskUploadModal)
	{
		_webdiskUploadModal = bootstrap.Modal.getOrCreateInstance(el);

		if(!el.dataset.webdiskUploadModalBound)
		{
			el.dataset.webdiskUploadModalBound = '1';
			el.addEventListener('hidden.bs.modal', function()
			{
				webdiskResetUploadModalForm();
				webdiskCleanupModalBackdrops();
			});
			el.addEventListener('shown.bs.modal', function()
			{
				webdiskSyncUploadModalTargetFolder();
				webdiskInitUploadModalDnD();
			});
			webdiskBindUploadModalClose(el);
		}
	}

	webdiskResetUploadModalForm();
	_webdiskUploadModal.show();
}

function webdiskInitUploadModalDnD()
{
	var zone = EBID('wdUploadModalDnD');

	if(!zone || _webdiskUploadModalDnDInited)
		return;

	_webdiskUploadModalDnDInited = true;

	initDnDUpload(zone,
		'webdisk.php?action=dndUpload',
		function()
		{
			webdiskCleanupModalBackdrops();
			switchWebdiskFolder(currentWebdiskFolderID);
		},
		webdiskDnDFileDone,
		function()
		{
			var folderId = (typeof currentWebdiskFolderID !== 'undefined' && currentWebdiskFolderID != -1)
				? currentWebdiskFolderID
				: 0;

			return('&folder=' + folderId);
		},
		{ useOverlay: false });
}
function webdiskFindFolderTreeNode(folderID)
{
	var needle = 'switchWebdiskFolder(' + folderID + ')';
	var trees = (typeof webdiskFolderTrees != 'undefined' && webdiskFolderTrees.length)
		? webdiskFolderTrees
		: (typeof webdisk_d != 'undefined' ? [webdisk_d] : []);

	for(var t=0; t<trees.length; t++)
	{
		var tree = trees[t];
		if(!tree || !tree.aNodes)
			continue;
		for(var n=0; n<tree.aNodes.length; n++)
		{
			var url = tree.aNodes[n].url || '';
			if(url.indexOf(needle) < 0)
				continue;
			return { tree: tree, nodeId: tree.aNodes[n].id };
		}
	}

	return null;
}
function webdiskGetTreeIDbyFolderID(folderID)
{
	var info = webdiskFindFolderTreeNode(folderID);
	return(info ? info.nodeId : 0);
}
function webdiskOpenTreeToFolder(folderID)
{
	var info = webdiskFindFolderTreeNode(folderID);
	if(!info || !info.tree || typeof info.tree.openTo != 'function')
		return;
	info.tree.openTo(info.nodeId, true);
}
function webdiskReloadThumbnails()
{
	var imgs = document.querySelectorAll('#wdContentDiv .bm-webdisk-thumb, #wdContentTable .bm-webdisk-thumb');
	var i, img, src;

	for(i = 0; i < imgs.length; i++)
	{
		img = imgs[i];
		img.loading = 'eager';
		src = img.getAttribute('src');
		if(src)
		{
			img.src = '';
			img.src = src;
		}
	}
}
function switchWebdiskFolder(folderID)
{
	if(EBID('folderLoading')) EBID('folderLoading').style.display = '';

	MakeXMLRequest(bmAppendSession('webdisk.php?inline=true&folder='+folderID+'&_='+Date.now()), function(e)
			{
				if(e.readyState == 4)
				{
					webdiskClearInfo();
					currentWebdiskFolderID = folderID;
					EBID('mainContentArea').innerHTML = e.responseText;
					var wdPage = document.querySelector('.bm-webdisk-page');
					window.webdiskReadonly = !!(wdPage && wdPage.getAttribute('data-readonly') === '1');
					webdiskResetUploadModalState();
					initWDSel();
					webdiskReloadThumbnails();
					if(typeof webdiskReloadPreviewFromDom === 'function')
						webdiskReloadPreviewFromDom();
					initDnDUpload(EBID('wdDnDArea'), bmAppendSession('webdisk.php?folder='+folderID+'&action=dndUpload'), function()
							{
								switchWebdiskFolder(currentWebdiskFolderID);
							}, webdiskDnDFileDone);

					webdiskOpenTreeToFolder(folderID);

					_lastSelectedWebdiskID = 0;
					_lastSelectedWebdiskType = 0;

					if(EBID('folderLoading')) EBID('folderLoading').style.display = 'none';
				}
			});
}
function selectWebdiskLink(obj)
{
	if(currentWebdiskLink && EBID(currentWebdiskLink))
		EBID(currentWebdiskLink).className = 'webdiskLink';
	obj.className = 'webdiskLinkSelected';
	currentWebdiskLink = obj.id;
}
function updateWebdiskViewMode(c, folder, sid)
{
	var fld = '';
	if(currentWebdiskFolderID > -1)
		fld = '&folder=' + currentWebdiskFolderID;
	else if(folder != '')
		fld = '&folder=' + folder;
	document.location.href = bmAppendSession('webdisk.php?' + fld + '&do=changeViewMode&viewmode=' + c.value);
}
function webdiskClearInfo()
{
	currentID = -1;
	currentType = -1;
	currentTile = '';
}
function webdiskSetUploader(email)
{
	var text = email || '';
	var show = text.length > 0;
	var dt = EBID('wdUploaderDt');
	var dd = EBID('wdUploader');
	var row = EBID('wdUploaderRow');
	if(dt)
		dt.style.display = show ? '' : 'none';
	if(dd)
	{
		dd.style.display = show ? '' : 'none';
		if(dd.textContent !== undefined)
			dd.textContent = text;
		else
			dd.innerHTML = text;
	}
	if(row)
		row.style.display = show ? '' : 'none';
}
function webdiskShowInfo(type, fullTitle, title, size, ext, date, id, shared, viewable, uploader)
{
	currentID = id;
	currentType = type;
	currentTitle = fullTitle;

	// reset links
	if(EBID('wdCutLink'))
		EBID('wdCutLink').className = '';
	if(EBID('wdCopyLink'))
		EBID('wdCopyLink').className = '';

	// details
	EBID('webdiskDetailInfoNote').style.display = 'none';
	EBID('webdiskDetailInfo').style.display = '';
	//EBID('wdExt').src = 'webdisk.php?action=displayExtension&ext=' + ext ;
	EBID('wdTitle').innerHTML = title;
	EBID('wdSize').innerHTML = size;
	EBID('wdDate').innerHTML = date;
	webdiskSetUploader(type == 2 ? uploader : '');
	EBID('wdShared').style.display = shared ? '' : 'none';

	// actions
	EBID('webdiskDetailActionsNote').style.display = 'none';
	if(EBID('webdiskDetailActions'))
		EBID('webdiskDetailActions').style.display = (type == 1 || type == 2) && !window.webdiskReadonly ? '' : 'none';

	// folder
	EBID('webdiskDetailFolderActions').style.display = type == 1 ? '' : 'none';
	if(EBID('wdStopShareLink'))
		EBID('wdStopShareLink').style.display = type == 1 && shared ? '' : 'none';
	if(EBID('wdStopFileShareLink'))
		EBID('wdStopFileShareLink').style.display = type == 2 && shared ? '' : 'none';

	// file
	EBID('webdiskDetailFileActions').style.display = type == 2 ? '' : 'none';
	EBID('webdiskDetailFileActionsView').style.display = type == 2 && viewable ? '' : 'none';

	// zip
	if(EBID('webdiskDetailZIPActions'))
		EBID('webdiskDetailZIPActions').style.display = type==2 && ext=='zip' && !window.webdiskReadonly ? '' : 'none';

	// multiple
	EBID('webdiskMultiActions').style.display = 'none';
}
function webdiskStopShareFolder(folderID)
{
	currentType = 1;
	currentID = folderID;
	webdiskStopShare();
}
function webdiskStopShare()
{
	if(currentType != 1 || currentID <= 0)
		return;

	if(!confirm(lang['stopsharing_confirm']))
		return;

	// F2: CSRF-token required by CsrfEnforceOnStateChange()
	document.location.href = bmAppendCsrf(bmAppendSession('webdisk.php?action=stopShare&id=' + currentID
		+ '&folder=' + currentWebdiskFolderID
		));
}
function webdiskStopFileShare()
{
	if(currentType != 2 || currentID <= 0)
		return;

	if(!confirm(lang['stopsharing_confirm']))
		return;

	// F2: CSRF-token required by CsrfEnforceOnStateChange()
	document.location.href = bmAppendCsrf(bmAppendSession('webdisk.php?action=stopFileShare&id=' + currentID
		+ '&folder=' + currentWebdiskFolderID
		));
}
function webdiskClipboardAction(action)
{
	if(_wdSel.sel.length < 1)
		return;

	transferSelectedWebdiskItems();
	var itemStr = EBID('selectedWebdiskItems').value;

	MakeXMLRequest('webdisk.php?action=clipboardAction&do=' + action + '&items=' + escape(itemStr) ,
		function (e)
		{
			if(e.readyState == 4)
			{
				if(action == 'cut')
				{
					if(EBID('wdCutLink')) EBID('wdCutLink').className = 'wdSelLink';
					if(EBID('wdCutLink2')) EBID('wdCutLink2').className = 'wdSelLink';
				}
				else if(action == 'copy')
				{
					if(EBID('wdCopyLink')) EBID('wdCopyLink').className = 'wdSelLink';
					if(EBID('wdCopyLink2')) EBID('wdCopyLink2').className = 'wdSelLink';
				}
			}
		});
}
function webdiskDoRename(newName, id, type)
{
	currentID = id;
	currentType = type;
	MakeXMLRequest('webdisk.php?action=renameItem&folder=' + currentFolder + '&type=' + type + '&id=' + id + '&name=' + encodeURIComponent(newName) , function(e)
			{
				if(e.readyState == 4)
				{
					if(type == 1)
					{
						reloadWebdiskFolderList();
					}
					switchWebdiskFolder(currentWebdiskFolderID);
				}
			});
	return(false);
}
function webdiskRename(folder, id, type, title)
{
	currentFolder = folder;
	var span = EBID('wd_' + type + '_' + id);
	var call = "return webdiskDoRename(this.value, " + id + ", " + type + ");";
	span.innerHTML = "<input type=\"text\" style=\"text-align:center;width:100%;\" name=\"newName\" id=\"tNewName\" value=\"" + title + "\" size=\"16\" onkeypress=\"if(event.keyCode == 13) " + call + "\" onblur=\"" + call + "\" />";
	EBID('tNewName').focus();
	EBID('tNewName').select();
}
function webdiskCreateFolder()
{
	var folderName = EBID('folderName').value;
	MakeXMLRequest('webdisk.php?action=createFolder&rpc=true&folder='+currentWebdiskFolderID+'&folderName='+encodeURIComponent(folderName)+'', function(e)
			{
				if(e.readyState == 4)
				{
					switchWebdiskFolder(currentWebdiskFolderID);
					reloadWebdiskFolderList();
				}
			});
	EBID('folderName').value = '';
	return(false);
}
function reloadWebdiskFolderList()
{
	if(!EBID('folderList'))
		return;

	MakeXMLRequest(bmAppendSession('webdisk.php?action=getFolderList'), function(http)
			{
				if(http.readyState == 4 && http.responseText.length > 10 && http.responseText.indexOf('var') >= 0)
				{
					eval(http.responseText);
					initWebdiskFolderTree();
				}
			});
}
function moveWebdiskItems(items, destFolder)
{
	if(!items) return;
	if(destFolder == currentWebdiskFolderID) return;

	MakeXMLRequest('webdisk.php?action=moveItems&items=' + escape(items) + '&destFolderID=' + destFolder , function(http)
			{
				if(http.readyState == 4)
				{
					switchWebdiskFolder(currentWebdiskFolderID);
					if(http.responseText.indexOf(',ReloadFolderList') != -1) reloadWebdiskFolderList();
				}
			});
}
function enableWebdiskDragTargets()
{
	var dragEnter = function(event)
	{
		if((event.dataTransfer.types.contains && !event.dataTransfer.types.contains('wditems'))
				|| (event.dataTransfer.types.indexOf && event.dataTransfer.types.indexOf('wditems') < 0))
			return;
	}

	var dragLeave = function(event)
	{
		if((event.dataTransfer.types.contains && !event.dataTransfer.types.contains('wditems'))
				|| (event.dataTransfer.types.indexOf && event.dataTransfer.types.indexOf('wditems') < 0))
			return;

		this.style.textDecoration = 'none';
	}

	var dragOver = function(event)
	{
		if((event.dataTransfer.types.contains && !event.dataTransfer.types.contains('wditems'))
				|| (event.dataTransfer.types.indexOf && event.dataTransfer.types.indexOf('wditems') < 0))
			return;

		event.stopPropagation();
		event.preventDefault();

		this.style.textDecoration = 'underline';

		event.dataTransfer.effectAllowed 	= 'move';
		event.dataTransfer.dropEffect 		= 'move';
	}

	var dragDrop = function(event)
	{
		event.stopPropagation();
		event.preventDefault();

		if(!event.dataTransfer.getData('wditems'))
			return;

		var As = this.getElementsByTagName('a');
		var folderID = -128;

		for(var i=0; i<As.length; i++)
		{
			if(As[i].href.indexOf('switchWebdiskFolder') != -1)
			{
				folderID = parseInt(As[i].href.replace(/[^-0-9]/g, ''));
				break;
			}
		}

		moveWebdiskItems(event.dataTransfer.getData('wditems'), folderID);

		this.style.backgroundColor = '';
		this.style.textDecoration = 'none';
	}

	var folderList = EBID('folderList');
	var treeDIVs = folderList.getElementsByTagName('div');

	for(var i=0; i<treeDIVs.length; i++)
	{
		var div = treeDIVs[i];
		if(div.className != 'dTreeNode') continue;

		var imgs = div.getElementsByTagName('img');

		addEvent(div, 'dragenter', dragEnter);
		addEvent(div, 'dragleave', dragLeave);
		addEvent(div, 'dragover', dragOver);
		addEvent(div, 'drop', dragDrop);
	}
}
