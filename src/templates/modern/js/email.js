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

var _mailSel;
var draftAutoSaveStarted = false;
var _currentPreviewMailID = 0;

function loadDraft(id)
{
	document.location.href = 'email.compose.php?redirect='+encodeURIComponent(id)+'&sid='+currentSID;
}

function hideDraftNote(setNoNotify, draftID)
{
	var note = EBID('draftNote');
	if(!note)
		return;
	note.style.display = 'none';
	composeSizer(true);

	if(setNoNotify)
	{
		if(EBID('deleteDraft') && EBID('deleteDraft').checked)
			MakeXMLRequest('email.compose.php?action=deleteDraft&id='+draftID+'&sid='+currentSID, false);
		else
			MakeXMLRequest('email.compose.php?action=setNoDraftNotify&sid='+currentSID, false);
	}
}

function beginDraftAutoSave()
{
	if(draftAutoSaveStarted || !autoSaveDrafts || autoSaveDraftsInterval <= 0)
		return;

	editor.submit();
	if(EBID('subject').length == 0 && EBID('emailText').length == 0)
		return;

	hideDraftNote();
	draftAutoSaveStarted = true;
	window.setTimeout(saveDraft, 1000*autoSaveDraftsInterval);
}

function saveDraft()
{
	editor.submit();

	var form = document.forms.f1;
	var oldDo = EBID('do').value;
	EBID('do').value = 'saveDraft';
	var formData = ajaxFormData(form) + '&autoSave=true';
	EBID('do').value = oldDo;

	var xh = GetXMLHTTP();
	if(xh)
	{
		xh.open('POST', form.action, true);
		xh.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		var saveDate = new Date();

		xh.onreadystatechange = function()
		{
			if(xh.readyState == 4)
			{
				var draftID = parseInt(xh.responseText);
				if(draftID > 0)
				{
					EBID('baseDraftID').value = draftID;
					EBID('autoSaveNote').innerHTML = '<small><i class="fa fa-floppy-o"></i> '
						+ lang['lastsavedat'].replace('%1', ('0'+saveDate.getHours()).slice(-2))
											 .replace('%2', ('0'+saveDate.getMinutes()).slice(-2))
						+ '</small>';
					window.setTimeout(saveDraft, 1000*autoSaveDraftsInterval);
				}
			}
		};

		xh.send(formData);
	}
}

function readMailHideBottomLayers()
{
	var elements = document.getElementsByTagName('div');
	for(var i=0; i<elements.length; i++)
	{
		if(elements[i].id.length > 12
				&& elements[i].id.substr(0, 12) == 'bottomLayer_')
		{
			elements[i].style.display = 'none';
		}
	}

	EBID('mailReadScrollContainer').setAttribute('class', 'scrollContainer withBottomBar');
}

function readMailShowBottomLayer(name)
{
	var justClose = EBID('bottomLayer_'+name).style.display == '';

	readMailHideBottomLayers();

	if(!justClose)
	{
		EBID('mailReadScrollContainer').setAttribute('class', 'scrollContainer withBottomBarAndLayer');
		EBID('bottomLayer_'+name).style.display = '';
	}
}

function updatePreviewPosition(s)
{
	document.location.href = 'email.php?folder='+currentFolderID+'&do=setPreviewPosition&pos='+escape(s.value)+'&sid='+currentSID;
}

function _switchFolder(e, retainPreviewMail)
{
	if(e.readyState == 4)
	{
		_lastSelectedMailID = 0;
		var folderObj = eval('('+e.responseText+')');

		currentFolderID = folderObj.id;
		currentSortColumn = folderObj.sortColumn;
		currentSortOrder = folderObj.sortOrder;
		currentPageNo = folderObj.pageNo;
		currentPageCount = folderObj.pageCount;

		initFolderRefresh(currentFolderID, 0);
		setWindowTitle(folderObj.windowTitle);

		EBID('folderMailArea').innerHTML = folderObj.html;
		initMailSel();

		if(EBID('previewArea'))
		{
			if(retainPreviewMail)
			{
				if(EBID('mail_' + _currentPreviewMailID + '_ntr'))
				{
					_mailSel.select(EBID('mail_' + _currentPreviewMailID + '_ntr'));
				}
				else
				{
					EBID('previewArea').innerHTML = '';
					showMultiSelPreview(0);
				}
			}
			else
			{
				EBID('previewArea').innerHTML = '';
				showMultiSelPreview(0);
			}
		}

		if(EBID('folderLoading')) EBID('folderLoading').style.display = 'none';
	}
}

