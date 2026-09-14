<div class="bm-organizer-page bm-organizer-form-page">
	<form name="f2" method="post" action="{sessionurl file='organizer.calendar.php' params="action=calendars&do={if $calendarItem}save&id={$calendarItem.id}{else}add{/if}"}" class="bm-organizer-form" onsubmit="return(checkCalendarGroupForm(this));">
		{csrffield}
		<div id="contentHeader" class="bm-compose-header">
			<div class="left">
				<span class="bm-compose-header-title">{if $calendarItem}{lng p="editcalendar"}{else}{lng p="addcalendar"}{/if}</span>
			</div>
		</div>
		<div class="bm-organizer-form-body">
			<div class="mb-3">
				<label class="form-label required" for="title">{lng p="title"}</label>
				<input type="text" class="form-control" name="title" id="title" value="{if isset($calendarItem.title)}{text value=$calendarItem.title allowEmpty=true}{/if}" />
			</div>
			<div class="mb-3">
				<label class="form-label">{lng p="color"}</label>
				<div class="d-flex flex-wrap gap-3">
					{section name=c loop=6}
					<label class="form-check mb-0">
						<input type="radio" class="form-check-input" name="color" value="{$smarty.section.c.index}"{if (!$calendarItem && $smarty.section.c.index==0) || (isset($calendarItem.color) && $calendarItem.color==$smarty.section.c.index)} checked="checked"{/if} />
						<span class="calendarDate_{$smarty.section.c.index}" style="display:inline-block;width:12px;height:12px;margin-left:4px;"></span>
					</label>
					{/section}
				</div>
			</div>
			<button type="submit" class="btn btn-primary">{lng p="ok"}</button>
		</div>
	</form>
</div>
