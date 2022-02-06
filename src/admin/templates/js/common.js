var loadActions = [], _usersToDelete = [], _usersMax, _usersStep, _refreshURL, rc_statusdiv;

function expandStatsDay(type, userID, day)
{
	var statsTable = EBID(type+'StatsTable');
	var tbody = EBID(type+'Stats_'+day);
	var chevron = EBID(type+'Stats_'+day+'_chevron');

	chevron.className = chevron.className == 'fa fa-chevron-right' ? 'fa fa-chevron-down' : 'fa fa-chevron-right';
	tbody.style.display = (tbody.style.display == 'none') ? '' : 'none';

	if(tbody.getElementsByTagName('tr').length > 0)
		return;

	MakeXMLRequest('abuse.php?sid=' + currentSID
						+ '&do=statsDetails&type=' + type + '&userid=' + encodeURIComponent(userID)
						+ '&day=' + encodeURIComponent(day),
	function(http)
	{
		if(http.readyState == 4)
		{
			var data = eval('(' + http.responseText + ')');
			for(var i=0; i<data.length; ++i)
			{
				var tr = document.createElement('tr'),
					timeTd = document.createElement('td'),
					mailsTd = document.createElement('td'),
					fieldTd = document.createElement('td');

				timeTd.style.paddingLeft = '2em';
				timeTd.innerHTML = data[i].hour;
				mailsTd.innerHTML = data[i].mails;

				if(type == 'send')
					fieldTd.innerHTML = data[i].recipients;
				else
					fieldTd.innerHTML = data[i].size;

				tr.appendChild(timeTd);
				tr.appendChild(mailsTd);
				tr.appendChild(fieldTd);
				tbody.appendChild(tr);
			}
		}
	});
}

function toggleFieldset(elem)
{
	while(elem)
	{
		if(typeof(elem.tagName) != 'undefined' && elem.tagName.toUpperCase() == 'FIELDSET')
			break;
		elem = elem.parentNode;
	}

	if(!elem)
		return;

	elem.className = elem.className == 'collapsed' ? 'uncollapsed' : 'collapsed';
}

function changeCaptchaProvider(elem)
{
	var currentProvider = elem.value;

	var elems = document.getElementsByTagName('fieldset');
	for(var i=0; i<elems.length; ++i)
	{
		var el = elems[i];

		if(typeof(el.id) == 'undefined' || el.id.length < 4 || el.id.substr(0, 3) != 'cp_')
			continue;

		el.style.display = (el.id == 'cp_'+currentProvider) ? '' : 'none';
	}
}

function userMassActionFormSubmit(form)
{
	// no action required for all actions except 'delete'
	if(EBID('massAction').value != 'delete')
	{
		spin(form);
		return(true);
	}

	// build refresh url
	_refreshURL = form.action
		+ '&page=' + encodeURIComponent(EBID('page').value)
		+ '&sortBy=' + encodeURIComponent(EBID('sortBy').value)
		+ '&sortOrder=' + encodeURIComponent(EBID('sortOrder').value)
		+ '&massAction=-';
	if(EBID('query'))
		_refreshURL += '&query=' + encodeURIComponent(EBID('query').value);

	// get user IDs and filter params
	_usersToDelete = [];
	var inputs = document.getElementsByTagName('input');
	for(var i=0; i<inputs.length; i++)
	{
		if(inputs[i].getAttribute('type') != 'checkbox')
			continue;

		if(!inputs[i].checked)
			continue;

		var name = inputs[i].getAttribute('name');

		if((name.length > 6 && name.substr(0, 6) == 'status')
			|| (name.length > 6 && name.substr(0, 6) == 'group_'))
		{
			_refreshURL += '&' + name + '=true';
		}
		else if(name.length > 5 && name.substr(0, 5) == 'user_')
		{
			var userID = name.substr(5);
			_usersToDelete.push(userID);
		}
	}

	_usersMax = _usersToDelete.length;
	_usersStep = 1;

	// init progress indicator
	rc_statusdiv = document.createElement('div');
	rc_statusdiv.style.textAlign = 'center';
	rc_statusdiv.innerHTML = '1 / ' + _usersToDelete.length + ' ...';

	var spinner = document.createElement('img');
	spinner.src = './templates/images/load_32.gif';
	spinner.style.padding = '20px';

	var center = document.createElement('div');
	center.style.textAlign = 'center';
	center.appendChild(spinner);
	center.appendChild(document.createElement('br'));
	center.appendChild(rc_statusdiv);

	form.innerHTML = '';
	form.insertBefore(center, form.firstChild);

	deleteUsersStep();
	return(false);
}

