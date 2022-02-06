<form action="prefs.email.php?action=receive&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="recvmethod"}</legend>
		
		<table>
			<tr>
				<td width="40" valign="top" rowspan="1"><img src="{$tpldir}images/ico_prefs_receiving.png" border="0" alt="" width="32" height="32" /></td>				
				<td class="td1" width="220">{lng p="recvmethod"}:</td>
				<td class="td2"><select name="receive_method">
					<option value="pop3"{if $bm_prefs.receive_method=='pop3'} selected="selected"{/if}>{lng p="pop3gateway"}</option>
					<option value="pipe"{if $bm_prefs.receive_method=='pipe'} selected="selected"{/if}>{lng p="pipeetc"}</option>
				</select></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="pop3gateway"}</legend>
		
		<table>
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/ico_prefs_login.png" border="0" alt="" width="32" height="32" /></td>				
				<td class="td1" width="220">{lng p="pop3host"}:</td>
				<td class="td2"><input type="text" name="pop3_host" value="{text allowEmpty=true value=$bm_prefs.pop3_host}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="pop3port"}:</td>
				<td class="td2"><input type="text" name="pop3_port" value="{$bm_prefs.pop3_port}" size="6" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="pop3user"}:</td>
				<td class="td2"><input type="text" name="pop3_user" value="{text allowEmpty=true value=$bm_prefs.pop3_user}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="pop3pass"}:</td>
				<td class="td2"><input type="password" autocomplete="off" name="pop3_pass" value="{text allowEmpty=true value=$bm_prefs.pop3_pass}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="fetchcount"}:</td>
				<td class="td2"><input type="text" name="fetchcount" value="{$bm_prefs.fetchcount}" size="6" /></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="miscprefs"}</legend>
		
		<table>
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/ico_prefs_misc.png" border="0" alt="" width="32" height="32" /></td>				
				<td class="td1" width="220">{lng p="recpdetection"}:</td>
				<td class="td2"><select name="recipient_detection">
					<option value="static"{if $bm_prefs.recipient_detection=='static'} selected="selected"{/if}>{lng p="rd_static"}</option>
					<option value="dynamic"{if $bm_prefs.recipient_detection=='dynamic'} selected="selected"{/if}>{lng p="rd_dynamic"}</option>
				</select></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="errormail"}?</td>
				<td class="td2"><select name="errormail">
					<option value="yes"{if $bm_prefs.errormail=='yes'} selected="selected"{/if}>{lng p="yes"}</option>
					<option value="no"{if $bm_prefs.errormail=='no'} selected="selected"{/if}>{lng p="no"}</option>
					<option value="soft"{if $bm_prefs.errormail=='soft'} selected="selected"{/if}>{lng p="errormail_soft"}</option>
				</select></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="detectduplicates"}?</td>
				<td class="td2"><input name="detect_duplicates"{if $bm_prefs.detect_duplicates=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="returnpathcheck"}?</td>
				<td class="td2"><input name="returnpath_check"{if $bm_prefs.returnpath_check=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="failure_forward"}?</td>
				<td class="td2"><input name="failure_forward"{if $bm_prefs.failure_forward=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1" width="220">{lng p="mailmax"}:</td>
				<td class="td2"><input type="text" name="mailmax" value="{$bm_prefs.mailmax/1024}" size="6" /> KB</td>
			</tr>
		</table>
	</fieldset>
	
	<p>
		<div style="float:right;" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />&nbsp;
		</div>
	</p>
</form>
