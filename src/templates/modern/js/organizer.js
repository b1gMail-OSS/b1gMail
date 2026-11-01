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

/**************************************************************************
 * Notes
 *************************************************************************/
function checkNoteForm(form)
{
	if(form.elements['text'].value.length < 2)
	{
		alert(lang['fillin']);
		return(false);
	}
	return(true);
}

/**************************************************************************
 * Addressbook
 *************************************************************************/
var _addrSel;
function initAddrSel()
{
	var sel = new selecTable(EBID('addressTable'), 'tr', false);
	sel.cbGetItemID = function(element)
	{
		return(element.id.substr(5));
	}
	sel.cbRowFilter = function(element)
	{
		return(element.id.substr(0, 5) == 'addr_');
	}
	sel.cbSelectSingleItem = function(element)
	{
		MakeXMLRequest('organizer.addressbook.php?action=showContact&id='+this.getItemID(element)+'&sid=' + currentSID, function(http)
			{
				if(http.readyState == 4)
				{
					EBID('previewArea').innerHTML = http.responseText;
					EBID('multiSelPreview').style.display = 'none';
					EBID('previewArea').style.display = '';
				}
			});
	}
	sel.cbSelectionChanged = function()
	{
		if(this.sel.length <= 1 || !EBID('previewArea') || !EBID('multiSelPreview'))
			return;
		showAddrMultiSelPreview(this.sel.length);
	}
	sel.cbItemContextMenu = function(element, event)
	{
		return(false);
	}
	sel.cbItemDoubleClick = function(element)
	{
		document.location.href = bmAppendSession('organizer.addressbook.php?action=editContact&id='+this.getItemID(element));
	}
	sel.init();
	_addrSel = sel;
}
function showAddrMultiSelPreview(no)
{
	EBID('previewArea').style.display = 'none';
	EBID('multiSelPreview').style.display = '';

	if(no > 0)
	{
		EBID('multiSelPreview_count').innerHTML = no + ' ' + lang['contactsselected'];
	}
	else
	{
		EBID('multiSelPreview_count').innerHTML = lang['nocontactselected'];;
	}
}
function transferSelectedAddresses()
{
	var f = EBID('addrIDs');
	if(f)
	{
		f.value = '';

		var IDs = _addrSel.getIDList();

		for(i=0; i<IDs.length; i++)
		{
			f.value += IDs[i] + ';';
		}

		if(f.value.length > 0)
			f.value = f.value.substr(0, f.value.length-1);
	}
}
function abExport()
{
	openOverlay('organizer.addressbook.php?sid=' + currentSID + '&action=exportDialog',
		lang['export'],
		440,
		160,
		true);
}
function abImport()
{
	openOverlay('organizer.addressbook.php?sid=' + currentSID + '&action=importDialogStart',
		lang['import'],
		440,
		190,
		true);
}
function abGroups()
{
	openOverlay('organizer.addressbook.php?sid=' + currentSID + '&action=groups',
		lang['groups'],
		550,
		400,
		true);
}
function updateCurrentGroup(id, sid)
{
	document.location.href = bmAppendSession('organizer.addressbook.php?group=' + id);
}
function checkContactForm(form)
{
	if((form.elements['vorname'].value.length < 1
		|| form.elements['nachname'].value.length < 1)
		&& form.elements['firma'].value.length < 1)
	{
		alert(lang['fillin']);
		return(false);
	}
	// Make sure the hidden SmartyDateTime fields for the birthday are in sync
	// with the visible <input type="date"> before submit.
	if(typeof syncSmartyDate === 'function')
	{
		syncSmartyDate('geburtsdatum', 'geburtsdatum_');
	}
	return(true);
}
function addrFunction(what)
{
	if(what == 'selfComplete'
		&& (trim(EBID('vorname').value).length == 0
			|| trim(EBID('nachname').value).length == 0))
	{
		alert(lang['fillinname'])
	}
	else
	{
		EBID('submitAction').value = what;
		document.forms.f1.submit();
	}
}
function addrImportVCF()
{
	openOverlay('organizer.addressbook.php?action=vcfImportDialog&sid=' + currentSID,
			lang['importvcf'],
			520,
			150);
}
function checkGroupForm(form)
{
	if(form.elements['title'].value.length < 1)
	{
		alert(lang['fillin']);
		return(false);
	}
	return(true);
}
function addrUserPicture(id)
{
	openOverlay('organizer.addressbook.php?action=userPictureDialog&id=' + id + '&sid=' + currentSID,
		lang['userpicture'],
		520,
		360,
		true);
}
function addrImportDialog(sid)
{
	var book = EBID('importAddressbook'),
		url = 'organizer.addressbook.php?action=importDialog&type=' + EBID('importType').value + '&encoding=' + EBID('importEncoding').value + '&sid=' + sid;
	if(book)
		url += '&importBook=' + encodeURIComponent(book.value);
	openOverlay(url,
		lang['import'],
		520,
		130);
}
/**************************************************************************
 * Tasks
 *************************************************************************/
