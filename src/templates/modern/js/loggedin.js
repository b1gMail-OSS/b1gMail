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

var currentID = -1;
var currentIDs = [];
var currentSortColumn = '';
var currentSortOrder = '';
var currentPageNo = 1;
var currentPageCount = 1;
var currentType = -1;
var currentFolder = -1;
var currentTitle = '';
var currentEMail = '';
var currentEMailID = 0;
var preMenuMouseDown = null;
var refreshInterval = 0;
var refreshFolder = 0;
var refreshTimer = null;
var formSubmitOK = false;
var disableHide = false;
var _checkSMIMEAction = '';
var diffScreenClientX = 0, diffScreenClientY = 0;

var _vSepDragging = false, _vSepStartY = 0;
var _hSepDragging = false, _hSepStartX = 0;

var _nextFolderID = 0;

var _lastSelectedMailID = 0;

var narrowMode = false;
var notificationCount = 0;
var windowTitle = '';

function prefsAbort()
{
	parent.hideOverlay();
}

function setWindowTitle(title)
{
	windowTitle = title;
	refreshWindowTitle();
}

function refreshWindowTitle()
{
	if(windowTitle.length == 0)
		windowTitle = top.document.title;
	top.document.title = (notificationCount > 0 ? '('+notificationCount+') ' : '') + windowTitle;
}

function hideNotifications(really)
{
	if(!disableHide || really==true)
		EBID('notifyBox').style.display = 'none';
}
function showNotifications(elem)
{
	document.onmousedown = hideNotifications;
	showHeaderPopup('notifyBox', elem);
	refreshNotifications();
}
function setNotificationCount(count)
{
	if(count > 0)
	{
		var newNotifications = (count > parseInt(EBID('notifyCount').innerHTML));
		EBID('notifyCount').innerHTML = (count > 99) ? '99' : count;
		EBID('notifyCount').style.display = '';
		notificationCount = count;

		if(newNotifications)
		{
			EBID('notifyIcon').className = EBID('notifyIcon').className.replace(' animated', '') + ' animated';

			if(typeof(notifySound) != 'undefined' && notifySound)
			{
				(new Audio('./res/notify.mp3')).play();
			}
		}
	}
	else
	{
		notificationCount = 0;

		EBID('notifyCount').style.display = 'none';
		EBID('notifyIcon').className = EBID('notifyIcon').className.replace(' animated', '');
	}

	refreshWindowTitle();
}
function refreshNotifications()
{
	var container = EBID('notifyInner');
	if(container.innerHTML == '')
	{
		container.innerHTML = '<center><br /><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></center>';
	}

	MakeXMLRequest('start.php?action=getNotifications&sid=' + currentSID, function(e)
		{
			if(e.readyState == 4)
			{
				container.innerHTML = e.responseText;
				setNotificationCount(0);
			}
		});
}

function toggleDropdownNavMenu()
{
	var elem = EBID('dropdownNavMenu');

	var hideFunc = function(event)
	{
		if(typeof(event.target) != 'undefined')
		{
			if(isChildOf(event.target, elem))
				return true;
		}

		if(typeof(event.button) != 'undefined' && event.button==2)
			return(true);
		elem.style.display = 'none';
	}

	if(elem.style.display == '')
	{
		elem.style.display = 'none';
	}
	else
	{
		elem.style.display = '';
		addEvent(document, 'mouseup', hideFunc);
	}
}

function _previewNote(request)
{
	if(request.readyState == 4)
	{
		EBID('notePreview').innerHTML = NL2BR(HTMLEntities(request.responseText));
	}
}
function previewNote(sid, id)
{
	MakeXMLRequest('organizer.notes.php?action=getNoteText&sid=' + sid + '&id=' + id, _previewNote);
}

function autoSetPreviewPos()
{
	MakeXMLRequest('email.php?action=setPreviewPosition&pos='+(window.innerWidth>=1150?'right':'bottom')+'&sid='+currentSID, false);
}

function setTaskDone(sid, id, done)
{
	var reload = EBID('taskListContainer') ? true : false;

	if(EBID('sbTask_'+id))
		EBID('sbTask_'+id).checked = done;

	MakeXMLRequest('organizer.todo.php?do=setTaskDone&id=' + id + (reload?'&listOnly=true&taskListID='+currentTaskListID:'') + '&done=' + (done ? 'true' : 'false') + '&sid=' + currentSID, function(e)
	{
		if(e.readyState == 4)
		{
			if(reload)
				reloadTaskList(e.responseText);
		}
	});
}