function switchFolder(folderID, retainPreviewMail)
{
	if(EBID('folderMailArea'))
	{
		if(!retainPreviewMail)
		{
			_currentPreviewMailID = 0;
			if(EBID('previewArea'))
				EBID('previewArea').innerHTML = '';
		}
		if(EBID('folderLoading')) EBID('folderLoading').style.display = '';
		MakeXMLRequest('email.php?inline=true&folder='+folderID+(typeof(folderNarrowView)!='undefined'&&folderNarrowView?'&narrow=true':'')+'&sid='+currentSID, function(e) {
			_switchFolder(e, retainPreviewMail);
		});
	}
	else
	{
		document.location.href = 'email.php?folder='+folderID+'&sid='+currentSID;
	}
}

function initMailSel()
{
	var sel = new selecTable(EBID('mailTable'), 'tr', true);
	sel.cbGetItemID = function(element)
	{
		return(element.id.substr(5, element.id.length-9));
	}
	sel.cbRowFilter = function(element)
	{
		return(element.id.length > 9 && element.id.substr(0, 5) == 'mail_' && element.id.substr(element.id.length-4) == '_ntr');
	}
	sel.cbSelectSingleItem = function(element)
	{
		if(EBID('previewArea'))
			togglePreviewPane(this.getItemID(element), tplDir, currentSID);
	}
	sel.cbSelectionChanged = function()
	{
		if(this.sel.length <= 1 || !EBID('previewArea') || !EBID('multiSelPreview'))
			return;
		_lastSelectedMailID = 0;
		showMultiSelPreview(this.sel.length);
	}
	sel.cbItemContextMenu = function(element, event)
	{
		currentID = this.getItemID(element);
		showMailMenu(event);
	}
	sel.cbMultiItemsContextMenu = function(elements, event)
	{
		if(elements.length == 0)
			return;

		var itemIDs = [];
		for (var i=0; i<elements.length; ++i)
			itemIDs.push(this.getItemID(elements[i]));

		currentIDs = itemIDs;
		showMailMenu(event, null, true);
	}
	sel.cbItemDragStart = function(element, event)
	{
		var dragImg = document.createElement('img');
		dragImg.src = tplDir + 'images/li/drag_email' + (this.sel.length>1?'s':'') + '.png';
		dragImg.width = 32;
		dragImg.height = 32;

		event.dataTransfer.setData('emails', this.getIDList());
		event.dataTransfer.setDragImage(dragImg, -10, -10);

		return(true);
	}
	sel.cbItemDoubleClick = function(element)
	{
		if(currentFolderID == -3)
			document.location.href = 'email.compose.php?redirect=' + this.getItemID(element) + '&sid=' + currentSID;
		else
			document.location.href = 'email.read.php?id=' + this.getItemID(element) + '&sid=' + currentSID;
	}
	sel.multiContextMenu = true;
	sel.init();
	_mailSel = sel;
}

function switchPage(page)
{
	_currentPreviewMailID = 0;
	if(EBID('previewArea'))
		EBID('previewArea').innerHTML = '';
	if(EBID('folderLoading')) EBID('folderLoading').style.display = '';
	MakeXMLRequest('email.php?inline=true&folder='+currentFolderID+(typeof(folderNarrowView)!='undefined'&&folderNarrowView?'&narrow=true':'')+'&page='+page+'&sort='+currentSortColumn+'&order='+currentSortOrder+'&sid='+currentSID, _switchFolder);
}

function composeSizer(justFire)
{
	var or = function()
	{
		var height = getElementMetrics(EBID('mainContent'), 'h') - 1;
		if(EBID('draftNote') && EBID('draftNote').style.display != 'none')
			height -= getElementMetrics(EBID('draftNote'), 'h');
		height -= getElementMetrics(EBID('composeHeader'), 'h');
		height -= getElementMetrics(EBID('contentHeader'), 'h');
		height -= getElementMetrics(EBID('contentFooter'), 'h');

		if(EBID('safecodeFooter'))
			height -= getElementMetrics(EBID('safecodeFooter'), 'h');

		if(height < 240) height = 240;

		EBID('composeText').style.height = height + 'px';

		if(editor)
			editor.setHeight(height);

		if(typeof(IE7) != 'undefined') IE7.recalc();
	}

	if(!justFire)
		addEvent(window, 'resize', or);

	or();
}

function readSizer()
{
	var textArea = EBID('textArea');

	var height = getElementMetrics(EBID('mainContent'), 'h');
	height -= getElementMetrics(EBID('contentHeader'), 'h');
	height -= getElementMetrics(EBID('mailHeader'), 'h');
	height -= getElementMetrics(EBID('bigFormToolbar'), 'h');
	height -= getElementMetrics(EBID('afterText'), 'h');
	height -= getElementMetrics(EBID('textP'), 'h') - getElementMetrics(textArea, 'h');
	height -= 50;

	if(height < 150)
		height = 150;

	textArea.style.height = height + 'px';
}

