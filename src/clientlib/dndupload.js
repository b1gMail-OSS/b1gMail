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

function bmFormatBytes(bytes)
{
	if(bytes < 1024)
		return(bytes + ' B');
	if(bytes < 1024 * 1024)
		return(Math.round(bytes / 1024 * 100) / 100 + ' KB');
	if(bytes < 1024 * 1024 * 1024)
		return(Math.round(bytes / 1024 / 1024 * 100) / 100 + ' MB');

	return(Math.round(bytes / 1024 / 1024 / 1024 * 100) / 100 + ' GB');
}

function bmGetWebdiskMaxUploadBytes()
{
	if(typeof window.webdiskMaxUploadBytes !== 'undefined' && window.webdiskMaxUploadBytes > 0)
		return(window.webdiskMaxUploadBytes);

	return(0);
}

function bmWebdiskUploadAlert(message, fileName)
{
	if(typeof webdiskShowUploadAlert === 'function')
	{
		if(fileName && typeof webdiskFormatUploadAlertMessage === 'function')
			webdiskShowUploadAlert(webdiskFormatUploadAlertMessage(message, fileName), 'danger', true);
		else
			webdiskShowUploadAlert(message, 'danger', false);
	}
	else if(typeof bmShowPageAlert === 'function')
		bmShowPageAlert((fileName ? fileName + ':\n' : '') + message, 'danger', 'webdiskPageAlert');
	else
		alert((fileName ? fileName + ':\n' : '') + message);
}

function bmForceHideDnDOverlay()
{
	var olBg = EBID('__olBackground');
	var containers = document.querySelectorAll('.__olContainer');
	var i;

	for(i = 0; i < containers.length; i++)
	{
		if(containers[i].parentNode)
			containers[i].parentNode.removeChild(containers[i]);
	}

	if(olBg && olBg.parentNode)
		olBg.parentNode.removeChild(olBg);

	if(typeof olOverlays !== 'undefined')
	{
		for(var id in olOverlays)
		{
			if(olOverlays.hasOwnProperty(id))
				olOverlays[id] = null;
		}
	}
}

function bmDnDCsrfToken()
{
	if(typeof bmCsrfToken !== 'undefined' && bmCsrfToken)
		return bmCsrfToken;
	if(typeof bmSessionConfig !== 'undefined' && bmSessionConfig && bmSessionConfig.csrfToken)
		return bmSessionConfig.csrfToken;
	var inp = document.querySelector('input[name="csrf_token"]');
	return inp ? inp.value : '';
}

