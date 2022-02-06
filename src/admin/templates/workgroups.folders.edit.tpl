<form method="post" action="workgroups.php?action=folders&do=edit&id={$folder.id}&save=true&sid={$sid}" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="edit"}: {text value=$folder.titel}</legend>
		
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="2"><img src="{$tpldir}images/workgroup_mail32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="130">{lng p="title"}:</td>
				<td class="td2"><input type="text" name="titel" value="{text value=$folder.titel}" style="width:85%;" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="itemsperpage"}:</td>
				<td class="td2"><input type="text" name="perpage" value="{$folder.perpage}" size="8" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="permissions"}</legend>

		<table class="list">
			<tr>
				<th>{lng p="workgroup"}</th>
				<th style="text-align:center;width:130px;">{lng p="noaccess"}</th>
				<th style="text-align:center;width:130px;">{lng p="readonly"}</th>
				<th style="text-align:center;width:130px;">{lng p="readwrite"}</th>
			</tr>
			
			{foreach from=$groups item=group key=groupID}
			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}">
				<td><img src="{$tpldir}images/ico_workgroup.png" border="0" width="16" height="16" alt="" align="absmiddle" /> {text value=$group.title}</td>
				<td style="text-align:center;"><input type="radio" name="groups[{$group.id}]" value="no" {if !$folder.groups.$groupID} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" name="groups[{$group.id}]" value="ro" {if $folder.groups.$groupID=='ro'} checked="checked"{/if} /></td>
				<td style="text-align:center;"><input type="radio" name="groups[{$group.id}]" value="rw" {if $folder.groups.$groupID=='rw'} checked="checked"{/if} /></td>
			</tr>
			{/foreach}
		</table>
	</fieldset>

	<p>
		<div style="float:left" class="buttons">
			<input class="button" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='workgroups.php?action=folders&sid={$sid}';" />
		</div>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
