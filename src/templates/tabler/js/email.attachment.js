/*
 * b1gMail – E-Mail-Anhänge: Dialoge und Webdisk-Vorschau
 */

function mailAttachmentDownloadUrl(mailId, attachment, inline)
{
	var url = 'email.read.php?id=' + encodeURIComponent(mailId)
		+ '&action=downloadAttachment&attachment=' + encodeURIComponent(attachment)
		+ '&sid=' + currentSID;

	if(inline)
		url += '&view=true';

	return(url);
}

function mailAttachmentFileMeta(mailId, attachment, filename, mimetype)
{
	var mime = (mimetype || '').toLowerCase();

	return({
		id: 'mail:' + mailId + ':' + attachment,
		mailId: mailId,
		attachment: attachment,
		title: filename || '',
		isMail: true,
		isImage: mime.indexOf('image/') === 0,
		isPdf: mime === 'application/pdf',
		isText: mime === 'text/plain',
		isEditableText: false
	});
}

function mailOpenViewableAttachment(mailId, attachment, filename, mimetype)
{
	var file = mailAttachmentFileMeta(mailId, attachment, filename, mimetype);

	if(typeof webdiskEnsurePreviewModalInBody === 'function')
		webdiskEnsurePreviewModalInBody();

	if(typeof webdiskInitPreview === 'function')
		webdiskInitPreview();

	var modal = EBID('wdPreviewModal');

	if(!_wdPreview || !modal)
	{
		window.open(mailAttachmentDownloadUrl(mailId, attachment, true), '_blank');
		return;
	}

	_wdPreview.files = [file];
	_wdPreview.index = 0;
	_wdPreview.galleryFiles = [];

	var startRender = function()
	{
		webdiskPreviewRender();
	};

	if(modal.classList.contains('show') || modal.classList.contains('in'))
		startRender();
	else if(typeof webdiskPreviewModalShow === 'function')
		webdiskPreviewModalShow(modal, startRender);
	else if(_wdPreview.modal)
	{
		modal.addEventListener('shown.bs.modal', startRender, { once: true });
		_wdPreview.modal.show();
	}
	else
		startRender();

	if(typeof webdiskPreviewBindKeys === 'function')
		webdiskPreviewBindKeys();
}

function mailOpenAttachment(mailId, attachment, openKind, title, filename, mimetype)
{
	if(openKind === 'viewable')
	{
		mailOpenViewableAttachment(mailId, attachment, filename, mimetype);
		return;
	}

	if(openKind === 'vcf' || openKind === 'ics')
	{
		openOverlay(
			'email.read.php?action=attachmentDialog&id=' + encodeURIComponent(mailId)
				+ '&attachment=' + encodeURIComponent(attachment) + '&sid=' + currentSID,
			title || filename || '',
			520,
			openKind === 'ics' ? 480 : 460,
			false
		);
		return;
	}

	window.location.href = mailAttachmentDownloadUrl(mailId, attachment, false);
}

/**
 * Send calendar RSVP (accept / decline / tentative) for the invite card in mail read/preview.
 */
function mailCalendarRsvp(partstat, card)
{
	var xhr = GetXMLHTTP(),
		url,
		commentEl,
		comment,
		buttons,
		i,
		mailId,
		sid,
		attKey,
		msg;

	card = card || EBID('bmMailCalendarCard');

	if(!xhr || !card)
		return;

	mailId = card.getAttribute('data-mail-id') || '';
	sid = card.getAttribute('data-sid') || '';
	if(typeof currentSID !== 'undefined' && currentSID)
		sid = sid || currentSID;
	attKey = card.getAttribute('data-calendar-attachment') || '';

	if(!mailId || !sid || !attKey)
		return;

	commentEl = card.querySelector('#bmMailRsvpComment');
	comment = commentEl ? commentEl.value : '';
	buttons = card.querySelectorAll('.bm-mail-rsvp-btn, .bm-mail-calendar-rsvp-actions button');

	for(i = 0; i < buttons.length; i++)
		buttons[i].disabled = true;

	url = 'email.read.php?id=' + encodeURIComponent(mailId)
		+ '&action=calendarRsvp&attachment=' + encodeURIComponent(attKey)
		+ '&partstat=' + encodeURIComponent(partstat)
		+ '&ajax=1&sid=' + encodeURIComponent(sid);

	if(comment)
		url += '&comment=' + encodeURIComponent(comment);

	xhr.open('GET', url, true);
	xhr.onreadystatechange = function()
	{
		var data;

		if(xhr.readyState != 4)
			return;

		for(i = 0; i < buttons.length; i++)
			buttons[i].disabled = false;

		try
		{
			data = JSON.parse(xhr.responseText || '{}');
		}
		catch(ex)
		{
			data = { ok: false };
		}

		if(!data.ok)
		{
			alert((typeof lang !== 'undefined' && lang.error) ? lang.error : 'Fehler');
			return;
		}

		msg = (typeof lang !== 'undefined' && lang.mail_att_rsvp_done)
			? lang.mail_att_rsvp_done
			: 'Antwort gesendet.';

		if(data.replySent && typeof lang !== 'undefined' && lang.mail_att_reply_sent)
			msg += ' ' + lang.mail_att_reply_sent;

		alert(msg);
		card.style.display = 'none';
	};
	xhr.send(null);
}

/**
 * Wire RSVP buttons and calendar links (required after preview pane innerHTML load).
 *
 * @param {Document|HTMLElement} [root]
 */
function bmMailCalendarInviteInit(root)
{
	var scope = root || document,
		card,
		btns,
		links,
		i;

	if(scope.getElementById)
		card = scope.getElementById('bmMailCalendarCard');
	else if(scope.querySelector)
		card = scope.querySelector('#bmMailCalendarCard');
	else
		card = EBID('bmMailCalendarCard');

	if(!card || card.getAttribute('data-bm-rsvp-init') === '1')
		return;

	card.setAttribute('data-bm-rsvp-init', '1');

	btns = card.querySelectorAll('.bm-mail-rsvp-btn');
	for(i = 0; i < btns.length; i++)
	{
		btns[i].addEventListener('click', function(ev)
		{
			ev.preventDefault();
			mailCalendarRsvp(this.getAttribute('data-partstat') || 'accepted', card);
		});
	}

	links = scope.querySelectorAll
		? scope.querySelectorAll('.bm-mail-open-calendar-date')
		: document.querySelectorAll('.bm-mail-open-calendar-date');

	for(i = 0; i < links.length; i++)
	{
		links[i].addEventListener('click', function(ev)
		{
			if(typeof showOverlay === 'function')
			{
				ev.preventDefault();
				showOverlay(this.href);
			}
		});
	}
}