function deleteUsersStep()
{
	if(_usersToDelete.length == 0)
	{
		document.location.href = _refreshURL;
		return;
	}

	var userID = _usersToDelete.pop();

	MakeXMLRequest('users.php?sid=' + currentSID
						+ '&singleAction=delete'
						+ '&singleID=' + encodeURIComponent(userID),
	function(http)
	{
		if(http.readyState == 4)
		{
			rc_statusdiv.innerHTML = (_usersStep++) + ' / ' + _usersMax + ' ...';
			deleteUsersStep();
		}
	});
}

function addEvent(elem, event, handler)
{
	if(document.addEventListener)
		elem.addEventListener(event, handler, false);
	else if(document.attachEvent)
		elem.attachEvent(event, handler);
}

function getElementMetrics(elem, m)
{
	var x, y, w, h;

	w = elem.offsetWidth;
	h = elem.offsetHeight;

	x = elem.offsetLeft;
	y = elem.offsetTop;

	while(elem = elem.offsetParent)
	{
		x += elem.offsetLeft;
		y += elem.offsetTop;
	}

	return(eval(m));
}

function openWindow(url, name, w, h, clean)
{
	var wa = (screen.width-w)/2;
	var l = 0;
	var ha = (screen.height-h)/2 - 60;
	var features;

	if(clean)
		features = 'scrollbars=no,scrolling=no,toolbar=no,statusbar=no,menubar=no,resizable=no,width='+w+',height='+h+',top=' + ha + ',left=' + wa;
	else
		features = 'scrollbars=yes,scrolling=yes,toolbar=no,statusbar=no,menubar=no,resizable=yes,width='+w+',height='+h+',top=' + ha + ',left=' + wa;

	var hwnd = window.open(url,name,features);
	return(hwnd);
}

function registerLoadAction(a)
{
	loadActions.push(a);
}

function documentLoader()
{
	for(var i=0; i<loadActions.length; i++)
		if(typeof(loadActions[i]) == 'string')
			eval(loadActions[i]);
		else
			loadActions[i]();
}

function EBID(f)
{
	return(document.getElementById(f));
}

function showHelp()
{
	var mainURL = document.location.href,
		lastPart = mainURL.substring(mainURL.lastIndexOf('/')+1),
		qmPos = lastPart.indexOf('?'),
		theFile = '',
		theAction = '',
		thePlugin = '';

	if(qmPos == -1)
	{
		theFile = lastPart;
		theAction = '';
	}
	else
	{
		theFile = lastPart.substring(0, qmPos);

		var params = lastPart.substring(qmPos+1).split('&');
		for(var i=0; i<params.length; i++)
		{
			var eqPos = params[i].indexOf('=');
			if(eqPos == -1)
				continue;
			if(params[i].substring(0, eqPos) == 'action')
			{
				theAction = params[i].substring(eqPos+1);
			}
			else if(theFile == 'plugin.page.php'
				&& params[i].substring(0, eqPos) == 'plugin')
			{
				thePlugin = params[i].substring(eqPos+1);
			}
		}
	}

	theFile = theFile.substring(0, theFile.lastIndexOf('.php'));

	window.open('https://service.b1gmail.org/help/jump.php?file=' + escape(theFile)
			+ (thePlugin != '' ? '&plugin=' + escape(thePlugin) : '')
			+ '&action=' + escape(theAction),
		'help',
		'dependent=yes,width=890,height=630,location=no,menubar=no,resizable=yes,scrollbars=no,status=yes');
}

function menuItem(id)
{
	var i = 0, obj;

 	for(i=0; i<6; i++)
	{
		if(EBID('menu_item_'+i))
			EBID('menu_item_'+i).className = i == id ? 'menuBarActive' : 'menuBar';
		if(EBID('menu_tbody_'+i))
			EBID('menu_tbody_'+i).style.display = i == id ? '' : 'none';
	}
}

function setTitle(t)
{
	parent.document.title = t;
}

function preloadImages()
{
	var load32 = new Image;
	load32.src = 'templates/images/load_32.gif';
}

function spin(frm)
{
	frm.style.display = 'none';

	var spinner = document.createElement('img');
	spinner.src = './templates/images/load_32.gif';
	spinner.style.padding = '20px';

	var center = document.createElement('div');
	center.style.textAlign = 'center';
	center.appendChild(spinner);

	frm.parentNode.insertBefore(center, frm);
}

function MakeXMLRequest(url, callback, param)
{
	var xmlHTTP = false;

	if(typeof(XMLHttpRequest) != "undefined")
	{
		xmlHTTP = new XMLHttpRequest();
	}
	else
	{
		try
		{
			xmlHTTP = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e)
		{
			try
			{
				xmlHTTP = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e)
			{
			}
		}
	}

	if(!xmlHTTP)
	{
		return(false);
	}
	else
	{
		xmlHTTP.open("GET", url, true);
		if(typeof(callback) == "string")
		{
			xmlHTTP.onreadystatechange = function xh_readyChange()
				{
					eval(callback + "(xmlHTTP)");
				}
		}
		else if(callback != null)
		{
			xmlHTTP.onreadystatechange = function xh_readyChangeCallback()
				{
					callback(xmlHTTP, param);
				}
		}
		xmlHTTP.send(null);
		return(true);
	}
}

