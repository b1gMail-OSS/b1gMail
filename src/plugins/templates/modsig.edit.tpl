<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="{$pageURL}&action=edit&id={$sig.signatureid}&save=true&sid={$sid}" method="post" onsubmit="spin(this);">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="../plugins/templates/images/modsig_sig32.png" border="0" alt="" width="32" height="32" /></td>
				<td colspan="2" style="border: 1px solid #DDDDDD;background-color:#FFFFFF;">
					<textarea name="text" id="text" class="plainTextArea" style="width:100%;height:120px;font-family:courier;">{text value=$sig.text allowEmpty=true}</textarea>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modsig_html"}?</td>
				<td class="td2"><input type="checkbox" name="html"{if $sig.html} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1" width="160">{lng p="weight"}:</td>
				<td class="td2"><input type="text" name="weight" size="4" value="{$sig.weight}" />%</td>
			</tr>
			<tr>
				<td class="td1">{lng p="groups"}:</td>
				<td class="td2">
					<input type="checkbox" name="groups[]" value="*" id="group_all"{if $sig.groups=='*'} checked="checked"{/if} />
						<label for="group_all"><b>{lng p="all"}</b></label>
					{foreach from=$groups item=group key=groupID}
						<input type="checkbox" name="groups[]" value="{$groupID}" id="group_{$groupID}"{if $group.checked} checked="checked"{/if} />
							<label for="group_{$groupID}"><b>{text value=$group.title}</b></label><br />
					{/foreach}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="paused"}?</td>
				<td class="td2"><input type="checkbox" name="paused"{if $sig.paused} checked="checked"{/if} /></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</p>
	</form>
</fieldset>