function ajaxFormData(f)
{
	var data = '';

	for(var i=0; i<f.elements.length; i++)
	{
		var el = f.elements[i], str = '';
		var type = el.type.toLowerCase(), name = encodeURIComponent(el.name), value = encodeURIComponent(el.value ? el.value : '');

		if(name.length == 0)
			continue;

		if(type == 'checkbox' && el.checked)
			str = name + '=' + (value ? value : 'On');
		else if(type == 'text' || type == 'textarea' || type == 'password' || type == 'button' || type == 'reset'
			|| type == 'submit' || type == 'image' || type == 'hidden' || (type == 'radio' && el.checked))
			str = name + '=' + value;
		else if(type.indexOf('select') != -1)
		{
			for(var j=0; j<el.options.length; j++)
				if(el.options[j].selected)
					str = name + '=' + encodeURIComponent(el.options[j].value ? el.options[j].value : el.options[j].text);
		}

		if(str != '')
		{
			if(data != '') data += '&';
			data += str;
		}
	}

	return data;
}

function ajaxFormSubmit(f)
{
	var data = ajaxFormData(f);

	var xh = GetXMLHTTP();
	if(xh)
	{
		xh.open('POST', f.action, true);
		xh.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

		xh.onreadystatechange = function()
		{
			if(xh.readyState == 4)
			{
				f.innerHTML = xh.responseText;
			}
		};

		f.innerHTML = '<center><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></center>';
		xh.send(data);
	}

	return(false);
}

function initVSep()
{
	var sepParent = EBID('mainContent');
	var vSep1 = EBID('vSep1'), vSep2 = EBID('vSep2'), vSepSep = EBID('vSepSep');

	vSepSep.onmousedown = function(event) { if(!event&&window.event) event=window.event; _vSepDragging = true; _vSepStartY = event.clientY - getElementMetrics(vSepSep, 'y'); return(false); };

 	var omu = function() { _vSepDragging = false; setCookie('vSepHeight1', parseInt(vSep1.style.height)); if(typeof(IE7) != 'undefined') IE7.recalc(); };
 	var omd = function() { if(_vSepDragging) return(false); return(true); };
 	var omm = function(event)
 	{
 		if(!event&&window.event) event=window.event;

 		diffScreenClientX = event.screenX - event.clientX;
 		diffScreenClientY = event.screenY - event.clientY;

		if(_vSepDragging)
		{
			var sepHeight = getElementMetrics(EBID('vSepSep'), 'h');

			var height1 = event.clientY - getElementMetrics(sepParent, 'y');
			height1 = max(45, min(height1, getElementMetrics(sepParent, 'h') - 45)) - _vSepStartY;

			var height2 = getElementMetrics(sepParent, 'h')-height1-sepHeight;

			vSep1.style.height = height1 + 'px';
			vSep2.style.height = height2 + 'px';
			vSep2.style.top = (height1 + sepHeight) + 'px';
			vSepSep.style.top = height1 + 'px';

			return(false);
		}

		return(true);
	};
	var or = function(event)
	{
 		if(!event&&window.event) event=window.event;

		var parentHeight = getElementMetrics(sepParent, 'h'),
			sepHeight = getElementMetrics(EBID('vSepSep'), 'h');

		var initialHeight1 = Math.floor(parentHeight/2)-5;

		if(getCookie('vSepHeight1'))
			initialHeight1 = max(45, min(parentHeight-45-sepHeight, parseInt(getCookie('vSepHeight1'))));

		initialHeight2 = parentHeight-initialHeight1-sepHeight;

		vSep1.style.height = initialHeight1 + 'px';
		vSep2.style.height = initialHeight2 + 'px';
		vSep2.style.top = (initialHeight1 + sepHeight) + 'px';
		vSepSep.style.top = initialHeight1 + 'px';

		if(typeof(IE7) != 'undefined') IE7.recalc();
	}

	addEvent(document, 'mouseup', omu);
	addEvent(document, 'mousedown', omd);
	addEvent(document, 'mousemove', omm);
	addEvent(window, 'resize', or);

	or();
}

