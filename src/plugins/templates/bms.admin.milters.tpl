<form action="{$pageURL}&sid={$sid}&action=smtp&do=milters&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="bms_milters"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th>{lng p="title"}</th>
						<th>{lng p="type"}</th>
						<th>{lng p="address"}</th>
						<th>{lng p="port"}</th>
						<th>{lng p="bms_defaultaction"}</th>
						<th>{lng p="options"}</th>
						<th style="width: 80px;">{lng p="pos"}</th>
						<th>{lng p="delete"}</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$milters item=milter key=milterID}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td><input type="text" class="form-control" name="milters[{$milterID}][title]" value="{text value=$milter.title allowEmpty=true}" size="16" /></td>
							<td>
								<select name="milters[{$milterID}][flags][]" class="form-select">
									<option value="0"{if ($milter.flags&1)==0} selected="selected"{/if}>{lng p="bms_tcp"}</option>
									<option value="1"{if ($milter.flags&1)} selected="selected"{/if}>{lng p="bms_local"}</option>
								</select>
							</td>
							<td><input type="text" class="form-control" name="milters[{$milterID}][hostname]" value="{text value=$milter.hostname allowEmpty=true}" size="32" />
							<td><input type="text" class="form-control" name="milters[{$milterID}][port]" value="{text value=$milter.port allowEmpty=true}" size="6" />
							<td>
								<select name="milters[{$milterID}][default_action]" class="form-select">
									<option value="116"{if $milter.default_action==116} selected="selected"{/if}>{lng p="bms_milter_tempfail"}</option>
									<option value="97"{if $milter.default_action==97} selected="selected"{/if}>{lng p="bms_milter_accept"}</option>
									<option value="114"{if $milter.default_action==114} selected="selected"{/if}>{lng p="bms_milter_reject"}</option>
								</select>
							</td>
							<td>
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="milters[{$milterID}][flags][]" value="2" id="milter{$milterID}_flag2"{if $milter.flags&2} checked="checked"{/if}>
									<span class="form-check-label">{lng p="bms_milter_nonauth"}</span>
								</label>
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="milters[{$milterID}][flags][]" value="4" id="milter{$milterID}_flag4"{if $milter.flags&4} checked="checked"{/if}>
									<span class="form-check-label">{lng p="bms_milter_auth"}</span>
								</label>
							</td>
							<td><input type="text" class="form-control" name="milters[{$milterID}][pos]" value="{$milter.pos}" size="6" /></td>
							<td><input type="checkbox" class="form-check-input" name="milters[{$milterID}][delete]" /></td>
						</tr>
					{/foreach}

					{cycle name=class values="td1,td2" assign=class}
					<tr class="{$class}">
						<td><input type="text" class="form-control" name="milters[0][title]" value="" size="16" /></td>
						<td>
							<select name="milters[0][flags][]" class="form-select">
								<option value="0">{lng p="bms_tcp"}</option>
								<option value="1">{lng p="bms_local"}</option>
							</select>
						</td>
						<td><input type="text" class="form-control" name="milters[0][hostname]" value="" size="32" />
						<td><input type="text" class="form-control" name="milters[0][port]" value="0" size="6" />
						<td>
							<select name="milters[0][default_action]" class="form-select">
								<option value="116">{lng p="bms_milter_tempfail"}</option>
								<option value="97">{lng p="bms_milter_accept"}</option>
								<option value="114">{lng p="bms_milter_reject"}</option>
							</select>
						</td>
						<td>
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="milters[0][flags][]" value="2" id="milter0_flag2" checked="checked">
								<span class="form-check-label">{lng p="bms_milter_nonauth"}</span>
							</label>
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="milters[0][flags][]" value="4" id="milter0_flag4" checked="checked">
								<span class="form-check-label">{lng p="bms_milter_auth"}</span>
							</label>
						</td>
						<td><input type="text" class="form-control" name="milters[0][pos]" value="{$nextPos}" size="6" /></td>
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
