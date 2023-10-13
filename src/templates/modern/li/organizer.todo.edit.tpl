<div id="contentHeader">
	<div class="left">
		<i class="fa fa-tasks" aria-hidden="true"></i>
		{if isset($task)}{lng p="edittask"}{else}{lng p="addtask"}{/if}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
<form name="f1" method="post" action="organizer.todo.php?action={if isset($task)}saveTask&id={$task.id}{else}createTask{/if}&sid={$sid}" onsubmit="return(checkTodoForm(this));">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {if isset($task)}{lng p="edittask"}{else}{lng p="addtask"}{/if}</th>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="priority">{lng p="tasklist"}:</label></td>
			<td class="listTableRight">
				<select name="taskListID" id="taskListID">
					{foreach from=$taskLists item=taskList}
					<option value="{$taskList.tasklistid}"{if (!isset($task)&&$taskListID==$taskList.tasklistid)||(isset($task)&&$task.tasklistid==$taskList.tasklistid)} selected="selected"{/if}>{text value=$taskList.title}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="titel">{lng p="title"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="titel" id="titel" value="{if isset($task.titel)}{text value=$task.titel allowEmpty=true}{/if}" style="width:100%;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="begin"}:</td>
			<td class="listTableRight">
				{html_select_date prefix="beginn" time=$task.beginn|default:0 end_year="+5" start_year="-5" field_order="DMY" field_separator="."}, 
				{html_select_time prefix="beginn" time=$task.beginn|default:0 display_seconds=false}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="due"}:</td>
			<td class="listTableRight">
				{html_select_date prefix="faellig" time=$task.faellig|default:0 end_year="+5" start_year="-5" field_order="DMY" field_separator="."}, 
				{html_select_time prefix="faellig" time=$task.faellig|default:0 display_seconds=false}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="erledigt">{lng p="done"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="erledigt" id="erledigt" value="{if isset($task)}{$task.erledigt}{else}0{/if}" size="5" /> %
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="akt_status">{lng p="status"}:</label></td>
			<td class="listTableRight">
				<select name="akt_status" id="akt_status">
					<option value="16"{if isset($task) && $task.akt_status==16} selected="selected"{/if}>{lng p="taskst_16"}</option>
					<option value="32"{if isset($task) && $task.akt_status==32} selected="selected"{/if}>{lng p="taskst_32"}</option>
					<option value="64"{if isset($task) && $task.akt_status==64} selected="selected"{/if}>{lng p="taskst_64"}</option>
					<option value="128"{if isset($task) && $task.akt_status==128} selected="selected"{/if}>{lng p="taskst_128"}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="priority">{lng p="priority"}:</label></td>
			<td class="listTableRight">
				<select name="priority" id="priority">
					<option value="1"{if isset($task) && $task.priority==1} selected="selected"{/if}>{lng p="prio_1"}</option>
					<option value="0"{if !isset($task) || $task.priority==0} selected="selected"{/if}>{lng p="prio_0"}</option>
					<option value="-1"{if isset($task) && $task.priority==-1} selected="selected"{/if}>{lng p="prio_-1"}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="comments">{lng p="comment"}:</label></td>
			<td class="listTableRight">
				<textarea class="textInput" name="comments" id="comments">{if isset($task.comments)}{text value=$task.comments allowEmpty=true}{/if}</textarea>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>
</div></div>