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

var mySID, dHeight, lastFile, lastAddress, lastPath, nextPath;

function _fillContent(req)
{
	if(req.readyState == 4)
	{
		EBID('fileList').innerHTML = req.responseText;
		EBID('fileList').scrollLeft = EBID('fileList').scrollWidth;
		lastPath = nextPath;
		lastFile = -1;
	}
}

function _createFolder(e)
{
	if(e.readyState == 4)
	{
		var responseCode = parseInt(e.responseText);
		if(responseCode != 0)
			changePath(responseCode);
		else
			alert(lang['foldererror']);
	}
}

function createFolder()
{
	var folderName = prompt(lang['folderprompt'], lang['newfolder']);
	if(folderName)
	{
		MakeXMLRequest('webdisk.php?sid=' + mySID + '&action=webdiskDialogCreateFolder&path=' + lastPath + '&title=' + escape(folderName),
			_createFolder);
	}
}

function dialogInit(sid)
{
	dHeight = EBID('fileList').clientHeight;
	mySID = sid;
	changePath(0);
}

function changePath(id)
{
	nextPath = id;
	MakeXMLRequest('webdisk.php?sid=' + mySID + '&action=webdiskDialogContent&path=' + id + '&height=' + dHeight, _fillContent);
}

function changeFile(id, path, title)
{
	EBID('filename').value = title;
	EBID('fileid').value = id;
	EBID('file_' + id).className = 'contentItemActive';
	if(lastFile != -1)
		EBID('file_' + lastFile).className = 'contentItem';
	lastFile = id;
	lastPath = path;
}

function closeOpenDialog(field, param)
{
	parent.overlayDocument().document.getElementById(field).value = EBID('filename').value;
	parent.overlayDocument().document.getElementById(field + '_id').value = lastFile;
	parent.hideOverlay();
}

function closeSaveDialog(field, param)
{
	if(param)
	{
		document.location.href = param + '&filename=' + escape(EBID('filename').value) + '&path=' + lastPath;
	}
	else
	{
		parent.overlayDocument().document.getElementById(field).value = EBID('filename').value;
		parent.overlayDocument().document.getElementById(field + '_id').value = lastFile;
		parent.hideOverlay();
	}
}

function selectAddressItem(item)
{
	if(lastAddress)
		lastAddress.className = 'addressItem';
	item.className = 'addressItemActive';
	lastAddress = item;
}

function fillAddressField(f, data, o)
{
	// clear
	var field = EBID(f);
	if(field.hasChildNodes())
	{
		var node;
		while(node = field.firstChild)
			field.removeChild(node);
	}

	// fill
	for(var i=data.length-1; i>=0; i--)
	{
		addAddressField(data[i], field, i, o);
	}
}

function fillAttAddressField(f, data, o)
{
	// clear
	var field = EBID(f);
	if(field.hasChildNodes())
	{
		var node;
		while(node = field.firstChild)
			field.removeChild(node);
	}

	// fill
	for(var i=data.length-1; i>=0; i--)
	{
		addAttAddressField(data[i], field, i, o);
	}
}

function fillNumberAddressField(f, data, o)
{
	// clear
	var field = EBID(f);
	if(field.hasChildNodes())
	{
		var node;
		while(node = field.firstChild)
			field.removeChild(node);
	}

	// fill
	for(var i=data.length-1; i>=0; i--)
	{
		addAddressField(data[i], field, i, true);
	}
}

function exchangeAddress(a)
{
	var current = a.innerHTML,
		id = a.id.substr(a.id.lastIndexOf('_')+1);
	if(current == Addr[id][1])
		current = Addr[id][2];
	else
		current = Addr[id][1];
	a.innerHTML = current;
}

