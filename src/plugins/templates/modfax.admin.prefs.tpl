<form action="{$pageURL}&action=prefs&save=true&sid={$sid}" method="post" id="prefsForm" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="common"}</legend>
		
		<table>
			<tr>
				<td class="td1" width="220">{lng p="safecode"}?</td>
				<td class="td2"><input type="checkbox" name="send_safecode"{if $faxPrefs.send_safecode} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_refund_on_error"}?</td>
				<td class="td2"><input type="checkbox" name="refund_on_error"{if $faxPrefs.refund_on_error} checked="checked"{/if} /></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="defaults"}</legend>
		
		<table>
			<tr>
				<td class="td1" width="220">{lng p="modfax_country_prefix"}:</td>
				<td class="td2"><input type="text" name="default_country_prefix" value="{text value=$faxPrefs.default_country_prefix allowEmpty=true}" size="8" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="defaultgateway"}:</td>
				<td class="td2"><select name="default_faxgateid">
				{foreach from=$gateways item=gwTitle key=gwID}
					<option value="{$gwID}"{if $gwID==$faxPrefs.default_faxgateid} selected="selected"{/if}>{text value=$gwTitle}</option>
				{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_fromname"}:</td>
				<td class="td2"><input type="text" name="default_name" value="{text value=$faxPrefs.default_name allowEmpty=true}" size="36" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_fromno"}:</td>
				<td class="td2"><input type="text" name="default_no" value="{text value=$faxPrefs.default_no allowEmpty=true}" size="28" /></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="modfax_perms"}</legend>
		
		<table>
			<tr>
				<td class="td1" width="220">{lng p="modfax_allow_ownname"}?</td>
				<td class="td2"><input type="checkbox" name="allow_ownname"{if $faxPrefs.allow_ownname} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_allow_ownno"}?</td>
				<td class="td2"><input type="checkbox" name="allow_ownno"{if $faxPrefs.allow_ownno} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_allow_pdf"}?</td>
				<td class="td2"><input type="checkbox" name="allow_pdf"{if $faxPrefs.allow_pdf} checked="checked"{/if} /></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="modfax_faxtpl"}</legend>
		
		<table>
		{foreach from=$tplBlocks item=block key=blockID}
			<tr>
				<td class="td1" width="220">{lng p="modfax_block"} {$blockID+1}:</td>
				<td class="td2"><select name="tpl_blocks[{$blockID}]">
					<option value="-1"{if $block==-1} selected="selected"{/if}>--------</option>
					<option value="0"{if $block==0} selected="selected"{/if}>{lng p="modfax_textblock"}</option>
					<option value="1"{if $block==1} selected="selected"{/if}>{lng p="modfax_pagebreak"}</option>
					<option value="2"{if $block==2} selected="selected"{/if}>{lng p="modfax_cover"}</option>
					<option value="3"{if $block==3} selected="selected"{/if}>{lng p="modfax_pdffile"}</option>
				</select></td>
			</tr>
		{/foreach}
		</table>
	</fieldset>
	
	<p>
		<div style="float:right;" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />&nbsp;
		</div>
	</p>
</form>

<form action="https://www.sofort-ueberweisung.de/payment/createNew/" method="post" target="suWindow" id="suForm">
	<input type="hidden" name="project_name" value="b1gMail PremiumAccount Plugin" />
	<input type="hidden" name="project_homepage" value="{$bmURL}" />
	<input type="hidden" name="project_shop_system_id" value="142" />
	<input type="hidden" name="projectssetting_currency_id" value="{$pacc_prefs.currency}" />
	<input type="hidden" name="projectssetting_interface_success_link" value="http://-USER_VARIABLE_2-" />
	<input type="hidden" name="projectssetting_interface_success_link_redirect" value="1" />
	<input type="hidden" name="projectssetting_interface_cancel_link" value="http://-USER_VARIABLE_3-" />
	<input type="hidden" name="projectssetting_project_password" value="{$pacc_prefs.su_prjpass}" />
	<input type="hidden" name="projectssetting_locked_amount" value="1" />
	<input type="hidden" name="projectssetting_locked_reason_1" value="1" />
	<input type="hidden" name="projectssetting_locked_reason_2" value="1" />
	<input type="hidden" name="projectsnotification_http_activated" value="1" />
	<input type="hidden" name="projectsnotification_http_url" value="{$bmURL}index.php?action=paccSUCallback" />
	<input type="hidden" name="projectsnotification_http_method" value="1" />
	<input type="hidden" name="backlink" value="{$bmURL}admin/plugin.page.php?plugin=PremiumAccountPlugin&action=suBack&sid={$sid}" />
</form>