var rc_rebuild = '', rc_perpage = 0, rc_all = -1, rc_fetched = 0, rc_id = '';

function _sendNewsletter(e)
{
	if(e.readyState == 4)
	{
		var text = e.responseText;

		if(text == 'DONE')
		{
			document.location.href = 'newsletter.php?sid=' + currentSID + '&do=done&id=' + escape(rc_id);
		}
		else
		{
			text = text.split('/');
			if(text.length==1)
			{
				rc_statusdiv.innerHTML = text[0] + ' ...';
			}
			else if(text.length==2)
			{
				rc_statusdiv.innerHTML = text[0] + ' / ' + text[1] + ' ...';
			}

			MakeXMLRequest('newsletter.php?sid=' + currentSID
								+ '&do=sendStep'
								+ '&id=' + escape(rc_id)
								+ '&perpage=' + escape(rc_perpage)
								+ '&pos=' + text[0],
							_sendNewsletter);
		}
	}
}

function tbxRelease2(versionID)
{
	document.location.href = 'toolbox.php?do=doRelease&versionid='+versionID+'&sid='+currentSID;
}

function tbxRelease(versionID, os)
{
	var winDone = false, macDone = false;

	EBID('releaseButton').style.display = 'none';
	EBID('releaseLoad').style.display = '';

	MakeXMLRequest('toolbox.php?sid=' + currentSID
					+ '&do=generateVersion'
					+ '&versionid=' + escape(versionID)
					+ '&os=win',
				function(e)
				{
					if(e.readyState == 4)
					{
						if(e.responseText == 'OK')
						{
							winDone = true;

							if(winDone && macDone)
								tbxRelease2(versionID);
						}
						else
						{
							var msg = 'Unknown error.';
							if(e.responseText.substring(0, 6) == 'ERROR:')
								msg = e.responseText.substring(6);
							EBID('releaseButton').style.display = '';
							EBID('releaseLoad').style.display = 'none';
							alert('Error: ' + msg);
						}
					}
				});
	MakeXMLRequest('toolbox.php?sid=' + currentSID
					+ '&do=generateVersion'
					+ '&versionid=' + escape(versionID)
					+ '&os=mac',
				function(e)
				{
					if(e.readyState == 4)
					{
						if(e.responseText == 'OK')
						{
							macDone = true;

							if(winDone && macDone)
								tbxRelease2(versionID);
						}
						else
						{
							var msg = 'Unknown error.';
							if(e.responseText.substring(0, 6) == 'ERROR:')
								msg = e.responseText.substring(6);
							EBID('releaseButton').style.display = '';
							EBID('releaseLoad').style.display = 'none';
							alert('Error: ' + msg);
						}
					}
				});
}

function tbxTest(versionID, os)
{
	EBID('testButton_'+os).style.display = 'none';
	EBID('testLink_'+os).style.display = 'none';
	EBID('testLoad_'+os).style.display = '';

	MakeXMLRequest('toolbox.php?sid=' + currentSID
					+ '&do=generateVersion'
					+ '&versionid=' + escape(versionID)
					+ '&os=' + escape(os),
				function(e)
				{
					if(e.readyState == 4)
					{
						if(e.responseText == 'OK')
						{
							EBID('testButton_'+os).style.display = 'none';
							EBID('testLink_'+os).style.display = '';
							EBID('testLoad_'+os).style.display = 'none';

							parent.location.href = 'toolbox.php?do=downloadVersion&versionid='+versionID+'&os='+os+'&sid='+currentSID;
						}
						else
						{
							var msg = 'Unknown error.';
							if(e.responseText.substring(0, 6) == 'ERROR:')
								msg = e.responseText.substring(6);

							EBID('testButton_'+os).style.display = '';
							EBID('testLink_'+os).style.display = 'none';
							EBID('testLoad_'+os).style.display = 'none';

							alert('Error: ' + msg);
						}
					}
				});
}

function sendNewsletter()
{
	rc_statusdiv = EBID('status');

	MakeXMLRequest('newsletter.php?sid=' + currentSID
						+ '&do=sendStep'
						+ '&id=' + escape(rc_id)
						+ '&perpage=' + escape(rc_perpage)
						+ '&pos=0',
					_sendNewsletter);
}