function folderViewOptions(folderID)
{
	openOverlay('email.php?sid=' + currentSID + '&do=viewOptions&folder=' + folderID,
		lang['viewoptions'],
		650,
		140,
		true);
}

function registerFolderHotkeyHandler()
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
					_mailSel.selectAll();
				return(false);

			case 85: // u
				EBID('massAction').value = 'markunread';
				transferSelectedMailIDs();
				document.forms['f1'].submit();
				return(false);

			case 82: // r
				EBID('massAction').value = 'markread';
				transferSelectedMailIDs();
				document.forms['f1'].submit();
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
					EBID('massAction').value = 'delete';
					transferSelectedMailIDs();
					document.forms['f1'].submit();
				}
				return(false);
			}
		}
	}
}

function reloadFolderList(data)
{
	if(!EBID('folderList'))
		return;

	if(data)
	{
		if(data.length > 10 && data.indexOf('var') >= 0)
		{
			EBID('folderList').innerHTML = '';
			eval(data);
			EBID('folderList').innerHTML = d;
			enableFolderDragTargets();
		}

		return;
	}

	MakeXMLRequest('email.php?action=getFolderList&sid=' + currentSID, function(http)
			{
				if(http.readyState == 4 && http.responseText)
					reloadFolderList(http.responseText);
			});
}

function showMailSource(id)
{
	openOverlay('email.read.php?action=showSource&id='+id+'&sid='+currentSID,
		lang['source'],
		500,
		460,
		true);
}

function showDeliveryStatus(mailID)
{
	openOverlay('email.read.php?action=deliveryStatus&id='+mailID+'&sid='+currentSID,
		lang['deliverystatus'],
		500,
		300,
		false);
}

function showAttachedMail(mailID, attID, title)
{
	openOverlay('email.read.php?action=attachedMail&id='+mailID+'&attachment='+attID+'&sid='+currentSID,
		title,
		640,
		480,
		false);
}

function showAttachedZIP(mailID, attID, title)
{
	openOverlay('email.read.php?action=attachedZIP&id='+mailID+'&attachment='+attID+'&sid='+currentSID,
		title,
		640,
		470,
		false);
}

function moveMail(id)
{
	openOverlay('email.read.php?action=move&id='+id+'&sid='+currentSID,
		lang['movemail'],
		450,
		380,
		true);
}

function saveAttachmentToWebdisk(id, attachment, fileName, sid)
{
	openOverlay('webdisk.php?action=importFromMail&id='+id+'&attachment='+attachment+'&filename='+escape(fileName)+'&sid=' + sid,
		lang['browse'],
		650,
		385,
		true);
}

function updateGroupMode(c, fs, sid)
{
	document.location.href = 'email.php?sid=' + sid + '&' + fs + '&do=changeGroupMode&groupmode=' + c.value;
}

function initEMailTextArea(code)
{
	var iframe = EBID('textArea'), lastSize = 0;

	var resCB = function()
	{
		var iframeDoc;

		if(iframe.document)
			iframeDoc = iframe.document;
		else
			iframeDoc = iframe.contentDocument;

		if(iframeDoc && iframeDoc.getElementById('__bmMailText'))
		{
			var h = iframeDoc.getElementById('__bmMailText').clientHeight;
			if(h > lastSize + 60)
			{
				iframe.style.height = (h > 140 ? h+60 : 200) + 'px';
				lastSize = h;
			}
		}
		else if(iframeDoc)
		{
			var h = iframe.document.documentElement.scrollHeight;
			if(h > lastSize + 60)
			{
				iframe.style.height = (h > 140 ? h+60 : 200) + 'px';
				lastSize = h;
			}
		}

		window.setTimeout(resCB, 500);
	}

	var cb = function()
	{
		var iframeDoc;
		if(iframe.document)
			iframeDoc = iframe.document;
		else
			iframeDoc = iframe.contentDocument;

		if(iframeDoc.location.href == 'about:blank')
		{
			removeEvent(iframe, 'load', cb);

			var html = iframeDoc.getElementsByTagName('html');
			html.item(0).innerHTML = code;
		}
		else if(iframeDoc.location.href == document.location.href)
		{
			removeEvent(iframe, 'load', cb);

			var doc = iframe.contentWindow.document;
			doc.open();
			doc.write(code);
			doc.close();
		}

		var mm = function(event)
		{
			if(typeof(parent._hSepDragging) == 'undefined')
				return;

			if(!parent._hSepDragging && !parent._vSepDragging)
				return;

			if(parent.document.createEvent)
			{
				var ev = parent.document.createEvent('MouseEvents');
				ev.initMouseEvent('mousemove', true, true, window, 0,
					event.screenX,
					event.screenY,
					event.screenX - parent.diffScreenClientX,
					event.screenY - parent.diffScreenClientX,
					false, false, false, false, 0, null);
				parent.document.dispatchEvent(ev);
			}
		}

		var mc = function(event)
		{
			if(parent.document.createEvent)
			{
				var ev = parent.document.createEvent('MouseEvents');
				ev.initMouseEvent('mouseup', true, true, window, 0,
					event.screenX,
					event.screenY,
					event.screenX - parent.diffScreenClientX,
					event.screenY - parent.diffScreenClientX,
					false, false, false, false, 0, null);
				parent.document.dispatchEvent(ev);
			}
		}

		addEvent(iframeDoc, 'mousemove', mm);
		addEvent(iframeDoc, 'click', mc);
		resCB();
	};

	// webkit seems not to support addEvent(load), so emulate it
	if(/WebKit/i.test(navigator.userAgent) || /Opera/i.test(navigator.userAgent))
	{
		var _isLoaded = false;
		var _timer = setInterval(function()
		{
			if (/loaded|complete/.test(document.readyState))
			{
				if(!_isLoaded) cb();
				_isLoaded = true;
			}
			else
				_isLoaded = false;
		}, 50);
	}
	else
		addEvent(iframe, 'load', cb);
}

