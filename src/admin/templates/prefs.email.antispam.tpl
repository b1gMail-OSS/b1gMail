<form action="prefs.email.php?action=antispam&save=true&sid={$sid}" method="post" onsubmit="spin(this)">	
	<fieldset>
		<legend>{lng p="dnsbl"}</legend>
	
		<table width="90%">
			<tr>
				<td align="left" rowspan="3" valign="top" width="40"><img src="{$tpldir}images/antispam_dnsbl.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="enable"}?</td>
				<td class="td2"><input name="spamcheck"{if $bm_prefs.spamcheck=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="dnsblservers"}:</td>
				<td class="td2">
					<textarea style="width:100%;height:80px;" name="dnsbl">{text value=$bm_prefs.dnsbl allowEmpty=true}</textarea>
					<small>{lng p="sepby"}</small>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="dnsblreq"}:</td>
				<td class="td2"><input type="text" name="dnsbl_requiredservers" value="{$bm_prefs.dnsbl_requiredservers}" size="6" /></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="bayes"}</legend>
	
		<table width="90%">
			<tr>
				<td align="left" rowspan="3" valign="top" width="40"><img src="{$tpldir}images/antispam_bayes.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="enable"}?</td>
				<td class="td2"><input name="use_bayes"{if $bm_prefs.use_bayes=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bayesmode"}:</td>
				<td class="td2"><select name="bayes_mode">
					<option value="local"{if $bm_prefs.bayes_mode=='local'} selected="selected"{/if}>{lng p="bayeslocal"}</option>
					<option value="global"{if $bm_prefs.bayes_mode=='global'} selected="selected"{/if}>{lng p="bayesglobal"}</option>
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="bayesdb"}:</td>
				<td class="td2">{$bayesWordCount} {lng p="entries"} <input{if $bayesWordCount==0} disabled="disabled"{/if} class="button" type="button" value=" {lng p="reset"} " onclick="if(confirm('{lng p="bayesresetq"}')) document.location.href='prefs.email.php?action=antispam&resetBayesDB=true&sid={$sid}';" /></td>
			</tr>
		</table>
	</fieldset>
	
	<p>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
