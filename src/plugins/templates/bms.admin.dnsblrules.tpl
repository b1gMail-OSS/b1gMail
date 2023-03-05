<form action="{$pageURL}&sid={$sid}&action=smtp&do=dnsblRules&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="bms_dnsbl_rules"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th>{lng p="bms_dnsbl"}</th>
						<th>{lng p="bms_classification"}</th>
						<th>{lng p="type"}</th>
						<th>{lng p="delete"}</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$dnsbls item=dnsbl}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td><input type="text" class="form-control" name="dnsbls[{$dnsbl.id}][host]" value="{$dnsbl.host}" size="32" /></td>
							<td>
								<select name="dnsbls[{$dnsbl.id}][classification]" class="form-select">
									<option value="1"{if $dnsbl.classification==1} selected="selected"{/if}>{lng p="bms_origin_default"}</option>
									<option value="2"{if $dnsbl.classification==2} selected="selected"{/if}>{lng p="bms_origin_trusted"}</option>
									<option value="3"{if $dnsbl.classification==3} selected="selected"{/if}>{lng p="bms_origin_dialup"}</option>
									<option value="4"{if $dnsbl.classification==4} selected="selected"{/if}>{lng p="bms_origin_reject"}</option>
									<option value="5"{if $dnsbl.classification==5} selected="selected"{/if}>{lng p="bms_origin_nogrey"}</option>
									<option value="6"{if $dnsbl.classification==6} selected="selected"{/if}>{lng p="bms_origin_nogreyandban"}</option>
								</select>
							</td>
							<td>
								<select name="dnsbls[{$dnsbl.id}][type]" class="form-select">
									<option value="ipv4"{if $dnsbl.type=='ipv4'} selected="selected"{/if}>IPv4</option>
									<option value="ipv6"{if $dnsbl.type=='ipv6'} selected="selected"{/if}>IPv6</option>
									<option value="both"{if $dnsbl.type=='both'} selected="selected"{/if}>{lng p="both"}</option>
								</select>
							</td>
							<td><input type="checkbox" class="form-check-input" name="dnsbls[{$dnsbl.id}][delete]" /></td>
						</tr>
					{/foreach}

					{cycle name=class values="td1,td2" assign=class}
					<tr class="{$class}">
						<td><input type="text" class="form-control" name="dnsbls[0][host]" value="" size="32" /></td>
						<td>
							<select name="dnsbls[0][classification]" class="form-select">
								<option value="1">{lng p="bms_origin_default"}</option>
								<option value="2">{lng p="bms_origin_trusted"}</option>
								<option value="3">{lng p="bms_origin_dialup"}</option>
								<option value="4">{lng p="bms_origin_reject"}</option>
								<option value="5">{lng p="bms_origin_nogrey"}</option>
								<option value="6">{lng p="bms_origin_nogreyandban"}</option>
							</select>
						</td>
						<td>
							<select name="dnsbls[0][type]" class="form-select">
								<option value="ipv4">IPv4</option>
								<option value="ipv6">IPv6</option>
								<option value="both">{lng p="both"}</option>
							</select>
						</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
		</div>
	</fieldset>

	<div class="row">
		<div class="col-md-6">
			<input class="btn btn-primary" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='{$pageURL}&action=smtp&sid={$sid}';" />
		</div>
		<div class="col-md-6 text-end">
			<input class="btn btn-primary" type="submit" value=" {lng p="save"} " />
		</div>
	</div>
</form>
