<div class="bm-mail-calendar-card card mb-3" id="bmMailCalendarCard"
	 data-mail-id="{$mailID}"
	 data-sid="{$sid}"
	{if $calendarInviteCard.type == 'invite' && !empty($calendarInviteCard.attachment)}
	 data-calendar-attachment="{$calendarInviteCard.attachment}"
	{/if}
	{if $calendarInviteCard.type == 'reply' && $calendarInviteCard.dateID > 0}
	 data-calendar-date-id="{$calendarInviteCard.dateID}"
	{/if}>
	<div class="card-body">
		{if $calendarInviteCard.type == 'invite'}
		{assign var=ev value=$calendarInviteCard.event}
		<div class="bm-mail-calendar-card-header d-flex gap-3 align-items-center mb-3">
			<span class="avatar avatar-xl bg-azure-lt text-azure bm-mail-calendar-avatar flex-shrink-0">
				<i class="ti ti-calendar-event icon" aria-hidden="true"></i>
			</span>
			<div class="flex-fill min-w-0">
				<div class="text-secondary small text-uppercase fw-semibold">{lng p="mail_att_invite_heading"}</div>
				<h3 class="h4 mb-1 text-truncate">{if $ev.title}{text value=$ev.title}{else}{lng p="calendar"}{/if}</h3>
				<div class="text-secondary small">
					{if $ev.wholeDay}
						{date timestamp=$ev.startdate dayonly=true}
						{if $ev.enddate > $ev.startdate + 43200} – {date timestamp=$ev.enddate dayonly=true}{/if}
					{else}
						{date timestamp=$ev.startdate nice=true} – {date timestamp=$ev.enddate nice=true}
					{/if}
					{if $ev.location}<span class="ms-1">· {text value=$ev.location}</span>{/if}
				</div>
				{if !empty($ev.organizer_cn) || !empty($ev.organizer_email)}
				<div class="text-secondary small mt-1">{lng p="from"}: {if !empty($ev.organizer_cn)}{text value=$ev.organizer_cn}{elseif !empty($ev.organizer_email)}{email value=$ev.organizer_email}{/if}</div>
				{/if}
			</div>
		</div>

		{if !empty($ev.text)}
		<p class="text-secondary small mb-3">{text value=$ev.text cut=500}</p>
		{/if}

		{if !isset($calendarInviteCard.canReply) || $calendarInviteCard.canReply}
		<div class="mb-3">
			<label class="form-label" for="bmMailRsvpComment">{lng p="mail_att_rsvp_comment"}</label>
			<textarea class="form-control" id="bmMailRsvpComment" rows="2" maxlength="2000"></textarea>
		</div>

		<div class="bm-mail-calendar-rsvp-actions d-flex flex-wrap gap-2">
			<button type="button" class="btn btn-success bm-mail-rsvp-btn" data-partstat="accepted">
				<i class="ti ti-check icon me-1" aria-hidden="true"></i>{lng p="mail_att_rsvp_accept"}
			</button>
			<button type="button" class="btn btn-outline-danger bm-mail-rsvp-btn" data-partstat="declined">
				<i class="ti ti-x icon me-1" aria-hidden="true"></i>{lng p="mail_att_rsvp_decline"}
			</button>
			<button type="button" class="btn btn-outline-secondary bm-mail-rsvp-btn" data-partstat="tentative">
				<i class="ti ti-help icon me-1" aria-hidden="true"></i>{lng p="mail_att_rsvp_tentative"}
			</button>
		</div>
		{/if}
		{else}
		<div class="bm-mail-calendar-card-header d-flex gap-3 align-items-center">
			<span class="avatar avatar-xl bm-mail-calendar-avatar bm-mail-calendar-avatar--{$calendarInviteCard.partstat|default:'tentative'} flex-shrink-0">
				{if $calendarInviteCard.partstat == 'accepted'}
					<i class="ti ti-circle-check-filled icon icon-filled" aria-hidden="true"></i>
				{elseif $calendarInviteCard.partstat == 'declined'}
					<i class="ti ti-circle-x-filled icon icon-filled" aria-hidden="true"></i>
				{else}
					<i class="ti ti-help-circle-filled icon icon-filled" aria-hidden="true"></i>
				{/if}
			</span>
			<div class="flex-fill">
				<p class="mb-2">{text value=$calendarInviteCard.message}</p>
				{if $calendarInviteCard.dateID > 0}
				<a class="btn btn-sm btn-outline-primary bm-mail-open-calendar-date" href="organizer.calendar.php?action=showDate&amp;id={$calendarInviteCard.dateID}&amp;sid={$sid}">
					<i class="ti ti-calendar icon me-1" aria-hidden="true"></i>{lng p="mail_att_reply_open_event"}
				</a>
				{/if}
			</div>
		</div>
		{/if}
	</div>
</div>
