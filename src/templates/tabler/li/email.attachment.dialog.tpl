{include file="li/dialog.head.tpl" dialogTitle=$filename dialogBodyClass="bm-dialog-mail-attachment bm-dialog-modal-sections" dialogOnLoad="documentLoader()"}

<div class="bm-mail-attachment-dialog">
	<div class="modal-body">
		{if $openKind == 'vcf' && isset($vcard)}
		<div class="bm-mail-attachment-preview bm-mail-attachment-preview-vcf">
			<div class="bm-mail-attachment-preview-hero">
				<span class="avatar avatar-lg bg-primary-lt text-primary">
					{if $vcard.vorname || $vcard.nachname}
						<span class="bm-mail-attachment-initials">{if $vcard.vorname}{text value=$vcard.vorname|truncate:1:false}{/if}{if $vcard.nachname}{text value=$vcard.nachname|truncate:1:false}{/if}</span>
					{else}
						<i class="ti ti-address-book icon" aria-hidden="true"></i>
					{/if}
				</span>
				<div class="bm-mail-attachment-preview-heading">
					<div class="bm-mail-attachment-preview-name">
						{if $vcard.vorname || $vcard.nachname}
							{text value=$vcard.vorname} {text value=$vcard.nachname}
						{else}
							{text value=$filename}
						{/if}
					</div>
					{if $vcard.firma}
					<div class="text-secondary small">{text value=$vcard.firma}</div>
					{/if}
				</div>
			</div>
			<dl class="row bm-mail-vcard-dl mb-0">
				<dt class="col-sm-4">{lng p="firstname"}</dt>
				<dd class="col-sm-8">{if $vcard.vorname}{text value=$vcard.vorname}{else}—{/if}</dd>
				<dt class="col-sm-4">{lng p="surname"}</dt>
				<dd class="col-sm-8">{if $vcard.nachname}{text value=$vcard.nachname}{else}—{/if}</dd>
				<dt class="col-sm-4">{lng p="company"}</dt>
				<dd class="col-sm-8">{if $vcard.firma}{text value=$vcard.firma}{else}—{/if}</dd>
				<dt class="col-sm-4">{lng p="email"}</dt>
				<dd class="col-sm-8">{if $vcard.email}{email value=$vcard.email}{else}—{/if}</dd>
			</dl>
		</div>
		{elseif $openKind == 'ics' && isset($calendarEvent)}
		<div class="bm-mail-attachment-preview bm-mail-attachment-preview-ics">
			<div class="bm-mail-attachment-preview-hero">
				<span class="avatar avatar-lg bg-azure-lt text-azure">
					<i class="ti ti-calendar-event icon" aria-hidden="true"></i>
				</span>
				<div class="bm-mail-attachment-preview-heading">
					<div class="bm-mail-attachment-preview-name">
						{if $calendarEvent.title}{text value=$calendarEvent.title}{else}{lng p="calendar"}{/if}
					</div>
					<div class="text-secondary small bm-mail-attachment-ics-summary">
						{if $calendarEvent.wholeDay}
							{date timestamp=$calendarEvent.startdate dayonly=true}
							{if $calendarEvent.enddate > $calendarEvent.startdate + 43200}
								– {date timestamp=$calendarEvent.enddate dayonly=true}
							{/if}
						{else}
							{date timestamp=$calendarEvent.startdate nice=true}
							– {date timestamp=$calendarEvent.enddate nice=true}
						{/if}
						{if $calendarEvent.location}<span class="bm-mail-attachment-ics-loc"> · {text value=$calendarEvent.location}</span>{/if}
					</div>
				</div>
			</div>
			<dl class="row bm-mail-vcard-dl mb-0">
				<dt class="col-sm-4">{lng p="begin"}</dt>
				<dd class="col-sm-8">
					{if $calendarEvent.wholeDay}
						{date timestamp=$calendarEvent.startdate dayonly=true} ({lng p="wholeday"})
					{else}
						{date timestamp=$calendarEvent.startdate nice=true}
					{/if}
				</dd>
				<dt class="col-sm-4">{lng p="end"}</dt>
				<dd class="col-sm-8">
					{if $calendarEvent.wholeDay}
						{date timestamp=$calendarEvent.enddate dayonly=true}
					{else}
						{date timestamp=$calendarEvent.enddate nice=true}
					{/if}
				</dd>
				{if $calendarEvent.location}
				<dt class="col-sm-4">{lng p="location"}</dt>
				<dd class="col-sm-8">{text value=$calendarEvent.location}</dd>
				{/if}
				{if $calendarEvent.text}
				<dt class="col-sm-4">{lng p="text"}</dt>
				<dd class="col-sm-8">{text value=$calendarEvent.text|truncate:400}</dd>
				{/if}
			</dl>
		</div>
		{else}
		<p class="text-secondary mb-0">{text value=$filename escape=true}</p>
		{/if}

		{if $openKind == 'ics' && isset($calendarEvent) && $canCalendarReply|default:false}
		<div class="bm-mail-attachment-ics-options mt-3">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" id="mailAttSendCalendarReply" checked="checked" />
				<span class="form-check-label">{lng p="mail_att_reply_accept"}</span>
			</label>
			<div class="form-text">{lng p="mail_att_reply_accept_d"}</div>
		</div>
		{/if}
	</div>

	<div class="modal-footer bm-mail-attachment-dialog-footer">
		<button type="button" class="btn btn-ghost-secondary" onclick="if(typeof parent.hideOverlay==='function')parent.hideOverlay();">
			{lng p="cancel"}
		</button>
		<div class="bm-mail-attachment-dialog-footer-actions">
			<a class="btn btn-outline-secondary" href="email.read.php?id={$mailID}&action=downloadAttachment&attachment={$attachment|escape:'url'}&sid={$sid}" target="_blank" rel="noopener">
				<i class="ti ti-download icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="download"}
			</a>
			{if $openKind == 'vcf'}
			<button type="button" class="btn btn-primary" onclick="mailAttachmentImportContact({$mailID}, '{$attachment|escape:'javascript'}', '{$sid|escape:'javascript'}');">
				<i class="ti ti-address-book-plus icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="mail_att_import_contact"}
			</button>
			{elseif $openKind == 'ics' && $organizerEnabled && isset($calendarEvent)}
			<button type="button" class="btn btn-primary" id="mailAttImportCalendarBtn" onclick="mailAttachmentImportCalendar({$mailID}, '{$attachment|escape:'javascript'}', '{$sid|escape:'javascript'}');">
				<i class="ti ti-calendar-plus icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="mail_att_import_calendar"}
			</button>
			{/if}
		</div>
	</div>
