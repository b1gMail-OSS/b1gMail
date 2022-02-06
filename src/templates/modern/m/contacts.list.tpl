<div data-role="header" data-position="fixed">
	<h1>{$pageTitle}</h1>
</div>

<div data-role="content">
	<ul data-role="listview" data-filter="true" data-filter-placeholder="{lng p="search"}...">	
	{foreach from=$list item=addresses key=letter}
		<li data-role="list-divider">{$letter}</li>
		{foreach from=$addresses key=addressID item=address}
			<li>
				<a href="contacts.php?action=show&id={$addressID}&sid={$sid}" data-transition="slide">
					{if !$address.vorname&&!$address.nachname&&$address.firma}
					<strong>{text value=$address.firma}</strong>
					{else}
					{text value=$address.vorname}
					<strong>{text value=$address.nachname}</strong>
					{/if}
				</a>
			</li>
		{/foreach}
	{/foreach}
	</ul>
</div>

