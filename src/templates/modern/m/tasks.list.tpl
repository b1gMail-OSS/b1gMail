<div data-role="header" data-position="fixed">
	<a href="{sessionurl file='tasks.php' params='action=lists'}" data-icon="arrow-l" data-direction="reverse" data-transition="slide">{lng p="tasklists"}</a>
	<h1>{$pageTitle}</h1>
	<a href="{sessionurl file='tasks.php' params="action=add&list={$taskListID}"}" data-rel="dialog" data-role="button" data-icon="plus">{lng p="add"}</a>
</div>

<div data-role="content">
	<ul data-role="listview" data-filter="true" data-filter-placeholder="{lng p="search"}...">	
	{foreach from=$list key=taskID item=task}
		<li>
			<div class="listCheckbox">
				<input type="checkbox" name="t{$taskID}" íd="taskDone_{$taskID}"{if $task.akt_status==64} checked="checked"{/if} />
			</div>
			<a href="{sessionurl file='tasks.php' params="action=edit&id={$taskID}"}" class="listCheckboxText" data-transition="slide">{text value=$task.titel}</a>
		</li>
	{/foreach}
	</ul>
</div>

{literal}
<script>
<!--
	initTaskList();
//-->
</script>
{/literal}