var currentTaskListID = 0, _tasksSel;
function initTasksSel()
{
	var sel = new selecTable(EBID('tasksTable'), 'tr', true);
	sel.cbGetItemID = function(element)
	{
		return(element.id.substr(5));
	}
	sel.cbRowFilter = function(element)
	{
		return(element.id.substr(0, 5) == 'task_');
	}
	sel.cbSelectSingleItem = function(element)
	{
	}
	sel.cbSelectionChanged = function()
	{
	}
	sel.cbItemContextMenu = function(element, event)
	{
		return(false);
	}
	sel.cbItemDragStart = function(element, event)
	{
		var dragImg = document.createElement('img');
		dragImg.src = tplDir + 'images/li/drag_task' + (this.sel.length>1?'s':'') + '.png';
		dragImg.width = 32;
		dragImg.height = 32;

		event.dataTransfer.setData('taskitems', this.getIDList());
		event.dataTransfer.setDragImage(dragImg, -10, -10);

		return(true);
	}
	sel.cbItemDoubleClick = function(element)
	{
		document.location.href = bmAppendSession('organizer.todo.php?action=editTask&id='+this.getItemID(element));
	}
	sel.init();
	_tasksSel = sel;
}

function enableTodoDragTargets()
{
	var dragEnter = function(event)
	{
		if((event.dataTransfer.types.contains && !event.dataTransfer.types.contains('taskitems'))
				|| (event.dataTransfer.types.indexOf && event.dataTransfer.types.indexOf('taskitems') < 0))
			return;
	}

	var dragLeave = function(event)
	{
		if((event.dataTransfer.types.contains && !event.dataTransfer.types.contains('taskitems'))
				|| (event.dataTransfer.types.indexOf && event.dataTransfer.types.indexOf('taskitems') < 0))
			return;

		this.style.textDecoration = 'none';
	}

	var dragOver = function(event)
	{
		if((event.dataTransfer.types.contains && !event.dataTransfer.types.contains('taskitems'))
				|| (event.dataTransfer.types.indexOf && event.dataTransfer.types.indexOf('taskitems') < 0))
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

		if(!event.dataTransfer.getData('taskitems'))
			return;

		var destID = this.id.substr(9);
		moveTasks(event.dataTransfer.getData('taskitems'), destID);

		this.style.backgroundColor = '';
		this.style.textDecoration = 'none';
	}

	var listContainer = EBID('taskListsContainer');
	if(!listContainer) return;
	var items = listContainer.querySelectorAll('.taskList');

	for(var i=0; i<items.length; i++)
	{
		var A = items[i];

		addEvent(A, 'dragenter', dragEnter);
		addEvent(A, 'dragleave', dragLeave);
		addEvent(A, 'dragover', dragOver);
		addEvent(A, 'drop', dragDrop);
	}
}
function moveTasks(tasks, destID)
{
	if(!tasks) return;
	if(destID == currentTaskListID) return;

	MakeXMLRequest('organizer.todo.php?do=moveTasks&listOnly=true&taskListID='+currentTaskListID+'&tasks=' + escape(tasks) + '&destID=' + destID + '&sid=' + currentSID, function(http)
		{
			if(http.readyState == 4)
			{
				reloadTaskList(http.responseText, true);
			}
		});
}
function transferSelectedTasks()
{
	var f = EBID('taskIDs');
	if(f)
	{
		f.value = '';

		var IDs = _tasksSel.getIDList();

		for(i=0; i<IDs.length; i++)
		{
			f.value += IDs[i] + ';';
		}

		if(f.value.length > 0)
			f.value = f.value.substr(0, f.value.length-1);
	}
}
function checkTodoForm(form)
{
	if(form.elements['titel'].value.length < 2
		|| form.elements['erledigt'].value.length < 1)
	{
		alert(lang['fillin']);
		return(false);
	}
	return(true);
}
function todoXmlVal(item, tag)
{
	var n = item.getElementsByTagName(tag);
	if(!n.length || !n.item(0).childNodes.length)
		return '';
	return n.item(0).childNodes.item(0).nodeValue;
}
function todoActionIconButton(iconClass, title, onclick)
{
	var a = document.createElement('a');
	a.href = '#';
	a.className = 'btn btn-icon';
	a.title = title;
	a.setAttribute('aria-label', title);
	a.onclick = onclick;
	var i = document.createElement('i');
	i.className = iconClass;
	i.setAttribute('aria-hidden', 'true');
	a.appendChild(i);
	return a;
}
function todoDisabledIconButton(iconClass, title)
{
	var span = document.createElement('span');
	span.className = 'btn btn-icon disabled';
	span.setAttribute('aria-disabled', 'true');
	span.title = title;
	var i = document.createElement('i');
	i.className = iconClass;
	i.setAttribute('aria-hidden', 'true');
	span.appendChild(i);
	return span;
}
function reloadTodoLists(xml, scrollDown)
{
	if(typeof(xml) == 'undefined')
	{
		MakeXMLRequest('organizer.todo.php?action=getLists&sid='+currentSID, function(r)
		{
			if(r.readyState == 4)
				reloadTodoLists(r.responseXML, scrollDown);
		});
	}
	else
	{
		var container = EBID('taskListsContainer');
		if(!container) return;

		while(container.firstChild)
			container.removeChild(container.firstChild);

		var canShare = container.getAttribute('data-can-share') == '1';
		var shareTitle = container.getAttribute('data-share-title') || '';
		var leaveTitle = container.getAttribute('data-leave-title') || '';
		var shareIcon = container.getAttribute('data-share-icon') || 'fa fa-share';
		var trashIcon = container.getAttribute('data-trash-icon') || 'fa fa-trash-o';
		var items = xml.getElementsByTagName('item');
		for(var i=0; i<items.length; i++)
		{
			var item = items.item(i),
				itemTitle = todoXmlVal(item, 'title'),
				itemID = todoXmlVal(item, 'tasklistid'),
				canShareItem = todoXmlVal(item, 'can_share') == '1',
				canDelete = todoXmlVal(item, 'can_delete') == '1',
				canLeave = todoXmlVal(item, 'can_leave') == '1';

			var wrap = document.createElement('div');
			wrap.className = 'taskList' + (String(currentTaskListID) == String(itemID) ? ' selected' : '');
			wrap.id = 'taskList_'+itemID;
			wrap.onclick = (function(id) { return function() { selectTaskList(id); }; })(itemID);

			var titleA = document.createElement('a');
			titleA.href = '#';
			titleA.className = 'bm-organizer-tasklist-title';
			titleA.appendChild(document.createTextNode(itemTitle));
			titleA.onclick = (function(id) { return function() { selectTaskList(id); return false; }; })(itemID);
			wrap.appendChild(titleA);

			var actions = document.createElement('span');
			actions.className = 'bm-organizer-tasklist-actions';
			actions.onclick = function(e) { if(e) e.stopPropagation(); };

			if(canShare && canShareItem)
			{
				actions.appendChild(todoActionIconButton(shareIcon, shareTitle, (function(id) {
					return function(e) {
						if(e) { e.preventDefault(); e.stopPropagation(); }
						return organizerOpenOverlay(bmAppendSession('organizer.todo.php?action=share&id='+id), shareTitle, 520, 360);
					};
				})(itemID)));
			}

			if(canDelete)
			{
				var delImg = document.createElement('img');
				delImg.src = tplDir + 'images/li/delcross.png';
				delImg.onclick = (function(id) {
					return function(e) {
						if(e) { e.preventDefault(); e.stopPropagation(); }
						deleteTaskList(id);
						return false;
					};
				})(itemID);
				actions.appendChild(delImg);
			}
			else if(canLeave)
			{
				actions.appendChild(todoActionIconButton(trashIcon, leaveTitle, (function(id) {
					return function(e) {
						if(e) { e.preventDefault(); e.stopPropagation(); }
						return organizerOpenOverlay(bmAppendSession('organizer.todo.php?action=leaveshare&id='+id), leaveTitle, 480, 260);
					};
				})(itemID)));
			}

			wrap.appendChild(actions);
			container.appendChild(wrap);
		}

		enableTodoDragTargets();

		if(scrollDown)
			EBID('taskListsScrollContainer').scrollTop = getElementMetrics(container, 'h');
	}
}
function deleteTaskList(id)
{
	if(confirm(lang['realdel']))
	{
		MakeXMLRequest('organizer.todo.php?action=deleteList&tasklistid='+encodeURIComponent(id)+'&sid='+currentSID, function(r)
		{
			if(r.readyState == 4)
			{
				if(currentTaskListID == id)
					selectTaskList(0);
				reloadTodoLists(r.responseXML, false);
			}
		});
	}
}
function reloadTaskList(data, noFocus)
{
	if(!data)
	{
		selectTaskList(currentTaskListID, true);
	}
	else
	{
		EBID('taskListContainer').innerHTML = data;
		initTasksSel();
		if(!noFocus && EBID('newTaskText')) EBID('newTaskText').focus();
	}
}
function selectTaskList(id, nocheck)
{
	if(!nocheck)
	{
		if(currentTaskListID == id) return;
	}

	MakeXMLRequest('organizer.todo.php?taskListID='+encodeURIComponent(id)+'&listOnly=true&sid='+currentSID, function(r)
	{
		if(r.readyState == 4)
		{
			EBID('taskListContainer').innerHTML = r.responseText;

			if(!nocheck)
			{
				if(EBID('taskList_'+currentTaskListID))
					EBID('taskList_'+currentTaskListID).className = EBID('taskList_'+currentTaskListID).className.replace(' selected', '');
				EBID('taskList_'+id).className += ' selected';
			}

			currentTaskListID = id;
			initTasksSel();
			if(EBID('newTaskText')) EBID('newTaskText').focus();
		}
	});
}
function addTodoList()
{
	var title = EBID('addListTitle').value;
	if(title.length < 1)
		return;

	EBID('addListTitle').value = '';

	MakeXMLRequest('organizer.todo.php?action=addList&title='+encodeURIComponent(title)+'&sid='+currentSID, function(r)
	{
		if(r.readyState == 4)
		{
			reloadTodoLists(r.responseXML, true);
		}
	});
}
function addTask()
{
	if(EBID('newTaskText').value.length < 1)
	{
		EBID('newTaskText').focus();
		return(false);
	}

	MakeXMLRequest('organizer.todo.php?do=addTask&title='+encodeURIComponent(EBID('newTaskText').value)+'&listOnly=true&taskListID='+currentTaskListID+'&sid='+currentSID, function(r)
	{
		if(r.readyState == 4)
		{
			reloadTaskList(r.responseText);
		}
	});
}
function newTaskKeyPress(event)
{
	if(event.keyCode == 13)
	{
		addTask();
		return(false);
	}
	return(true);
}
function todoListInputKeyPress(event)
{
	if(event.keyCode == 13)
	{
		addTodoList();
		return(false);
	}
	return(true);
}

