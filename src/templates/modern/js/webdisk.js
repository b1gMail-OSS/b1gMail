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
	iconClass = iconClass || 'fa fa-share';
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
		a.innerHTML = '<i class="' + iconClass + '" aria-hidden="true"></i>';
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
				openOverlay(url, title, 520, 360, true);
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
 * Rendert eine Icon-Gruppe (Edit / Share / Delete) rechts am Ordner-Baum-
 * Eintrag – analog zur Kalender-Sidebar (.bm-organizer-sidebar-cal-actions).
 * map: { <folderId>: { share, rename, del, parent, title } }
 */
function attachWebdiskFolderRowActions(map)
{
	if(!map) return;
	var root = EBID('folderList');
	if(!root) return;
	var links = root.querySelectorAll('a.node, a.nodeSel');
	for(var i=0; i<links.length; i++)
	{
		var href = links[i].getAttribute('href') || '';
		var m = href.match(/switchWebdiskFolder\((-?\d+)\)/);
		if(!m) continue;
		var id = parseInt(m[1], 10);
		var cfg = map[id];
		if(!cfg) continue;
		if(!cfg.share && !cfg.rename && !cfg.del) continue;

		// Bestehende Actions-Gruppe entfernen (Tree evtl. neu gerendert)
		var parent = links[i].parentNode;
		var oldGroup = parent.querySelector('.bm-folder-tree-actions');
		if(oldGroup) parent.removeChild(oldGroup);

		var group = document.createElement('span');
		group.className = 'bm-folder-tree-actions';
		group.setAttribute('role', 'group');
		group.setAttribute('aria-label', webdiskEditTitle);
		// Klick auf Icon-Wrapper darf den Ordner nicht umschalten
		group.onclick = function(e) { if(e && e.stopPropagation) e.stopPropagation(); };

		if(cfg.rename)
			group.appendChild(_wdMakeRowAction('fa-pencil', webdiskEditTitle,
				(function(fid, title) { return function(e) {
					if(e){ if(e.preventDefault) e.preventDefault(); if(e.stopPropagation) e.stopPropagation(); }
					webdiskRenameFolderNode(fid, title);
					return false;
				}; })(id, cfg.title || '')));

		if(cfg.share)
			group.appendChild(_wdMakeRowAction('fa-share', webdiskShareTitleFolder,
				(function(fid) { return function(e) {
					if(e){ if(e.preventDefault) e.preventDefault(); if(e.stopPropagation) e.stopPropagation(); }
					var url = bmAppendSession('webdisk.php?action=share&id=' + fid);
					openOverlay(url, webdiskShareTitleFolder, 640, 480, true);
					return false;
				}; })(id)));

		if(cfg.del)
			group.appendChild(_wdMakeRowAction('fa-trash-o', webdiskDeleteTitle,
				(function(fid, pid) { return function(e) {
					if(e){ if(e.preventDefault) e.preventDefault(); if(e.stopPropagation) e.stopPropagation(); }
					if(!confirm(webdiskDeleteConfirmMessage)) return false;
					document.location.href = bmAppendCsrf(bmAppendSession(
						'webdisk.php?action=deleteItem&type=1&folder=' + pid + '&id=' + fid));
					return false;
				}; })(id, cfg.parent || 0)));

		parent.appendChild(group);
	}
}

function _wdMakeRowAction(iconClass, title, onClick)
{
	var a = document.createElement('a');
	a.href = '#';
	a.className = 'bm-folder-tree-action';
	a.title = title;
	a.setAttribute('aria-label', title);
	a.innerHTML = '<i class="fa ' + iconClass + '" aria-hidden="true"></i>';
	a.onclick = onClick;
	return a;
}

function webdiskRenameFolderNode(id, oldTitle)
{
	var newName = prompt(webdiskRenamePromptTitle, oldTitle || '');
	if(newName == null) return;
	newName = ('' + newName).replace(/^\s+|\s+$/g, '');
	if(newName == '' || newName == oldTitle) return;
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
		else
			document.location.href = 'webdisk.php?action=downloadFile&id='+itemID[1]+'&sid='+currentSID;
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
			document.location.href = 'webdisk.php?action=downloadFile&id=' + itemID[1] + '&sid=' + currentSID;
			return;
		}
	}

	if(currentType == 1 && currentID > 0)
		webdiskMassDownload();
	else if(currentType == 2 && currentID > 0)
		document.location.href = 'webdisk.php?action=downloadFile&id=' + currentID + '&sid=' + currentSID;
}

