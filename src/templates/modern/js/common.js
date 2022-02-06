/*
 * b1gMail client library
 * (c) 2021 Patrick Schlangen et al
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

var loadActions = [];
var clientTZ = (new Date()).getTimezoneOffset() * (-60);

function isChildOf(child, parent)
{
	var node = child;
	while(node)
	{
		if(node == parent)
			return true;
		node = node.parentNode;
	}
	return false;
}

function updatePaymentMethod(field)
{
	var paymentMethodID = field.value;
	var tbodies = document.getElementsByTagName('tbody');

	for(var i=0; i<tbodies.length; i++)
	{
		if(tbodies[i].id.length > 14 && tbodies[i].id.substr(0, 14) == 'paymentMethod_')
		{
			var id = tbodies[i].id.substr(14);
			if(-id == paymentMethodID)
				tbodies[i].style.display = '';
			else
				tbodies[i].style.display = 'none';
		}
	}
}

function formatNumber(num, decimals)
{
	return num.toFixed(decimals).replace('.', lang['decsep']);
}

function updatePaymentCountry(field)
{
	var countryID = field.value;
	var amount = 0, tax = 0, taxRate = 0, showTaxNote = false, taxNote = '';

	if(typeof(bmPayment.vatRates[countryID]) != 'undefined')
	{
		taxRate = bmPayment.vatRates[countryID];
		tax = bmPayment.baseAmount * (taxRate / 100);

		if(bmPayment.vatMode == 'enthalten')
		{
			amount = bmPayment.baseAmount;
			showTaxNote = true;
		}
		else if(bmPayment.vatMode == 'add')
		{
			amount = bmPayment.baseAmount + tax;
			showTaxNote = true;
		}
		else if(bmPayment.vatMode == 'nomwst')
		{
			amount = bmPayment.baseAmount;
			showTaxNote = false;
		}
	}
	else
	{
		amount = bmPayment.baseAmount;
		showTaxNote = false;
	}

	if(showTaxNote)
		taxNote = lang['taxnote'].replace('%1', formatNumber(taxRate, 2));

	EBID('paymentAmount').innerHTML = formatNumber(amount, 2) + ' ' + bmPayment.currency;
	EBID('taxNote').innerHTML = taxNote;
}

function getCookie(name)
{
	var cookies = document.cookie.split(';');

	for(var i=0; i<cookies.length; i++)
	{
		var eqPos = cookies[i].indexOf('=');

		if(eqPos > 0)
		{
			key = trim(cookies[i].substr(0, eqPos));
			value = trim(unescape(cookies[i].substr(eqPos+1)));
		}
		else
		{
			key = trim(cookies[i]);
			value = '';
		}

		if(key == name)
			return(value);
	}

	return('');
}

function setCookie(name, value)
{
	var expires = new Date();
	expires.setTime(expires.getTime() + 31536000000);
	document.cookie = name + '=' + escape(value) + ';expires=' + expires.toUTCString();
}

function registerLoadAction(a)
{
	loadActions.push(a);
}

function windowResize()
{
}

function addEvent(elem, event, handler)
{
	if(document.addEventListener)
		elem.addEventListener(event, handler, false);
	else if(document.attachEvent)
		elem.attachEvent('on'+event, handler);
}

function removeEvent(elem, event, handler)
{
	if(document.removeEventListener)
		elem.removeEventListener(event, handler);
	else if(document.detachEvent)
		elem.detachEvent('on'+event, handler);
}

function documentLoader()
{
	if(EBID('mainBanner') && EBID('mainContent') && trim(EBID('mainBanner').innerHTML).length > 1)
	{
		EBID('mainBanner').style.display = 'block';
		EBID('mainContent').className = 'withBanner';
	}

	for(var i=0; i<loadActions.length; i++)
		if(typeof(loadActions[i]) == 'string')
			eval(loadActions[i]);
		else
			loadActions[i]();

	addEvent(window, 'resize', windowResize);
	addEvent(window, 'dragover', function(event) { event.preventDefault(); });
	addEvent(window, 'drop', function(event) { event.preventDefault(); });

	if(Math.random() > 0.5)
	{
		if(typeof(currentSID) != 'undefined')
			MakeXMLRequest('cron.php?sid='+currentSID, null);
		else
			MakeXMLRequest('cron.php');
	}

	ftsBGIndex();
	notifyPollInstall();
}

function ftsBGIndex()
{
	if(typeof(ftsBGIndexing) != 'undefined' && typeof(currentSID) != 'undefined' && ftsBGIndexing)
	{
		MakeXMLRequest('search.php?action=ftsBGIndexing&sid='+currentSID, function(e)
			{
				if(e.readyState == 4)
				{
					if(e.responseText == '0')
						window.setTimeout(ftsBGIndex, 1000);
					else if(e.responseText == '2')
						window.setTimeout(ftsBGIndex, 15000);
				}
			});
	}
}

function notifyPollInstall()
{
	if(typeof(notifyInterval) != 'undefined' && typeof(currentSID) != 'undefined' && notifyInterval > 0)
		window.setTimeout(notifyPoll, notifyInterval*1000);
}

function notifyPoll()
{
	MakeXMLRequest('start.php?action=getNotificationCount&sid='+currentSID, function(e)
		{
			if(e.readyState == 4)
			{
				var count = parseInt(e.responseText);
				setNotificationCount(count);
				notifyPollInstall();
			}
		});
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

function getDocumentMetrics(m)
{
	var scrollX, scrollY, rScrollY, pageW, pageH, windowW, windowH;
	var docElement = document.documentElement && document.documentElement.clientHeight
						? document.documentElement
						: document.body;

	if(self.pageYOffset)
		rScrollY = self.pageYOffset;
	else
		rScrollY = docElement.scrollTop;

	if(window.innerHeight && window.scrollMaxY)
	{
		scrollX = document.body.scrollWidth;
		scrollY = window.innerHeight + window.scrollMaxY;
	}
	else if(document.body.scrollHeight > document.body.offsetHeight)
	{
		scrollX = document.body.scrollWidth;
		scrollY = document.body.scrollHeight;
	}
	else
	{
		scrollX = document.body.offsetWidth;
		scrollY = document.body.offsetHeight;
	}

	if(self.innerWidth)
	{
		windowW = self.innerWidth;
		windowH = self.innerHeight;
	}
	else
	{
		windowW = docElement.clientWidth;
		windowH = docElement.clientHeight;
	}

	pageW = scrollX < windowW ? windowW : scrollX;
	pageH = scrollY < windowH ? windowH : scrollY;

	return(eval(m));
}

function checkAll(check, form, m)
{
	for(var i=0; form.elements[i]; i++)
	{
		if(form.elements[i].type == 'checkbox' && form.elements[i].id != 'allChecker'
			&& (!m || (form.elements[i].id.substr(0, m.length) == m))
			&& !form.elements[i].disabled)
			form.elements[i].checked = check;
	}
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

function NiceSize(s)
{
	if(s < 1024)
	{
		return(s + " Byte");
	}
	else if(s < 1024*1024)
	{
		return(Math.round(s/1024, 2) + " KB");
	}
	else if(s < 1024*1024*1024)
	{
		return(Math.round(s/1024/1024, 2) + " MB");
	}
	else
	{
		return(Math.round(s/1024/1024/1024, 2) +  " GB");
	}
}

function Shorten(s, l)
{
	if(s.length > l-3)
		s = s.substr(0, l-3) + '...';
	return(s);
}

function NL2BR(s)
{
	s = s.replace(/\r/g, "");
	s = s.replace(/\n/g, "<br />");
	return(s);
}

function stripTags(s)
{
	s = s.replace(/<\/?[^>]+>/gi, "");
	return(s);
}

function EBID(k)
{
	return(document.getElementById(k));
}

function DateFormat(format, d)
{
	var d = new Date(d * 1000);
	var res = "";

	for(var i=0; i<format.length; i++)
	{
		var c = format.substr(i, 1);
		if(c == 'd')
		{
			if(d.getDate() < 10)
				res += "0" + d.getDate();
			else
				res += d.getDate();
		}
		else if(c == 'm')
		{
			if(d.getMonth()+1 < 10)
				res += "0" + (d.getMonth()+1);
			else
				res += d.getMonth()+1;
		}
		else if(c == 'Y')
		{
			res += d.getFullYear();
		}
		else if(c == 'H')
		{
			if(d.getHours() < 10)
				res += "0" + d.getHours();
			else
				res += d.getHours();
		}
		else if(c == 'i')
		{
			if(d.getMinutes() < 10)
				res += "0" + d.getMinutes();
			else
				res += d.getMinutes();
		}
		else if(c == 's')
		{
			if(d.getSeconds() < 10)
				res += "0" + d.getSeconds();
			else
				res += d.getSeconds();
		}
		else
		{
			res += c;
		}
	}

	return(res);
}

function prepareMailText(s)
{
	s = HTMLEntities(s);
	s = s.replace(/\t/g, "&nbsp;&nbsp;&nbsp;&nbsp;");
	s = s.replace(/  /g, "&nbsp;&nbsp;");
	s = s.replace(/\&amp;quot\;/g, "&quot;");
	s = NL2BR(s);
	return(s);
}

function prepareMailTextHTML(s, secure)
{
	if(secure)
	{
		s = s.replace(/<script/gi, "<DISABLED-script");
		s = s.replace(/src=\"http([s]+)\:\/\//gi, "src=\"http://block-images/");
	}
	s = s.replace(/href=([\"\']{0,1})http/gi, "target=\"_blank\" href=$1http");
	s = s.replace(/mailto\:([^\n\r\\\"\'\> ]+)/gi, "javascript:parent.composeMail('$1');");
	return(s);
}

function HTMLEntities(s)
{
	s = s.replace(/\</g, "&lt;");
	s = s.replace(/\>/g, "&gt;");
	s = s.replace(/\"/g, "&quot;");
	return(s);
}

function FormatDate(d)
{
	var result = "";
	var now = new Date();

	if(d.getDay() == now.getDay()
		&& d.getMonth() == now.getMonth()
		&& d.getYear() == now.getYear())
	{
		// heute => uhrzeit
		return(DateFormat("H:i:s", d.getTime()/1000));
	}
	else
	{
		// spaeter => datum
		return(DateFormat("d.m.Y", d.getTime()/1000));
	}
}

function min(a, b)
{
	return(a < b ? a : b);
}

function max(a, b)
{
	return(a > b ? a : b);
}

function GetXMLHTTP()
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

	return(xmlHTTP);
}

function MakeXMLRequest(url, callback, param, cClose)
{
	var xmlHTTP = GetXMLHTTP();

	if(!xmlHTTP)
	{
		return(false);
	}
	else
	{
		xmlHTTP.open("GET", url, true);
		if(cClose)
			xmlHTTP.setRequestHeader("Connection", "close");
		if(typeof(callback) == "string")
		{
			xmlHTTP.onreadystatechange = function xh_readyChange()
				{
					eval(callback + "(xmlHTTP)");
				}
		}
		else if(callback != null && callback != false)
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

function isNumeric(str)
{
	var charset = "0123456789";
	for(var i=0; i<str.length; i++)
		if(charset.indexOf(str.substr(i, 1), 0) == -1)
			return(false);
	return(true);
}

function isAlphabetic(str)
{
	var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	for(var i=0; i<str.length; i++)
		if(charset.indexOf(str.substr(i, 1), 0) == -1)
			return(false);
	return(true);
}

function passwordSecurity(pw, div)
{
	var value = 0;
	var pwLength = pw.length;
	var differentChars = 0;
	var numbers = 0;
	var alpha = 0;
	var others = 0;

	for(var i=0; i<pw.length; i++)
	{
		var c = pw.substr(i, 1);

		if(isNumeric(c))
			numbers++;
		else if(isAlphabetic(c))
			alpha++;
		else
			others++;

		var unique = true;

		for(var j=i; j<pw.length; j++)
		{
			var d = pw.substr(j, 1);
			if((d == c) && (j != i))
				unique = false;
		}

		if(unique)
			differentChars++;
	}

	pwLength = differentChars;
	value  = (pwLength / 8) * 100;
	if(numbers == pwLength)
		value *= 0.5;
	value += others * 18;

	if(pwLength < 4)
		value = 0;

	if(value > 100)
		value = 100;
	if(value < 0)
		value = 0;

	EBID(div).style.width = value + '%';
}

function trim(str)
{
	return(str.replace(/\s+$/,"").replace(/^\s+/,""));
}

function markFieldAsInvalid(f)
{
	var field = false;

	if(EBID(f))
	{
		field = EBID(f);
	}
	else
	{
		var inputs = document.getElementsByName(f);
		if(inputs.length == 1)
			field = inputs[0];
	}

	if(field)
		field.className = 'invalidField';
}

function advancedOptions(field, dir1, dir2, tpldir)
{
	var body = EBID('advanced_' + field + '_body'), body2 = EBID('advanced_' + field + '_body2');
	var arrow = EBID('advanced_' + field + '_arrow');

	if(body.style.display=='')
	{
		body.style.display = 'none';
		arrow.src = tpldir + 'images/li/mini_arrow_' + dir1 + '.png';

		if(body2)
			body2.style.display = '';
	}
	else
	{
		body.style.display = '';
		arrow.src = tpldir + 'images/li/mini_arrow_' + dir2 + '.png';

		if(body2)
			body2.style.display = 'none';
	}
}

function _checkAddressAvailability(xmlHTTP)
{
	if(xmlHTTP.readyState == 4)
	{
		var xml = xmlHTTP.responseXML;
		var tag = xml.getElementsByTagName('available');

		if(tag.length > 0
			&& tag.item(0).childNodes.length > 0)
		{
			var available = tag.item(0).childNodes.item(0).data;
			if(available == 1)
			{
				EBID('addressAvailabilityIndicator').innerHTML = '<i class="fa fa-check" style="color:green;"></i> ' + lang['addravailable'];
			}
			else if(available == 2)
			{
				EBID('addressAvailabilityIndicator').innerHTML = '<i class="fa fa-exclamation-triangle" style="color:red;"></i> ' + lang['addrinvalid'];
			}
			else
			{
				EBID('addressAvailabilityIndicator').innerHTML = '<i class="fa fa-remove" style="color:red;"></i> ' + lang['addrtaken'];
			}
		}
		else
		{
			EBID('addressAvailabilityIndicator').innerHTML = '';
		}
	}
}

function checkAddressAvailability()
{
	if(EBID('email_local').value.length < 1)
	{
		EBID('addressAvailabilityIndicator').innerHTML = '';
		return;
	}

	var address = EBID('email_local').value + '@' + EBID('email_domain').value;
	EBID('addressAvailabilityIndicator').innerHTML = '<i class="fa fa-spinner fa-pulse fa-fw"></i>';
	MakeXMLRequest('index.php?action=checkAddressAvailability&address=' + encodeURI(address), _checkAddressAvailability);
}

function getTZOffset()
{
	if(!serverTZ)
		return(clientTZ);
	else
		return(clientTZ - serverTZ);
}

function getPageXOffset()
{
	if(window.pageXOffset)
		return(window.pageXOffset);
	else if(document.documentElement && document.documentElement.scrollLeft)
		return(document.documentElement.scrollLeft);
	return(0);
}
function getPageYOffset()
{
	if(window.pageYOffset)
		return(window.pageYOffset);
	else if(document.documentElement && document.documentElement.scrollTop)
		return(document.documentElement.scrollTop);
	return(0);
}
