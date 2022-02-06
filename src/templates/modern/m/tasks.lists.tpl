<div data-role="header" data-position="fixed">
	<h1>{$pageTitle}</h1>
</div>

<div data-role="content">
	<ul data-role="listview" data-filter="true" data-filter-placeholder="{lng p="search"}...">	
	{foreach from=$lists key=taskListID item=taskList}
		<li>
			<a href="tasks.php?list={$taskListID}&sid={$sid}" data-transition="slide">{text value=$taskList.title}</a>
		</li>
	{/foreach}
	</ul>
</div>