function addAddressField(data, field, i, o)
{
	var node = document.createElement('div');
	node.className = 'addressItem';
	node.setAttribute('id', '__addrItem_' + field.id + '_' + i);
	node.onclick = function() { selectAddressItem(this); return(false); }

	var image = document.createElement('img');
	image.setAttribute('border', 0);
	image.setAttribute('align', 'absmiddle');
	if(data[1].indexOf("@contact.groups") != -1)
		image.setAttribute('src', tplDir + 'images/li/ico_contact_groups.png');
	else
		image.setAttribute('src', tplDir + 'images/li/addr_priv.png');
	image.setAttribute('width', '16');
	image.setAttribute('height', '16');

	var small = document.createElement('small');
	small.appendChild(document.createTextNode('  - '));

	var link;

	if(o && data[1] != ' - ' && data[2] != ' - ')
	{
		var link = document.createElement('a');
		link.setAttribute('href', '#');
		link.setAttribute('id', 'emailAddress_' + i);
		link.onclick = function() { exchangeAddress(this); return(false); }
		link.appendChild(document.createTextNode(data[1]));
	}
	else
	{
		link = document.createTextNode(data[1] == ' - ' ? data[2] : data[1]);
	}

	small.appendChild(link);

	var leftdiv = document.createElement('div');
	leftdiv.style.cssFloat = 'left';
	leftdiv.appendChild(image);
	leftdiv.appendChild(document.createTextNode(' ' + data[0]));
	leftdiv.appendChild(small);

	if(!o)
	{
		var delimg = document.createElement('img');
		delimg.style.paddingRight = '10px';
		delimg.setAttribute('border', 0);
		delimg.setAttribute('align', 'absmiddle');
		delimg.setAttribute('src', tplDir + 'images/li/ico_delete.png');
		delimg.setAttribute('width', '16');
		delimg.setAttribute('height', '16');
		delimg.setAttribute('id', 'delAddress_' + field.id + '_' + i);
		delimg.style.marginLeft = '10px';
		delimg.onclick = function() { deleteAddress(this); return(false); }

		leftdiv.appendChild(delimg);
	}

	node.appendChild(leftdiv);

	field.appendChild(node);
}

function addAttAddressField(data, field, i, o)
{
	var node = document.createElement('div');
	node.className = 'addressItem';
	node.setAttribute('id', '__addrItem_' + field.id + '_' + i);
	node.onclick = function() { selectAddressItem(this); return(false); }

	var image = document.createElement('img');
	image.setAttribute('border', 0);
	image.setAttribute('align', 'absmiddle');
	image.setAttribute('src', tplDir + 'images/li/addr_priv.png');
	image.setAttribute('width', '16');
	image.setAttribute('height', '16');

	var leftdiv = document.createElement('div');
	leftdiv.style.cssFloat = 'left';
	leftdiv.appendChild(image);
	leftdiv.appendChild(document.createTextNode(' ' + data[2] + ', ' + data[1]));

	if(!o)
	{
		var delimg = document.createElement('img');
		delimg.style.paddingRight = '10px';
		delimg.setAttribute('border', 0);
		delimg.setAttribute('align', 'absmiddle');
		delimg.setAttribute('src', tplDir + 'images/li/ico_delete.png');
		delimg.setAttribute('width', '16');
		delimg.setAttribute('height', '16');
		delimg.setAttribute('id', 'delAddress_' + field.id + '_' + i);
		delimg.style.marginLeft = '10px';
		delimg.onclick = function() { deleteAddress(this); return(false); }

		leftdiv.appendChild(delimg);
	}

	node.appendChild(leftdiv);

	field.appendChild(node);
}

function deleteAddress(img)
{
	var id = img.id.substring(img.id.lastIndexOf('_')+1),
		field = img.id.substring(img.id.indexOf('_')+1);
	field = field.substring(0, field.indexOf('_'));
	EBID(field).removeChild(EBID('__addrItem_' + field + '_' + id));
	if(field == 'to')
		toAddr[id] = null;
	else if(field == 'cc')
		ccAddr[id] = null;
	else if(field == 'bcc')
		bccAddr[id] = null;
	else if(field == 'attendees')
		attAddr[id] = null;
}