function initHSep(instName)
{
	if(!instName) instName = '';
	var hSep1 = EBID('hSep1'), hSep2 = EBID('hSep2'), hSepSep = EBID('hSepSep');
	var sepParent = hSep1.parentNode;

	hSepSep.onmousedown = function(event) { if(!event&&window.event) event=window.event; _hSepDragging = true; _hSepStartX = event.clientX - getElementMetrics(hSepSep, 'x'); return(false); };

 	var omu = function() { _hSepDragging = false; setCookie('hSepWidth1'+instName, parseInt(hSep1.style.width)); if(typeof(IE7) != 'undefined') IE7.recalc(); };
 	var omd = function() { if(_hSepDragging) return(false); return(true); };
 	var omm = function(event)
 	{
 		if(!event&&window.event) event=window.event;

 		diffScreenClientX = event.screenX - event.clientX;
 		diffScreenClientY = event.screenY - event.clientY;

		if(_hSepDragging)
		{
			var sepWidth = getElementMetrics(EBID('hSepSep'), 'w');

			var width1 = event.clientX - getElementMetrics(sepParent, 'x');
			width1 = max(320+_hSepStartX, min(width1, getElementMetrics(sepParent, 'w') - 45)) - _hSepStartX;
			var width2 = getElementMetrics(sepParent, 'w')-width1-sepWidth;

			hSep1.style.width = width1 + 'px';
			hSep2.style.width = width2 + 'px';
			hSep2.style.left = (width1 + sepWidth) + 'px';
			hSepSep.style.left = width1 + 'px';

			return(false);
		}

		return(true);
	};
	var or = function(event)
	{
 		if(!event&&window.event) event=window.event;

		var parentWidth = getElementMetrics(sepParent, 'w'),
			sepWidth = getElementMetrics(EBID('hSepSep'), 'w');

		var initialWidth1 = max(320, Math.floor(parentWidth/2.7)-5);

		if(getCookie('hSepWidth1'+instName))
			initialWidth1 = max(320, min(parentWidth-200-sepWidth, parseInt(getCookie('hSepWidth1'+instName))));

		var initialWidth2 = parentWidth-initialWidth1-sepWidth;

		hSep1.style.width = initialWidth1 + 'px';
		hSep2.style.width = initialWidth2 + 'px';
		hSep2.style.left = (initialWidth1 + sepWidth) + 'px';
		hSepSep.style.left = initialWidth1 + 'px';

		if(typeof(IE7) != 'undefined') IE7.recalc();
	}

	addEvent(document, 'mouseup', omu);
	addEvent(document, 'mousedown', omd);
	addEvent(document, 'mousemove', omm);
	addEvent(window, 'resize', or);
	or();
}

/**************************************************************************
 * Search
 *************************************************************************/
function changeSearchPage(p)
{
	EBID('searchSideBarPage').value = p;
	document.forms['searchSideBarForm'].submit();
}
function changeSearchSort(sort, order)
{
	EBID('searchSideBarSort').value = sort;
	EBID('searchSideBarOrder').value = order;
	document.forms['searchSideBarForm'].submit();
}
function _performSearch(e)
{
	hideNewMenu();
	if(e.readyState == 4)
	{
		EBID('searchResults').innerHTML = e.responseText;
		EBID('searchResultBody').style.display = '';
		EBID('searchSpinner').style.display = 'none';
	}
}
function searchFieldKeyPress(event, details)
{
	if(event.keyCode == 13)
	{
		var q = EBID('searchField').value;
		if(trim(q).length > 2)
		{
			EBID('searchResultBody').style.display = 'none';
			EBID('searchSpinner').style.display = '';

			if(details)
				document.location.href = 'search.php?q=' + escape(q) + '&sid=' + currentSID;
			else
				MakeXMLRequest('search.php?action=quickSearch&q=' + escape(q) + '&sid=' + currentSID, _performSearch);
		}
		else
		{
			EBID('searchResultBody').style.display = 'none';
			EBID('searchSpinner').style.display = 'none';
		}
	}
}
function hideSearchPopup(really)
{
	if(!disableHide || really==true)
		EBID('searchPopup').style.display = 'none';
}
function showHeaderPopup(name, elem)
{
	EBID(name).style.right = (getElementMetrics(document.body, 'w')-getElementMetrics(elem, 'x')-getElementMetrics(elem, 'w')-14) + 'px';
	EBID(name).style.display = '';
}
function showSearchPopup(elem)
{
	document.onmousedown = hideSearchPopup;
	EBID('searchSpinner').style.display = 'none';
	showHeaderPopup('searchPopup', elem);
	EBID('searchField').focus();
}
function toggleResultMassActions(form, id)
{
	var showMassActions = false;

	if(!EBID("massActions_" + id))
		return;

	for(var i=0; form.elements[i]; i++)
	{
		if(form.elements[i].type == 'checkbox' && form.elements[i].id != 'allChecker'
		   && form.elements[i].id.substr(0, ("checkbox_"+id+"_").length) == "checkbox_"+id+"_"
		   && form.elements[i].checked)
		{
			showMassActions = true;
			break;
		}
	}

	EBID("massActions_" + id).style.display = showMassActions ? '' : 'none';
}

