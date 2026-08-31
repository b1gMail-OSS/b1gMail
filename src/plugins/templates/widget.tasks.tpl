<div class="innerWidget" style="max-height: 150px; overflow-y: auto;">
<table width="100%" cellspacing="0" cellpadding="0">
{foreach from=$bmwidget_tasks_items key=taskID item=task}
	<tr>
		<td><input type="checkbox" onclick="setTaskDone('', {$taskID}, this.checked);"{if $task.akt_status==64} checked="checked"{/if} />
		<a href="{sessionurl file='organizer.todo.php' params="action=editTask&id={$taskID}"}">{text value=$task.titel cut=30}</a></td>
		<td align="right">
		{if $task.priority==1}<i class="fa fa-exclamation" aria-hidden="true"></i>{/if}
		</td>
	</tr>
{/foreach}
</table>
</div>