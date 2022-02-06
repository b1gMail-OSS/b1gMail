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

function updateMaxChars(field)
{
	var length = field.value.length;
	var maxChars = parseInt(EBID('maxChars').innerHTML);

	// crop, if needed
	if(length > maxChars)
		field.value = field.value.substring(0, maxChars);
	length = field.value.length;

	// update text
	EBID('charCount').innerHTML = length;

	// progressbar width?
	var pbWidth = parseInt(EBID('pb_charCountBar').style.width);
	var newValueWidth = Math.ceil(pbWidth/maxChars * length);
	if(newValueWidth > pbWidth-2)
		newValueWidth = pbWidth-2;
	EBID('pb_charCountBar_value').style.width = ((newValueWidth == 0) ? 1 : newValueWidth) + 'px';
}
function smsTypeChanged()
{
	var type = parseInt(EBID('type').value),
		typePrice = smsTypePrices[type],
		typeFlags = smsTypeFlags[type],
		typeLength = smsTypeLengths[type],
		warnDiv = EBID('priceWarning'),
		sendButton = EBID('sendButton');
	warnDiv.style.display = 'none';
	sendButton.disabled = false;

	EBID('ownFromTR').style.display = (typeFlags & 1) ? 'none' : '';
	EBID('maxChars').innerHTML = typeLength;

	updateMaxChars(EBID('smsText'));

	if(typePrice > accountBalance)
	{
		var warning = lang['pricewarning'];
		warning = warning.replace('%1', typePrice);
		warning = warning.replace('%2', accountBalance);

		warnDiv.innerHTML = warning;
		warnDiv.style.display = '';
		sendButton.disabled = true;
	}
}
function checkSMSComposeForm()
{
	var type = parseInt(EBID('type').value),
		typeFlags = smsTypeFlags[type];

	if((((EBID('from') && EBID('from').value.length < 3)
		|| (EBID('from_no') && EBID('from_no').value.length < 3)) && (typeFlags&1)==0)
		|| (EBID('to') && EBID('to').value.length < 3)
		|| (EBID('to_no') && EBID('to_no').value.length < 3)
		|| EBID('smsText').value.length < 3)
	{
		alert(lang['fillin']);
		return(false);
	}

	return(true);
}
function openCellphoneAddressbook(sid)
{
	openOverlay('organizer.addressbook.php?sid=' + sid + '&action=numberPopup',
		lang['addressbook'],
		450,
		380,
		true);
}
