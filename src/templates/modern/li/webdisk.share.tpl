<div id="contentHeader">
	<div class="left">
		<i class="fa fa-share-square-o" aria-hidden="true"></i>
		{lng p="webdisk"}-{lng p="sharing"} ({text value=$folderName})
	</div>
</div>

<div class="scrollContainer"><div class="pad">
<form action="webdisk.php?action=saveShareSettings&folder={$folderID}&id={$id}&sid={$sid}" method="post">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="sharing"}</th>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="folder"}:</td>
			<td class="listTableRight">{text value=$folderName}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="shareFolder">{lng p="share"}:</label></td>
			<td class="listTableRight"><input type="checkbox" name="shareFolder" id="shareFolder" {if $folderShared}checked="checked" {/if}/></td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="password">{lng p="password"}:</label></td>
			<td class="listTableRight"><input type="password" id="password" name="sharePW" value="{text value=$folderPW allowEmpty=true}" size="30" /></td>
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
