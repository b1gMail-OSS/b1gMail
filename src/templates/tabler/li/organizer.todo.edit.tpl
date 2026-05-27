<div class="bm-organizer-page bm-organizer-form-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-list-check icon icon-sm" aria-hidden="true"></i>
			{if isset($task)}{lng p="edittask"}{else}{lng p="addtask"}{/if}
		</div>
	</div>

	<form name="f1" method="post" action="organizer.todo.php?action={if isset($task)}saveTask&id={$task.id}{else}createTask{/if}&sid={$sid}" class="card bm-organizer-form-card" onsubmit="return(checkTodoForm(this));">
		<div class="card-body">
			<h3 class="card-title mb-4">{if isset($task)}{lng p="edittask"}{else}{lng p="addtask"}{/if}</h3>

			<div class="row g-3">
				<div class="col-md-6">
					<label class="form-label required" for="taskListID">{lng p="tasklist"}</label>
					<select class="form-select" name="taskListID" id="taskListID">
						{foreach from=$taskLists item=taskList}
						<option value="{$taskList.tasklistid}"{if (!isset($task)&&$taskListID==$taskList.tasklistid)||(isset($task)&&$task.tasklistid==$taskList.tasklistid)} selected="selected"{/if}>{text value=$taskList.title}</option>
						{/foreach}
					</select>
				</div>

				<div class="col-md-6">
					<label class="form-label required" for="titel">{lng p="title"}</label>
					<input type="text" class="form-control" name="titel" id="titel" value="{if isset($task.titel)}{text value=$task.titel allowEmpty=true}{/if}" />
				</div>

				<div class="col-md-6">
					<label class="form-label">{lng p="begin"}</label>
					<div class="bm-organizer-datetime">
						{html_select_date prefix="beginn" time=$task.beginn|default:0 end_year="+5" start_year="-5" field_order="DMY" field_separator="."},
						{html_select_time prefix="beginn" time=$task.beginn|default:0 display_seconds=false}
					</div>
				</div>

				<div class="col-md-6">
					<label class="form-label">{lng p="due"}</label>
					<div class="bm-organizer-datetime">
						{html_select_date prefix="faellig" time=$task.faellig|default:0 end_year="+5" start_year="-5" field_order="DMY" field_separator="."},
						{html_select_time prefix="faellig" time=$task.faellig|default:0 display_seconds=false}
					</div>
				</div>

				<div class="col-md-4">
					<label class="form-label required" for="erledigt">{lng p="done"}</label>
					<div class="input-group">
						<input type="text" class="form-control" name="erledigt" id="erledigt" value="{if isset($task)}{$task.erledigt}{else}0{/if}" />
						<span class="input-group-text">%</span>
					</div>
				</div>

				<div class="col-md-4">
					<label class="form-label" for="akt_status">{lng p="status"}</label>
					<select class="form-select" name="akt_status" id="akt_status">
						<option value="16"{if isset($task) && $task.akt_status==16} selected="selected"{/if}>{lng p="taskst_16"}</option>
						<option value="32"{if isset($task) && $task.akt_status==32} selected="selected"{/if}>{lng p="taskst_32"}</option>
						<option value="64"{if isset($task) && $task.akt_status==64} selected="selected"{/if}>{lng p="taskst_64"}</option>
						<option value="128"{if isset($task) && $task.akt_status==128} selected="selected"{/if}>{lng p="taskst_128"}</option>
					</select>
				</div>

				<div class="col-md-4">
					<label class="form-label" for="priority">{lng p="priority"}</label>
					<select class="form-select" name="priority" id="priority">
						<option value="1"{if isset($task) && $task.priority==1} selected="selected"{/if}>{lng p="prio_1"}</option>
						<option value="0"{if !isset($task) || $task.priority==0} selected="selected"{/if}>{lng p="prio_0"}</option>
						<option value="-1"{if isset($task) && $task.priority==-1} selected="selected"{/if}>{lng p="prio_-1"}</option>
					</select>
				</div>

				<div class="col-12">
					<label class="form-label" for="comments">{lng p="comment"}</label>
					<textarea class="form-control" name="comments" id="comments" rows="5">{if isset($task.comments)}{text value=$task.comments allowEmpty=true}{/if}</textarea>
				</div>
			</div>

			<div class="btn-list mt-4">
				<button type="submit" class="btn btn-primary">{lng p="ok"}</button>
				<button type="reset" class="btn">{lng p="reset"}</button>
			</div>
		</div>
	</form>
</div>
