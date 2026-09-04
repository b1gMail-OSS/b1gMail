<!DOCTYPE html>

<html>

<head>
	<title>{text value=$filename}</title>
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	<link rel="shortcut icon" type="image/png" href="{$tpldir}res/favicon.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	<link href="clientlib/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<script src="clientlang.php"></script>
	<script src="{$tpldir}clientlib/overlay.js"></script>
	<script src="{$tpldir}js/common.js"></script>
	<script src="{$tpldir}js/loggedin.js"></script>
	<script src="{$tpldir}js/dialog.js"></script>
</head>

<body onload="documentLoader()">

<div class="bm-mail-attachment-dialog">
	{if $openKind == 'vcf' && isset($vcard)}
	<div class="bm-mail-attachment-preview bm-mail-attachment-preview-vcf">
		<div class="bm-mail-attachment-preview-hero">
			<span class="bm-mail-attachment-avatar">
				{if $vcard.vorname || $vcard.nachname}
					<span class="bm-mail-attachment-initials">{if $vcard.vorname}{text value=$vcard.vorname|truncate:1:false}{/if}{if $vcard.nachname}{text value=$vcard.nachname|truncate:1:false}{/if}</span>
				{else}
					<i class="fa fa-address-book-o" aria-hidden="true"></i>
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
				<div class="bm-mail-attachment-preview-sub">{text value=$vcard.firma}</div>
				{/if}
			</div>
		</div>
		<table class="bm-mail-vcard-table" width="100%">
			<tr><th width="30%">{lng p="firstname"}</th><td>{if $vcard.vorname}{text value=$vcard.vorname}{else}—{/if}</td></tr>
			<tr><th>{lng p="surname"}</th><td>{if $vcard.nachname}{text value=$vcard.nachname}{else}—{/if}</td></tr>
			<tr><th>{lng p="company"}</th><td>{if $vcard.firma}{text value=$vcard.firma}{else}—{/if}</td></tr>
			<tr><th>{lng p="email"}</th><td>{if $vcard.email}{email value=$vcard.email}{else}—{/if}</td></tr>
		</table>
	</div>
	{elseif $openKind == 'ics' && isset($calendarEvent)}
	<div class="bm-mail-attachment-preview bm-mail-attachment-preview-ics">
		<div class="bm-mail-attachment-preview-hero">
			<span class="bm-mail-attachment-avatar bm-mail-attachment-avatar--ics">
				<i class="fa fa-calendar" aria-hidden="true"></i>
			</span>
			<div class="bm-mail-attachment-preview-heading">
				<div class="bm-mail-attachment-preview-name">
					{if $calendarEvent.title}{text value=$calendarEvent.title}{else}{lng p="calendar"}{/if}
				</div>
				<div class="bm-mail-attachment-preview-sub bm-mail-attachment-ics-summary">
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
		<table class="bm-mail-vcard-table" width="100%">
			<tr>
				<th width="30%">{lng p="begin"}</th>
				<td>
					{if $calendarEvent.wholeDay}
						{date timestamp=$calendarEvent.startdate dayonly=true} ({lng p="wholeday"})
					{else}
						{date timestamp=$calendarEvent.startdate nice=true}
					{/if}
				</td>
			</tr>
			<tr>
				<th>{lng p="end"}</th>
				<td>
					{if $calendarEvent.wholeDay}
						{date timestamp=$calendarEvent.enddate dayonly=true}
					{else}
						{date timestamp=$calendarEvent.enddate nice=true}
					{/if}
				</td>
			</tr>
			{if $calendarEvent.location}
			<tr><th>{lng p="location"}</th><td>{text value=$calendarEvent.location}</td></tr>
			{/if}
			{if $calendarEvent.text}
			<tr><th>{lng p="text"}</th><td>{text value=$calendarEvent.text|truncate:400}</td></tr>
			{/if}
		</table>
	</div>
	{else}
	<p>{text value=$filename escape=true}</p>
	{/if}

	{if $openKind == 'ics' && isset($calendarEvent) && $canCalendarReply|default:false}
	<p class="bm-mail-attachment-ics-options">
		<label>
			<input type="checkbox" id="mailAttSendCalendarReply" checked="checked" />
			{lng p="mail_att_reply_accept"}
		</label>
		<br /><small>{lng p="mail_att_reply_accept_d"}</small>
	</p>
	{/if}

	<p>
		<table width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td align="left">
					<input type="button" onclick="if(typeof parent.hideOverlay==='function')parent.hideOverlay();" value="{lng p="cancel"}" />
				</td>
				<td align="right">
					<a href="email.read.php?id={$mailID}&action=downloadAttachment&attachment={$attachment|escape:'url'}" target="_blank" rel="noopener">
						<i class="fa fa-download" aria-hidden="true"></i> {lng p="download"}
					</a>
					{if $openKind == 'vcf'}
					&nbsp;
					<input type="button" class="primary" value="{lng p="mail_att_import_contact"}" onclick="mailAttachmentImportContact({$mailID}, '{$attachment|escape:'javascript'}', '{$sid|escape:'javascript'}');" />
					{elseif $openKind == 'ics' && $organizerEnabled && isset($calendarEvent)}
					&nbsp;
					<input type="button" class="primary" id="mailAttImportCalendarBtn" value="{lng p="mail_att_import_calendar"}" onclick="mailAttachmentImportCalendar({$mailID}, '{$attachment|escape:'javascript'}', '{$sid|escape:'javascript'}');" />
					{/if}
				</td>
			</tr>
		</table>
	</p>
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

</body>
</html>