function _rebuildCaches(e)
{
	if(e.readyState == 4)
	{
		var text = e.responseText;

		if(text == 'DONE')
		{
			document.location.href = 'optimize.php?action=cache&sid=' + currentSID + '&do=done';
		}
		else
		{
			text = text.split('/');
			if(text.length==1)
			{
				rc_statusdiv.innerHTML = text[0] + ' ...';
			}
			else if(text.length==2)
			{
				rc_statusdiv.innerHTML = text[0] + ' / ' + text[1] + ' ...';
			}

			MakeXMLRequest('optimize.php?sid=' + currentSID
								+ '&action=cache'
								+ '&do=rebuild'
								+ '&rebuild=' + rc_rebuild
								+ '&perpage=' + escape(rc_perpage)
								+ '&pos=' + text[0],
							_rebuildCaches);
		}
	}
}

function rebuildCaches()
{
	var form = EBID('form');
	rc_rebuild = EBID('rebuild_usersizes').checked
					? 'usersizes'
					: EBID('rebuild_disksizes').checked
						? 'disksizes'
						: 'mailsizes';
	rc_perpage = EBID('perpage').value;

	rc_statusdiv = document.createElement('div');
	rc_statusdiv.style.textAlign = 'center';

	var spinner = document.createElement('img');
	spinner.src = './templates/images/load_32.gif';
	spinner.style.padding = '20px';

	var center = document.createElement('div');
	center.style.textAlign = 'center';
	center.appendChild(spinner);
	center.appendChild(document.createElement('br'));
	center.appendChild(rc_statusdiv);

	form.innerHTML = '';
	form.insertBefore(center, form.firstChild);

	MakeXMLRequest('optimize.php?sid=' + currentSID
						+ '&action=cache'
						+ '&do=rebuild'
						+ '&rebuild=' + rc_rebuild
						+ '&perpage=' + escape(rc_perpage)
						+ '&pos=0',
					_rebuildCaches);
}

function updateSort(field)
{
	if(EBID('sortBy').value != field)
		EBID('sortBy').value = field;
	else
		EBID('sortOrder').value = EBID('sortOrder').value == 'asc' ? 'desc' : 'asc';
	spin(document.forms.f1);
	document.forms.f1.submit();
}

function updatePage(no)
{
	EBID('page').value = no;
	spin(document.forms.f1);
	document.forms.f1.submit();
}

function singleAction(action, id)
{
	EBID('singleAction').value = action;
	EBID('singleID').value = id;
	spin(document.forms.f1);
	document.forms.f1.submit();
}

function invertSelection(form, prefix, to, toState)
{
	for(var i=0; i<form.elements.length; i++)
		if(form.elements[i].type == 'checkbox' && form.elements[i].name.substring(0, prefix.length) == prefix)
			form.elements[i].checked = to ? toState : !form.elements[i].checked;
}

function invertSelection2(form, prefix, suffix)
{
	for(var i=0; i<form.elements.length; i++)
	{
		if(form.elements[i].type == 'checkbox'
			&& form.elements[i].name.substring(0, prefix.length) == prefix
			&& form.elements[i].name.substring(form.elements[i].name.length-suffix.length) == suffix)
		{
			form.elements[i].checked = !form.elements[i].checked;
		}
	}
}

function executeAction(f)
{
	var url = EBID(f).value;
	if(url && url.length>1)
		if(url.substring(0, 6) == 'popup;')
			window.open(url.substring(6));
		else
			document.location.href = url;
}

function _fetchPOP3(e)
{
	if(e.readyState == 4)
	{
		var text = e.responseText;

		if(text == 'DONE')
		{
			document.location.href = 'maintenance.php?action=pop3gateway&sid=' + currentSID + '&do=done';
		}
		else
		{
			text = text.split('/');
			if(text.length==1)
			{
				rc_statusdiv.innerHTML = text[0] + ' ...';
			}
			else if(text.length==2)
			{
				if(rc_all == -1)
					rc_all = text[1];
				rc_fetched += parseInt(text[0]);
				rc_statusdiv.innerHTML = rc_fetched + ' / ' + rc_all + ' ...';
			}

			MakeXMLRequest('maintenance.php?sid=' + currentSID
								+ '&action=pop3gateway'
								+ '&do=fetch'
								+ '&perpage=' + escape(rc_perpage),
							_fetchPOP3);
		}
	}
}

function fetchPOP3()
{
	rc_all = -1;
	rc_fetched = 0;

	var form = EBID('form');
	rc_perpage = EBID('perpage').value;

	rc_statusdiv = document.createElement('div');
	rc_statusdiv.style.textAlign = 'center';

	var spinner = document.createElement('img');
	spinner.src = './templates/images/load_32.gif';
	spinner.style.padding = '20px';

	var center = document.createElement('div');
	center.style.textAlign = 'center';
	center.appendChild(spinner);
	center.appendChild(document.createElement('br'));
	center.appendChild(rc_statusdiv);

	form.innerHTML = '';
	form.insertBefore(center, form.firstChild);

	MakeXMLRequest('maintenance.php?sid=' + currentSID
						+ '&action=pop3gateway'
						+ '&do=fetch'
						+ '&perpage=' + escape(rc_perpage),
					_fetchPOP3);
}

