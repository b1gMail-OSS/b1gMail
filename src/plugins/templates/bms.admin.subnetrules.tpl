<form action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=smtp&action=subnetRules&save=true"}" method="post" onsubmit="spin(this)">
	{csrffield}
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_subnet_rules"}</legend>
		
		<div class="card mb-3"><div class="table-responsive"><table class="table table-vcenter table-striped card-table">
			<tr>
				<th>{lng p="bms_subnet"}</th>
				<th>{lng p="bms_classification"}</th>
				<th>{lng p="delete"}</th>
			</tr>
			
			{foreach from=$subnets item=subnet}
			<tr>
				<td>
					<div class="input-group" style="max-width:22rem;">
						<input class="form-control" type="text" name="subnets[{$subnet.id}][ip]" value="{$subnet.ip}" />
						<span class="input-group-text">/</span>
						<input class="form-control" type="text" name="subnets[{$subnet.id}][mask]" value="{$subnet.mask}" style="max-width:5rem;" />
					</div>
				</td>
				<td><select class="form-select" name="subnets[{$subnet.id}][classification]">
					<option value="1"{if $subnet.classification==1} selected="selected"{/if}>{lng p="bms_origin_default"}</option>
					<option value="2"{if $subnet.classification==2} selected="selected"{/if}>{lng p="bms_origin_trusted"}</option>
					<option value="3"{if $subnet.classification==3} selected="selected"{/if}>{lng p="bms_origin_dialup"}</option>
					<option value="4"{if $subnet.classification==4} selected="selected"{/if}>{lng p="bms_origin_reject"}</option>
					<option value="5"{if $subnet.classification==5} selected="selected"{/if}>{lng p="bms_origin_nogrey"}</option>
					<option value="6"{if $subnet.classification==6} selected="selected"{/if}>{lng p="bms_origin_nogreyandban"}</option>
				</select></td>
				<td class="text-center"><label class="form-check justify-content-center mb-0"><input class="form-check-input" type="checkbox" name="subnets[{$subnet.id}][delete]" /></label></td>
			</tr>
			{/foreach}
			<tr>
				<td>
					<div class="input-group" style="max-width:22rem;">
						<input class="form-control" type="text" name="subnets[0][ip]" value="" />
						<span class="input-group-text">/</span>
						<input class="form-control" type="text" name="subnets[0][mask]" value="" style="max-width:5rem;" />
					</div>
				</td>
				<td><select class="form-select" name="subnets[0][classification]">
					<option value="1">{lng p="bms_origin_default"}</option>
					<option value="2">{lng p="bms_origin_trusted"}</option>
					<option value="3">{lng p="bms_origin_dialup"}</option>
					<option value="4">{lng p="bms_origin_reject"}</option>
					<option value="5">{lng p="bms_origin_nogrey"}</option>
					<option value="6">{lng p="bms_origin_nogreyandban"}</option>
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
