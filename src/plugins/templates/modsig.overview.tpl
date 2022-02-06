<fieldset>
	<legend>{lng p="modsig_signatures"}</legend>
	
	<form action="{$pageURL}&sid={$sid}&do=massAction" method="post" name="f1" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'sigs[]');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="modsig_signature"}</th>
			<th width="80">{lng p="modsig_html"}?</th>
			<th width="75">{lng p="weight"}</th>
			<th width="70">{lng p="modsig_used"}</th>
			<th width="60">&nbsp;</th>
		</tr>
		
		{foreach from=$signatures item=sig}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="../plugins/templates/images/modsig_sig.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center"><input type="checkbox" name="sigs[]" value="{$sig.signatureid}" /></td>
			<td>{$sig.displayText}</td>
			<td><input type="checkbox" disabled="disabled"{if $sig.html} checked="checked"{/if} /></td>
			<td>{$sig.weight}%</td>
			<td>{$sig.counter}</td>
			<td>
				<a href="{$pageURL}&{if !$sig.paused}de{/if}activate={$sig.signatureid}&sid={$sid}"><img src="{$tpldir}images/{if !$sig.paused}ok{else}error{/if}.png" width="16" height="16" alt="{if $sig.paused}{lng p="continue"}{else}{lng p="pause"}{/if}" border="0" /></a>
				<a href="{$pageURL}&action=edit&id={$sig.signatureid}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="{$pageURL}&delete={$sig.signatureid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="8">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" class="smallInput">
						<option value="-">------------</option>
						
						<optgroup label="{lng p="actions"}">
							<option value="pause">{lng p="pause"}</option>
							<option value="continue">{lng p="continue"}</option>
							<option value="delete">{lng p="delete"}</option>
						</optgroup>
					</select>&nbsp;
				</div>
				<div style="float:left;">
					<input type="submit" name="executeMassAction" value=" {lng p="execute"} " class="smallInput" />
				</div>
			</td>
		</tr>
	</table>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="modsig_add"}</legend>
	
	<form action="{$pageURL}&add=true&sid={$sid}" method="post" onsubmit="spin(this);">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="../plugins/templates/images/modsig_sig32.png" border="0" alt="" width="32" height="32" /></td>
				<td colspan="2" style="border: 1px solid #DDDDDD;background-color:#FFFFFF;">
					<textarea name="text" id="text" class="plainTextArea" style="width:100%;height:120px;font-family:courier;"></textarea>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modsig_html"}?</td>
				<td class="td2"><input type="checkbox" name="html" /></td>
			</tr>
			<tr>
				<td class="td1" width="160">{lng p="weight"}:</td>
				<td class="td2"><input type="text" name="weight" size="4" value="100" />%</td>
			</tr>
			<tr>
				<td class="td1">{lng p="groups"}:</td>
				<td class="td2">
					<input type="checkbox" name="groups[]" value="*" id="group_all" checked="checked" />
						<label for="group_all"><b>{lng p="all"}</b></label>
					{foreach from=$groups item=group key=groupID}
						<input type="checkbox" name="groups[]" value="{$groupID}" id="group_{$groupID}" />
							<label for="group_{$groupID}"><b>{text value=$group.title}</b></label><br />
					{/foreach}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="paused"}?</td>
				<td class="td2"><input type="checkbox" name="paused" /></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>