function rebuildBlobStor()
{
	var all = -1, fetched = 0, form = EBID('buildForm'), perpage = EBID('buildPerPage').value,
		rebuild = EBID('rebuild_email').checked ? 'email' : 'webdisk';

	var statusdiv = document.createElement('div');
	statusdiv.style.textAlign = 'center';

	var spinner = document.createElement('img');
	spinner.src = './templates/images/load_32.gif';
	spinner.style.padding = '20px';

	var center = document.createElement('div');
	center.style.textAlign = 'center';
	center.appendChild(spinner);
	center.appendChild(document.createElement('br'));
	center.appendChild(statusdiv);

	form.innerHTML = '';
	form.insertBefore(center, form.firstChild);

	var _rebuildBlobStor = function(e)
	{
		if(e.readyState == 4)
		{
			var text = e.responseText;

			if(text == 'DONE')
			{
				document.location.href = 'optimize.php?action=filesystem&sid=' + currentSID;
			}
			else
			{
				text = text.split('/');
				if(text.length==1)
				{
					statusdiv.innerHTML = text[0] + ' ...';
				}
				else if(text.length==2)
				{
					if(all == -1)
						all = text[1];
					fetched += parseInt(text[0]);
					statusdiv.innerHTML = fetched + ' / ' + all + ' ...';
				}

				MakeXMLRequest('optimize.php?sid=' + currentSID
									+ '&action=filesystem'
									+ '&do=rebuildBlobStor'
									+ '&rebuild=' + rebuild
									+ '&perpage=' + escape(perpage)
									+ '&all=' + escape(all),
								_rebuildBlobStor);
			}
		}
	};

	MakeXMLRequest('optimize.php?sid=' + currentSID
						+ '&action=filesystem'
						+ '&do=rebuildBlobStor'
						+ '&rebuild=' + rebuild
						+ '&perpage=' + escape(perpage),
					_rebuildBlobStor);
}

function vacuumBlobStor()
{
	var all = -1, fetched = 0, form = EBID('vacuumForm'), perpage = EBID('vacuumPerPage').value;

	var statusdiv = document.createElement('div');
	statusdiv.style.textAlign = 'center';

	var spinner = document.createElement('img');
	spinner.src = './templates/images/load_32.gif';
	spinner.style.padding = '20px';

	var center = document.createElement('div');
	center.style.textAlign = 'center';
	center.appendChild(spinner);
	center.appendChild(document.createElement('br'));
	center.appendChild(statusdiv);

	form.innerHTML = '';
	form.insertBefore(center, form.firstChild);

	var _vacuumBlobStor = function(e)
	{
		if(e.readyState == 4)
		{
			var text = e.responseText;

			if(text == 'DONE')
			{
				document.location.href = 'optimize.php?action=filesystem&sid=' + currentSID;
			}
			else
			{
				text = text.split('/');
				if(text.length==1)
				{
					statusdiv.innerHTML = text[0] + ' ...';
				}
				else if(text.length==2)
				{
					if(all == -1)
						all = text[1];
					pos = parseInt(text[0]);
					statusdiv.innerHTML = pos + ' / ' + all + ' ...';
				}

				MakeXMLRequest('optimize.php?sid=' + currentSID
									+ '&action=filesystem'
									+ '&do=vacuumBlobStor'
									+ '&pos=' + escape(pos)
									+ '&perpage=' + escape(perpage)
									+ '&all=' + escape(all),
								_vacuumBlobStor);
			}
		}
	};

	MakeXMLRequest('optimize.php?sid=' + currentSID
						+ '&action=filesystem'
						+ '&do=vacuumBlobStor'
						+ '&pos=0'
						+ '&perpage=' + escape(perpage),
					_vacuumBlobStor);
}

function buildIndex()
{
	var all = -1, fetched = 0, form = EBID('buildForm'), perpage = EBID('buildPerPage').value;

	var statusdiv = document.createElement('div');
	statusdiv.style.textAlign = 'center';

	var spinner = document.createElement('img');
	spinner.src = './templates/images/load_32.gif';
	spinner.style.padding = '20px';

	var center = document.createElement('div');
	center.style.textAlign = 'center';
	center.appendChild(spinner);
	center.appendChild(document.createElement('br'));
	center.appendChild(statusdiv);

	form.innerHTML = '';
	form.insertBefore(center, form.firstChild);

	var _buildIndex = function(e)
	{
		if(e.readyState == 4)
		{
			var text = e.responseText;

			if(text == 'DONE')
			{
				document.location.href = 'maintenance.php?action=fts&sid=' + currentSID;
			}
			else
			{
				text = text.split('/');
				if(text.length==1)
				{
					statusdiv.innerHTML = text[0] + ' ...';
				}
				else if(text.length==2)
				{
					if(all == -1)
						all = text[1];
					fetched += parseInt(text[0]);
					statusdiv.innerHTML = fetched + ' / ' + all + ' ...';
				}

				MakeXMLRequest('maintenance.php?sid=' + currentSID
									+ '&action=fts'
									+ '&do=buildIndex'
									+ '&perpage=' + escape(perpage)
									+ '&all=' + escape(all),
								_buildIndex);
			}
		}
	};

	MakeXMLRequest('maintenance.php?sid=' + currentSID
						+ '&action=fts'
						+ '&do=buildIndex'
						+ '&perpage=' + escape(perpage),
					_buildIndex);
}

