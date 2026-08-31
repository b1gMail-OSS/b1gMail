<fieldset>
	<legend>{lng p="sharedfolders"}</legend>
	
	<form name="f1" action="{sessionurl file='workgroups.php' params="action=folders"}" method="post">
		{csrffield}
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th>{lng p="title"}</th>
			<th width="70">&nbsp;</th>
		</tr>
		
		{foreach from=$folders item=folder}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/ico_sharedfolder.png" border="0" width="16" height="16" alt="" /></td>
			<td><a href="{sessionurl file='workgroups.php' params="action=folders&do=edit&id={$folder.id}"}">{text value=$folder.titel}</a></td>
			<td>
				<a href="{sessionurl file='workgroups.php' params="action=folders&do=edit&id={$folder.id}"}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="{sessionurl file='workgroups.php' params="action=folders&delete={$folder.id}"}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}
	</table>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="add"}</legend>
	
	<form method="post" action="{sessionurl file='workgroups.php' params="action=folders&create=true"}" onsubmit="spin(this)">
		{csrffield}
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="1"><img src="{$tpldir}images/workgroup_mail32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="130">{lng p="title"}:</td>
				<td class="td2"><input type="text" name="titel" value="" style="width:85%;" /></td>
			</tr>
		</table>

		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>
