<div data-role="header">
	<h1>{lng p="addtask"}</h1>
</div>

<div data-role="content">
	<form action="tasks.php?do=add&list={$taskListID}&sid={$sid}" method="post">
		<div data-role="fieldcontain">
			<label for="title">{lng p="title"}:</label>
			<input type="text" name="titel" id="title" value=""  />
		</div>
		<button type="submit" data-icon="check" data-theme="b">{lng p="ok"}</button>
		<a data-role="button" href="tasks.php?list={$taskListID}&sid={$sid}" data-rel="back">{lng p="cancel"}</a>
	</form>
</div>
