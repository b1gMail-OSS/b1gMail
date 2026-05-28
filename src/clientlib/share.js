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

var share_locationBar, share_contentLayer, share_tplDir,
	share_newFolder, share_currentFolder = -1, share_oldFolder,
	share_userName = '', share_currentPW = '', share_currentPWfor = 0,
	share_tablerMode = false, share_fileToken = '';

function shareInit(userName, tplDir, tablerMode, fileToken)
{
	share_userName = userName;
	share_locationBar = EBID('locationBar');
	share_contentLayer = EBID('contentLayer');
	share_tplDir = tplDir;
	share_tablerMode = !!tablerMode;
	share_fileToken = fileToken || '';

	shareOpenFolder(0);
}

function shareClearNode(node)
{
	if(!node)
		return;

	while(node.firstChild)
		node.removeChild(node.firstChild);
}

function shareIsFolder(item)
{
	return item['type'] == 1 || item['type'] == '1';
}

function shareGetItemIcon(item, pathContext)
{
	if(item['icon'])
		return item['icon'];

	if(shareIsFolder(item))
	{
		if(pathContext && (item['id'] == 0 || item['id'] == '0'))
			return 'ti-home';
		if(item['ext'] == '.SHAREDFOLDER')
			return 'ti-folder-share';

		return 'ti-folder';
	}

	return 'ti-file';
}

function shareFormatModified(ts)
{
	var modified = new Date(ts * 1000),
		modifiedDate = '';

	if(modified.getDate() < 10)
		modifiedDate += '0' + modified.getDate() + '.';
	else
		modifiedDate += modified.getDate() + '.';
	if(modified.getMonth() < 9)
		modifiedDate += '0' + (modified.getMonth() + 1) + '.';
	else
		modifiedDate += (modified.getMonth() + 1) + '.';
	modifiedDate += modified.getYear() < 999 ? modified.getYear() + 1900 : modified.getYear();

	return modifiedDate;
}

function shareFormatSize(size)
{
	if(size < 1024)
		return size + ' B';
	if(size < 1024 * 1024)
		return Math.round(size / 1024) + ' KB';
	if(size < 1024 * 1024 * 1024)
		return Math.round(size / 1024 / 1024) + ' MB';

	return Math.round(size / 1024 / 1024 / 1024 * 10) / 10 + ' GB';
}

function shareCreateIconElement(item, pathContext)
{
	var icon = document.createElement('i');
	icon.className = 'ti ' + shareGetItemIcon(item, pathContext)
		+ (share_tablerMode ? ' bm-share-item-icon' : '');
	icon.setAttribute('aria-hidden', 'true');
	return icon;
}

function shareGetPathItemIcon(item)
{
	if(item['id'] == 0 || item['id'] == '0')
		return(null);

	if(item['icon'])
		return(item['icon']);

	if(item['ext'] == '.SHAREDFOLDER' || item['share'] == 'yes' || item['share'] === '1')
		return('ti-folder-share');

	return('ti-folder');
}

function shareCreatePathIconElement(item)
{
	var iconClass = shareGetPathItemIcon(item);

	if(!iconClass)
		return(null);

	var icon = document.createElement('i');
	icon.className = 'ti ' + iconClass + ' bm-share-breadcrumb-icon';
	icon.setAttribute('aria-hidden', 'true');
	return(icon);
}

function shareAppendBreadcrumbSegment(path, pathItem, isLast)
{
	var el, pathIcon;

	if(isLast)
	{
		el = document.createElement('span');
		el.className = 'bm-share-breadcrumb-item bm-share-breadcrumb-item--current';
	}
	else
	{
		el = document.createElement('a');
		el.className = 'bm-share-breadcrumb-item';
		el.href = 'javascript:shareOpenFolder(' + pathItem['id'] + ', ' + (pathItem['pw'] || pathItem['share_pw']) + ')';
	}

	pathIcon = shareCreatePathIconElement(pathItem);
	if(pathIcon)
		el.appendChild(pathIcon);
	el.appendChild(document.createTextNode(pathItem['title']));
	path.appendChild(el);
}

