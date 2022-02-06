<div data-role="header">
	<h1>{lng p="createfolder"}</h1>
</div>

<div data-role="content">
	<form action="webdisk.php?do=createFolder&folder={$folderID}&sid={$sid}" method="post">
		<div data-role="fieldcontain">
			<label for="title">{lng p="title"}:</label>
			<input type="text" name="title" id="title" value=""  />
		</div>
		<button type="submit" data-icon="check" data-theme="b">{lng p="ok"}</button>
		<a data-role="button" href="webdisk.php?folder={$folderID}&sid={$sid}" data-rel="back">{lng p="cancel"}</a>
	</form>
</div>
