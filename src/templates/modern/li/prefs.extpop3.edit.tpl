<div id="contentHeader">
	<div class="left">
		<i class="fa fa-external-link" aria-hidden="true"></i>
		{if $account}{lng p="editpop3"}{else}{lng p="addpop3"}{/if}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form name="f1" method="post" action="prefs.php?action=extpop3&do={if $account}saveAccount&id={$account.id}{else}createAccount{/if}&sid={$sid}" onsubmit="return checkPOP3AccountForm(this);">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {if $account}{lng p="editpop3"}{else}{lng p="addpop3"}{/if}</th>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="paused">{lng p="paused"}?</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="paused" id="paused"{if $account && $account.paused} checked="checked"{/if} />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="p_host">{lng p="pop3server"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="p_host" id="p_host" value="{text value=$account.p_host allowEmpty=true}" size="48" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="p_port">{lng p="port"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="p_port" id="p_port" value="{if $account}{text value=$account.p_port allowEmpty=true}{else}110{/if}" size="6" />
				<input type="checkbox" name="p_ssl" id="p_ssl"{if $account&&$account.p_ssl} checked="checked"{/if} onclick="if(this.checked&&EBID('p_port').value==110) EBID('p_port').value=995; else if(!this.checked&&EBID('p_port').value==995) EBID('p_port').value=110;" />
				<label for="p_ssl">SSL</label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="p_user">{lng p="username"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="p_user" id="p_user" value="{text value=$account.p_user allowEmpty=true}" size="48" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="p_pass">{lng p="password"}:</label></td>
			<td class="listTableRight">
				<input type="password" name="p_pass" id="p_pass" value="{text value=$account.p_pass allowEmpty=true}" size="24" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="p_target">{lng p="pop3target"}:</label></td>
			<td class="listTableRight">
				<select name="p_target" id="p_target">
					<option value="-1">({lng p="default"})</option>
					
					<optgroup label="{lng p="folders"}">
					{foreach from=$dropdownFolderList key=dFolderID item=dFolderTitle}
					{if $dFolderID>0}<option value="{$dFolderID}" style="font-family:courier;"{if $account && $account.p_target==$dFolderID} selected="selected"{/if}>{$dFolderTitle}</option>{/if}
					{/foreach}
					</optgroup>
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="p_keep">{lng p="keepmails"}?</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="p_keep" id="p_keep"{if $account && $account.p_keep} checked="checked"{/if} />
			</td>
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
