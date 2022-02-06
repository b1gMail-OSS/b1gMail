<div id="contentHeader">
	<div class="left">
		<i class="fa fa-file-archive-o" aria-hidden="true"></i>
		{lng p="extract"} ({text value=$fileName})
	</div>
</div>

<div class="scrollContainer"><div class="pad">
<form action="webdisk.php?action=doExtractFile&folder={$folder}&id={$id}&sid={$sid}" method="post">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="extract"}</th>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="filename"}:</td>
			<td class="listTableRight">&nbsp;<i class="fa fa-file-archive-o" aria-hidden="true"></i>
										&nbsp;{text value=$fileName}</td>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="targetfolder"}:</td>
			<td class="listTableRight">&nbsp;<i class="fa fa-folder-open-o" aria-hidden="true"></i>
										&nbsp;<a href="webdisk.php?folder={$folder}&sid={$sid}">{text value=$folderName}</a></td>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="existingfiles"}:</td>
			<td class="listTableRight">
				<input type="radio" name="existingFiles" id="keepExistingFiles" checked="checked" value="keep" />
				<label for="keepExistingFiles">{lng p="keep"}</label>
				
				<input type="radio" name="existingFiles" id="overwriteExistingFiles" value="overwrite" />
				<label for="overwriteExistingFiles">{lng p="overwrite"}</label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="zipfile"}:</td>
			<td class="listTableRight"><input type="checkbox" name="deleteAfterExtraction" id="deleteAfterExtraction" />
										<label for="deleteAfterExtraction">{lng p="deleteafterextract"}</label></td>
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