function showWebdiskItemDetails(type, id)
{
	_lastSelectedWebdiskID = type;
	_lastSelectedWebdiskType = id;
	var _requestTimeWDFolder = currentWebdiskFolderID;
	var _requestType = type;
	var _requestID = id;

	MakeXMLRequest('webdisk.php?action=itemInfo&type='+escape(type)+'&id='+escape(id)+'&sid='+currentSID, function(e)
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
	EBID('webdiskDetailActions').style.display = 'none';

	// folder
	EBID('webdiskDetailFolderActions').style.display = 'none';
	if(EBID('wdStopFileShareLink'))
		EBID('wdStopFileShareLink').style.display = 'none';

	// file
	EBID('webdiskDetailFileActions').style.display = 'none';
	EBID('webdiskDetailFileActionsView').style.display = 'none';

	// zip
	EBID('webdiskDetailZIPActions').style.display = 'none';

	// multiple
	EBID('webdiskMultiActions').style.display = '';

	transferSelectedWebdiskItems();
	var items = EBID('selectedWebdiskItems') ? EBID('selectedWebdiskItems').value : '';
	if(!items)
		return;

	var _requestFolder = currentWebdiskFolderID;
	var _requestCount = no;

	MakeXMLRequest('webdisk.php?action=selectionInfo&items=' + encodeURIComponent(items) + '&sid=' + currentSID, function(e)
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
function webdiskShowUploadForm()
{
	MakeXMLRequest(bmAppendSession('webdisk.php?inline=true&do=uploadFilesForm&fileCount='+escape(EBID('fileCount').value)+'&folder='+currentWebdiskFolderID), function(e)
			{
				if(e.readyState == 4)
				{
					EBID('mainContent').innerHTML = e.responseText;
					initDnDUpload(EBID('wdDnDArea'), bmAppendSession('webdisk.php?folder='+currentWebdiskFolderID+'&action=dndUpload'), function()
							{
								switchWebdiskFolder(currentWebdiskFolderID);
							});
				}
			});
	return(false);
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
	info.tree.openTo(info.nodeId);
}
function switchWebdiskFolder(folderID)
{
	if(EBID('folderLoading')) EBID('folderLoading').style.display = '';

	MakeXMLRequest(bmAppendSession('webdisk.php?inline=true&folder='+folderID), function(e)
			{
				if(e.readyState == 4)
				{
					webdiskClearInfo();
					currentWebdiskFolderID = folderID;
					EBID('mainContent').innerHTML = e.responseText;
					initWDSel();
					initDnDUpload(EBID('wdDnDArea'), bmAppendSession('webdisk.php?folder='+folderID+'&action=dndUpload'), function()
							{
								switchWebdiskFolder(currentWebdiskFolderID);
							});

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
	document.location.href = 'webdisk.php?sid=' + sid + fld + '&do=changeViewMode&viewmode=' + c.value;
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
	EBID('wdCutLink').className = '';
	EBID('wdCopyLink').className = '';

	// details
	EBID('webdiskDetailInfoNote').style.display = 'none';
	EBID('webdiskDetailInfo').style.display = '';
	//EBID('wdExt').src = 'webdisk.php?action=displayExtension&ext=' + ext + '&sid=' + currentSID;
	EBID('wdTitle').innerHTML = title;
	EBID('wdSize').innerHTML = size;
	EBID('wdDate').innerHTML = date;
	webdiskSetUploader(type == 2 ? uploader : '');
	EBID('wdShared').style.display = shared ? '' : 'none';

	// actions
	EBID('webdiskDetailActionsNote').style.display = 'none';
	EBID('webdiskDetailActions').style.display = '';

	// folder
	EBID('webdiskDetailFolderActions').style.display = type == 1 ? '' : 'none';
	if(EBID('wdStopFileShareLink'))
		EBID('wdStopFileShareLink').style.display = type == 2 && shared ? '' : 'none';

	// file
	EBID('webdiskDetailFileActions').style.display = type == 2 ? '' : 'none';
	EBID('webdiskDetailFileActionsView').style.display = type == 2 && viewable ? '' : 'none';

	// zip
	EBID('webdiskDetailZIPActions').style.display = type==2 && ext=='zip' ? '' : 'none';

	// multiple
	EBID('webdiskMultiActions').style.display = 'none';
}
function webdiskStopFileShare()
{
	if(currentType != 2 || currentID <= 0)
		return;

	if(!confirm(lang['stopsharing_confirm']))
		return;

	document.location.href = bmAppendCsrf('webdisk.php?action=stopFileShare&id=' + currentID
		+ '&folder=' + currentWebdiskFolderID
		+ '&sid=' + currentSID);
}
function webdiskClipboardAction(action)
{
	if(_wdSel.sel.length < 1)
		return;

	transferSelectedWebdiskItems();
	var itemStr = EBID('selectedWebdiskItems').value;

	MakeXMLRequest('webdisk.php?action=clipboardAction&do=' + action + '&items=' + escape(itemStr) + '&sid=' + currentSID,
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
	MakeXMLRequest('webdisk.php?action=renameItem&folder=' + currentFolder + '&type=' + type + '&id=' + id + '&name=' + encodeURIComponent(newName) + '&sid=' + currentSID, function(e)
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
	MakeXMLRequest('webdisk.php?action=createFolder&rpc=true&folder='+currentWebdiskFolderID+'&folderName='+encodeURIComponent(folderName)+'&sid=' + currentSID, function(e)
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

	MakeXMLRequest('webdisk.php?action=getFolderList&sid=' + currentSID, function(http)
			{
				if(http.readyState == 4 && http.responseText.length > 10 && http.responseText.indexOf('var') >= 0)
				{
					EBID('folderList').innerHTML = '';
					eval(http.responseText);
					EBID('folderList').innerHTML = (typeof bmWebdiskFolderTreesHtml == 'function') ? bmWebdiskFolderTreesHtml() : webdisk_d;
					enableWebdiskDragTargets();
					if(typeof attachWebdiskFolderShareActions === 'function')
						attachWebdiskFolderShareActions(typeof webdiskFolderShareActions !== 'undefined' ? webdiskFolderShareActions : {}, 'fa fa-share', webdiskShareTitleWebdisk, webdiskShareTitleFolder);
					if(typeof attachWebdiskFolderRowActions === 'function' && typeof webdiskFolderRowActions !== 'undefined')
						attachWebdiskFolderRowActions(webdiskFolderRowActions);
				}
			});
}
function moveWebdiskItems(items, destFolder)
{
	if(!items) return;
	if(destFolder == currentWebdiskFolderID) return;

	MakeXMLRequest('webdisk.php?action=moveItems&items=' + escape(items) + '&destFolderID=' + destFolder + '&sid=' + currentSID, function(http)
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
