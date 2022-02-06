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
function showWebdiskItemDetails(type, id)
{
	_lastSelectedWebdiskID = type;
	_lastSelectedWebdiskType = id;
	var _requestTimeWDFolder = currentWebdiskFolderID;

	MakeXMLRequest('webdisk.php?action=itemInfo&type='+escape(type)+'&id='+escape(id)+'&sid='+currentSID, function(e)
			{
				if(e.readyState == 4)
				{
					if(_requestTimeWDFolder != currentWebdiskFolderID)
						return;
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

						webdiskShowInfo(
							type,
							title,
							shortTitle,
							size,
							ext,
							created,
							id,
							share,
							viewable);
					}
				}
			});
}
function selectedWebdiskCountChanged(no)
{
	if(no <= 1)
		return;

	_lastSelectedWebdiskID = 0;
	_lastSelectedWebdiskType = 0;

	// reset links
	EBID('wdCutLink').className = '';
	EBID('wdCopyLink').className = '';

	// details
	EBID('webdiskDetailInfoNote').style.display = 'none';
	EBID('webdiskDetailInfo').style.display = '';
	EBID('wdExt').src = tplDir + 'images/li/drag_wditems.png';
	EBID('wdTitle').innerHTML = no + ' ' + lang['items'];
	EBID('wdSize').innerHTML = '-';
	EBID('wdDate').innerHTML = '-';
	EBID('wdShared').style.display = 'none';

	// actions
	EBID('webdiskDetailActionsNote').style.display = 'none';
	EBID('webdiskDetailActions').style.display = 'none';

	// folder
	EBID('webdiskDetailFolderActions').style.display = 'none';

	// file
	EBID('webdiskDetailFileActions').style.display = 'none';
	EBID('webdiskDetailFileActionsView').style.display = 'none';

	// zip
	EBID('webdiskDetailZIPActions').style.display = 'none';

	// multiple
	EBID('webdiskMultiActions').style.display = '';
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
	MakeXMLRequest('webdisk.php?inline=true&do=uploadFilesForm&fileCount='+escape(EBID('fileCount').value)+'&folder='+currentWebdiskFolderID+'&sid='+currentSID, function(e)
			{
				if(e.readyState == 4)
				{
					EBID('mainContent').innerHTML = e.responseText;
					initDnDUpload(EBID('wdDnDArea'), 'webdisk.php?sid='+currentSID+'&folder='+currentWebdiskFolderID+'&action=dndUpload', function()
							{
								switchWebdiskFolder(currentWebdiskFolderID);
							});
				}
			});
	return(false);
}
function webdiskGetTreeIDbyFolderID(folderID)
{
	var folderList = EBID('folderList');
	var treeAs = folderList.getElementsByTagName('a');

	for(var i=0; i<treeAs.length; i++)
	{
		var a = treeAs[i];

		if(a.href.indexOf('switchWebdiskFolder') < 0) continue;
		if(a.href.indexOf('('+folderID+')') > 0)
		{
			var idx = a.id.indexOf('webdisk_d');

			return(parseInt(a.id.substring(idx+9)));
		}

		continue;
	}

	return(0);
}
function switchWebdiskFolder(folderID)
{
	if(EBID('folderLoading')) EBID('folderLoading').style.display = '';

	MakeXMLRequest('webdisk.php?inline=true&folder='+folderID+'&sid='+currentSID, function(e)
			{
				if(e.readyState == 4)
				{
					webdiskClearInfo();
					currentWebdiskFolderID = folderID;
					EBID('mainContent').innerHTML = e.responseText;
					initWDSel();
					initDnDUpload(EBID('wdDnDArea'), 'webdisk.php?sid='+currentSID+'&folder='+folderID+'&action=dndUpload', function()
							{
								switchWebdiskFolder(currentWebdiskFolderID);
							});

					var treeID = webdiskGetTreeIDbyFolderID(folderID);
					if(treeID > 0)
					{
						//webdisk_d.closeAll();
						webdisk_d.openTo(treeID);
					}

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
function webdiskShowInfo(type, fullTitle, title, size, ext, date, id, shared, viewable)
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
	EBID('wdExt').src = 'webdisk.php?action=displayExtension&ext=' + ext + '&sid=' + currentSID;
	EBID('wdTitle').innerHTML = title;
	EBID('wdSize').innerHTML = type == 1 ? ' - ' : size;
	EBID('wdDate').innerHTML = date;
	EBID('wdShared').style.display = shared ? '' : 'none';

	// actions
	EBID('webdiskDetailActionsNote').style.display = 'none';
	EBID('webdiskDetailActions').style.display = '';

	// folder
	EBID('webdiskDetailFolderActions').style.display = type == 1 ? '' : 'none';

	// file
	EBID('webdiskDetailFileActions').style.display = type == 2 ? '' : 'none';
	EBID('webdiskDetailFileActionsView').style.display = type == 2 && viewable ? '' : 'none';

	// zip
	EBID('webdiskDetailZIPActions').style.display = type==2 && ext=='zip' ? '' : 'none';

	// multiple
	EBID('webdiskMultiActions').style.display = 'none';
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
					EBID('folderList').innerHTML = webdisk_d;
					enableWebdiskDragTargets();
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
