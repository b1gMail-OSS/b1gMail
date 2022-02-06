<div data-role="header" data-position="fixed">
	<a href="tasks.php?list={$taskListID}&sid={$sid}" data-icon="arrow-l" data-direction="reverse" data-transition="slide">{text value=$taskListTitle}</a>
	<h1>{$pageTitle}</h1>
	<button type="button" onclick="$('#taskForm').submit()" data-icon="check" data-theme="b">{lng p="save"}</button>
</div>

<div data-role="content">
	<form id="taskForm" action="tasks.php?do=save&id={$task.id}&list={$task.tasklistid}&sid={$sid}" method="post">
		<div data-role="fieldcontain">
			<label for="title">{lng p="title"}:</label>
			<input type="text" name="titel" id="title" value="{text value=$task.titel allowEmpty=true}"  />
		</div>

		<div data-role="fieldcontain">
			<label for="akt_status">{lng p="status"}:</label>
			<select name="akt_status" id="akt_status">
				<option value="16"{if $task && $task.akt_status==16} selected="selected"{/if}>{lng p="taskst_16"}</option>
				<option value="32"{if $task && $task.akt_status==32} selected="selected"{/if}>{lng p="taskst_32"}</option>
				<option value="64"{if $task && $task.akt_status==64} selected="selected"{/if}>{lng p="taskst_64"}</option>
				<option value="128"{if $task && $task.akt_status==128} selected="selected"{/if}>{lng p="taskst_128"}</option>
			</select>
		</div>

		<div data-role="fieldcontain">
			<label for="priority">{lng p="priority"}:</label>
			<select name="priority" id="priority">
				<option value="1"{if $task && $task.priority==1} selected="selected"{/if}>{lng p="prio_1"}</option>
				<option value="0"{if !$task || $task.priority==0} selected="selected"{/if}>{lng p="prio_0"}</option>
				<option value="-1"{if $task && $task.priority==-1} selected="selected"{/if}>{lng p="prio_-1"}</option>
			</select>
		</div>

		<h3>{lng p="comment"}</h3>
		<textarea name="comments" style="min-height:120px;">{text value=$task.comments allowEmpty=true}</textarea>
	</form>
</div>
