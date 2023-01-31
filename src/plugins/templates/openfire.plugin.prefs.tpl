<fieldset>
	<legend>{lng p="prefs"}</legend>

	<form action="{$pageURL}&sid={$sid}" name="save" id="save" method="post" onsubmit="spin(this)">
	{if $erfolg}<center>{$erfolg}</center>{/if}
	<table>
		<tr>
			<td align="left" rowspan="7" valign="top" width="48"><img src="../plugins/templates/images/openfire_logo.png" border="0" alt="" /></td>
			<td class="td1" width="220">{lng p="enable"}?</td>
			<td class="td2"><input name="openfire_enableAuth"{if $openfire_prefs.enableAuth} checked="checked"{/if} type="checkbox" /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="openfire_domain"}:</td>
			<td class="td2"><input type="text" name="openfire_domain" value="{if isset($openfire_prefs.domain)}{text value=$openfire_prefs.domain}{/if}" size="36" /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="openfire_port"}:</td>
			<td class="td2"><input type="text" name="openfire_port" value="{if isset($openfire_prefs.port)}{text value=$openfire_prefs.port}{/if}" size="36" /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="openfire_https"}?</td>
			<td class="td2"><input name="openfire_https"{if !empty($openfire_prefs.https)} checked="checked"{/if} type="checkbox" /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="openfire_secretkey"}:</td>
			<td class="td2"><input type="text" name="openfire_userservice_secretkey" value="{if isset($openfire_prefs.secretkey)}{text value=$openfire_prefs.secretkey}{/if}" size="36" /></td>
		</tr>
	</table>
	<p>
		<div style="float:right;">
			<input type="submit" name="save" value=" {lng p="save"} " class="button" />
		</div>
	</p>
	</form>
</fieldset>

<center><p /><font size="1">b1gMail Openfire-Integration &copy; <a href="http://www.sebijk.com" target="_blank" rel="noreferrer">Home of the Sebijk.com</a></font></center>