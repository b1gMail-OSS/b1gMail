<form action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=smtp&action=dnsblRules&save=true"}" method="post" onsubmit="spin(this)">
	{csrffield}
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_dnsbl_rules"}</legend>

		<div class="card mb-3"><div class="table-responsive"><table class="table table-vcenter table-striped card-table">
			<tr>
				<th>{lng p="bms_dnsbl"}</th>
				<th>{lng p="bms_matchips"}</th>
				<th>{lng p="bms_classification"}</th>
				<th>{lng p="type"}</th>
				<th>{lng p="delete"}</th>
			</tr>

			{foreach from=$dnsbls item=dnsbl}
			<tr>
				<td><input class="form-control" type="text" name="dnsbls[{$dnsbl.id}][host]" value="{text value=$dnsbl.host allowEmpty=true}" size="32" /></td>
				<td><input class="form-control" type="text" name="dnsbls[{$dnsbl.id}][match_ips]" value="{text value=$dnsbl.match_ips allowEmpty=true}" size="32" /></td>
				<td><select class="form-select" name="dnsbls[{$dnsbl.id}][classification]">
					<option value="1"{if $dnsbl.classification==1} selected="selected"{/if}>{lng p="bms_origin_default"}</option>
					<option value="2"{if $dnsbl.classification==2} selected="selected"{/if}>{lng p="bms_origin_trusted"}</option>
					<option value="3"{if $dnsbl.classification==3} selected="selected"{/if}>{lng p="bms_origin_dialup"}</option>
					<option value="4"{if $dnsbl.classification==4} selected="selected"{/if}>{lng p="bms_origin_reject"}</option>
					<option value="5"{if $dnsbl.classification==5} selected="selected"{/if}>{lng p="bms_origin_nogrey"}</option>
					<option value="6"{if $dnsbl.classification==6} selected="selected"{/if}>{lng p="bms_origin_nogreyandban"}</option>
				</select></td>
				<td><select class="form-select" name="dnsbls[{$dnsbl.id}][type]">
					<option value="ipv4"{if $dnsbl.type=='ipv4'} selected="selected"{/if}>IPv4</option>
					<option value="ipv6"{if $dnsbl.type=='ipv6'} selected="selected"{/if}>IPv6</option>
					<option value="both"{if $dnsbl.type=='both'} selected="selected"{/if}>{lng p="both"}</option>
				</select></td>
				<td class="text-center"><label class="form-check justify-content-center mb-0"><input class="form-check-input" type="checkbox" name="dnsbls[{$dnsbl.id}][delete]" /></label></td>
			</tr>
			{/foreach}
			<tr>
				<td><input class="form-control" type="text" name="dnsbls[0][host]" value="" size="32" /></td>
				<td><input class="form-control" type="text" name="dnsbls[0][match_ips]" value="" size="32" /></td>
				<td><select class="form-select" name="dnsbls[0][classification]">
					<option value="1">{lng p="bms_origin_default"}</option>
					<option value="2">{lng p="bms_origin_trusted"}</option>
					<option value="3">{lng p="bms_origin_dialup"}</option>
					<option value="4">{lng p="bms_origin_reject"}</option>
					<option value="5">{lng p="bms_origin_nogrey"}</option>
					<option value="6">{lng p="bms_origin_nogreyandban"}</option>
				</select></td>
				<td><select class="form-select" name="dnsbls[0][type]">
					<option value="ipv4">IPv4</option>
					<option value="ipv6">IPv6</option>
					<option value="both">{lng p="both"}</option>
				</select></td>
				<td>&nbsp;</td>
			</tr>
		</table></div></div>
	</fieldset>

	<div class="d-flex justify-content-between mt-3 mb-2">
		<button type="button" class="btn btn-outline-secondary" onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=smtp"}';"><i class="ti ti-chevron-left me-1"></i> {lng p="back"}</button>
		<button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> {lng p="save"}</button>
	</div>
</form>