function _togglePreviewPane(e)
{
	if(e.readyState == 4)
	{
		EBID('previewArea').innerHTML = e.responseText;

		EBID('previewArea').style.display = '';
		EBID('multiSelPreview').style.display = 'none';

		if(EBID('textArea') && EBID('textArea_raw'))
		{
			initEMailTextArea(EBID('textArea_raw').value);
		}

		if(EBID('previewLoading')) EBID('previewLoading').style.display = 'none';
	}
}
function togglePreviewPane(mailID, tpldir, sid)
{
	if(mailID != -1 && _lastSelectedMailID != mailID)
	{
		_lastSelectedMailID = mailID;
		_currentPreviewMailID = mailID;

		if(EBID('previewLoading')) EBID('previewLoading').style.display = '';

		MakeXMLRequest('email.read.php?preview=true&id=' + mailID + (typeof(folderNarrowView)!='undefined'&&folderNarrowView?'&narrow=true':'') + '&sid=' + sid, _togglePreviewPane);

		if((EBID('mail_'+mailID+'_nspan1') && EBID('mail_'+mailID+'_nspan1').className.indexOf('unread') >= 0)
			|| (EBID('mail_'+mailID+'_span1') && EBID('mail_'+mailID+'_span1').className.indexOf('unread') >= 0))
			window.setTimeout('folderFlagMail('+mailID+', 1, false)', 500);
	}
}

function showMultiSelPreview(no)
{
	_currentPreviewMailID = 0;

	EBID('previewArea').style.display = 'none';
	EBID('multiSelPreview').style.display = '';

	if(no > 0)
	{
		EBID('multiSelPreview_count').innerHTML = no + ' ' + lang['mailsselected'];
	}
	else
	{
		EBID('multiSelPreview_count').innerHTML = lang['nomailsselected'];;
	}
}

function enableFolderDragTargets()
{
	var dragEnter = function(event)
	{
		if((event.dataTransfer.types.contains && !event.dataTransfer.types.contains('emails'))
				|| (event.dataTransfer.types.indexOf && event.dataTransfer.types.indexOf('emails') < 0))
			return;
	}

	var dragLeave = function(event)
	{
		if((event.dataTransfer.types.contains && !event.dataTransfer.types.contains('emails'))
				|| (event.dataTransfer.types.indexOf && event.dataTransfer.types.indexOf('emails') < 0))
			return;

		this.style.textDecoration = 'none';
	}

	var dragOver = function(event)
	{
		if((event.dataTransfer.types.contains && !event.dataTransfer.types.contains('emails'))
				|| (event.dataTransfer.types.indexOf && event.dataTransfer.types.indexOf('emails') < 0))
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

		if(!event.dataTransfer.getData('emails'))
			return;

		var As = this.getElementsByTagName('a');
		var folderID = -128;

		for(var i=0; i<As.length; i++)
		{
			if(As[i].href.indexOf("switchFolder") != -1)
			{
				folderID = parseInt(As[i].href.replace(/[^-0-9]/g, ''));
				break;
			}
		}

		if(folderID == -128) return;

		moveMails(event.dataTransfer.getData('emails'), folderID);

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
		if(imgs.length==1 && imgs[0].src.indexOf('intellifolder')!=-1) continue;

		addEvent(div, 'dragenter', dragEnter);
		addEvent(div, 'dragleave', dragLeave);
		addEvent(div, 'dragover', dragOver);
		addEvent(div, 'drop', dragDrop);
	}
}

function transferSelectedMailIDs()
{
	var f = EBID('selectedMailIDs'), i;

	if(f)
	{
		f.value = '';

		var IDs = _mailSel.getIDList();

		for(i=0; i<IDs.length; i++)
		{
			f.value += IDs[i] + ';';
		}

		if(f.value.length > 0)
			f.value = f.value.substr(0, f.value.length-1);
	}
}

