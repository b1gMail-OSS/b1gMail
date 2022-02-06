<div data-role="header" data-position="fixed">
	<a href="calendar.php?sid={$sid}" data-icon="arrow-l" data-direction="reverse" data-transition="slide">{lng p="calendar"}</a>
	<h1>{$pageTitle}</h1>
</div>

<div data-role="content">
	<ul data-role="listview" data-inset="true">
		<li>
			<strong>{lng p="begin"}:</strong><br />
			{if ($date.flags&1)}{date timestamp=$date.startdate dayonly=true} ({lng p="wholeday"}){else}{date timestamp=$date.startdate nice=true elapsed=true}{/if}
				{if $date.orig_startdate}<small> ({lng p="thisevent"} {if ($date.flags&1)}{date timestamp=$date.orig_startdate dayonly=true}{else}{date timestamp=$date.orig_startdate nice=true}{/if})</small>{/if}
		</li>
		<li>
			<strong>{lng p="end"}:</strong><br />
			{if ($date.flags&1)}{date timestamp=$date.enddate dayonly=true} ({lng p="wholeday"}){else}{date timestamp=$date.enddate nice=true elapsed=true}{/if}
				{if $date.orig_enddate}<small> ({lng p="thisevent"} {if ($date.flags&1)}{date timestamp=$date.orig_enddate dayonly=true}{else}{date timestamp=$date.orig_enddate nice=true}{/if})</small>{/if}
		</li>
		{if $date.location}<li>
			<strong>{lng p="location"}:</strong><br />
			{text value=$date.location}
		</li>{/if}
		<li>
			<strong>{lng p="reminder"}:</strong><br />
			{if ($date.flags&2)||($date.flags&4)}{lng p="yes"}{else}{lng p="no"}{/if}
		</li>
		<li>
			<strong>{lng p="repeating"}:</strong><br />
			{if $date.repeat_flags!=0}{lng p="yes"}{else}{lng p="no"}{/if}
		</li>
	</ul>

	{if $attendees}
	<h2>{lng p="attendees"}</h2>
	<ul data-role="listview" data-inset="true">
	{foreach from=$attendees item=person}
		<li>
			{text value=$person.vorname} <strong>{text value=$person.nachname}</strong>
		</li>
	{/foreach}	
	</ul>
	<a href="email.php?action=compose&to={$mailTo}&subject={$mailSubject}&sid={$sid}" data-role="button">{lng p="mailattendees"}</a>
	{/if}

	{if $notes}
	<h2>{lng p="notes"}</h2>
	<ul data-role="listview" data-inset="true">
		<li>{$notes}</li>
	</ul>
	{/if}
</div>
