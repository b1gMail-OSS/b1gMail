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

function initDnDUpload(elem, url, doneAction, fileDoneAction, urlAddFunc)
{
	var files, i, pbText, pbValue, currentXH;

	var _dragEnter = function(event)
	{
	};

	var _dragLeave = function(event)
	{
		elem.setAttribute('class', elem.className.replace(/dragOver/g, ''));
	};

	var _dragOver = function(event)
	{
		if((event.dataTransfer.types.contains && (!event.dataTransfer.types.contains('Files') || event.dataTransfer.types.contains('text/html')))
				|| (event.dataTransfer.types.indexOf && (event.dataTransfer.types.indexOf('Files') < 0 || event.dataTransfer.types.indexOf('text/html') >= 0)))
			return;

		event.stopPropagation();
		event.preventDefault();

		event.dataTransfer.effectAllowed 	= 'copy';
		event.dataTransfer.dropEffect 		= 'copy';

		elem.setAttribute('class', trim(elem.className.replace(/dragOver/g, '') + ' dragOver'));
	};

	var _uploadFile = function(done)
	{
		var reader = new FileReader(), file = files[i];

		reader.onerror = function(event)
		{

		};
		reader.onloadend = function(event)
		{
			var data = event.target.result, commaPos;
			if(data == 'data:')
			{
				data = '';
			}
			else
			{
				if(data == null || (commaPos = data.indexOf(',')) > data.length)
				{
					i++;

					if(i >= files.length-1)
						done();
					else
						_uploadFile(done);

					return;
				}
				data = data.substring(commaPos+1);
			}

			var xh = GetXMLHTTP();
			if(xh)
			{
				currentXH = xh;
				var _this = this;
				addEvent(xh.upload, 'progress', function(event)
					{
						if(event.lengthComputable)
						{
							var progress = event.loaded / event.total;
							pbValue.style.width = Math.ceil(progress * 198) + 'px';
						}
					});
				xh.open('POST', url + '&filename=' + encodeURIComponent(file.name)
									+ '&size=' + file.size
									+ '&type=' + encodeURIComponent(file.type)
									+ (urlAddFunc ? urlAddFunc() : ''), true);
				xh.setRequestHeader('Content-Type', 'application/octet-stream');

				xh.onreadystatechange = function()
				{
					if(xh.readyState == 4)
					{
						if(fileDoneAction)
							fileDoneAction(xh.responseText);

						i++;

						if(i >= files.length)
							done();
						else
							_uploadFile(done);
					}
				};

				xh.send(data);
			}
		};

		var fileNameShort = file.name;
		if(fileNameShort.length > 25)
			fileNameShort = fileNameShort.substring(0, 22) + '...';
		pbText.nodeValue = lang['uploading'] + ': "' + fileNameShort + '" (' + (i+1) + ' / ' + files.length + ')';
		pbValue.style.width = '1px';
		reader.readAsDataURL(file);
	};

	var _drop = function(event)
	{
		event.stopPropagation();
		event.preventDefault();

		elem.setAttribute('class', elem.className.replace(/dragOver/g, ''));

		files = event.dataTransfer.files;
		if(typeof(files) == 'undefined' || files == null || files.length <= 0)
			return;

		var done = function()
		{
			ol.hide();
			if(doneAction)
				doneAction();
		};

		var spinImg = document.createElement('i');
		spinImg.setAttribute('class', 'fa fa-spinner fa-pulse fa-fw fa-3x');

		pbValue = document.createElement('div');
		pbValue.setAttribute('class', 'progressBarValue');
		pbValue.style.width = '0px';

		var pbDiv = document.createElement('div');
		pbDiv.setAttribute('class', 'progressBar');
		pbDiv.style.marginLeft = 'auto';
		pbDiv.style.marginRight = 'auto';
		pbDiv.style.width = '200px';
		pbDiv.appendChild(pbValue);

		var cancelButton = document.createElement('button');
		cancelButton.appendChild(document.createTextNode(lang['cancel']));
		addEvent(cancelButton, 'click', function() {
			if(currentXH)
				currentXH.abort();
			done();
		});

		var olDiv = document.createElement('div');
		olDiv.style.paddingTop = '1.5em';
		olDiv.style.textAlign = 'center';
		olDiv.appendChild(spinImg);
		olDiv.appendChild(document.createElement('br'));
		olDiv.appendChild(document.createElement('br'));
		olDiv.appendChild(pbText = document.createTextNode(lang['uploading'] + '...'));
		olDiv.appendChild(document.createElement('br'));
		olDiv.appendChild(document.createElement('br'));
		olDiv.appendChild(pbDiv);
		olDiv.appendChild(document.createElement('br'));
		olDiv.appendChild(cancelButton);

		var ol = new Overlay(true);
		ol.setSize(420, 165);
		ol.setCaption(lang['uploading']);
		ol.olContent.appendChild(olDiv);
		ol.show();

		i = 0;
		_uploadFile(done);
	};

	addEvent(elem, 'dragenter',	_dragEnter);
	addEvent(elem, 'dragleave',	_dragLeave);
	addEvent(elem, 'dragover',	_dragOver);
	addEvent(elem, 'drop',		_drop);
}