function hideMailMenu(e)
{
	if(e.button == 2) return;
	var mailMenu = EBID('mailMenu'), multiMailMenu = EBID('multiMailMenu');
	mailMenu.style.display = 'none';
	multiMailMenu.style.display = 'none';
}

function showMailMenu(e, posElem, multi)
{
	document.onmouseup = hideMailMenu;
	var mailMenu = multi ? EBID('multiMailMenu') : EBID('mailMenu');

	var offsetX = getElementMetrics(mailMenu.parentNode, 'x');
	var offsetY = getElementMetrics(mailMenu.parentNode, 'y');

	mailMenu.style.display = '';

	if(typeof(posElem) == 'undefined' || posElem == null)
	{
		var oX = getElementMetrics(mailMenu, 'w'), oY = 0;

		if(e.button == 2) oX = 0;

		if(e.clientY > window.innerHeight-getElementMetrics(mailMenu, 'h')-40)
			oY = getElementMetrics(mailMenu, 'h');

		mailMenu.style.left = (e.clientX + getPageXOffset() - offsetX - oX) + 'px';
		mailMenu.style.top = min(getElementMetrics(mailMenu.parentNode, 'h') - getElementMetrics(mailMenu, 'h') - 20,
				e.clientY + getPageYOffset() - offsetY - oY) + 'px';
	}
	else
	{
		mailMenu.style.left = (getElementMetrics(posElem, 'x') + getElementMetrics(posElem, 'w') + getPageXOffset() - offsetX - getElementMetrics(mailMenu, 'w')) + 'px';
		mailMenu.style.top  = (getElementMetrics(posElem, 'y') + getElementMetrics(posElem, 'h') + getPageYOffset() - offsetY) + 'px';
	}

	if(!multi)
	{
		var color = 0;
		for(var i=1; i<=4; i++)
		{
			if(EBID('mail_' + currentID + '_col' + i) && EBID('mail_' + currentID + '_col' + i).className.substring(0, 10) == 'mailColor_')
			{
				color = EBID('mail_' + currentID + '_col' + i).className.substring(10, 11);
				break;
			}
			else if(EBID('mail_' + currentID + '_ncol' + i))
			{
				var cn = EBID('mail_' + currentID + '_ncol' + i).className;
				var pos = cn.indexOf('mailColor_');
				if(pos != -1)
				{
					color = cn.substr(pos+10, 1);
					break;
				}
			}
		}

		for(var i=0; i<=6; i++)
			EBID('mailColorButton_' + i).className = 'mailColorButton_' + i + (i==color?'_a':'');
	}
}

function hideFolderMenu(e)
{
	var folderMenu = EBID('folderMenu');
	folderMenu.style.display = 'none';
}

function showFolderMenu(e)
{
	document.onmouseup = hideFolderMenu;
	var folderMenu = EBID('folderMenu');
	var offsetX = getElementMetrics(folderMenu.parentNode, 'x');
	var offsetY = getElementMetrics(folderMenu.parentNode, 'y');
	folderMenu.style.left = (e.clientX + getPageXOffset() - offsetX) + 'px';
	folderMenu.style.top = (e.clientY + getPageYOffset() - offsetY) + 'px';
	folderMenu.style.display = '';
}

