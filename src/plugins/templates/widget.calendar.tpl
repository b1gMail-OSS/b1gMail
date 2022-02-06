<div class="innerWidget">
	<center>
		{$bmwidget_calendar_html}
	</center>
	
	<div class="clndrWdgtDates">
	{if $bmwidget_calendar_nextDates}
		<ul>
			{foreach from=$bmwidget_calendar_nextDates item=_date}
			<li>
				<span class="date">{date timestamp=$_date.startdate format="%a., %d.%m."}</span>
				<a href="organizer.calendar.php?date={$_date.startdate}&sid={$sid}">{text value=$_date.title cut=35}</a>
			</li>
			{/foreach}
		</ul>
	{else}
		<div style="text-align:center;font-size:10px;font-style:italic;">{lng p="nodatesin31d"}</div>
	{/if}
	</div>
</div>