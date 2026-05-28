{include file="li/dialog.head.tpl" dialogTitle=$date.title dialogBodyClass="bm-dialog-calendar-date bm-dialog-modal-sections" dialogOnLoad="documentLoader()"}

<div class="bm-calendar-showdate">
	<div class="modal-body">
		<h3 class="modal-title">{lng p="date2"}</h3>
		<dl class="row mb-0">
			<dt class="col-sm-3">{lng p="begin"}</dt>
			<dd class="col-sm-9">
				{if ($date.flags&1)}{date timestamp=$date.startdate dayonly=true} ({lng p="wholeday"}){else}{date timestamp=$date.startdate nice=true elapsed=true}{/if}
				{if !empty($date.orig_startdate)}<span class="text-secondary"> ({lng p="thisevent"} {if ($date.flags&1)}{date timestamp=$date.orig_startdate dayonly=true}{else}{date timestamp=$date.orig_startdate nice=true}{/if})</span>{/if}
			</dd>
			<dt class="col-sm-3">{lng p="end"}</dt>
			<dd class="col-sm-9">
				{if ($date.flags&1)}{date timestamp=$date.enddate dayonly=true} ({lng p="wholeday"}){else}{date timestamp=$date.enddate nice=true elapsed=true}{/if}
				{if !empty($date.orig_enddate)}<span class="text-secondary"> ({lng p="thisevent"} {if ($date.flags&1)}{date timestamp=$date.orig_enddate dayonly=true}{else}{date timestamp=$date.orig_enddate nice=true}{/if})</span>{/if}
			</dd>
			<dt class="col-sm-3">{lng p="location"}</dt>
			<dd class="col-sm-9">{if $date.location}{text value=$date.location}{else}—{/if}</dd>
			<dt class="col-sm-3">{lng p="reminder"}</dt>
			<dd class="col-sm-9">
				<label class="form-check form-switch mb-0">
					<input type="checkbox" class="form-check-input" {if (isset($date.flags) && (($date.flags&2) || ($date.flags&4) || $date.flags&8))} checked="checked"{/if} disabled="disabled" />
				</label>
			</dd>
			<dt class="col-sm-3">{lng p="repeating"}</dt>
			<dd class="col-sm-9">
				<label class="form-check form-switch mb-0">
					<input type="checkbox" class="form-check-input" {if $date.repeat_flags!=0} checked="checked"{/if} disabled="disabled" />
				</label>
			</dd>
		</dl>
	</div>

	<div class="modal-body">
		<h3 class="modal-title">{lng p="attendees"}</h3>
		<div class="addressDiv bm-calendar-attendee-list">
			{if !$attendees}
				<p class="text-secondary mb-0"><i>({lng p="none"})</i></p>
			{else}
				{foreach from=$attendees item=person}
				<div class="addressItem bm-calendar-attendee-item" onclick="parent.document.location.href='organizer.addressbook.php?sid={$sid}&action=editContact&id={$person.id}';">
					{if $person.partstat == 'accepted'}
						<i class="ti ti-circle-check-filled icon icon-sm text-success me-1" title="{lng p="mail_att_partstat_accepted"}" aria-hidden="true"></i>
					{elseif $person.partstat == 'declined'}
						<i class="ti ti-circle-x-filled icon icon-sm text-danger me-1" title="{lng p="mail_att_partstat_declined"}" aria-hidden="true"></i>
					{elseif $person.partstat == 'tentative'}
						<i class="ti ti-help-circle-filled icon icon-sm text-warning me-1" title="{lng p="mail_att_partstat_tentative"}" aria-hidden="true"></i>
					{else}
						<i class="ti ti-user icon icon-sm text-secondary me-1" title="{lng p="mail_att_partstat_needs"}" aria-hidden="true"></i>
					{/if}
					{text value=$person.nachname}, {text value=$person.vorname}
				</div>
				{/foreach}
			{/if}
		</div>
	</div>

	<div class="modal-body">
		<h3 class="modal-title">{lng p="notes"}</h3>
		<textarea class="form-control" rows="4" readonly="readonly">{text value=$date.text}</textarea>
	</div>

	<div class="modal-footer bm-calendar-showdate-footer">
		<div class="bm-calendar-showdate-footer-start">
			{if $attendees}
			<button type="button" class="btn btn-ghost-primary" onclick="parent.document.location.href='email.compose.php?to={$mailTo}&subject={$mailSubject}&sid={$sid}';">
				<i class="ti ti-mail icon" aria-hidden="true"></i>
				{lng p="mailattendees"}
			</button>
			{/if}
		</div>
		<div class="bm-calendar-showdate-footer-actions">
			<button type="button" class="btn btn-ghost-danger" onclick="if(confirm('{lng p="realdel"}')) parent.document.location.href='organizer.calendar.php?action=deleteDate&id={$date.id}&sid={$sid}';">
				<i class="ti ti-trash icon" aria-hidden="true"></i>
				{lng p="delete"}
			</button>
			<button type="button" class="btn btn-primary" onclick="parent.document.location.href='organizer.calendar.php?action=editDate&id={$date.id}{if $date.repeat_flags!=0}&jumpbackDate={$date.startdate}{/if}&sid={$sid}';">
				<i class="ti ti-pencil icon" aria-hidden="true"></i>
				{lng p="edit"}
			</button>
		</div>
	</div>
</div>

<script>
<!--
	registerLoadAction(initCalendarShowDate);

	function initCalendarShowDate()
	{
		if(typeof parent.setOverlayTitle != 'function')
			return;

		parent.setOverlayTitle(
			"{text noentities=true escape=true value=$date.title}",
			"{lng p="group"}: {text noentities=true escape=true value=$groups[$date.group].title}"
		);
	}
//-->
</script>

{include file="li/dialog.foot.tpl"}