function folderFlagMail(id, flag, value)
{
	currentID = id;
	var add = '';

	if(flag == 1)
		add = '&getFolderList=true';

	if(!(id instanceof Array))
		id = [ id ];

	if(id.length == 0)
		return;

	var idsParam = '';
	for(var i=0; i<id.length; ++i)
		idsParam += '&ids['+i+']='+id[i];

	MakeXMLRequest('email.php?action=flagMessage&flag=' + flag + add + '&value=' + (value ? 1 : 0) + idsParam + '&sid=' + currentSID, function(http)
	{
		if(http.readyState == 4)
		{
			var results = http.responseText.split(',');

			for(var i=0; i<id.length; ++i)
			{
				var cid = id[i];

				if(flag == 1)
				{
					var unread = results[i] == '1';
					var span1 = EBID('mail_'+cid+'_span1');
					var span2 = EBID('mail_'+cid+'_span2');

					if(span1 && span2)
					{
						span1.className = span2.className = (unread ? 'unreadMail' : 'readMail');
					}
					else
					{
						span1 = EBID('mail_'+cid+'_nspan1');
						span2 = EBID('mail_'+cid+'_nspan2');
						icon = EBID('mail_'+cid+'_nicon');

						if(span1 && span2)
						{
							span1.className = 'date' + (unread ? ' unread' : '');
							span2.className = 'sender' + (unread ? ' unread' : '');

							if(typeof(icon.src) != 'undefined')
							{
								if(unread)
									icon.src = icon.src.replace('markread', 'markunread');
								else
									icon.src = icon.src.replace('markunread', 'markread');
							}
							else
							{
								icon.className = unread ? 'fa fa-envelope' : 'fa fa-envelope-o';
							}
						}
					}
				}

				else if(flag == 16)
				{
					var img = EBID('mail_'+cid+'_flagimg');
					if(typeof(img.src) != 'undefined')
					{
						img.src = img.src.replace(/mailico(.*)/gi, results[i] == '1' ? 'mailico_flagged.png' : 'mailico_empty.gif');
						if(narrowMode) img.style.display = results[i] == '1' ? '' : 'none';
					}
					else
					{
						img.className = 'fa ' + (results[i] == '1' ? 'fa-flag-o' : '');
					}
				}

				else if(flag == 4096)
				{
					var img = EBID('maildone_'+cid);
					if(typeof(img.src) != 'undefined')
						img.style.display = (results[i] == '1') ? '' : 'none';
					else
						img.className = (results[i] == '1') ? 'fa fa-check' : '';
				}
			}

			if(flag == 1)
			{
				if(results.length > id.length)
					reloadFolderList(results.slice(id.length).join(','));
				else
					reloadFolderList();
			}
		}
	});
}
function deleteMail(id)
{
	if(!(id instanceof Array))
		id = [ id ];

	if(id.length == 0)
		return;

	var idsParam = '';
	for(var i=0; i<id.length; ++i)
		idsParam += '&ids['+i+']='+id[i];

	MakeXMLRequest('email.php?do=deleteMail&rpc=true&getFolderList=true&'+idsParam+'&sid=' + currentSID, function(http)
			{
				if(http.readyState == 4)
				{
					switchPage(currentPageNo);

					if(http.responseText.substr(1, 1) == ',')
						reloadFolderList(http.responseText.substr(2));
				}
			});
}
function moveMails(mails, destFolder)
{
	if(!mails) return;
	if(destFolder == currentFolderID) return;

	MakeXMLRequest('email.php?action=moveMails&getFolderList=true&mails=' + escape(mails) + '&destFolderID=' + destFolder + '&sid=' + currentSID, function(http)
			{
				if(http.readyState == 4)
				{
					switchPage(currentPageNo);

					if(http.responseText.substr(1, 1) == ',')
						reloadFolderList(http.responseText.substr(2));
				}
			});
}
function _folderColorMail(http)
{
	if(http.readyState == 4)
	{
		var arr = http.responseText.split(',');
		var id = arr[0], color = arr[1];

		if(EBID('mail_' + id + '_col2'))
			EBID('mail_' + id + '_col2').className = (currentSortColumn == 'von' && color == 0)
														? 'listTableTDActive'
														: (color == 0 ? '' : 'mailColor_' + color);

		if(EBID('mail_' + id + '_col3'))
			EBID('mail_' + id + '_col3').className = (currentSortColumn == 'betreff' && color == 0)
														? 'listTableTDActive'
														: (color == 0 ? '' : 'mailColor_' + color);

		if(EBID('mail_' + id + '_col4'))
			EBID('mail_' + id + '_col4').className = (currentSortColumn == 'fetched' && color == 0)
														? 'listTableTDActive'
														: (color == 0 ? '' : 'mailColor_' + color);

		if(EBID('mail_' + id + '_ncol1'))
			EBID('mail_' + id + '_ncol1').className = 'narrowRow ' + (color == 0 ? '' : 'mailColor_' + color);
	}
}
function folderColorMail(id, color)
{
	MakeXMLRequest('email.php?action=colorMessage&color=' + color + '&id=' + id + '&sid=' + currentSID, _folderColorMail);
}
function mailReply(mailID, all)
{
	var tArea = EBID('textArea');
	var sText = '';

	if(tArea.contentDocument && tArea.contentDocument.getSelection)
		sText = tArea.contentDocument.getSelection();
	else if(tArea.selection)
		sText = tArea.selection.createRange().text;
	else if(parent.frames.textArea.document)
		sText = parent.frames.textArea.document.selection.createRange().text

	if(typeof(sText) == 'object')
		sText = sText.toString();

	if(sText.length > 3)
	{
		if(all) document.getElementById('quoteForm').action += '&all=true';
		document.getElementById('quoteText').value = sText;
		document.getElementById('quoteForm').submit();
	}
	else
	{
		document.location.href = 'email.compose.php?sid='+currentSID+'&reply='+mailID+(all?'&all=true':'');
	}
}

