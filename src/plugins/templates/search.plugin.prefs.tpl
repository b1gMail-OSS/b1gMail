<fieldset class="mb-4">
	<legend class="h4 mb-3">{lng p="searchprovider"}</legend>

	<form action="{sessionurl file='plugin.page.php' params="plugin={$searchPlugin}&do=prefs&save=true"}" method="post" onsubmit="spin(this)">
		{csrffield}

		<p class="text-secondary mb-3">{lng p="includeinsearch"}:</p>

		<div class="mb-2">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="searchIn[mails]" id="searchMails"{if $searchIn.mails} checked="checked"{/if} />
				<span class="form-check-label" for="searchMails"><b>{lng p="emails"}</b></span>
			</label>
		</div>
		<div class="mb-2">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="searchIn[attachments]" id="searchAtt"{if $searchIn.attachments} checked="checked"{/if} />
				<span class="form-check-label" for="searchAtt"><b>{lng p="attachments"}</b></span>
			</label>
		</div>
		<div class="mb-2">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="searchIn[sms]" id="searchSMS"{if $searchIn.sms} checked="checked"{/if} />
				<span class="form-check-label" for="searchSMS"><b>{lng p="smsoutbox"}</b></span>
			</label>
		</div>
		<div class="mb-2">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="searchIn[calendar]" id="searchCalendar"{if $searchIn.calendar} checked="checked"{/if} />
				<span class="form-check-label" for="searchCalendar"><b>{lng p="calendar"}</b></span>
			</label>
		</div>
		<div class="mb-2">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="searchIn[tasks]" id="searchTasks"{if $searchIn.tasks} checked="checked"{/if} />
				<span class="form-check-label" for="searchTasks"><b>{lng p="tasks"}</b></span>
			</label>
		</div>
		<div class="mb-2">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="searchIn[addressbook]" id="searchAddressbook"{if $searchIn.addressbook} checked="checked"{/if} />
				<span class="form-check-label" for="searchAddressbook"><b>{lng p="addressbook"}</b></span>
			</label>
		</div>
		<div class="mb-2">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="searchIn[notes]" id="searchNotes"{if $searchIn.notes} checked="checked"{/if} />
				<span class="form-check-label" for="searchNotes"><b>{lng p="notes"}</b></span>
			</label>
		</div>
		<div class="mb-3">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="searchIn[webdisk]" id="searchWebdisk"{if $searchIn.webdisk} checked="checked"{/if} />
				<span class="form-check-label" for="searchWebdisk"><b>{lng p="webdisk"}</b></span>
			</label>
		</div>

		<div class="alert alert-warning mb-3" role="alert">
			<i class="ti ti-alert-triangle me-1"></i> {lng p="mailsearchwarn"}
		</div>

		<div class="text-end">
			<button type="submit" class="btn btn-primary">
				<i class="ti ti-device-floppy me-1"></i> {lng p="save"}
			</button>
		</div>
	</form>
</fieldset>