function shareParseFolder(xml)
{
	var result = [],
		pathResult = [],
		nodes = xml.getElementsByTagName('contents').item(0).firstChild.childNodes,
		pathNodes = xml.getElementsByTagName('path').item(0).firstChild.childNodes;

	for(var i = 0; i < nodes.length; i++)
	{
		var node = nodes[i];
		if(node.nodeName == 'item')
		{
			var array = node.childNodes[0].childNodes,
				item = new Object;

			for(var j = 0; j < array.length; j++)
				if(array[j].nodeType == 1)
					if(array[j].firstChild)
						item[array[j].nodeName] = array[j].firstChild.data;
					else
						item[array[j].nodeName] = '';

			result.push(item);
		}
	}
	for(var i = 0; i < pathNodes.length; i++)
	{
		var node = pathNodes[i];
		if(node.nodeName == 'item')
		{
			var array = node.childNodes[0].childNodes,
				item = new Object;

			for(var j = 0; j < array.length; j++)
				if(array[j].nodeType == 1)
					if(array[j].firstChild)
						item[array[j].nodeName] = array[j].firstChild.data;
					else
						item[array[j].nodeName] = '';

			pathResult.push(item);
		}
	}

	var res = new Object;
	res['contents'] = result;
	res['path'] = pathResult;
	return(res);
}

function shareDisplayContentTabler(data)
{
	var table = EBID('shareContentBody') || EBID('contentTable'),
		path = EBID('locationBar');

	shareClearNode(table);

	if(data['contents'].length === 0)
	{
		var emptyRow = document.createElement('tr');
		emptyRow.className = 'bm-share-empty';
		var emptyCell = document.createElement('td');
		emptyCell.colSpan = 4;
		emptyCell.className = 'text-center text-secondary py-5';
		emptyCell.appendChild(document.createTextNode(
			typeof lang !== 'undefined' && lang['nothingfound']
				? lang['nothingfound']
				: '—'));
		emptyRow.appendChild(emptyCell);
		table.appendChild(emptyRow);
	}
	else
	{
		for(var i = 0; i < data['contents'].length; i++)
		{
			var item = data['contents'][i],
				tr = document.createElement('tr'),
				tdTitle = document.createElement('td'),
				tdModified = document.createElement('td'),
				tdSize = document.createElement('td'),
				tdActions = document.createElement('td'),
				aLink = document.createElement('a');

			aLink.className = 'bm-share-item-link text-reset';
			if(shareIsFolder(item))
				aLink.href = 'javascript:shareOpenFolder(' + item['id'] + ', ' + item['pw'] + ')';
			else
				aLink.href = 'javascript:shareOpenFile(' + item['id'] + ', ' + (item['pw'] || item['share_pw']) + ')';

			aLink.appendChild(shareCreateIconElement(item, false));
			aLink.appendChild(document.createTextNode(item['title']));
			tdTitle.appendChild(aLink);

			tdModified.className = 'text-secondary';
			tdModified.appendChild(document.createTextNode(shareFormatModified(item['modified'])));

			tdSize.className = 'text-secondary text-end';
			tdSize.appendChild(document.createTextNode(
				shareIsFolder(item) ? '—' : shareFormatSize(item['size'])));

			tdActions.className = 'text-end';
			if(!shareIsFolder(item) && (item['type'] == 2 || item['type'] == '2'))
			{
				var btn = document.createElement('a');
				btn.className = 'btn btn-sm btn-ghost-primary';
				btn.href = 'javascript:shareOpenFile(' + item['id'] + ', ' + (item['pw'] || item['share_pw']) + ')';
				btn.title = typeof lang !== 'undefined' && lang['download']
					? lang['download']
					: 'Download';
				btn.innerHTML = '<i class="ti ti-download" aria-hidden="true"></i>';
				tdActions.appendChild(btn);
			}

			tr.appendChild(tdTitle);
			tr.appendChild(tdModified);
			tr.appendChild(tdSize);
			tr.appendChild(tdActions);
			table.appendChild(tr);
		}
	}

	shareClearNode(path);

	for(var i = 0; i < data['path'].length; i++)
	{
		var pathItem = data['path'][i],
			isLast = (i === data['path'].length - 1);

		if(i > 0)
		{
			var sep = document.createElement('i');
			sep.className = 'ti ti-chevron-right bm-share-breadcrumb-sep';
			sep.setAttribute('aria-hidden', 'true');
			path.appendChild(sep);
		}

		shareAppendBreadcrumbSegment(path, pathItem, isLast);
	}
}