function sendMailConfirmation(id)
{
	MakeXMLRequest('email.read.php?action=sendConfirmation&id=' + id + '&sid=' + currentSID, false);
	EBID('confirmationDiv').style.display = 'none';
}
function setMailSpamStatus(id, spam, interactive)
{
	if(!(id instanceof Array))
		id = [ id ];

	if(id.length == 0)
		return;

	var idsParam = '';
	for(var i=0; i<id.length; ++i)
		idsParam += '&ids['+i+']='+id[i];

	MakeXMLRequest('email.read.php?action=setSpamStatus&spam='+(spam?'true':'false')+idsParam+'&sid=' + currentSID, function(http)
		{
			if(interactive && typeof(currentPageNo) != 'undefined' && http.readyState == 4)
				switchPage(currentPageNo);
		});
	EBID('spamQuestionDiv').style.display = 'none';
}
function _checkFolderRefresh(obj)
{
	if(obj.readyState == 4)
	{
		if(parseInt(obj.responseText) > 0)
		{
			switchFolder(refreshFolder, true);
			reloadFolderList();
		}
	}
}
function checkFolderRefresh()
{
	MakeXMLRequest('cron.php?out=text', null);
	MakeXMLRequest('email.php?action=getRecentMailCount&folder=' + refreshFolder + '&sid=' + currentSID, _checkFolderRefresh);
	refreshTimer = window.setTimeout('checkFolderRefresh()', refreshInterval*1000);
}
function initFolderRefresh(folder, interval)
{
	refreshFolder = folder;
	if(interval > 0 && (refreshInterval != interval || refreshTimer == null))
	{
		if(refreshTimer != null)
		{
			window.clearTimeout(refreshTimer);
			refreshTimer = null;
		}
		refreshInterval = interval;
		refreshTimer = window.setTimeout('checkFolderRefresh()', refreshInterval*1000);
	}
}
function printMail(id, sid)
{
	var enableExternal = false;

	if(parent.frames && parent.frames.mailFrame && parent.frames.mailFrame.location)
		enableExternal = parent.frames.mailFrame.location.href.indexOf('enableExternal') != -1;
	else if(parent.frames && parent.frames.textArea && parent.frames.textArea.location)
		enableExternal = parent.frames.textArea.location.href.indexOf('enableExternal') != -1;

	openWindow('email.read.php?id='+id+'&print=true'+(enableExternal?'&enableExternal=true':'')+'&sid='+sid, 'print'+id, 560, 680);
}

/**************************************************************************
 * Folders
 *************************************************************************/
function updateFolderSubscription(id, obj, sid)
{
	MakeXMLRequest('email.folders.php?action=setFolderSubscription&id=' + id + '&subscribe=' + (obj.checked ? '1' : '0') + '&sid=' + sid, function(e)
			{
				if(e.readyState == 4)
					reloadFolderList();
			});
}
function checkFolderForm(form)
{
	if(form.elements['titel'].value.length < 2)
	{
		alert(lang['fillin']);
		return(false);
	}
	return(true);
}

/**************************************************************************
 * Compose
 *************************************************************************/
