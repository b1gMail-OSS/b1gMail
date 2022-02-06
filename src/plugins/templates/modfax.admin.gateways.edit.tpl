<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="{$pageURL}&action=gateways&do=edit&save=true&id={$gateway.faxgateid}&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="4"><img src="../plugins/templates/images/modfax_gateway.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="title"}:</td>
				<td class="td2"><input type="text" style="width:85%;" id="title" name="title" value="{text value=$gateway.title allowEmpty=true}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_numberformat"}:</td>
				<td class="td2">
					<select name="number_format" id="number_format">
						<option value="1"{if $gateway.number_format==1} selected="selected"{/if}>{lng p="modfax_number_internat_00"}</option>
						<option value="2"{if $gateway.number_format==2} selected="selected"{/if}>{lng p="modfax_number_internat_plus"}</option>
						<option value="3"{if $gateway.number_format==3} selected="selected"{/if}>{lng p="modfax_number_internat_none"}</option>
						<option value="4"{if $gateway.number_format==4} selected="selected"{/if}>{lng p="modfax_number_nat"}</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_protocol"}:</td>
				<td class="td2">
					<input type="radio" name="protocol" value="1" id="protocol1"{if $gateway.protocol==1} checked="checked"{/if} onchange="toggleFaxGatePrefsForm()" />
					<label for="protocol1">{lng p="modfax_email"}</label>
					<input type="radio" name="protocol" value="2" id="protocol2"{if $gateway.protocol==2} checked="checked"{/if} onchange="toggleFaxGatePrefsForm()" />
					<label for="protocol2">{lng p="modfax_http"}</label>
				</td>
			</tr>
			
			<tbody id="protocol1_prefs" style="display:{if $gateway.protocol!=1}none{/if};">
			<tr>
				<td width="40" rowspan="5">&nbsp;</td>
				<td class="td1">{lng p="modfax_emailfrom"}:</td>
				<td class="td2">
					<input type="text" style="width:60%;" id="email_from" name="email_from" value="{text value=$prefs.from allowEmpty=true}" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_emailto"}:</td>
				<td class="td2">
					<input type="text" style="width:60%;" id="email_to" name="email_to" value="{text value=$prefs.to allowEmpty=true}" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_emailsubject"}:</td>
				<td class="td2">
					<input type="text" style="width:60%;" id="email_subject" name="email_subject" value="{text value=$prefs.subject allowEmpty=true}" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_emailtext"}:</td>
				<td class="td2">
					<textarea name="email_text" id="email_text" style="width:55%;height:120px;">{text value=$prefs.text allowEmpty=true}</textarea>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_emailpdffile"}:</td>
				<td class="td2">
					<input type="text" style="width:35%;" id="email_pdffile" name="email_pdffile" value="{text value=$prefs.pdffile allowEmpty=true}" />
				</td>
			</tr>
			</tbody>
			
			<tbody id="protocol2_prefs" style="display:{if $gateway.protocol!=2}none{/if};">
			<tr>
				<td width="40" rowspan="3">&nbsp;</td>
				<td class="td1">{lng p="modfax_httpurl"}:</td>
				<td class="td2">
					<input type="text" style="width:60%;" id="http_url" name="http_url" value="{text value=$prefs.url allowEmpty=true}" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_httprequest"}:</td>
				<td class="td2">
					<textarea name="http_request" id="http_request" style="width:55%;height:120px;">{text value=$prefs.request allowEmpty=true}</textarea>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="returnvalue"}:</td>
				<td class="td2">
					<input type="text" style="width:35%;" id="http_returnvalue" name="http_returnvalue" value="{text value=$prefs.returnvalue allowEmpty=true}" />
				</td>
			</tr>
			</tbody>
			
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td width="40">&nbsp;</td>
				<td class="td1">{lng p="modfax_supportsstatus"}?</td>
				<td class="td2">
					<input type="radio" name="status_mode" value="0" id="status0"{if $gateway.status_mode==0} checked="checked"{/if} onchange="toggleFaxGatePrefsForm2()" />
					<label for="status0">{lng p="no"}</label>
					<input type="radio" name="status_mode" value="1" id="status1"{if $gateway.status_mode==1} checked="checked"{/if} onchange="toggleFaxGatePrefsForm2()" />
					<label for="status1">{lng p="modfax_status1"}</label>
					<input type="radio" name="status_mode" value="2" id="status2"{if $gateway.status_mode==2} checked="checked"{/if} onchange="toggleFaxGatePrefsForm2()" />
					<label for="status2">{lng p="modfax_status2"}</label>
				</td>
			</tr>
			
			<tbody id="status1_prefs" style="display:{if $gateway.status_mode!=1}none{/if};">
			<tr>
				<td width="40" rowspan="5">&nbsp;</td>
				<td class="td1">{lng p="modfax_emailfrom"}:</td>
				<td class="td2">
					<input type="text" style="width:60%;" id="status_emailfrom" name="status_emailfrom" value="{text value=$status_prefs.emailfrom allowEmpty=true}" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_emailto"}:</td>
				<td class="td2">
					<input type="text" style="width:60%;" id="status_emailto" name="status_emailto" value="{text value=$status_prefs.emailto allowEmpty=true}" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_emailsubject"}:</td>
				<td class="td2">
					<input type="text" style="width:60%;" id="status_emailsubject" name="status_emailsubject" value="{text value=$status_prefs.emailsubject allowEmpty=true}" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_statuscode_from"}:</td>
				<td class="td2">
					<select name="status_code_field" id="status_code_field">
						<option value="subject"{if $status_prefs.code_field=='subject'} selected="selected"{/if}>{lng p="subject"}</fieldset>
						<option value="text"{if $status_prefs.code_field=='text'} selected="selected"{/if}>{lng p="text"}</fieldset>
					</select>
					<input type="text" size="32" id="status_code_regex" name="status_code_regex" value="{text value=$status_prefs.code_regex allowEmpty=true}" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_success_from"}:</td>
				<td class="td2">
					<select name="status_success_field" id="status_success_field">
						<option value="subject"{if $status_prefs.success_field=='subject'} selected="selected"{/if}>{lng p="subject"}</fieldset>
						<option value="text"{if $status_prefs.success_field=='text'} selected="selected"{/if}>{lng p="text"}</fieldset>
					</select>
					<input type="text" size="32" id="status_success_regex" name="status_success_regex" value="{text value=$status_prefs.success_regex allowEmpty=true}" />
				</td>
			</tr>
			</tbody>
			
			<tbody id="status2_prefs" style="display:{if $gateway.status_mode!=2}none{/if};">
			<tr>
				<td width="40" rowspan="3">&nbsp;</td>
				<td class="td1">{lng p="modfax_statuscode_param"}:</td>
				<td class="td2">
					<input type="text" style="width:60%;" id="status_code_param" name="status_code_param" value="{text value=$status_prefs.code_param allowEmpty=true}" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_result_param"}:</td>
				<td class="td2">
					<input type="text" style="width:60%;" id="status_result_param" name="status_result_param" value="{text value=$status_prefs.result_param allowEmpty=true}" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_success_from"}:</td>
				<td class="td2">
					<input type="text" style="width:60%;" id="status_result_regex" name="status_result_regex" value="{text value=$status_prefs.result_regex allowEmpty=true}" />
				</td>
			</tr>
			</tbody>
			
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td width="40" rowspan="2">&nbsp;</td>
				<td class="td1">{lng p="user"}:</td>
				<td class="td2"><input type="text" size="36" id="user" name="user" value="{text value=$gateway.user allowEmpty=true}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="password"}:</td>
				<td class="td2"><input type="password" autocomplete="off" size="36" id="pass" name="pass" value="{text value=$gateway.pass allowEmpty=true}" /></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</p>
	</form>
</fieldset>

<script>
<!--
	{literal}function toggleFaxGatePrefsForm()
	{
		var protocol = EBID('protocol1').checked ? 1 : 2;
		EBID('protocol1_prefs').style.display = protocol == 1 ? '' : 'none';
		EBID('protocol2_prefs').style.display = protocol == 2 ? '' : 'none';
	}
	
	function toggleFaxGatePrefsForm2()
	{
		var status = EBID('status1').checked ? 1 : (EBID('status2').checked ? 2 : 0);
		EBID('status1_prefs').style.display = status == 1 ? '' : 'none';
		EBID('status2_prefs').style.display = status == 2 ? '' : 'none';
	}{/literal}
//-->
</script>