/**************************************************************************
 * Calendar
 *************************************************************************/
function calendarDaySizer()
{
	var wholeDay = EBID('calendarWholeDayBody'), wholeDayHeight = getElementMetrics(wholeDay, 'h'),
		day = EBID('calendarDayBody');
	var cParent = EBID('calendarContainer');

	var or = function()
	{
		var parentHeight = getElementMetrics(cParent, 'h');
		day.style.height = (parentHeight-wholeDayHeight) + 'px';

		var wt = EBID('weekWholeDayTable');
		if(wt)
		{
			var dayIndex = 0;
			var TDs = wt.getElementsByTagName('td');
			for(var i=0; i<TDs.length; i++)
			{
				var TD = TDs[i];
				if(TD.className == 'calendarWeekDayCaption')
				{
					var timeRow = EBID('timeRow_'+dayIndex+'_0');
					if(!timeRow)
					{
						alert("nada");
						continue;
					}

					var dayW = getElementMetrics(timeRow, 'w');
					TD.style.width = (dayW-3) + 'px';

					dayIndex++;
				}
			}
		}

		if(typeof(IE7) != 'undefined') IE7.recalc();
	}

	addEvent(window, 'resize', or);

	or();
}
function generateAttendeeList()
{
	var attendees = EBID('attendeeList');

	// clear
	var node;
	while(node = attendees.firstChild)
		attendees.removeChild(node);

	// files
	var files = EBID('attendees').value.split(';');

	// generate new table
	var table = document.createElement('table');
	table.style.marginTop = '5px';
	table.style.marginBottom = '5px';
	table.className = 'listTable';

	var tbody = document.createElement('tbody');

	// head
	var headTR = document.createElement('tr'),
		headTH = document.createElement('th');
	headTH.className = 'listTableHead';
	headTH.colSpan = '3';
	headTH.style.textAlign = 'center';
	headTH.appendChild(document.createTextNode(lang['attendees']));
	headTR.appendChild(headTH);
	tbody.appendChild(headTR);

	// add file rows
	var className = 'listTableTD', fileCount = 0;
	for(var i=0; i<files.length; i++)
	{
		if(files[i] != '')
		{
			var params	= files[i].split(','),
				attID			= params[0],
				attLastName		= params[1],
				attFirstName	= params[2];

			var img = document.createElement('img');
			img.setAttribute('src', tplDir + 'images/li/addr_priv.png');
			img.setAttribute('width', '16');
			img.setAttribute('height', '16');
			img.setAttribute('align', 'absmiddle');
			img.setAttribute('border', '0');
			img.setAttribute('alt', '');

			var td1 = document.createElement('td');
			td1.style.paddingLeft = '4px';
			td1.appendChild(img);
			td1.appendChild(document.createTextNode(' ' + attLastName + ', ' + attFirstName));

			var del = document.createElement('a'),
				delImg = document.createElement('img');
			delImg.setAttribute('src', tplDir + 'images/li/ico_delete.png');
			delImg.setAttribute('width', '16');
			delImg.setAttribute('height', '16');
			delImg.setAttribute('align', 'absmiddle');
			delImg.setAttribute('border', '0');
			delImg.setAttribute('alt', '');
			del.setAttribute('href', 'javascript:deleteAttendee(' + attID + ');');
			del.appendChild(delImg);

			var td2 = document.createElement('td');
			td2.setAttribute('width', '20%');
			td2.style.paddingRight = '4px';
			td2.style.textAlign = 'right';
			td2.appendChild(del);

			var tr = document.createElement('tr');
			tr.className = className;
			tr.appendChild(td1);
			tr.appendChild(td2);
			tbody.appendChild(tr);

			// cycle class name
			if(className == 'listTableTD')
				className = 'listTableTD2';
			else
				className = 'listTableTD';
			fileCount++;
		}
	}

	table.appendChild(tbody);

	if(fileCount > 0)
		attendees.appendChild(table);
}
function deleteAttendee(id)
{
	var attendees = EBID('attendees'),
		attendeeList = attendees.value.split(';'),
		newAttendeeList = '';

	// remove entry from list
	for(var i=0; i<attendeeList.length; i++)
	{
		if(attendeeList[i] != '')
		{
			var params = attendeeList[i].split(',');
			if(params[0] != id)
				newAttendeeList += ';' + attendeeList[i];
		}
	}

	// rebuild table
	attendees.value = newAttendeeList;
	generateAttendeeList();
}
function addAttendee(sid)
{
	openOverlay('organizer.addressbook.php?sid=' + sid + '&action=attendeePopup&attendeeList=' + escape(EBID('attendees').value),
		lang['addattendee'],
		420,
		310,
		true);
}
function toggleRepeatingDiv(c)
{
	EBID('repeatingDiv').style.display = c.checked ? '' : 'none';
}
function checkCalendarDateForm(form)
{
	if(form.elements['title'].value.length < 2)
	{
		alert(lang['fillin']);
		return(false);
	}
	if(typeof syncSmartyDate === 'function')
	{
		syncSmartyDate('startdate');
		syncSmartyDate('enddate');
	}
	function pickTs(prefix)
	{
		var y = form.elements[prefix + 'Year'],
			m = form.elements[prefix + 'Month'],
			d = form.elements[prefix + 'Day'];
		if(!y || !m || !d) return null;
		var yy = parseInt(y.value, 10),
			mm = parseInt(m.value, 10) - 1,
			dd = parseInt(d.value, 10),
			hh = 0, mi = 0;
		var h = form.elements[prefix + 'Hour'],
			n = form.elements[prefix + 'Minute'];
		if(h) hh = parseInt(h.value, 10);
		if(n) mi = parseInt(n.value, 10);
		return new Date(yy, mm, dd, hh, mi, 0).getTime();
	}
	var startTs = pickTs('startdate'),
		endTs = pickTs('enddate');
	if(startTs !== null && endTs !== null && endTs < startTs)
	{
		alert((typeof lang !== 'undefined' && lang['fillin']) ? lang['fillin'] : 'End date must be after start date.');
		return(false);
	}
	return(true);
}
function toggleWholeDay(el)
{
	var checked = !!(EBID('wholeDay1') && EBID('wholeDay1').checked),
		startTime = EBID('startdate_time'),
		endTime = EBID('enddate_time');
	if(startTime) startTime.style.display = checked ? 'none' : '';
	if(endTime) endTime.style.display = checked ? 'none' : '';
	if(typeof syncSmartyDate === 'function')
	{
		syncSmartyDate('startdate');
		syncSmartyDate('enddate');
	}
}
// Sync HTML5 <input type="date"> / <input type="time"> back to the classic
// SmartyDateTime hidden fields (…Day, …Month, …Year, …Hour, …Minute) so that
// the server code (SmartyDateTime()) continues to work unchanged.
// hiddenPrefix is optional; useful e.g. for 'geburtsdatum_' where the visual
// element ids (geburtsdatum_date) differ from the SmartyDateTime prefix.
function syncSmartyDate(prefix, hiddenPrefix)
{
	if(typeof hiddenPrefix !== 'string') hiddenPrefix = prefix;

	var dateEl = EBID(prefix + '_date'),
		timeEl = EBID(prefix + '_time'),
		wholeDay = !!(EBID('wholeDay1') && EBID('wholeDay1').checked),
		dayEl = EBID(hiddenPrefix + 'Day'),
		monthEl = EBID(hiddenPrefix + 'Month'),
		yearEl = EBID(hiddenPrefix + 'Year'),
		hourEl = EBID(hiddenPrefix + 'Hour'),
		minuteEl = EBID(hiddenPrefix + 'Minute');

	if(dateEl && dayEl && monthEl && yearEl)
	{
		if(dateEl.value)
		{
			var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(dateEl.value);
			if(m)
			{
				yearEl.value = m[1];
				monthEl.value = m[2];
				dayEl.value = m[3];
			}
		}
		else
		{
			// Empty date -> 0/0/0 so SmartyDateTime() returns 0 (= no date set)
			dayEl.value = '0';
			monthEl.value = '0';
			yearEl.value = '0';
		}
	}

	if(hourEl && minuteEl)
	{
		if(wholeDay)
		{
			hourEl.value = '0';
			minuteEl.value = '0';
		}
		else if(timeEl && timeEl.value)
		{
			var t = /^(\d{2}):(\d{2})/.exec(timeEl.value);
			if(t)
			{
				hourEl.value = t[1];
				minuteEl.value = t[2];
			}
		}
	}
}
function checkCalendarGroupForm(form)
{
	if(form.elements['title'].value.length < 2)
	{
		alert(lang['fillin']);
		return(false);
	}
	return(true);
}
function organizerOpenOverlay(url, title, w, h)
{
	openOverlay(url, title, w, h, true);
	return false;
}
function updateCalendarViewMode(c, date, sid)
{
	document.location.href = bmAppendSession('organizer.calendar.php?view=' + c.value + '&date=' + date);
}
function updateCalendarGroup(c, date, sid)
{
	document.location.href = bmAppendSession('organizer.calendar.php?switchGroup=' + c.value + '&date=' + date);
}
function updateVisibleCalendars(date)
{
	var boxes = document.querySelectorAll('.visibleCalBox:checked'), ids = [], i;
	for(i=0; i<boxes.length; i++)
		ids.push(boxes[i].value);
	document.location.href = bmAppendSession('organizer.calendar.php?visibleCal=' + ids.join(',') + '&date=' + date);
}
function filterCalendarGroups()
{
	var cal = document.getElementById('calendar'), group = document.getElementById('group'), i, opt, cid;
	if(!cal || !group) return;
	cid = String(cal.value);
	for(i=0; i<group.options.length; i++)
	{
		opt = group.options[i];
		opt.hidden = (opt.value != '-1' && opt.getAttribute('data-calendar') != '0' && opt.getAttribute('data-calendar') != cid);
	}
	if(group.selectedIndex >= 0 && group.options[group.selectedIndex].hidden)
		group.value = '-1';
}
function filterAddressbookGroups()
{
	var book = document.getElementById('contactAddressbook'), rows, i, bid, inp;
	if(!book) return;
	bid = String(book.value);
	rows = document.querySelectorAll('.abGroupRow');
	for(i=0; i<rows.length; i++)
	{
		if(rows[i].getAttribute('data-addressbook') == bid)
			rows[i].style.display = '';
		else
		{
			rows[i].style.display = 'none';
			inp = rows[i].querySelector('input[type=checkbox]');
			if(inp)
				inp.checked = false;
		}
	}
}
function calendarDateClick(id)
{
	var parts = id.split('_');
	showCalendarDate(parts[1], parseInt(parts[2]), parseInt(parts[3]), true)
}
function initCalendar(resize)
{
	calDayBody = EBID('calendarDayBody');

	for(var i=0; i<calendarDates.length; i++)
	{
		var date = calendarDates[i],
			time = new Date((date[1]/*-getTZOffset()*/)*1000),
			hour = time.getHours(),
			minute = time.getMinutes();
			endTime = new Date((date[2]/*-getTZOffset()*/)*1000),
			endHour = endTime.getHours(),
			endMinute = endTime.getMinutes(),
			concurrentDates = [],
			myPos = 0,
			dayStr = date.length==6 ? date[5]+'_' : '';
		if(minute > 30)
		{
			hour++;
			minute = 0;
		}
		else if(minute > 0)
			minute = 30;
		if(endMinute > 30)
		{
			endHour++;
			endMinute = 0;
		}
		else if(endMinute > 0)
			endMinute = 30;
		var startRow = (hour*2+minute/30),
			endRow = (endHour*2+endMinute/30),
			addHeight = false;
		if(startRow < 0) startRow = 0;
		if(endRow < 0) endRow = 0;
		if(startRow > 47) startRow = 47;
		if(endRow > 47) { addHeight = true; endRow = 47; }

		// concurrent dates?
		for(var j=0; j<calendarDates.length; j++)
		{
			var cDate = calendarDates[j];
			if(i != j)
			{
				if((cDate[1] >= date[1] && cDate[1] <= date[2])
					|| (cDate[1] < date[1] && cDate[2] >= date[1]))
				{
					if(cDate[1] < date[1]
						|| (cDate[1] == date[1] && cDate[0] < date[0]))
						myPos++;
					concurrentDates.push(cDate);
				}
			}
		}

		var dateDivTop = parseInt(EBID('timeRow_' + dayStr + startRow).offsetTop) - 3,
			dateDivHeight = parseInt(EBID('timeRow_' + dayStr + endRow).offsetTop + (addHeight ? parseInt(EBID('timeRow_' + dayStr + endRow).offsetHeight) : 0)) - dateDivTop - 12,
			dateDivWidth = Math.floor(parseInt(EBID('timeRow_' + dayStr + startRow).offsetWidth) / (concurrentDates.length+1)),
			dateDivLeft = parseInt(EBID('timeRow_' + dayStr + startRow).offsetLeft) + myPos*(dateDivWidth);

		if(dayStr == '')
			dateDivWidth -= 20;
		else
			dateDivWidth -= 14;

		var dateDiv = EBID('date_' + date[0] + '_' + date[1] + '_' + date[2]);
		if(!dateDiv)
		{
			var dateDiv = document.createElement('div');
			dateDiv.id = 'date_' + date[0] + '_' + date[1] + '_' + date[2];
			dateDiv.className = 'calendarDate_' + date[4];
			dateDiv.onclick = function() { calendarDateClick(this.id); }
			dateDiv.appendChild(document.createTextNode(date[3]));
			dateDiv.style.position = 'absolute';
			calDayBody.appendChild(dateDiv);
		}

		dateDiv.style.left = dateDivLeft + 'px';
		dateDiv.style.top = dateDivTop + 'px';
		dateDiv.style.height = dateDivHeight + 'px';
		dateDiv.style.width = dateDivWidth + 'px';
	}

	if(!resize)
	{
		if(EBID('timeRow_0_'+calendarDayStart))
		{
			// week view
			var elem = EBID('timeRow_0_'+calendarDayStart);

			EBID('calendarDayBody').scrollTop = getElementMetrics(elem, 'y')
														- getElementMetrics(calDayBody, 'y')
														- 10;
		}
		else
		{
			// day view
			window.scrollBy(0, EBID('timeRow_'+calendarDayStart).offsetTop-10);
		}

		addEvent(window, 'resize', function() {
			initCalendar(true);
		});
	}
}