function optimizeIndex()
{
	var all = -1, pos = 0, form = EBID('optimizeForm'), perpage = EBID('optimizePerPage').value;

	var statusdiv = document.createElement('div');
	statusdiv.style.textAlign = 'center';

	var spinner = document.createElement('img');
	spinner.src = './templates/images/load_32.gif';
	spinner.style.padding = '20px';

	var center = document.createElement('div');
	center.style.textAlign = 'center';
	center.appendChild(spinner);
	center.appendChild(document.createElement('br'));
	center.appendChild(statusdiv);

	form.innerHTML = '';
	form.insertBefore(center, form.firstChild);

	var _buildIndex = function(e)
	{
		if(e.readyState == 4)
		{
			var text = e.responseText;

			if(text == 'DONE')
			{
				document.location.href = 'maintenance.php?action=fts&sid=' + currentSID;
			}
			else
			{
				text = text.split('/');
				if(text.length==1)
				{
					statusdiv.innerHTML = text[0] + ' ...';
				}
				else if(text.length==2)
				{
					if(all == -1)
						all = text[1];
					pos = parseInt(text[0]);
					statusdiv.innerHTML = pos + ' / ' + all + ' ...';
				}

				MakeXMLRequest('maintenance.php?sid=' + currentSID
									+ '&action=fts'
									+ '&do=optimizeIndex'
									+ '&pos=' + escape(pos)
									+ '&perpage=' + escape(perpage)
									+ '&all=' + escape(all),
								_buildIndex);
			}
		}
	};

	MakeXMLRequest('maintenance.php?sid=' + currentSID
						+ '&action=fts'
						+ '&do=optimizeIndex'
						+ '&pos=' + escape(pos)
						+ '&perpage=' + escape(perpage),
					_buildIndex);
}

var _trashExecParams, _trashExecLastPos = 0;

function _trashExec(e)
{
	if(e.readyState == 4)
	{
		var text = e.responseText;

		if(text == 'DONE')
		{
			document.location.href = 'maintenance.php?action=trash&sid=' + currentSID;
		}
		else
		{
			text = text.split('/');
			if(text.length==1)
			{
				rc_statusdiv.innerHTML = text[0] + ' ...';
			}
			else if(text.length==2)
			{
				rc_statusdiv.innerHTML = text[0] + ' / ' + text[1] + ' ...';
			}

			if(isNaN(text[0]) || parseInt(text[0]) == 0)
			{
				text[0] = parseInt(_trashExecLastPos) + parseInt(rc_perpage);
			}

			_trashExecLastPos = text[0];

			MakeXMLRequest('maintenance.php?sid=' + currentSID
								+ '&action=trash'
								+ '&do=exec'
								+ _trashExecParams
								+ '&perpage=' + escape(rc_perpage)
								+ '&pos=' + text[0],
							_trashExec);
		}
	}
}

function trashExec()
{
	_trashExecParams = '&days='+encodeURIComponent(EBID('days').value)
					+ '&size='+encodeURIComponent(EBID('size').value)
					+ (EBID('daysOnly').checked ? '&daysOnly=true' : '')
					+ (EBID('sizesOnly').checked ? '&daysOnly=true' : '');
	var inputs = document.getElementsByTagName('input');
	for(var i=0; i<inputs.length; i++)
	{
		if(inputs[i].getAttribute('type') != 'checkbox')
			continue;

		var name = inputs[i].getAttribute('name');
		if(name.substr(0, 7) != 'groups[')
			continue;

		if(inputs[i].checked)
			_trashExecParams += '&' + name + '=true';
	}

	var form = EBID('form');
	rc_perpage = EBID('perpage').value;

	rc_statusdiv = document.createElement('div');
	rc_statusdiv.style.textAlign = 'center';

	var spinner = document.createElement('img');
	spinner.src = './templates/images/load_32.gif';
	spinner.style.padding = '20px';

	var center = document.createElement('div');
	center.style.textAlign = 'center';
	center.appendChild(spinner);
	center.appendChild(document.createElement('br'));
	center.appendChild(rc_statusdiv);

	form.innerHTML = '';
	form.insertBefore(center, form.firstChild);

	MakeXMLRequest('maintenance.php?sid=' + currentSID
						+ '&action=trash'
						+ '&do=exec'
						+ _trashExecParams
						+ '&perpage=' + escape(rc_perpage)
						+ '&pos=0',
					_trashExec);
}

