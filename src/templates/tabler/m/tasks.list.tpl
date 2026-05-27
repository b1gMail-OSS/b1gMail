<div data-role="header" data-position="fixed">
	<a href="tasks.php?action=lists&sid={$sid}" data-icon="arrow-l" data-direction="reverse" data-transition="slide">{lng p="tasklists"}</a>
	<h1>{$pageTitle}</h1>
	<a href="tasks.php?action=add&list={$taskListID}&sid={$sid}" data-rel="dialog" data-role="button" data-icon="plus">{lng p="add"}</a>
</div>

<div data-role="content">
	<ul data-role="listview" data-filter="true" data-filter-placeholder="{lng p="search"}...">	
	{foreach from=$list key=taskID item=task}
		<li>
			<div class="listCheckbox">
				<label class="form-check mb-0"><input type="checkbox" class="form-check-input" name="t{$taskID}" íd="taskDone_{$taskID}"{if $task.akt_status==64} checked="checked"{/if} /></label>
			</div>
			<a href="tasks.php?action=edit&id={$taskID}&sid={$sid}" class="listCheckboxText" data-transition="slide">{text value=$task.titel}</a>
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
