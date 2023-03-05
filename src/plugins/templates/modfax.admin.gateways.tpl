<fieldset>
	<legend>{lng p="modfax_gateways_advanced"}</legend>

	<form action="{$pageURL}&action=gateways&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'gateways[]');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="title"}</th>
						<th width="120">{lng p="modfax_protocol"}</th>
						<th width="60">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$gateways item=gateway}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td align="center"><input type="checkbox"{if $gateway.default} disabled="disabled"{else} name="gateways[]" value="{$gateway.faxgateid}"{/if} /></td>
							<td>{text value=$gateway.title}</td>
							<td>{if $gateway.protocol==1}{lng p="modfax_email"}{else}{lng p="modfax_http"}{/if}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="{$pageURL}&action=gateways&do=edit&id={$gateway.faxgateid}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									{if !$gateway.default}<a href="{$pageURL}&action=gateways&delete={$gateway.faxgateid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>{/if}
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer">
				<div style="float: left;">{lng p="action"}:&nbsp;</div>
				<div style="float: left;">
					<div class="btn-group btn-group-sm">
						<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="delete">{lng p="delete"}</option>
								<option value="setdefault">{lng p="setdefault"}</option>
							</optgroup>
						</select>
						<input type="submit" name="executeMassAction" value="{lng p="execute"}" class="btn btn-sm btn-dark-lt" />
					</div>
				</div>
			</div>
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="addgateway"}</legend>

	<form action="{$pageURL}&action=gateways&add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="title"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="title" name="title" value="" placeholder="{lng p="title"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_numberformat"}</label>
			<div class="col-sm-10">
				<select name="number_format" id="number_format" class="form-select">
					<option value="1">{lng p="modfax_number_internat_00"}</option>
					<option value="2">{lng p="modfax_number_internat_plus"}</option>
					<option value="3">{lng p="modfax_number_internat_none"}</option>
					<option value="4">{lng p="modfax_number_nat"}</option>
				</select>
			</div>
		</div>
		<p>&nbsp;</p>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_protocol"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="radio" name="protocol" value="1" id="protocol1" checked="checked" onchange="toggleFaxGatePrefsForm()">
					<span class="form-check-label">{lng p="modfax_email"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" name="protocol" value="2" id="protocol2" onchange="toggleFaxGatePrefsForm()">
					<span class="form-check-label">{lng p="modfax_http"}</span>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_emailfrom"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="email_from" name="email_from" value="" placeholder="{lng p="modfax_emailfrom"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_emailto"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="email_to" name="email_to" value="" placeholder="{lng p="modfax_emailto"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_emailsubject"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="email_subject" name="email_subject" value="" placeholder="{lng p="modfax_emailsubject"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_emailtext"}</label>
			<div class="col-sm-10">
				<textarea name="email_text" id="email_text" class="form-control" placeholder="{lng p="modfax_emailtext"}"></textarea>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_emailpdffile"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="email_pdffile" name="email_pdffile" value="fax.pdf" placeholder="{lng p="modfax_emailpdffile"}">
			</div>
		</div>
		<p>&nbsp;</p>
		<div id="protocol2_prefs" style="display:none;">
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_httpurl"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="http_url" name="http_url" value="" placeholder="{lng p="modfax_httpurl"}">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_httprequest"}</label>
				<div class="col-sm-10">
					<textarea name="http_request" id="http_request" class="form-control" placeholder="{lng p="modfax_httprequest"}"></textarea>
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="returnvalue"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="http_returnvalue" name="http_returnvalue" value="100" placeholder="{lng p="returnvalue"}">
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_supportsstatus"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="radio" name="status_mode" value="0" id="status0" checked="checked" onchange="toggleFaxGatePrefsForm2()">
					<span class="form-check-label">{lng p="no"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" name="status_mode" value="1" id="status1" onchange="toggleFaxGatePrefsForm2()">
					<span class="form-check-label">{lng p="modfax_status1"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" name="status_mode" value="2" id="status2" onchange="toggleFaxGatePrefsForm2()">
					<span class="form-check-label">{lng p="modfax_status2"}</span>
				</label>
			</div>
		</div>
		<div id="status1_prefs" style="display:none;">
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_emailfrom"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="status_emailfrom" name="status_emailfrom" value="" placeholder="{lng p="modfax_emailfrom"}">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_emailto"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="status_emailto" name="status_emailto" value="" placeholder="{lng p="modfax_emailto"}">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_emailsubject"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="status_emailsubject" name="status_emailsubject" value="" placeholder="{lng p="modfax_emailsubject"}">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_statuscode_from"}</label>
				<div class="col-sm-10">
					<div class="input-group mb-2">
						<select name="status_code_field" id="status_code_field" class="form-select">
							<option value="subject">{lng p="subject"}</option>
							<option value="text">{lng p="text"}</option>
						</select>
						<input type="text" class="form-control" id="status_code_regex" name="status_code_regex" value="" />
					</div>
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_success_from"}</label>
				<div class="col-sm-10">
					<div class="input-group mb-2">
						<select name="status_success_field" id="status_success_field" class="form-select">
							<option value="subject">{lng p="subject"}</option>
							<option value="text" selected="selected">{lng p="text"}</option>
						</select>
						<input type="text" class="form-control" id="status_success_regex" name="status_success_regex" value="" />
					</div>
				</div>
			</div>
		</div>
		<div id="status2_prefs" style="display:none;">
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_statuscode_param"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="status_code_param" name="status_code_param" value="" placeholder="{lng p="modfax_statuscode_param"}">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_result_param"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="status_result_param" name="status_result_param" value="" placeholder="{lng p="modfax_result_param"}">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="modfax_success_from"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="status_result_regex" name="status_result_regex" value="" placeholder="{lng p="modfax_success_from"}">
				</div>
			</div>
		</div>
		<p>&nbsp;</p>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="user"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="user" name="user" value="" placeholder="{lng p="user"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="password"}</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" autocomplete="off" id="pass" name="pass" value="" placeholder="{lng p="password"}">
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="defaults"}</legend>

	<script>{literal}var texts = [];
		document.write('<ul>');
		texts[0] = "";
		document.write(' <li><a href="#" onclick="insFaxGateEMail(\'Massenversand\',\'1\',\'%%answer_email%%\',\'fax@massenversand.de\',\'0;%%to%%;%%pass%%;%%user%%;%%status_code%%;%%from_no%%\',texts[0],\'fax.pdf\',\'1\',\'fax@massenversand.de\',\'%%answer_email%%\',\'FAXBERICHT\',\'text\',\'Betreff: ([0-9]*)\',\'text\',\'erfolgreich\',\'\',\'\',\'\');">Massenversand</a></li>');
		texts[1] = "";
		document.write(' <li><a href="#" onclick="insFaxGateEMail(\'CompuTron GNetX\',\'2\',\'%%answer_email%%\',\'%%to%%@fax.gnetx.com\',\'%%pass%%:%%status_code%%\',texts[1],\'fax.pdf\',\'1\',\'fax@gnetx.com\',\'%%answer_email%%\',\'^Sendebericht TNR:([0-9]*)$\',\'subject\',\'^Sendebericht TNR:([0-9]*)$\',\'text\',\'status:0\',\'\',\'\',\'\');">CompuTron GNetX</a></li>');
		document.write('</ul>');{/literal}
	</script>
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
	}
	function insFaxGateEMail(title, number_format, email_from, email_to, email_subject, email_text, email_pdffile,
							 status_mode, status_emailfrom, status_emailto, status_emailsubject, status_code_field,
							 status_code_regex, status_success_field, status_success_regex, status_code_param,
							 status_result_param, status_result_regex)
	{
		EBID('protocol1').checked = true;
		toggleFaxGatePrefsForm();

		EBID('title').value = title;
		EBID('number_format').value = number_format;
		EBID('email_from').value = email_from;
		EBID('email_to').value = email_to;
		EBID('email_subject').value = email_subject;
		EBID('email_text').value = email_text;
		EBID('email_pdffile').value = email_pdffile;

		EBID('status'+status_mode).checked = true;
		toggleFaxGatePrefsForm2();

		EBID('status_emailfrom').value = status_emailfrom;
		EBID('status_emailto').value = status_emailto;
		EBID('status_emailsubject').value = status_emailsubject;
		EBID('status_code_field').value = status_code_field;
		EBID('status_code_regex').value = status_code_regex;
		EBID('status_success_field').value = status_success_field;
		EBID('status_success_regex').value = status_success_regex;

		EBID('status_code_param').value = status_code_param;
		EBID('status_result_param').value = status_result_param;
		EBID('status_result_regex').value = status_result_regex;
	}

	function insFaxGateHTTP(title, number_format, url, request, returnvalue,
							status_mode, status_emailfrom, status_emailto, status_emailsubject, status_code_field,
							status_code_regex, status_success_field, status_success_regex, status_code_param,
							status_result_param, status_result_regex)
	{
		EBID('protocol2').checked = true;
		toggleFaxGatePrefsForm();

		EBID('title').value = title;
		EBID('number_format').value = number_format;
		EBID('http_url').value = url;
		EBID('http_request').value = request;
		EBID('http_returnvalue').value = returnvalue;

		EBID('status'+status_mode).checked = true;
		toggleFaxGatePrefsForm2();

		EBID('status_emailfrom').value = status_emailfrom;
		EBID('status_emailto').value = status_emailto;
		EBID('status_emailsubject').value = status_emailsubject;
		EBID('status_code_field').value = status_code_field;
		EBID('status_code_regex').value = status_code_regex;
		EBID('status_success_field').value = status_success_field;
		EBID('status_success_regex').value = status_success_regex;

		EBID('status_code_param').value = status_code_param;
		EBID('status_result_param').value = status_result_param;
		EBID('status_result_regex').value = status_result_regex;
	}
	{/literal}
	//-->
</script>
