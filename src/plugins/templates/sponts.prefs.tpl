<fieldset>
	<legend>{lng p="prefs"}</legend>
	
	<form action="{$pageURL}&sid={$sid}&save=true" method="post" onsubmit="spin(this)">
	<table>
		<tr>
			<td align="left" rowspan="5" valign="top" width="40"><img src="../plugins/templates/images/sponts32.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="190">{lng p="sponts_host"}:</td>
			<td class="td2"><input type="text" name="host" value="{text value=$sponts_prefs.host allowEmpty=true}" size="36" /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="sponts_port"}:</td>
			<td class="td2"><input type="text" name="port" value="{text value=$sponts_prefs.port allowEmpty=true}" size="8" /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="sponts_scheme"}:</td>
			<td class="td2"><select name="scheme">
				<option value="0"{if $sponts_prefs.scheme==0} selected="selected"{/if}>CRAM-MD5</option>
				<option value="1"{if $sponts_prefs.scheme==1} selected="selected"{/if}>CRAM-SHA1</option>
			</select></td>
		</tr>
		<tr>
			<td class="td1">{lng p="sponts_login"}:</td>
			<td class="td2"><input type="text" name="login" value="{text value=$sponts_prefs.login allowEmpty=true}" size="36" /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="sponts_password"}:</td>
			<td class="td2"><input type="text" name="password" value="{text value=$sponts_prefs.password allowEmpty=true}" size="36" /></td>
		</tr>
	</table>
	<p>
		<div style="float:right;">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
	</form>
</fieldset>