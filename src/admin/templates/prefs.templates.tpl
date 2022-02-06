<fieldset>
	<legend>{lng p="templates"}</legend>

	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th>{lng p="template"}</th>
			<th>{lng p="author"}</th>
			<th width="60">&nbsp;</th>
		</tr>
		
		{foreach from=$templates item=templateInfo key=template}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/template.png" border="0" alt="" width="16" height="16" /></td>
			<td>{text value=$templateInfo.title}<br /><small>{text value=$template}</small></td>
			<td>{text value=$templateInfo.author}<br /><small>{text value=$templateInfo.website allowEmpty=true}</small></td>
			<td>
				{if $templateInfo.prefs}<a href="prefs.templates.php?do=prefs&template={$template}&sid={$sid}"><img src="{$tpldir}images/prefs.png" border="0" alt="{lng p="prefs"}" width="16" height="16" /></a>{/if}
			</td>
		</tr>
		{/foreach}
	</table>
</fieldset>

<fieldset>
	<legend>{lng p="defaultemplate"}</legend>
	
	<form action="prefs.templates.php?save=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/template32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="template"}:</td>
				<td class="td2"><select name="template">
				{foreach from=$templates item=templateInfo key=template}
					<option value="{$template}"{if $defaultTemplate==$template} selected="selected"{/if}>{text value=$templateInfo.title}</option>
				{/foreach}
				</select></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</p>
	</form>
</fieldset>