// -------------------------------------------------------------------
// Range selection: drag over calendar cells (day/week/month views) to
// open the "add date" dialog with pre-filled start time and duration.
// -------------------------------------------------------------------
function initCalendarRangeSelection()
{
	if(window._calendarRangeSelectionInited)
		return;
	window._calendarRangeSelectionInited = true;

	_calendarInjectRangeSelectionStyles();

	if(typeof calendarWeekDays !== 'undefined' && calendarWeekDays && calendarWeekDays.length > 0)
		_calendarInitHalfHourRange();

	if(document.querySelector && document.querySelector('td[data-day-timestamp]'))
		_calendarInitMonthRange();
}

function _calendarInjectRangeSelectionStyles()
{
	if(document.getElementById('calendarRangeSelectionStyles'))
		return;
	var css = '.calendarRangeHighlight{background-color:rgba(74,144,226,.35) !important;outline:1px solid rgba(74,144,226,.7);cursor:crosshair;}'
			+ 'body.calendarRangeSelecting, body.calendarRangeSelecting *{cursor:crosshair !important;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;}';
	var s = document.createElement('style');
	s.id = 'calendarRangeSelectionStyles';
	s.type = 'text/css';
	if(s.styleSheet) s.styleSheet.cssText = css;
	else s.appendChild(document.createTextNode(css));
	(document.head || document.getElementsByTagName('head')[0] || document.documentElement).appendChild(s);
}