function shareDisplayContentLegacy(data)
{
	var table2 = EBID('contentTable'),
		path = EBID('locationBar'),
		title = EBID('titleLayer'),
		table = document.createElement('tbody');

	if(table2.hasChildNodes())
	{
		var node;
		while(node = table2.firstChild)
			table2.removeChild(node);
	}

	var trClass = 'trRow1';
	for(var i = 0; i < data['contents'].length; i++)
	{
		var item = data['contents'][i],
			tr = document.createElement('tr'),
			tdTitle = document.createElement('td'),
			tdModified = document.createElement('td'),
			tdSize = document.createElement('td'),
			tdActions = document.createElement('td'),
			imgIcon = document.createElement('img'),
			imgDownload = document.createElement('img'),
			aLink = document.createElement('a'),
			aDownloadLink = document.createElement('a');

		var modifiedDate = shareFormatModified(item['modified']),
			size = item['size'];

		if(size < 1024)
			size += ' B';
		else if(size < 1024 * 1024)
			size = Math.round(size / 1024) + ' KB';
		else if(size < 1024 * 1024 * 1024)
			size = Math.round(size / 1024 / 1024) + ' MB';

		tr.className = trClass;
		imgIcon.setAttribute('border', '0');
		imgIcon.setAttribute('src', share_tplDir + 'images/li/webdisk_'
			+ (item['type'] == 1 ? 'folder' : 'file') + '.png');
		imgIcon.setAttribute('align', 'absmiddle');
		if(item['type'] == 1)
			aLink.href = 'javascript:shareOpenFolder(' + item['id'] + ', ' + item['pw'] + ')';
		else
			aLink.href = 'javascript:shareOpenFile(' + item['id'] + ', ' + (item['pw'] || item['share_pw']) + ')';
		aLink.setAttribute('style', 'display:block');
		aLink.appendChild(imgIcon);
		aLink.appendChild(document.createTextNode(' ' + item['title']));
		tdTitle.appendChild(aLink);
		tdTitle.className = 'tdTitle';
		tdModified.appendChild(document.createTextNode(modifiedDate));
		tdModified.className = 'tdModified';
		tdSize.appendChild(document.createTextNode(item['type'] == 1 ? '-' : size));
		tdSize.className = 'tdSize';

		if(item['type'] == 1)
			tdActions.appendChild(document.createTextNode(' '));
		else if(item['type'] == 2)
		{
			imgDownload.setAttribute('src', share_tplDir + 'images/li/ico_download.png');
			imgDownload.setAttribute('border', '0');
			imgDownload.setAttribute('align', 'absmiddle');
			aDownloadLink.setAttribute('href', 'javascript:shareOpenFile(' + item['id'] + ', ' + (item['pw'] || item['share_pw']) + ')');
			aDownloadLink.appendChild(imgDownload);
			tdActions.appendChild(aDownloadLink);
		}

		tdActions.className = 'tdActions';

		tr.appendChild(tdTitle);
		tr.appendChild(tdModified);
		tr.appendChild(tdSize);
		tr.appendChild(tdActions);
		table.appendChild(tr);

		if(trClass == 'trRow1')
			trClass = 'trRow2';
		else
			trClass = 'trRow1';
	}

	shareClearNode(title);
	shareClearNode(path);

	for(var i = 0; i < data['path'].length; i++)
	{
		var pathItem = data['path'][i],
			aLink = document.createElement('a'),
			imgIcon = document.createElement('img');

		imgIcon.setAttribute('border', '0');
		imgIcon.setAttribute('src', share_tplDir + 'images/li/'
			+ (pathItem['id'] == 0 ? 'ico_share' : 'webdisk_folder') + '.png');
		imgIcon.setAttribute('align', 'absmiddle');

		aLink.setAttribute('href', 'javascript:shareOpenFolder(' + pathItem['id'] + ', ' + pathItem['share_pw'] + ')');
		aLink.appendChild(imgIcon);
		aLink.appendChild(document.createTextNode(' ' + pathItem['title']));

		path.appendChild(aLink);

		if(i < data['path'].length - 1)
		{
			var imgArrow = document.createElement('img');
			imgArrow.setAttribute('border', '0');
			imgArrow.setAttribute('src', share_tplDir + 'images/share/arrow.png');
			imgArrow.setAttribute('align', 'absmiddle');
			imgArrow.setAttribute('style', 'padding-left:5px;padding-right:5px;width:5px;height:5px;');
			path.appendChild(imgArrow);
		}
		else
		{
			var titleIcon = EBID('titleIcon');
			if(titleIcon)
				titleIcon.src = share_tplDir + 'images/li/'
					+ (pathItem['id'] == 0 ? 'ico_share' : 'webdisk_folder') + '.png';
			title.appendChild(document.createTextNode(pathItem['title']));
		}
	}

	table2.appendChild(table);
}

