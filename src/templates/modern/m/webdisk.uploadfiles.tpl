<div data-role="header">
	<h1>{lng p="uploadfiles"}</h1>
</div>

<div data-role="content">
	<form action="webdisk.php?do=uploadFiles&folder={$folderID}&sid={$sid}" data-ajax="false" enctype="multipart/form-data" method="post">
		<div data-role="fieldcontain">
			<label for="file1">{lng p="file"} 1:</label>
			<input type="file" name="file1" id="file1" />
		</div>
		<div data-role="fieldcontain">
			<label for="file2">{lng p="file"} 2:</label>
			<input type="file" name="file2" id="file2" />
		</div>
		<div data-role="fieldcontain">
			<label for="file3">{lng p="file"} 3:</label>
			<input type="file" name="file3" id="file3" />
		</div>
		<button type="submit" data-icon="check" data-theme="b">{lng p="ok"}</button>
		<a data-role="button" href="webdisk.php?folder={$folderID}&sid={$sid}" data-rel="back">{lng p="cancel"}</a>
	</form>
</div>