function _calendarBuildAddDateUrl(date, time, durationHours, durationMinutes, wholeDay)
{
	var params = 'action=addDate&date=' + encodeURIComponent(date);
	if(time !== null && time !== undefined)
		params += '&time=' + encodeURIComponent(time);
	if(durationHours !== null && durationHours !== undefined)
		params += '&durationHours=' + encodeURIComponent(durationHours);
	if(durationMinutes !== null && durationMinutes !== undefined)
		params += '&durationMinutes=' + encodeURIComponent(durationMinutes);
	if(wholeDay)
		params += '&wholeDay=1';
	if(typeof currentSID !== 'undefined' && currentSID)
		params += '&sid=' + encodeURIComponent(currentSID);
	return 'organizer.calendar.php?' + params;
}

function _calendarNavigateTop(url)
{
	try {
		if(window.top && window.top !== window)
			window.top.location.href = url;
		else
			document.location.href = url;
	} catch(e) {
		document.location.href = url;
	}
}

function _calendarParseTimeRow(el)
{
	if(!el || !el.id) return null;
	var parts = el.id.split('_');
	if(parts.length === 3 && parts[0] === 'timeRow')
		return { day: parseInt(parts[1], 10), half: parseInt(parts[2], 10) };
	if(parts.length === 2 && parts[0] === 'timeRow')
		return { day: 0, half: parseInt(parts[1], 10) };
	return null;
}

