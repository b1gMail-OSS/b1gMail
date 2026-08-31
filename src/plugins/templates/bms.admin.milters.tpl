<form action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=smtp&action=milters&save=true"}" method="post" onsubmit="spin(this)">
	{csrffield}
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_milters"}</legend>

		<div class="card mb-3"><div class="table-responsive"><table class="table table-vcenter table-striped card-table">
			<tr>
				<th>{lng p="title"}</th>
				<th>{lng p="type"}</th>
				<th>{lng p="address"}</th>
				<th>{lng p="port"}</th>
				<th>{lng p="bms_defaultaction"}</th>
				<th>{lng p="options"}</th>
				<th width="80">{lng p="pos"}</th>
				<th>{lng p="delete"}</th>
			</tr>

			{foreach from=$milters item=milter key=milterID}
			<tr>
				<td><input class="form-control" type="text" name="milters[{$milterID}][title]" value="{text value=$milter.title allowEmpty=true}" size="16" /></td>
				<td><select class="form-select" name="milters[{$milterID}][flags][]">
						<option value="0"{if ($milter.flags&1)==0} selected="selected"{/if}>{lng p="bms_tcp"}</option>
						<option value="1"{if ($milter.flags&1)} selected="selected"{/if}>{lng p="bms_local"}</option>
					</select></td>
				<td><input class="form-control" type="text" name="milters[{$milterID}][hostname]" value="{text value=$milter.hostname allowEmpty=true}" size="32" />
				<td><input class="form-control" type="text" name="milters[{$milterID}][port]" value="{text value=$milter.port allowEmpty=true}" size="6" />
				<td><select class="form-select" name="milters[{$milterID}][default_action]">
						<option value="116"{if $milter.default_action==116} selected="selected"{/if}>{lng p="bms_milter_tempfail"}</option>
						<option value="97"{if $milter.default_action==97} selected="selected"{/if}>{lng p="bms_milter_accept"}</option>
						<option value="114"{if $milter.default_action==114} selected="selected"{/if}>{lng p="bms_milter_reject"}</option>
					</select></td>
				<td>
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="milters[{$milterID}][flags][]" value="2" id="milter{$milterID}_flag2"{if $milter.flags&2} checked="checked"{/if} /><span class="form-check-label" for="milter{$milterID}_flag2">{lng p="bms_milter_nonauth"}</span></label><br />
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="milters[{$milterID}][flags][]" value="4" id="milter{$milterID}_flag4"{if $milter.flags&4} checked="checked"{/if} /><span class="form-check-label" for="milter{$milterID}_flag4">{lng p="bms_milter_auth"}</span></label>
				</td>
				<td><input class="form-control" type="text" name="milters[{$milterID}][pos]" value="{$milter.pos}" size="6" /></td>
				<td class="text-center"><label class="form-check justify-content-center mb-0"><input class="form-check-input" type="checkbox" name="milters[{$milterID}][delete]" /></label></td>
			</tr>
			{/foreach}
			<tr>
				<td><input class="form-control" type="text" name="milters[0][title]" value="" size="16" /></td>
				<td><select class="form-select" name="milters[0][flags][]">
						<option value="0">{lng p="bms_tcp"}</option>
						<option value="1">{lng p="bms_local"}</option>
					</select></td>
				<td><input class="form-control" type="text" name="milters[0][hostname]" value="" size="32" />
				<td><input class="form-control" type="text" name="milters[0][port]" value="0" size="6" />
				<td><select class="form-select" name="milters[0][default_action]">
						<option value="116">{lng p="bms_milter_tempfail"}</option>
						<option value="97">{lng p="bms_milter_accept"}</option>
						<option value="114">{lng p="bms_milter_reject"}</option>
					</select></td>
				<td>
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="milters[0][flags][]" value="2" id="milter0_flag2" checked="checked" /><span class="form-check-label" for="milter0_flag2">{lng p="bms_milter_nonauth"}</span></label><br />
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="milters[0][flags][]" value="4" id="milter0_flag4" checked="checked" /><span class="form-check-label" for="milter0_flag4">{lng p="bms_milter_auth"}</span></label>
				</td>
				<td><input class="form-control" type="text" name="milters[0][pos]" value="{$nextPos}" size="6" /></td>
				<td>&nbsp;</td>
			</tr>
		</table></div></div>
	</fieldset>

	<div class="d-flex justify-content-between mt-3 mb-2">
		<button type="button" class="btn btn-outline-secondary" onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=smtp"}';"><i class="ti ti-chevron-left me-1"></i> {lng p="back"}</button>
		<button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> {lng p="save"}</button>
	</div>
</form>