function generateCodes(f)
{
	var randChars = '',
		randCount = parseInt(EBID('generator_count').value),
		randLength = parseInt(EBID('generator_length').value);

	if(EBID('generator_az').checked)
		randChars += 'abcdefghijklmnopqrstuvwxyz';
	if(EBID('generator_az2').checked)
		randChars += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	if(EBID('generator_09').checked)
		randChars += '0123456789';
	if(EBID('generator_special').checked)
		randChars += '.,_-&$';

	if(randChars != '' && randCount > 0 && randLength > 0)
	{
		var strs = '';
		for(var i=0; i<randCount; i++)
		{
			var str = '';
			for(var j=0; j<randLength; j++)
			{
				var rand = Math.round(Math.random()*(randChars.length-1));
				str += randChars.substr(rand, 1);
			}
			strs += str + "\n";
		}
		f.value = strs;
	}
}

function cachePrefs()
{
	var cacheDisable = EBID('cache_disable'),
		cacheb1gMail = EBID('cache_b1gmail'),
		cachememcache = EBID('cache_memcache');
	var prefs0 = EBID('prefs_0'),
		prefs1 = EBID('prefs_1'),
		prefs2 = EBID('prefs_2'),
		prefs3 = EBID('prefs_3');

	prefs0.style.display = prefs1.style.display = prefs2.style.display = prefs3.style.display = 'none';

	if(cacheDisable.checked)
		prefs0.style.display = '';
	else if(cacheb1gMail.checked)
		prefs1.style.display = '';
	else if(cachememcache.checked)
		prefs2.style.display = '';

	if(!cacheDisable.checked)
		prefs3.style.display = '';
}

function _determineNewsletterRecipients(e)
{
	if(e.readyState == 4)
	{
		var count = parseInt(e.responseText);
		EBID('recpCount').innerHTML = count;
		EBID('submitButton').disabled = count < 1;
		EBID('exportButton').disabled = count < 1;
	}
}

function determineNewsletterRecipients()
{
	var form = EBID('newsletterForm'),
		sendto = EBID('sendto_altmails').checked ? 'altmails' : 'mailboxes',
		groups = '',
		status = '';

	// groups
	for(var i=0; i<form.elements.length; i++)
		if(form.elements[i].type == 'checkbox' && form.elements[i].name.substring(0, 6) == 'group_'
			&& form.elements[i].checked)
			groups += ',' + form.elements[i].name.substring(6);
	if(groups.length > 0)
		groups = groups.substring(1);

	// status params
	if(EBID('statusActive').checked)
		status += '&statusActive=true';
	if(EBID('statusLocked').checked)
		status += '&statusLocked=true';
	if(EBID('statusNotActivated').checked)
		status += '&statusNotActivated=true';

	// countries
	var countries = '', countryInputs = EBID('countrySelectBox').getElementsByTagName('input');
	for(var i=0; i<countryInputs.length; i++)
		if(countryInputs[i].name == 'countries[]' && countryInputs[i].checked)
			countries += '&countries[]=' + countryInputs[i].value;

	// request
	MakeXMLRequest('newsletter.php?sid=' + currentSID
						+ '&do=determineRecipients'
						+ '&groups=' + escape(groups)
						+ '&sendto=' + escape(sendto)
						+ countries
						+ status,
					_determineNewsletterRecipients);
}

function loadNewsletterTemplate(box)
{
	var tplID = box.options[box.selectedIndex].value;

	if(tplID == 0)
		return;

	if(!confirm(lang['nwslttrtplwarn']))
		return;

	// request
	MakeXMLRequest('newsletter.php?sid=' + currentSID
						+ '&do=getTemplateData'
						+ '&templateID=' + escape(tplID),
					function(http)
					{
						if(http.readyState == 4)
						{
							var data = eval('(' + http.responseText + ')');
							EBID('from').value 		= data.from;
							EBID('subject').value 	= data.subject;
							EBID('priority').value 	= data.priority;
							if(data.mode == 'html')
							{
								editor.switchMode('html', true);
								EBID('mode_html').checked = true;
								EBID('mode_text').checked = false;
							}
							else
							{
								editor.switchMode('text', true);
								EBID('mode_html').checked = false;
								EBID('mode_text').checked = true;
							}
							editor.setText(data.body, data.mode=='html');
						}
					});
}

