<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="{$pageURL}&action=gateways&do=edit&save=true&id={$gateway.faxgateid}&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="title"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="title" name="title" value="{if isset($gateway.title)}{text value=$gateway.title allowEmpty=true}{/if}" placeholder="{lng p="title"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_numberformat"}</label>
			<div class="col-sm-10">
				<select name="number_format" id="number_format" class="form-select">
					<option value="1"{if $gateway.number_format==1} selected="selected"{/if}>{lng p="modfax_number_internat_00"}</option>
					<option value="2"{if $gateway.number_format==2} selected="selected"{/if}>{lng p="modfax_number_internat_plus"}</option>
					<option value="3"{if $gateway.number_format==3} selected="selected"{/if}>{lng p="modfax_number_internat_none"}</option>
					<option value="4"{if $gateway.number_format==4} selected="selected"{/if}>{lng p="modfax_number_nat"}</option>
				</select>
			</div>
		</div>
		<p>&nbsp;</p>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_protocol"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="radio" name="protocol" value="1"{if $gateway.protocol==1} checked="checked"{/if} id="protocol1" checked="checked" onchange="toggleFaxGatePrefsForm()">
					<span class="form-check-label">{lng p="modfax_email"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" name="protocol" value="2"{if $gateway.protocol==2} checked="checked"{/if} id="protocol2" onchange="toggleFaxGatePrefsForm()">
					<span class="form-check-label">{lng p="modfax_http"}</span>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_emailfrom"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="email_from" name="email_from" value="{if isset($prefs.from)}{text value=$prefs.from allowEmpty=true}{/if}" placeholder="{lng p="modfax_emailfrom"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_emailto"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="email_to" name="email_to" value="{if isset($prefs.to)}{text value=$prefs.to allowEmpty=true}{/if}" placeholder="{lng p="modfax_emailto"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_emailsubject"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="email_subject" name="email_subject" value="{if isset($prefs.subject)}{text value=$prefs.subject allowEmpty=true}{/if}" placeholder="{lng p="modfax_emailsubject"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_emailtext"}</label>
			<div class="col-sm-10">
				<textarea name="email_text" id="email_text" class="form-control" placeholder="{lng p="modfax_emailtext"}">{text value=$prefs.text allowEmpty=true}</textarea>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_emailpdffile"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="email_pdffile" name="email_pdffile" value="{if isset($prefs.pdffile)}{text value=$prefs.pdffile allowEmpty=true}{/if}" placeholder="{lng p="modfax_emailpdffile"}">
			</div>
		</div>
		<p>&nbsp;</p>
		<div id="protocol2_prefs" style="display:none;">
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_httpurl"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="http_url" name="http_url" value="{if isset($prefs.url)}{text value=$prefs.url allowEmpty=true}{/if}" placeholder="{lng p="modfax_httpurl"}">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_httprequest"}</label>
				<div class="col-sm-10">
					<textarea name="http_request" id="http_request" class="form-control" placeholder="{lng p="modfax_httprequest"}">{text value=$prefs.request allowEmpty=true}</textarea>
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="returnvalue"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="http_returnvalue" name="http_returnvalue" value="{if isset($prefs.returnvalue)}{text value=$prefs.returnvalue allowEmpty=true}{/if}" placeholder="{lng p="returnvalue"}">
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_supportsstatus"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="radio" name="status_mode" value="0"{if $gateway.status_mode==0} checked="checked"{/if} id="status0" checked="checked" onchange="toggleFaxGatePrefsForm2()">
					<span class="form-check-label">{lng p="no"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" name="status_mode" value="1"{if $gateway.status_mode==1} checked="checked"{/if} id="status1" onchange="toggleFaxGatePrefsForm2()">
					<span class="form-check-label">{lng p="modfax_status1"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" name="status_mode" value="2"{if $gateway.status_mode==2} checked="checked"{/if} id="status2" onchange="toggleFaxGatePrefsForm2()">
					<span class="form-check-label">{lng p="modfax_status2"}</span>
				</label>
			</div>
		</div>
		<div id="status1_prefs" style="display:{if $gateway.status_mode!=1}none{/if};">
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_emailfrom"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="status_emailfrom" name="status_emailfrom" value="{if isset($status_prefs.emailfrom)}{text value=$status_prefs.emailfrom allowEmpty=true}{/if}" placeholder="{lng p="modfax_emailfrom"}">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_emailto"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="status_emailto" name="status_emailto" value="{if isset($status_prefs.emailto)}{text value=$status_prefs.emailto allowEmpty=true}{/if}" placeholder="{lng p="modfax_emailto"}">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_emailsubject"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="status_emailsubject" name="status_emailsubject" value="{if isset($status_prefs.emailsubject)}{text value=$status_prefs.emailsubject allowEmpty=true}{/if}" placeholder="{lng p="modfax_emailsubject"}">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_statuscode_from"}</label>
				<div class="col-sm-10">
					<div class="input-group mb-2">
						<select name="status_code_field" id="status_code_field" class="form-select">
							<option value="subject"{if $status_prefs.code_field=='subject'} selected="selected"{/if}>{lng p="subject"}</option>
							<option value="text"{if $status_prefs.code_field=='text'} selected="selected"{/if}>{lng p="text"}</option>
						</select>
						<input type="text" class="form-control" id="status_code_regex" name="status_code_regex" value="{if isset($status_prefs.code_regex)}{text value=$status_prefs.code_regex allowEmpty=true}{/if}" />
					</div>
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_success_from"}</label>
				<div class="col-sm-10">
					<div class="input-group mb-2">
						<select name="status_success_field" id="status_success_field" class="form-select">
							<option value="subject"{if $status_prefs.success_field=='subject'} selected="selected"{/if}>{lng p="subject"}</option>
							<option value="text" {if $status_prefs.success_field=='text'} selected="selected"{/if}>{lng p="text"}</option>
						</select>
						<input type="text" class="form-control" id="status_success_regex" name="status_success_regex" value="{if isset($status_prefs.success_regex)}{text value=$status_prefs.success_regex allowEmpty=true}{/if}" />
					</div>
				</div>
			</div>
		</div>
		<div id="status2_prefs" style="display:{if $gateway.status_mode!=2}none{/if};">
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_statuscode_param"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="status_code_param" name="status_code_param" value="{if isset($status_prefs.code_param)}{text value=$status_prefs.code_param allowEmpty=true}{/if}" placeholder="{lng p="modfax_statuscode_param"}">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_result_param"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="status_result_param" name="status_result_param" value="{if isset($status_prefs.result_param)}{text value=$status_prefs.result_param allowEmpty=true}{/if}" placeholder="{lng p="modfax_result_param"}">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_success_from"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="status_result_regex" name="status_result_regex" value="{if isset($status_prefs.result_regex)}{text value=$status_prefs.result_regex allowEmpty=true}{/if}" placeholder="{lng p="modfax_success_from"}">
				</div>
			</div>
		</div>
		<p>&nbsp;</p>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="user"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="user" name="user" value="{if isset($gateway.user)}{text value=$gateway.user allowEmpty=true}{/if}" placeholder="{lng p="user"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="password"}</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" autocomplete="off" id="pass" name="pass" value="{if isset($gateway.pass)}{text value=$gateway.pass allowEmpty=true}{/if}" placeholder="{lng p="password"}">
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
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
