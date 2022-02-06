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
	share_userName = '', share_currentPW = '', share_currentPWfor = 0;

function shareInit(userName, tplDir)
{
	share_userName = userName;
	share_locationBar = EBID('locationBar');
	share_contentLayer = EBID('contentLayer');
	share_tplDir = tplDir;

	shareOpenFolder(0);
}

function shareParseFolder(xml)
{
	var result = [],
		pathResult = [],
		nodes = xml.getElementsByTagName('contents').item(0).firstChild.childNodes,
		pathNodes = xml.getElementsByTagName('path').item(0).firstChild.childNodes;

	for(var i=0; i<nodes.length; i++)
	{
		var node = nodes[i];
		if(node.nodeName == 'item')
		{
			var array = node.childNodes[0].childNodes,
				item = new Object;

			for(var j=0; j<array.length; j++)
				if(array[j].nodeType == 1)
					if(array[j].firstChild)
						item[array[j].nodeName] = array[j].firstChild.data;
					else
						item[array[j].nodeName] = '';

			result.push(item);
		}
	}
	for(var i=0; i<pathNodes.length; i++)
	{
		var node = pathNodes[i];
		if(node.nodeName == 'item')
		{
			var array = node.childNodes[0].childNodes,
				item = new Object;

			for(var j=0; j<array.length; j++)
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

function shareDisplayContent(data)
{
	var table2 = EBID('contentTable'),
		path = EBID('locationBar'),
		title = EBID('titleLayer'),
		table = document.createElement('tbody');

	// remove table contents
	if(table2.hasChildNodes())
	{
		var node;
		while(node = table2.firstChild)
			table2.removeChild(node);
	}

	// fill table
	var trClass = 'trRow1';
	for(var i=0; i<data['contents'].length; i++)
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

		// prepare data
		var modified = new Date(item['modified']*1000),
			modifiedDate = '',
			size = item['size'];
		if(modified.getDate() < 10)
			modifiedDate += '0' + modified.getDate() + '.';
		else
			modifiedDate += modified.getDate() + '.';
		if(modified.getMonth() < 9)
			modifiedDate += '0' + (modified.getMonth()+1) + '.';
		else
			modifiedDate += (modified.getMonth()+1) + '.';
		modifiedDate += modified.getYear() < 999 ? modified.getYear() + 1900 : modified.getYear();

		if(size < 1024)
			size += ' B';
		else if(size < 1024*1024)
			size = Math.round(size/1024) + ' KB';
		else if(size < 1024*1024*1024)
			size = Math.round(size/1024/1024) + ' MB';

		// prepare row
		tr.className = trClass;
		imgIcon.setAttribute('border', '0');
		imgIcon.setAttribute('src', share_tplDir + 'images/li/webdisk_'
			+ (item['type'] == 1 ? 'folder' : 'file') + '.png');
		imgIcon.setAttribute('align', 'absmiddle');
		if(item['type'] == 1)
			aLink.href = 'javascript:shareOpenFolder(' + item['id'] + ', ' + item['pw'] + ')';
		else
			aLink.href = 'javascript:shareOpenFile(' + item['id'] + ')';
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
			aDownloadLink.setAttribute('href', 'javascript:shareOpenFile(' + item['id'] + ')');
			aDownloadLink.appendChild(imgDownload);
			tdActions.appendChild(aDownloadLink);
		}

		tdActions.className = 'tdActions';

		// insert row
		tr.appendChild(tdTitle);
		tr.appendChild(tdModified);
		tr.appendChild(tdSize);
		tr.appendChild(tdActions);
		table.appendChild(tr);

		// swap class
		if(trClass == 'trRow1')
			trClass = 'trRow2';
		else
			trClass = 'trRow1';
	}

	// remove title contents
	if(title.hasChildNodes())
	{
		var node;
		while(node = title.firstChild)
			title.removeChild(node);
	}

	// remove path contents
	if(path.hasChildNodes())
	{
		var node;
		while(node = path.firstChild)
			path.removeChild(node);
	}

	// fill path
	for(var i=0; i<data['path'].length; i++)
	{
		var item = data['path'][i],
			aLink = document.createElement('a'),
			imgIcon = document.createElement('img');

		imgIcon.setAttribute('border', '0');
		imgIcon.setAttribute('src', share_tplDir + 'images/li/'
			+ (item['id'] == 0 ? 'ico_share' : 'webdisk_folder') + '.png');
		imgIcon.setAttribute('align', 'absmiddle');

		aLink.setAttribute('href', 'javascript:shareOpenFolder(' + item['id'] + ', ' + item['share_pw'] + ')');
		aLink.appendChild(imgIcon);
		aLink.appendChild(document.createTextNode(' ' + item['title']));

		path.appendChild(aLink);

		if(i < data['path'].length-1)
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
			EBID('titleIcon').src = share_tplDir + 'images/li/'
			+ (item['id'] == 0 ? 'ico_share' : 'webdisk_folder') + '.png';
			title.appendChild(document.createTextNode(item['title']));
		}
	}

	table2.appendChild(table);
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
			450,
			140,
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
			+ '&password=' + escape(share_currentPW),
			_shareOpenFolder);
	}
}

function shareOpenFile(fileID)
{
	document.location.href = 'index.php'
		+ '?action=getFile'
		+ '&user=' + share_userName
		+ '&id=' + fileID
		+ '&password=' + escape(share_currentPW);
}

function shareEnterProtectedDir()
{
	window.setTimeout('shareOpenFolder('+share_currentPWfor+')', 100);
}