function insertNoticeRow(image, text, link, target)
{
	var noticeTable = EBID('noticeTable'),
		noticeRow = document.createElement('tr'),
		noticeTD1 = document.createElement('td'),
		noticeTD2 = document.createElement('td'),
		noticeTD3 = document.createElement('td'),
		noticeImgPL = document.createElement('img'),
		noticeImgLink = document.createElement('img'),
		noticeLink = document.createElement('a');

	noticeImgPL.setAttribute('align', 'absmiddle');
	noticeImgPL.setAttribute('border', '0');
	noticeImgPL.setAttribute('src', 'templates/images/' + image);

	if(link)
	{
		noticeImgLink.setAttribute('align', 'absmiddle');
		noticeImgLink.setAttribute('border', '0');
		noticeImgLink.setAttribute('src', 'templates/images/go.png');
		noticeLink.setAttribute('href', link);
		if(target)
			noticeLink.setAttribute('target', target);
		noticeLink.appendChild(noticeImgLink);
	}

	// prepare row
	noticeTD1.setAttribute('width', '20');
	noticeTD1.setAttribute('valign', 'top');
	noticeTD1.appendChild(noticeImgPL);
	noticeTD2.setAttribute('valign', 'top');
	noticeTD2.appendChild(document.createTextNode(text));
	noticeTD3.setAttribute('valign', 'top');
	noticeTD3.setAttribute('align', 'right');
	noticeTD3.appendChild(noticeLink);

	// add row
	noticeRow.appendChild(noticeTD1);
	noticeRow.appendChild(noticeTD2);
	noticeRow.appendChild(noticeTD3);
	noticeTable.appendChild(document.createElement('tbody')).appendChild(noticeRow);
}

function togglePluginPackage(id)
{
	var packItem = EBID('package_' + id);
	var packItemImg = EBID('packageImage_' + id);

	if(packItem.style.display == '')
	{
		packItem.style.display = 'none';
		packItemImg.src = packItemImg.src.replace(/contract/, 'expand');
	}
	else
	{
		packItem.style.display = '';
		packItemImg.src = packItemImg.src.replace(/expand/, 'contract');
	}
}

function _checkPluginSignature(obj)
{
	if(obj.readyState == 4)
		EBID('sigLayer').innerHTML = obj.responseText;
}

function checkPluginSignature(signature)
{
	MakeXMLRequest('plugins.php?sid=' + currentSID
						+ '&action=install'
						+ '&do=checkSignature'
						+ '&signature=' + signature,
					_checkPluginSignature);
}

function _checkForPluginUpdates(obj)
{
	if(obj.readyState == 4)
	{
		var text = obj.responseText;
		text = text.split(';');

		if(text.length == 4)
		{
			var layerName = 'updates_' + text[0],
				resultCode = parseInt(text[1]);

			if(resultCode == 0)
			{
				EBID(layerName).innerHTML = '<font color="#666666">(' + lang['unknown'] + ')</font>';
			}
			else if(resultCode == 1)
			{
				EBID(layerName).innerHTML = '<img src="templates/images/ok.png" border="0" alt="" width="16" height="16" align="absmiddle" /> '
											+ lang['version'] + ': ' + text[2];
			}
			else if(resultCode == 2)
			{
				EBID(layerName).innerHTML = '<img src="templates/images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" /> '
											+ '<a href="' + text[3] + '" target="_blank">' + lang['version'] + ': ' + text[2] + '</a>';
			}
		}
	}
}

function checkForPluginUpdates(internalName)
{
	MakeXMLRequest('plugins.php?sid=' + currentSID
						+ '&action=updateCheck'
						+ '&plugin=' + internalName,
					_checkForPluginUpdates);
}

function dashboardOrderChanged()
{
	EBID('order').value = this.order;
}

function handleActivatePaymentInput(event, i)
{
	if(event.keyCode == 10 || event.keyCode == 13)
	{
		if(i == 0)
		{
			EBID('amount').focus();
		}
		else
		{
			activatePayment();
		}
		return(false);
	}

	return(true);
}

function activatePayment()
{
	if(EBID('activateButton').disabled)
		return;

	var vkCode = EBID('vkCode').value, amount = EBID('amount').value;

	EBID('activationResult').innerHTML = '';
	EBID('activateButton').disabled = true;

	MakeXMLRequest('payments.php?action=activatePayment&sid=' + currentSID + '&vkCode=' + escape(vkCode) + '&amount=' + escape(amount),
				   _activatePayment)
}

function _activatePayment(e)
{
	if(e.readyState == 4)
	{
		var text = e.responseText;

		if(text.substring(0, 3) == 'OK:')
		{
			EBID('activationResult').style.color = 'darkgreen';
			EBID('activationResult').innerHTML = text.substring(3);
			EBID('vkCode').value = 'VK-';
			EBID('amount').value = '';
		}
		else
		{
			EBID('activationResult').style.color = 'red';
			EBID('activationResult').innerHTML = text.substring(6);
		}

		EBID('vkCode').focus();
		EBID('activateButton').disabled = false;
	}
}