/**************************************************************************
 * File selector widget
 *************************************************************************/
function webdiskDialog(sid, type, field)
{
	openOverlay('webdisk.php?sid=' + sid + '&action=webdiskDialog&type=' + type + '&field=' + field,
		lang['browse'],
		650,
		type == 'save' ? 376 : 344,
		true);
}
function changeFileSelectorSource(c, name)
{
	EBID('fileSelector_local_' + name).style.display = c.value == 'local' ? '' : 'none';
	EBID('fileSelector_webdisk_' + name).style.display = c.value == 'local' ? 'none' : '';

	if(c.value != 'local')
	{
		EBID('localFile_' + name).value = '';
	}
	else
	{
		EBID('webdiskFile_' + name).value = '';
		EBID('webdiskFile_' + name + '_id').value = '';
	}
}

function composeMail(args)
{
	if(typeof(args) == 'undefined' || !args)
		args = '';

	openOverlay('email.compose.php?inline=true&sid='+currentSID+args,
			lang['compose'],
			getDocumentMetrics('windowW')*0.8,
			getDocumentMetrics('windowH')*0.8,
			false, true);
}
function accelKeyPressed(e)
{
	if(navigator.platform && navigator.platform.toLowerCase().indexOf("mac") != -1)
		return(!e.ctrlKey && e.metaKey);
	else
		return(!e.metaKey && e.ctrlKey);
}
function showCertificate(hash)
{
	openOverlay('prefs.php?action=keyring&do=showCertificate&hash='+hash+'&sid='+currentSID,
		lang['certificate'],
		450,
		380,
		true);
}
function toggleGroup(id, groupID)
{
	var groupItem = EBID('group_' + id);
	var groupItemImg = EBID('groupImage_' + id);

	if(groupItem.style.display == '')
	{
		groupItem.style.display = 'none';
		groupItemImg.src = groupItemImg.src.replace(/contract/, 'expand');

		if(groupID)
			setCookie('toggleGroup[' + groupID + ']', 'closed');
	}
	else
	{
		groupItem.style.display = '';
		groupItemImg.src = groupItemImg.src.replace(/expand/, 'contract');

		if(groupID)
			setCookie('toggleGroup[' + groupID + ']', 'open');
	}
}
function hideNewMenu(e)
{
	var folderMenu = EBID('newMenu');
	folderMenu.style.display = 'none';
}
function showNewMenu(elem)
{
	document.onmouseup = hideNewMenu;
	showHeaderPopup('newMenu', elem);
}
function objBigger(name)
{
	EBID(name).style.height = (parseInt(EBID(name).style.height)+25)+'px';
}
function objSmaller(name)
{
	EBID(name).style.height = (max(parseInt(EBID(name).style.height)-25, 100))+'px';
}
function quoteSelectedText()
{
	var tArea = EBID('textArea');
	var sText = '';

	if(tArea.contentDocument && tArea.contentDocument.getSelection)
		sText = tArea.contentDocument.getSelection();
	else if(tArea.selection)
		sText = tArea.selection.createRange().text;
	else if(parent.frames.textArea.document)
		sText = parent.frames.textArea.document.selection.createRange().text

	document.getElementById('quoteText').value = sText;
	document.getElementById('quoteForm').submit();
}
function searchSelectedText(sid)
{
	var tArea = EBID('textArea');
	var sText = '';

	if(tArea.contentDocument && tArea.contentDocument.getSelection)
		sText = tArea.contentDocument.getSelection();
	else if(tArea.selection)
		sText = tArea.selection.createRange().text;
	else if(parent.frames.textArea.document)
		sText = parent.frames.textArea.document.selection.createRange().text

	if(sText.replace(/\s/g, "").length > 0)
		window.open('start.php?sid='+sid+'&action=search&q=' + escape(sText));
	else
		alert(lang['selecttext']);
}
function hideAddressMenu(e)
{
	var mailMenu = EBID('addressMenu');
	mailMenu.style.display = 'none';
}
function showAddressMenu(e, readItem)
{
	document.onmouseup = hideAddressMenu;
	var mailMenu = EBID('addressMenu');
	var offsetX = getElementMetrics(mailMenu.parentNode, 'x');
	var offsetY = getElementMetrics(mailMenu.parentNode, 'y');
	mailMenu.style.left = (e.clientX + getPageXOffset() - offsetX) + 'px';
	mailMenu.style.top = (e.clientY + getPageYOffset() - offsetY) + 'px';
	mailMenu.style.display = '';
	EBID('addressMenuReadItem').style.display = readItem ? '' : 'none';
	EBID('addressMenuReadItemSep').style.display = readItem ? '' : 'none';
}