function addAddr(whereTo)
{
	if(lastAddress)
	{
		var elem = lastAddress.id.substring(lastAddress.id.lastIndexOf('_')+1);
		var field = EBID(whereTo);
		var myAddr = Addr[elem].slice(0);
		var eid;

		if(EBID('emailAddress_'+elem))
			if(EBID('emailAddress_'+elem).innerHTML == myAddr[1])
				myAddr[2] = ' - ';
			else
				myAddr[1] = ' - ';

		if(whereTo == 'to')
		{
			toAddr.push(myAddr);
			eid = toAddr.length-1;
		}
		else if(whereTo == 'cc')
		{
			ccAddr.push(myAddr);
			eid = ccAddr.length-1;
		}
		else if(whereTo == 'bcc')
		{
			bccAddr.push(myAddr);
			eid = bccAddr.length-1;
		}

		addAddressField(myAddr, field, eid);
	}
}

function addAttendee()
{
	if(lastAddress)
	{
		var elem = lastAddress.id.substring(lastAddress.id.lastIndexOf('_')+1);
		var field = EBID('attendees');
		var myAddr = Addr[elem].slice(0);
		var eid = attAddr.length-1;

		attAddr.push(myAddr);
		addAttAddressField(myAddr, field, eid);
	}
}

function initEMailAddresses(Addr, toAddr, ccAddr, bccAddr)
{
	fillAddressField('addresses', 	Addr,		true);
	fillAddressField('to',			toAddr);
	fillAddressField('cc', 			ccAddr);
	fillAddressField('bcc', 		bccAddr);
}

function initAttendees(Addr, attAddr)
{
	fillAttAddressField('addresses', 	Addr,		true);
	fillAttAddressField('attendees',	attAddr);
}

function initNumbers(Addr)
{
	fillNumberAddressField('addresses', 	Addr,		true);
}

function generateAddressString(addresses)
{
	var ret = '';
	for(var i=0; i<addresses.length; i++)
	{
		var addr = addresses[i];
		if(addr != null && addr[0] != '' && addr[0] != ' - ')
			ret += ', "' + addr[0].replace('"', '') + '" <' + (addr[1] == ' - ' ? addr[2] : addr[1]) + '>';
		else if(addr != null)
			ret += ', <' + (addr[1] == ' - ' ? addr[2] : addr[1]) + '>';
	}
	return(ret.substring(2));
}

function generateAttendeeString(addresses)
{
	var ret = '';
	for(var i=0; i<addresses.length; i++)
	{
		var addr = addresses[i];
		if(addr != null)
			ret += ';' + addr[0] + ',' + addr[2] + ',' + addr[1];
	}
	return(ret.substring(1));
}

function submitAddressDialog(mode)
{
	if(mode != 'handy')
	{
		parent.document.getElementById('to').value = generateAddressString(toAddr);
		parent.document.getElementById('cc').value = generateAddressString(ccAddr);
		parent.document.getElementById('bcc').value = generateAddressString(bccAddr);

		if(parent.document.getElementById('bcc').value.length > 0
			&& parent.document.getElementById('advanced_fields_body').style.display!='')
			parent.advancedOptions('fields', 'right', 'bottom', tplDir);
		parent.hideOverlay();
	}
}

function submitNumberDialog()
{
	if(lastAddress)
	{
		var elem = lastAddress.id.substring(lastAddress.id.lastIndexOf('_')+1);
		var number;

		if(EBID('emailAddress_'+elem))
			number = EBID('emailAddress_'+elem).innerHTML;
		else
			if(Addr[elem][1] != ' - ')
				number = Addr[elem][1];
			else
				number = Addr[elem][2];

		number = number.replace('+', '00');
		number = number.replace(/[^0-9]/g, '');

		var toPre = parent.document.getElementById('to_pre'),
			toNo = parent.document.getElementById('to_no'),
			to = parent.document.getElementById('to');

		if(toPre && toNo)
		{
			for(var i=0; i<toPre.options.length; i++)
			{
				if(number.length > toPre.options[i].text.length
					&& number.substr(0, toPre.options[i].text.length) == toPre.options[i].text)
				{
					toPre.selectedIndex = i;
					toNo.value = number.substr(toPre.options[i].text.length);
					break;
				}
			}
		}

		else if(to)
		{
			to.value = number;
		}
	}

	parent.hideOverlay();
}

function submitAttendeeDialog()
{
	parent.document.getElementById('attendees').value = generateAttendeeString(attAddr);
	parent.generateAttendeeList();
	parent.hideOverlay();
}