function shareDisplayContent(data)
{
	if(share_tablerMode)
		shareDisplayContentTabler(data);
	else
		shareDisplayContentLegacy(data);
}

function _shareOpenFolder(obj)
{
	if(obj.readyState == 4)
	{
		var folderData = shareParseFolder(obj.responseXML);
		share_oldFolder = share_currentFolder;
		share_currentFolder = share_newFolder;
		shareDisplayContent(folderData);
	}
}

function shareOpenFolder(folderID, needPW)
{
	if(needPW && (share_currentPWfor != folderID))
	{
		openOverlay('index.php?user=' + share_userName + '&action=passwordInput&folder=' + folderID,
			lang['protectedfolder'],
			share_tablerMode ? 480 : 450,
			share_tablerMode ? 220 : 140,
			true);
		return;
	}

	if(folderID != share_currentFolder)
	{
		share_newFolder = folderID;
		MakeXMLRequest('index.php'
			+ '?action=getFolder'
			+ '&user=' + share_userName
			+ '&id=' + folderID
			+ (share_fileToken ? '&file=' + encodeURIComponent(share_fileToken) : '')
			+ '&password=' + escape(share_currentPW),
			_shareOpenFolder);
	}
}

function shareOpenFile(fileID, needPW)
{
	if(needPW && (share_currentPWfor != ('f' + fileID)))
	{
		openOverlay('index.php?user=' + share_userName + '&action=passwordInputFile&id=' + fileID + '&file=' + encodeURIComponent(share_fileToken),
			lang['protectedfolder'],
			share_tablerMode ? 480 : 450,
			share_tablerMode ? 220 : 140,
			true);
		return;
	}

	document.location.href = 'index.php'
		+ '?action=getFile'
		+ '&user=' + share_userName
		+ '&id=' + fileID
		+ (share_fileToken ? '&file=' + encodeURIComponent(share_fileToken) : '')
		+ '&password=' + escape(share_currentPW);
}

function shareEnterProtectedDir()
{
	window.setTimeout('shareOpenFolder('+share_currentPWfor+')', 100);
}

function shareEnterProtectedFile()
{
	window.setTimeout('shareOpenFile(' + String(share_currentPWfor).substring(1) + ', false)', 100);
}
