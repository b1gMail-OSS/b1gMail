<div data-role="header" data-position="fixed">
	<h1>{$pageTitle}</h1>
</div>

<div data-role="content">
	{if $dates}
	<ul data-role="listview" data-filter="true" data-filter-placeholder="{lng p="search"}...">	
	{foreach from=$dates item=date}
		<li>
			<a href="calendar.php?action=show&id={$date.id}&start={$date.startdate}&end={$date.enddate}&sid={$sid}" data-transition="slide">
				{text value=$date.title}
				<p class="ui-li-aside">{if $date.flags&1}{date timestamp=$date.startdate dayonly=true}{else}{date timestamp=$date.startdate nice=true}{/if}</p>
			</a>
		</li>
	{/foreach}
	</ul>
	{else}
	{lng p="nodatesin6m"}
	{/if}
</div>

