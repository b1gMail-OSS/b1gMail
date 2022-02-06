<form action="prefs.email.php?action=antivirus&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="clamintegration"}</legend>
	
		<table>
			<tr>
				<td align="left" rowspan="3" valign="top" width="40"><img src="{$tpldir}images/antivirus.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="enable"}?</td>
				<td class="td2"><input name="use_clamd"{if $bm_prefs.use_clamd=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="host"}:</td>
				<td class="td2"><input type="text" name="clamd_host" value="{$bm_prefs.clamd_host}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="port"}:</td>
				<td class="td2"><input type="text" name="clamd_port" value="{$bm_prefs.clamd_port}" size="6" /></td>
			</tr>
		</table>
		
		<p>
			<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
			{lng p="clamwarning"}
		</p>
	</fieldset>
	
	<p>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