</div>

<script>
<!--
function mailAttachmentImportContact(mailId, attachment, sid)
{
	var url = 'email.read.php?id=' + encodeURIComponent(mailId)
		+ '&action=importVCF&attachment=' + encodeURIComponent(attachment)
		+ '&sid=' + encodeURIComponent(sid);

	if(window.parent && window.parent !== window)
		window.parent.document.location.href = url;
	else
		window.location.href = url;
}

function mailAttachmentImportCalendar(mailId, attachment, sid)
{
	var btn = EBID('mailAttImportCalendarBtn'),
		xhr = GetXMLHTTP(),
		url;

	if(!xhr)
		return;

	if(btn)
		btn.disabled = true;

	url = 'email.read.php?id=' + encodeURIComponent(mailId)
		+ '&action=importICS&attachment=' + encodeURIComponent(attachment)
		+ '&ajax=1&sid=' + encodeURIComponent(sid);

	if(EBID('mailAttSendCalendarReply') && EBID('mailAttSendCalendarReply').checked)
		url += '&sendReply=1';

	xhr.open('GET', url, true);
	xhr.onreadystatechange = function()
	{
		var data, msg;

		if(xhr.readyState != 4)
			return;

		if(btn)
			btn.disabled = false;

		try
		{
			data = JSON.parse(xhr.responseText || '{}');
		}
		catch(ex)
		{
			data = { ok: false };
		}

		if(data.ok)
		{
			msg = (typeof lang !== 'undefined' && lang['mail_att_calendar_added'])
				? lang['mail_att_calendar_added']
				: 'Termin wurde zum Kalender hinzugefügt.';

			if(data.replySent && typeof lang !== 'undefined' && lang['mail_att_reply_sent'])
				msg += '\n\n' + lang['mail_att_reply_sent'];

			if(window.parent && window.parent !== window)
			{
				if(typeof window.parent.hideOverlay === 'function')
					window.parent.hideOverlay();
				if(typeof window.parent.alert === 'function')
					window.parent.alert(msg);
			}
			else
				alert(msg);

			return;
		}

		alert((typeof lang !== 'undefined' && lang['error'])
			? lang['error']
			: 'Der Termin konnte nicht importiert werden.');
	};
	xhr.send(null);
}
//-->
</script>

{include file="li/dialog.foot.tpl"}
