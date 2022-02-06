<!--
/*
 * b1gMail utf8 converter client scripts
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

var steps = [
	'prepare',
	'analyzedb',
	'preptables',
	'convert',
	'collations',
	'langfiles',
	'resetcache',
	'complete'
];
var step = -1,
	args = '',
	pos = 0,
	allQ = -1;

function EBID(f)
{
	return(document.getElementById(f));
}

function Log(txt)
{
	var log = EBID('log');

	if(log.style.display == 'none')
		log.style.display = '';

	log.value = txt + "\n" + log.value;
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

function _stepStep(e)
{
	if(e.readyState == 4)
	{
		var response = e.responseText;

		if(response.substr(0, 3) == 'OK:')
		{
			response = response.substr(3);

			if(response == 'DONE')
			{
				stepInit(step+1);
			}
			else
			{
				var numbers = response.split('/');
				if(numbers.length == 2)
				{
					if(steps[step] == 'struct2' && allQ == -1)
						allQ = parseInt(numbers[1]);

					if(steps[step] == 'struct2')
						numbers[1] = '' + allQ;

					pos = parseInt(numbers[0]);
					EBID('step_' + steps[step] + '_progress').innerHTML = '<b>' + Math.round(pos / parseInt(numbers[1]) * 100) + '%</b> <small>('
						+ pos + ' / ' + parseInt(numbers[1]) + ')</small>';
					stepStep();
				}
				else
				{
					Log('Unexpected response - skipping position ' + pos);
					pos++;
					stepStep();
				}
			}
		}
		else
		{
			Log('Unexpected response - skipping position ' + pos);
			pos++;
			stepStep();
		}
	}
	else if(e.readyState < 0 || e.readyState > 4)
	{
		Log('Error in HTTP-Request: ' + e.readyState + ' - Trying again in 10s');
		window.setTimeout('stepStep()', 10000);
	}
}

function stepStep()
{
	MakeXMLRequest('utf8convert.php?' + args + '&step=4&do=' + steps[step] + '&pos=' + pos,
					_stepStep);
}

function stepInit(theStep)
{
	if(step != -1)
	{
		EBID('step_' + steps[step] + '_status').innerHTML = '<img src="../admin/templates/images/ok.png" border="0" alt="" width="16" height="16" />';
		EBID('step_' + steps[step] + '_progress').innerHTML = '<b>100%</b>';
	}

	if(theStep < steps.length)
	{
		step = theStep;
		EBID('step_' + steps[step] + '_text').innerHTML = '<b>' + EBID('step_' + steps[step] + '_text').innerHTML + '</b>';
		EBID('step_' + steps[step] + '_status').innerHTML = '<img src="../admin/templates/images/load_16.gif" border="0" alt="" width="16" height="16" />';

		pos = 0;
		stepStep();
	}
	else
	{
		EBID('done').style.display = '';
	}
}

function beginConversion()
{
	stepInit(0);
}

//-->
