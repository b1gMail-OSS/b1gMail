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

var currentSignature = 0;

function showStatement()
{
	openOverlay('prefs.php?action=membership&do=statement&sid=' + currentSID,
		lang['statement'],
		700,
		500,
		true);
}

function startSignatureEditor()
{
	var editor = new htmlEditor('html');
	editor.height = '100%';
	editor.onReady = function()
	{
		editor.start();
		editor.switchMode('html', true);
	}
	editor.init();
}

function showSignature(id)
{
	var elems = EBID('signaturesList').getElementsByTagName('LI');
	for(var i=0; i<elems.length; ++i)
	{
		if(elems[i].dataset.sigId == id)
			elems[i].className = 'sel';
		else
			elems[i].className = '';
	}

	EBID('sigItem').innerHTML = '';
	EBID('removeButton').disabled = true;

	if(id <= 0)
		return;

	MakeXMLRequest('prefs.php?action=signatures&do=edit&id='+id+'&sid='+currentSID, function(http)
			{
				if(http.readyState == 4 && http.responseText)
				{
					EBID('sigItem').innerHTML = http.responseText;
					EBID('removeButton').disabled = false;
					currentSignature = id;
					startSignatureEditor();
				}
			});
}

function addSignature()
{
	EBID('sigItem').innerHTML = '';
	MakeXMLRequest('prefs.php?action=signatures&do=add&sid='+currentSID, function(http)
			{
				if(http.readyState == 4 && http.responseText)
				{
					EBID('sigItem').innerHTML = http.responseText;
					EBID('removeButton').disabled = true;
					EBID('titel').focus();
					startSignatureEditor();
				}
			});
}

function updateAliasForm()
{
	var typ = EBID('typ_1').checked ? 1 : 3;

	EBID('tbody_1').style.display = typ == 1 ? '' : 'none';
	EBID('tbody_3').style.display = typ == 3 ? '' : 'none';
}
function checkPOP3AccountForm(form)
{
	if(form.elements['p_host'].value.length < 2
		|| form.elements['p_user'].value.length < 2
		|| form.elements['p_pass'].value.length < 2
		|| form.elements['p_port'].value.length < 1)
	{
		alert(lang['fillin']);
		return(false);
	}
	return(true);
}
function checkFilterForm(form)
{
	if(form.elements['title'].value.length < 2)
	{
		alert(lang['fillin']);
		return(false);
	}
	return(true);
}
function checkSignatureForm(form)
{
	if(form.elements['titel'].value.length < 2)
	{
		alert(lang['fillin']);
		return(false);
	}
	return(true);
}
function addPublicCert()
{
	openOverlay('prefs.php?sid=' + currentSID + '&action=keyring&do=importPublicCertificate',
		lang['addcert'],
		520,
		140,
		true);
}
function addPrivateCert(pkcs12Support)
{
	openOverlay('prefs.php?sid=' + currentSID + '&action=keyring&do=importPrivateCertificate',
		lang['addcert'],
		520,
		pkcs12Support ? 170 : 230,
		true);
}
function exportPrivateCert(hash)
{
	openOverlay('prefs.php?sid=' + currentSID + '&action=keyring&do=exportPrivateCertificate&hash=' + hash,
		lang['exportcert'],
		520,
		140,
		true);
}
