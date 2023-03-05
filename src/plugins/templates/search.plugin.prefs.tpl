<fieldset>
	<legend>{lng p="searchprovider"}</legend>

	<div class="alert alert-warning">{lng p="mailsearchwarn"}</div>

	<form action="{$pageURL}&sid={$sid}&do=save" method="post" onsubmit="spin(this)">
		<p>{lng p="includeinsearch"}:</p>

		<label class="form-check">
			<input class="form-check-input" type="checkbox" name="searchIn[mails]" id="searchMails"{if $searchIn.mails} checked="checked"{/if}>
			<span class="form-check-label">{lng p="emails"}</span>
		</label>
		<label class="form-check">
			<input class="form-check-input" type="checkbox" name="searchIn[attachments]" id="searchAtt"{if $searchIn.attachments} checked="checked"{/if}>
			<span class="form-check-label">{lng p="attachments"}</span>
		</label>
		<label class="form-check">
			<input class="form-check-input" type="checkbox" name="searchIn[sms]" id="searchSMS"{if $searchIn.sms} checked="checked"{/if}>
			<span class="form-check-label">{lng p="smsoutbox"}</span>
		</label>
		<label class="form-check">
			<input class="form-check-input" type="checkbox" name="searchIn[calendar]" id="searchCalendar"{if $searchIn.calendar} checked="checked"{/if}>
			<span class="form-check-label">{lng p="calendar"}</span>
		</label>
		<label class="form-check">
			<input class="form-check-input" type="checkbox" name="searchIn[tasks]" id="searchTasks"{if $searchIn.tasks} checked="checked"{/if}>
			<span class="form-check-label">{lng p="tasks"}</span>
		</label>
		<label class="form-check">
			<input class="form-check-input" type="checkbox" name="searchIn[addressbook]" id="searchAddressbook"{if $searchIn.addressbook} checked="checked"{/if}>
			<span class="form-check-label">{lng p="addressbook"}</span>
		</label>
		<label class="form-check">
			<input class="form-check-input" type="checkbox" name="searchIn[notes]" id="searchNotes"{if $searchIn.notes} checked="checked"{/if}>
			<span class="form-check-label">{lng p="notes"}</span>
		</label>
		<label class="form-check">
			<input class="form-check-input" type="checkbox" name="searchIn[webdisk]" id="searchWebdisk"{if $searchIn.webdisk} checked="checked"{/if}>
			<span class="form-check-label">{lng p="webdisk"}</span>
		</label>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>