function initDnDUpload(elem, url, doneAction, fileDoneAction, urlAddFunc, options)
{
	options = options || {};
	var useOverlay = (options.useOverlay !== false);
	var files, i, pbText, pbValue, currentXH, ol, progressWrap;

	if(typeof bmAppendSession === 'function')
		url = bmAppendSession(url);

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
			if(fileDoneAction)
				fileDoneAction(lang['wd_upload_failed'] + ': ' + file.name, file.name);
			else if(typeof window.webdiskMaxUploadBytes !== 'undefined')
				bmWebdiskUploadAlert((lang['wd_upload_failed'] || 'Upload failed'), file.name);
			else
				alert((lang['wd_upload_failed'] || 'Upload failed') + ': ' + file.name);

			i++;
			if(i >= files.length)
				done();
			else
				_uploadFile(done);
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
				xh.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
				var csrfToken = bmDnDCsrfToken();
				if(csrfToken)
					xh.setRequestHeader('X-CSRF-TOKEN', csrfToken);

				xh.onreadystatechange = function()
				{
					if(xh.readyState == 4)
					{
						if(typeof bmSessionHandleResponse === 'function' && bmSessionHandleResponse(xh))
							return;

						var response = (xh.responseText || '').replace(/^\s+|\s+$/g, ''),
							isWebdiskUpload = (typeof window.webdiskMaxUploadBytes !== 'undefined');

						if(fileDoneAction)
						{
							if(isWebdiskUpload)
							{
								if(xh.status >= 200 && xh.status < 300 && response === '1')
									fileDoneAction('1', file.name);
								else
								{
									if(!response)
										response = lang['internalerror'] || lang['wd_upload_failed'] || 'Error';
									fileDoneAction(response, file.name);
								}
							}
							else
								fileDoneAction(xh.responseText);
						}
						else if(isWebdiskUpload && (xh.status < 200 || xh.status >= 300 || response !== '1'))
						{
							if(!response)
								response = lang['internalerror'] || lang['wd_upload_failed'] || 'Error';
							bmWebdiskUploadAlert(response, file.name);
						}

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

		var maxBytes = bmGetWebdiskMaxUploadBytes();
		var rejected = [], rejectedType = [], j, okFiles = [], f;

		for(j = 0; j < files.length; j++)
		{
			f = files[j];

			if(maxBytes > 0 && f.size > maxBytes)
				rejected.push(f.name);
			else if(typeof webdiskIsFileForbidden === 'function' && webdiskIsFileForbidden(f.name, f.type))
				rejectedType.push(f.name);
			else
				okFiles.push(f);
		}

		if(rejected.length > 0)
		{
			var limitText = (lang['wd_filetoolarge'] || 'Too large (max. %s)')
				.replace('%s', bmFormatBytes(maxBytes));
			var rejectMsg = rejected.join('\n') + '\n\n' + limitText;
			bmWebdiskUploadAlert(rejectMsg);
		}

		if(rejectedType.length > 0)
		{
			var typeMsg = (lang['wd_fileforbidden'] || 'File type not allowed') + ':\n' + rejectedType.join('\n');
			bmWebdiskUploadAlert(typeMsg);
		}

		if(okFiles.length <= 0)
			return;

		files = okFiles;

		var hideProgress = function()
		{
			if(useOverlay && ol)
			{
				try { ol.hide(); } catch(exOl) {}
				ol = null;
			}
			else if(progressWrap && progressWrap.parentNode)
			{
				progressWrap.parentNode.removeChild(progressWrap);
				progressWrap = null;
			}

			if(elem)
				elem.setAttribute('class', trim(elem.className.replace(/bm-dnd-upload-active/g, '')));

			bmForceHideDnDOverlay();
		};

		var done = function()
		{
			hideProgress();
			if(doneAction)
				doneAction();
		};

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
		cancelButton.setAttribute('type', 'button');
		cancelButton.className = useOverlay ? '' : 'btn btn-sm btn-ghost-secondary mt-2';
		cancelButton.appendChild(document.createTextNode(lang['cancel']));
		addEvent(cancelButton, 'click', function() {
			if(currentXH)
				currentXH.abort();
			done();
		});

		if(useOverlay)
		{
			var spinImg = document.createElement('i');
			spinImg.setAttribute('class', 'fa fa-spinner fa-pulse fa-fw fa-3x');

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

			ol = new Overlay(true);
			ol.setSize(420, 165);
			ol.setCaption(lang['uploading']);
			ol.olContent.appendChild(olDiv);
			ol.show();
		}
		else
		{
			var statusEl = document.createElement('div');
			statusEl.className = 'bm-dnd-upload-status small text-secondary mb-2';
			statusEl.appendChild(pbText = document.createTextNode(lang['uploading'] + '...'));

			progressWrap = document.createElement('div');
			progressWrap.className = 'bm-dnd-upload-progress';
			progressWrap.appendChild(statusEl);
			progressWrap.appendChild(pbDiv);
			progressWrap.appendChild(cancelButton);

			elem.className = trim(elem.className.replace(/bm-dnd-upload-active/g, '') + ' bm-dnd-upload-active');
			elem.appendChild(progressWrap);
		}

		i = 0;
		_uploadFile(done);
	};

	addEvent(elem, 'dragenter',	_dragEnter);
	addEvent(elem, 'dragleave',	_dragLeave);
	addEvent(elem, 'dragover',	_dragOver);
	addEvent(elem, 'drop',		_drop);
}
