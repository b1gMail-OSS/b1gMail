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
			<td align="center">{acpicon file="ico_sharedfolder.png"}</td>
			<td><a href="{sessionurl file='workgroups.php' params="action=folders&do=edit&id={$folder.id}"}">{text value=$folder.titel}</a></td>
			<td>
				<a href="{sessionurl file='workgroups.php' params="action=folders&do=edit&id={$folder.id}"}">{acpicon file="edit.png"}</a>
				<a href="{sessionurl file='workgroups.php' params="action=folders&delete={$folder.id}"}" onclick="return confirm('{lng p="realdel"}');">{acpicon file="delete.png"}</a>
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
				<td width="40" valign="top" rowspan="1">{acpicon file="workgroup_mail32.png" size="lg"}</td>
				<td class="td1" width="130">{lng p="title"}:</td>
				<td class="td2"><input type="text" name="titel" value="" style="width:85%;" /></td>
			</tr>
		</table>

		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>