/**************************************************************************
 * Dashboard ajax code
 *************************************************************************/
function startBoardOrderChanged()
{
	MakeXMLRequest('start.php?action=saveWidgetOrder&order=' + escape(this.order) + '&sid=' + currentSID, false);
}
function organizerBoardOrderChanged()
{
	MakeXMLRequest('organizer.php?action=saveWidgetOrder&order=' + escape(this.order) + '&sid=' + currentSID, false);
}

function checkSafeCode(failAction, successAction)
{
	var data = '', haveSafecode = false;

	if(EBID('captchaContainer'))
	{
		var elems = EBID('captchaContainer').getElementsByTagName('input');

		for(var i=0; i<elems.length; ++i)
		{
			var el = elems[i], str = '';
			var type = el.type.toLowerCase(), name = encodeURIComponent(el.name), value = encodeURIComponent(el.value ? el.value : '');

			if(type == 'checkbox' && el.checked)
				str = name + '=' + (value ? value : 'On');
			else if(type == 'text' || type == 'textarea' || type == 'password' || type == 'button' || type == 'reset'
				|| type == 'submit' || type == 'image' || type == 'hidden' || (type == 'radio' && el.checked))
				str = name + '=' + value;

			if(str != '')
			{
				if(name == 'safecode')
					haveSafecode = true;
				data += '&' + str;
			}
		}

		var elems2 = EBID('captchaContainer').getElementsByTagName('textarea');
		for(var i=0; i<elems2.length; ++i)
		{
			var el = elems2[i];
			var str = encodeURIComponent(el.name) + '=' + encodeURIComponent(el.value);
			data += '&' + str;
		}
	}

	if(!haveSafecode && EBID('safecode'))
		data += '&safecode=' + encodeURIComponent(EBID('safecode').value);

	MakeXMLRequest('start.php?action=checkSafeCode&sid='+currentSID+data,
		function(obj)
		{
			if(obj.readyState == 4)
			{
				if(obj.responseText == '1')
				{
					if(typeof(successAction) != 'undefined' && successAction)
						eval(successAction);
					else
						document.forms.f1.submit();
				}
				else
				{
					if(EBID('safecode'))
					{
						markFieldAsInvalid('safecode');
						EBID('safecode').value = '';
						EBID('safecode').focus();
					}
					else if(EBID('captchaContainer'))
					{
						if(failAction)
							eval(failAction);

						var elems = EBID('captchaContainer').getElementsByTagName('input');
						for(var i=0; i<elems.length; ++i)
						{
							var el = elems[i], type = el.type.toLowerCase();

							if(type == 'text')
							{
								if(typeof(el.id) != 'undefined' && el.id)
									markFieldAsInvalid(el.id);
								el.value = '';
								el.focus();
								break;
							}
						}
					}
				}
			}
		});
	return(false);
}
function askReset()
{
	return(confirm(lang['reallyreset']));
}
function openAddressbook(sid, mode)
{
	if(mode == 'email')
	{
		mode += '&to=' + escape(EBID('to').value)
			+	'&cc=' + escape(EBID('cc').value)
			+	'&bcc=' + escape(EBID('bcc').value);
	}

	openOverlay('organizer.addressbook.php?sid=' + sid + '&action=addressPopup&mode=' + mode,
		lang['addressbook'],
		450,
		390,
		true);
}
function __addressbookLookup(e, obj)
{
	if(e.readyState == 4)
	{
		var suggestions = e.responseText.split(';');
		obj.setSuggestions(suggestions);
	}
}
function _addressbookLookup(obj, text)
{
	MakeXMLRequest('organizer.addressbook.php?action=lookupAddresses&sid=' + currentSID + '&text=' + escape(text), __addressbookLookup, obj);
}
function _loadMainContent(obj)
{
	if(obj.readyState == 4)
	{
		EBID('mainContent').innerHTML = obj.responseText;
	}
}
function loadMainContent(url)
{
	url += '&contentOnly=true';
	MakeXMLRequest(url, _loadMainContent);
}
function showAppropriateValueLayer(obj, id)
{
	var op = obj.value;

	if(EBID('folderValue_'+id))
		EBID('folderValue_'+id).style.display = 'none';
	if(EBID('mailValue_'+id))
		EBID('mailValue_'+id).style.display = 'none';
	if(EBID('draftValue_'+id))
		EBID('draftValue_'+id).style.display = 'none';
	if(EBID('colorValue_'+id))
		EBID('colorValue_'+id).style.display = 'none';

	if(op == 1)
		EBID('folderValue_'+id).style.display = '';
	if(op == 9)
		EBID('draftValue_'+id).style.display = '';
	if(op == 10)
		EBID('mailValue_'+id).style.display = '';
	if(op == 11)
		EBID('colorValue_'+id).style.display = '';
}
function setActionColor(id, color)
{
	for(var i=0; i<=6; i++)
		if(EBID('mailColorButton_'+i+'_'+id))
			EBID('mailColorButton_'+i+'_'+id).className = (color==i?'mailColorButton_'+i+'_a':'mailColorButton_'+i);
	EBID('color_val_'+id).value = color;
}
function showAppropriateFolderCondLayer(obj, id)
{
	var field = obj.value;

	if(EBID('boolComparison_'+id))
		EBID('boolComparison_'+id).style.display = 'none';
	if(EBID('priorityComparison_'+id))
		EBID('priorityComparison_'+id).style.display = 'none';
	if(EBID('folderComparison_'+id))
		EBID('folderComparison_'+id).style.display = 'none';
	if(EBID('defaultComparison_'+id))
		EBID('defaultComparison_'+id).style.display = 'none';
	if(EBID('attComparison_'+id))
		EBID('attComparison_'+id).style.display = 'none';
	if(EBID('colorComparison_'+id))
		EBID('colorComparison_'+id).style.display = 'none';

	if(field == 6 || field == 7 || field == 8 || field == 10 || field == 11 || field == 15)
		EBID('boolComparison_'+id).style.display = '';
	else if(field == 9)
		EBID('priorityComparison_'+id).style.display = '';
	else if(field == 12)
		EBID('folderComparison_'+id).style.display = '';
	else if(field == 13)
		EBID('attComparison_'+id).style.display = '';
	else if(field == 14)
		EBID('colorComparison_'+id).style.display = '';
	else
		EBID('defaultComparison_'+id).style.display = '';
}

function showCalendarDate(id, start, end, inFrame)
{
	//start += getTZOffset();
	//end += getTZOffset();

	if(inFrame)
		parent.openOverlay('organizer.calendar.php?sid=' + currentSID + '&action=showDate&start=' + start + '&end=' + end + '&id=' + id,
			lang['date'],
			550,
			465,
			true);
	else
		openOverlay('organizer.calendar.php?sid=' + currentSID + '&action=showDate&start=' + start + '&end=' + end + '&id=' + id,
			lang['date'],
			550,
			465,
			true);
}
