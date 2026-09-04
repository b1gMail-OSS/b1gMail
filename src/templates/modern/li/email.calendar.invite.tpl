<div class="bm-mail-calendar-card" id="bmMailCalendarCard"
	 data-mail-id="{$mailID}"
	 data-sid="{$sid}"
	{if $calendarInviteCard.type == 'invite' && !empty($calendarInviteCard.attachment)}
	 data-calendar-attachment="{$calendarInviteCard.attachment}"
	{/if}
	{if $calendarInviteCard.type == 'reply' && $calendarInviteCard.dateID > 0}
	 data-calendar-date-id="{$calendarInviteCard.dateID}"
	{/if}>
	<fieldset>
		<legend>{if $calendarInviteCard.type == 'invite'}{lng p="mail_att_invite_heading"}{else}{lng p="calendar"}{/if}</legend>
		{if $calendarInviteCard.type == 'invite'}
		{assign var=ev value=$calendarInviteCard.event}
		<div class="bm-mail-calendar-card-header">
			<span class="bm-mail-calendar-avatar" aria-hidden="true">
				<i class="fa fa-calendar" aria-hidden="true"></i>
			</span>
			<div class="bm-mail-calendar-card-heading">
				<h3 class="bm-mail-calendar-title">{if $ev.title}{text value=$ev.title}{else}{lng p="calendar"}{/if}</h3>
				<div class="bm-mail-calendar-when">
					{if $ev.wholeDay}
						{date timestamp=$ev.startdate dayonly=true}
						{if $ev.enddate > $ev.startdate + 43200} – {date timestamp=$ev.enddate dayonly=true}{/if}
					{else}
						{date timestamp=$ev.startdate nice=true} – {date timestamp=$ev.enddate nice=true}
					{/if}
					{if $ev.location}<span class="bm-mail-calendar-loc"> · {text value=$ev.location}</span>{/if}
				</div>
				{if !empty($ev.organizer_cn) || !empty($ev.organizer_email)}
				<div class="bm-mail-calendar-organizer">{lng p="from"}: {if !empty($ev.organizer_cn)}{text value=$ev.organizer_cn}{elseif !empty($ev.organizer_email)}{email value=$ev.organizer_email}{/if}</div>
				{/if}
			</div>
		</div>

		{if !empty($ev.text)}
		<p class="bm-mail-calendar-notes">{text value=$ev.text cut=500}</p>
		{/if}

		{if !isset($calendarInviteCard.canReply) || $calendarInviteCard.canReply}
		<p>
			<label for="bmMailRsvpComment"><b>{lng p="mail_att_rsvp_comment"}</b></label><br />
			<textarea id="bmMailRsvpComment" rows="2" maxlength="2000" style="width:100%;box-sizing:border-box;"></textarea>
		</p>

		<div class="bm-mail-calendar-rsvp-actions">
			<button type="button" class="primary bm-mail-rsvp-btn" data-partstat="accepted">
				<i class="fa fa-check" aria-hidden="true"></i> {lng p="mail_att_rsvp_accept"}
			</button>
			<button type="button" class="bm-mail-rsvp-btn" data-partstat="declined">
				<i class="fa fa-times" aria-hidden="true"></i> {lng p="mail_att_rsvp_decline"}
			</button>
			<button type="button" class="bm-mail-rsvp-btn" data-partstat="tentative">
				<i class="fa fa-question-circle" aria-hidden="true"></i> {lng p="mail_att_rsvp_tentative"}
			</button>
		</div>
		{/if}
		{else}
		<div class="bm-mail-calendar-card-header">
			<span class="bm-mail-calendar-avatar bm-mail-calendar-avatar--{$calendarInviteCard.partstat|default:'tentative'}" aria-hidden="true">
				{if $calendarInviteCard.partstat == 'accepted'}
					<i class="fa fa-check-circle" aria-hidden="true"></i>
				{elseif $calendarInviteCard.partstat == 'declined'}
					<i class="fa fa-times-circle" aria-hidden="true"></i>
				{else}
					<i class="fa fa-question-circle" aria-hidden="true"></i>
				{/if}
			</span>
			<div class="bm-mail-calendar-card-heading">
				<p>{text value=$calendarInviteCard.message}</p>
				{if $calendarInviteCard.dateID > 0}
				<a class="bm-mail-open-calendar-date" href="organizer.calendar.php?action=showDate&amp;id={$calendarInviteCard.dateID}">
					<i class="fa fa-calendar" aria-hidden="true"></i> {lng p="mail_att_reply_open_event"}
				</a>
				{/if}
			</div>
		</div>
		{/if}
	</fieldset>
</div>
