{if $success}
<fieldset>
	<legend>{lng p="success"}</legend>
	
	<div align="center" style="color:green;">
		<img src="{$tpldir}images/info.png" align="absmiddle" width="16" height="16" />
		{$success}
	</div>
</fieldset>
{elseif $error}
<fieldset>
	<legend>{lng p="error"}</legend>
	
	<div align="center" style="color:red;">
		<img src="{$tpldir}images/error.png" align="absmiddle" width="16" height="16" />
		{$error}
	</div>
</fieldset>
{/if}

<form action="{$pageURL}&sid={$sid}&action=editor&do=test" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="plzeditor_test"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="4" valign="top" width="40"><img src="../plugins/templates/images/plzeditor_test.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="100">{lng p="country"}:</td>
				<td class="td2"><select name="country">
				{foreach from=$plzFiles item=countryName key=countryID}
					<option value="{$countryID}"{if $countryID==$defaultCountryID} selected="selected"{/if}>{$countryName}</option>
				{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="plzeditor_zip"}:</td>
				<td class="td2"><input type="text" name="zip" value="" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="plzeditor_city"}:</td>
				<td class="td2"><input type="text" name="city" value="" size="32" /></td>
			</tr>
		</table>
		
		<p>
			<div style="float:right">
				<input class="button" type="submit" value=" {lng p="plzeditor_test"} " />
			</div>
		</p>
	</fieldset>
</form>

<form action="{$pageURL}&sid={$sid}&action=editor&do=add" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="plzeditor_add"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="4" valign="top" width="40"><img src="../plugins/templates/images/plzeditor_add.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="100">{lng p="country"}:</td>
				<td class="td2"><select name="country">
				{foreach from=$plzFiles item=countryName key=countryID}
					<option value="{$countryID}"{if $countryID==$defaultCountryID} selected="selected"{/if}>{$countryName}</option>
				{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="plzeditor_zip"}:</td>
				<td class="td2"><input type="text" name="zip" value="" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="plzeditor_city"}:</td>
				<td class="td2"><input type="text" name="city" value="" size="32" /></td>
			</tr>
		</table>
		
		<p>
			<div style="float:right">
				<input class="button" type="submit" value=" {lng p="plzeditor_add"} " />
			</div>
		</p>
	</fieldset>
</form>