function _calendarFindTimeCell(el)
{
	while(el && el.nodeType === 1)
	{
		if(el.id && el.id.indexOf && el.id.indexOf('timeRow_') === 0)
			return el;
		if(el.className && typeof el.className === 'string' && el.className.indexOf('calendarDate_') === 0)
			return null;
		el = el.parentNode;
	}
	return null;
}

function _calendarInitHalfHourRange()
{
	var isWeek = (calendarWeekDays.length > 1),
		container = document.getElementById('calendarDayBody');
	if(!container) return;

	// Linear index across days: idx = day * 48 + half. Simplifies ordering
	// when the user drags across day boundaries (multi-day selection).
	var dragStart = null, dragEnd = null, isDragging = false;

	function linearIdx(info) { return info.day * 48 + info.half; }

	function clearHighlight()
	{
		var els = container.querySelectorAll('.calendarRangeHighlight');
		for(var i=0; i<els.length; i++) els[i].classList.remove('calendarRangeHighlight');
	}

	function updateHighlight()
	{
		clearHighlight();
		if(!dragStart || !dragEnd) return;
		var startIdx = linearIdx(dragStart),
			endIdx = linearIdx(dragEnd);
		if(startIdx > endIdx) { var t = startIdx; startIdx = endIdx; endIdx = t; }
		for(var idx = startIdx; idx <= endIdx; idx++)
		{
			var d = Math.floor(idx / 48),
				h = idx % 48;
			var id = isWeek ? ('timeRow_' + d + '_' + h) : ('timeRow_' + h);
			var el = document.getElementById(id);
			if(el) el.classList.add('calendarRangeHighlight');
		}
	}

	function onMouseDown(e)
	{
		if(e.button !== undefined && e.button !== 0) return;
		var cell = _calendarFindTimeCell(e.target);
		if(!cell) return;
		var info = _calendarParseTimeRow(cell);
		if(!info) return;
		dragStart = info;
		dragEnd = info;
		isDragging = true;
		if(document.body) document.body.className += ' calendarRangeSelecting';
		updateHighlight();
		if(e.preventDefault) e.preventDefault();
	}

	function onMouseMove(e)
	{
		if(!isDragging) return;
		var cell = _calendarFindTimeCell(e.target);
		if(!cell) return;
		var info = _calendarParseTimeRow(cell);
		if(!info) return;
		// Multi-day selection is allowed in the week view. The day view has
		// only one column, so info.day is always 0 there.
		if(dragEnd.day !== info.day || dragEnd.half !== info.half)
		{
			dragEnd = info;
			updateHighlight();
		}
	}

	function onMouseUp()
	{
		if(!isDragging) return;
		isDragging = false;
		if(document.body) document.body.className = document.body.className.replace(/\s*calendarRangeSelecting/g, '');

		var startIdx = linearIdx(dragStart),
			endIdx = linearIdx(dragEnd);
		if(startIdx > endIdx) { var t = startIdx; startIdx = endIdx; endIdx = t; }

		var startDay = Math.floor(startIdx / 48),
			startHalf = startIdx % 48,
			halfCount = (endIdx - startIdx) + 1;

		clearHighlight();

		var startDayTs = calendarWeekDays[startDay];
		if(typeof startDayTs === 'undefined')
		{
			dragStart = dragEnd = null;
			return;
		}
		var timeTs = startDayTs + startHalf * 1800,
			durationTotalMin = halfCount * 30,
			durationHours = Math.floor(durationTotalMin / 60),
			durationMinutes = durationTotalMin % 60;

		var url = _calendarBuildAddDateUrl(startDayTs, timeTs, durationHours, durationMinutes);
		dragStart = dragEnd = null;
		_calendarNavigateTop(url);
	}

	addEvent(container, 'mousedown', onMouseDown);
	addEvent(container, 'mousemove', onMouseMove);
	addEvent(document, 'mouseup', onMouseUp);
}