function _placeSignature(obj)
{
	if(obj.readyState == 4)
		editor.insertText((editor.mode == 'html' ? '<br />' : "\n") + obj.responseText);
}
function placeSignature(id)
{
	MakeXMLRequest('email.compose.php?action=getSignature&id=' + escape(id) + '&mode=' + escape(editor.mode) + '&sid=' + currentSID,
					_placeSignature);
}
function initComposeAutoComplete()
{
	var toComplete = new Autocomplete();
	toComplete.setMode('semicolonSeparated');
	toComplete.setField('to');
	toComplete.setSearchFunction(_addressbookLookup);
	toComplete.setUp();

	var ccComplete = new Autocomplete();
	ccComplete.setMode('semicolonSeparated');
	ccComplete.setField('cc');
	ccComplete.setSearchFunction(_addressbookLookup);
	ccComplete.setUp();
}
function addAttachment(sid)
{
	openOverlay('email.compose.php?sid=' + sid + '&action=addAttachment&attList=' + escape(EBID('attachments').value),
		lang['addattach'],
		520,
		150,
		true);
}
function dndAttachmentUploaded(response)
{
	eval(response);
}
function dndAttachmentURLAddition()
{
	return('&attList=' + escape(EBID('attachments').value));
}
function generateAttachmentList()
{
	var attachments = EBID('attachmentList');

	// clear
	var node;
	while(node = attachments.firstChild)
		attachments.removeChild(node);

	// files
	var files = EBID('attachments').value.split(';');

	var fileCount = 0, firtElem = false, lastElem = false;
	for(var i=0; i<files.length; i++)
	{
		if(files[i] != '')
		{
			var params	= files[i].split(','),
				attID	= params[0],
				attName = params[1],
				attType = params[2];

			var link = document.createElement('a');
			link.title = attName;
			link.target = '_blank';
			link.href = 'email.compose.php?action=getAttachment&id=' + attID + '&type=' + escape(attType) + '&name=' + escape(attName) + '&sid=' + currentSID;
			link.appendChild(document.createTextNode(attName));

			var div = document.createElement('div');
			div.className = 'attachmentListEntry';
			div.appendChild(link);

			var del = document.createElement('a'),
				delImg = document.createElement('img');
			delImg.src = tplDir + 'images/li/ico_delete.png';
			delImg.width = '12';
			delImg.height = '12';
			delImg.align = 'absmiddle';
			delImg.border = '0';
			delImg.alt = '';
			del.href = 'javascript:deleteAttachment(' + attID + ');';
			del.appendChild(delImg);

			div.appendChild(del);

			attachments.appendChild(div);

			lastElem = div;

			if(fileCount == 0)
				firstElem = div;

			fileCount++;
		}
	}

	if(fileCount > 0 && getElementMetrics(firstElem, 'y') != getElementMetrics(lastElem, 'y'))
		attachments.style.height = '54px';
	else
		attachments.style.height = '';

	composeSizer(true);
}
function deleteAttachment(id)
{
	var attachments = EBID('attachments'),
		files = attachments.value.split(';'),
		newFiles = '';

	// remove entry from list
	for(var i=0; i<files.length; i++)
	{
		if(files[i] != '')
		{
			var params = files[i].split(',');
			if(params[0] != id)
				newFiles += ';' + files[i];
		}
	}

	// delete on server
	MakeXMLRequest('email.compose.php?action=deleteAttachment&sid=' + currentSID + '&id=' + id, null);

	// rebuild table
	attachments.value = newFiles;
	generateAttachmentList();
}
function checkComposeForm(form, attCheck, attKeywords)
{
	if(form.elements['to'].value.length < 3)
	{
		alert(lang['fillin']);
		return(false);
	}

	if(form.elements['subject'].value.length < 1)
	{
		if(!confirm(lang['sendwosubject']))
			return(false);
	}

	if(attCheck && EBID('attachments').value.length < 3)
	{
		attKeywords = attKeywords.split(',');

		var showWarning = false, msgText = editor.getPlainText().toLowerCase();

		msgText = msgText.replace(/^>.*$/mg, '');
		msgText = msgText.replace(/[^a-zA-Z0-9 ]/g, '');

		for(var i=0; i<attKeywords.length; i++)
		{
			var pos = msgText.indexOf(attKeywords[i].replace(/[^a-zA-Z0-9 ]/g, ''));

			if(pos >= 0)
			{
				showWarning = true;
				break;
			}
		}

		if(showWarning && !confirm(lang['attwarning']))
			return(false);
	}

	if(EBID('certMail').checked && (EBID('mailConfirmation').checked || (EBID('smimeSign') && EBID('smimeSign').checked) || (EBID('smimeEncryprt') && EBID('smimeEncrypt').checked)))
	{
		var msg = lang['certmailwarn'];
		if(EBID('mailConfirmation').checked)
			msg += ' - ' + lang['certmailconfirm'] + "\n";
		if(EBID('smimeSign').checked)
			msg += ' - ' + lang['certmailsign'] + "\n";
		if(EBID('smimeEncrypt').checked)
			msg += ' - ' + lang['certmailencrypt'] + "\n";

		if(confirm(msg))
		{
			EBID('mailConfirmation').checked = false;
			EBID('smimeSign').checked = false;
			EBID('smimeEncrypt').checked = false;
		}
		else
		{
			return;
		}
	}

	return(true);
}
function _checkSMIME(obj)
{
	if(obj.readyState == 4)
	{
		if(obj.responseText == '1')
		{
			eval(_checkSMIMEAction);
		}
		else
		{
			// handle error
			alert(obj.responseText);
		}
	}
}
function checkSMIME(action)
{
	var sign 	= EBID('smimeSign') && EBID('smimeSign').checked,
		encrypt = EBID('smimeEncrypt') && EBID('smimeEncrypt').checked;

	if(sign || encrypt)
	{
		// check
		_checkSMIMEAction = action;
		MakeXMLRequest('email.compose.php?action=checkSMIMEParams'
							+ '&sign=' 		+ (sign ? '1' : '0')
							+ '&encrypt='	+ (encrypt ? '1' : '0')
							+ '&from=' 		+ escape(EBID('from').value)
							+ '&to=' 		+ escape(EBID('to').value)
							+ '&cc=' 		+ escape(EBID('cc').value)
							+ '&bcc=' 		+ escape(EBID('bcc').value)
							+ '&sid=' 		+ currentSID,
						_checkSMIME);
	}
	else
	{
		// go on
		eval(action);
	}
}

function submitComposeForm()
{
	if(EBID('composeLoading'))
		EBID('composeLoading').style.display = '';
	document.forms.f1.submit();
}