function _calendarFindMonthCell(el)
{
	while(el && el.nodeType === 1)
	{
		if(el.className && typeof el.className === 'string')
		{
			if(el.className.indexOf('monthDate_') === 0) return null;
			if(el.className.indexOf('monthCellDay') !== -1) return null;
			if(el.className.indexOf('bm-organizer-month-event') !== -1) return null;
			if(el.className.indexOf('bm-organizer-month-day') !== -1) return null;
		}
		if(el.getAttribute && el.getAttribute('data-day-timestamp'))
			return el;
		el = el.parentNode;
	}
	return null;
}

function _calendarInitMonthRange()
{
	var cellsNodeList = document.querySelectorAll('td[data-day-timestamp]');
	if(cellsNodeList.length === 0) return;

	var cellList = [];
	for(var i=0; i<cellsNodeList.length; i++)
	{
		cellList.push({
			el: cellsNodeList[i],
			ts: parseInt(cellsNodeList[i].getAttribute('data-day-timestamp'), 10)
		});
	}
	cellList.sort(function(a,b){ return a.ts - b.ts; });

	var dragStartIdx = null, dragEndIdx = null, isDragging = false;

	function clearHighlight()
	{
		for(var i=0; i<cellList.length; i++) cellList[i].el.classList.remove('calendarRangeHighlight');
	}

	function updateHighlight()
	{
		clearHighlight();
		if(dragStartIdx === null || dragEndIdx === null) return;
		var min = Math.min(dragStartIdx, dragEndIdx),
			max = Math.max(dragStartIdx, dragEndIdx);
		for(var i=min; i<=max; i++) cellList[i].el.classList.add('calendarRangeHighlight');
	}

	function indexOfCellEl(el)
	{
		for(var i=0; i<cellList.length; i++) if(cellList[i].el === el) return i;
		return -1;
	}

	function onMouseDown(e)
	{
		if(e.button !== undefined && e.button !== 0) return;
		var cell = _calendarFindMonthCell(e.target);
		if(!cell) return;
		var idx = indexOfCellEl(cell);
		if(idx < 0) return;
		dragStartIdx = idx;
		dragEndIdx = idx;
		isDragging = true;
		if(document.body) document.body.className += ' calendarRangeSelecting';
		updateHighlight();
		if(e.preventDefault) e.preventDefault();
	}

	function onMouseMove(e)
	{
		if(!isDragging) return;
		var cell = _calendarFindMonthCell(e.target);
		if(!cell) return;
		var idx = indexOfCellEl(cell);
		if(idx < 0) return;
		if(idx !== dragEndIdx)
		{
			dragEndIdx = idx;
			updateHighlight();
		}
	}

	function onMouseUp()
	{
		if(!isDragging) return;
		isDragging = false;
		if(document.body) document.body.className = document.body.className.replace(/\s*calendarRangeSelecting/g, '');
		var min = Math.min(dragStartIdx, dragEndIdx),
			max = Math.max(dragStartIdx, dragEndIdx);
		clearHighlight();

		// Single-day click: do not intercept — let default day-number click work
		if(min === max)
		{
			dragStartIdx = dragEndIdx = null;
			return;
		}

		var startTs = cellList[min].ts,
			days = (max - min) + 1,
			// Whole-day multi-day event. durationHours is used server-side to
			// derive the enddate for multi-day whole-day events.
			durationHours = days * 24,
			durationMinutes = 0;

		var url = _calendarBuildAddDateUrl(startTs, null, durationHours, durationMinutes, true);
		dragStartIdx = dragEndIdx = null;
		_calendarNavigateTop(url);
	}

	addEvent(document, 'mousedown', onMouseDown);
	addEvent(document, 'mousemove', onMouseMove);
	addEvent(document, 'mouseup', onMouseUp);
}
