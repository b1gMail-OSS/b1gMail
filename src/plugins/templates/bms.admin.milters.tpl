<form action="{$pageURL}&sid={$sid}&action=smtp&do=milters&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="bms_milters"}</legend>

		<table class="list">
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
			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}">
				<td><input type="text" name="milters[{$milterID}][title]" value="{text value=$milter.title allowEmpty=true}" size="16" /></td>
				<td><select name="milters[{$milterID}][flags][]">
						<option value="0"{if ($milter.flags&1)==0} selected="selected"{/if}>{lng p="bms_tcp"}</option>
						<option value="1"{if ($milter.flags&1)} selected="selected"{/if}>{lng p="bms_local"}</option>
					</select></td>
				<td><input type="text" name="milters[{$milterID}][hostname]" value="{text value=$milter.hostname allowEmpty=true}" size="32" />
				<td><input type="text" name="milters[{$milterID}][port]" value="{text value=$milter.port allowEmpty=true}" size="6" />
				<td><select name="milters[{$milterID}][default_action]">
						<option value="116"{if $milter.default_action==116} selected="selected"{/if}>{lng p="bms_milter_tempfail"}</option>
						<option value="97"{if $milter.default_action==97} selected="selected"{/if}>{lng p="bms_milter_accept"}</option>
						<option value="114"{if $milter.default_action==114} selected="selected"{/if}>{lng p="bms_milter_reject"}</option>
					</select></td>
				<td>
					<input type="checkbox" name="milters[{$milterID}][flags][]" value="2" id="milter{$milterID}_flag2"{if $milter.flags&2} checked="checked"{/if} />
						<label for="milter{$milterID}_flag2">{lng p="bms_milter_nonauth"}</label><br />
					<input type="checkbox" name="milters[{$milterID}][flags][]" value="4" id="milter{$milterID}_flag4"{if $milter.flags&4} checked="checked"{/if} />
						<label for="milter{$milterID}_flag4">{lng p="bms_milter_auth"}</label>
				</td>
				<td><input type="text" name="milters[{$milterID}][pos]" value="{$milter.pos}" size="6" /></td>
				<td><input type="checkbox" name="milters[{$milterID}][delete]" /></td>
			</tr>
			{/foreach}

			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}">
				<td><input type="text" name="milters[0][title]" value="" size="16" /></td>
				<td><select name="milters[0][flags][]">
						<option value="0">{lng p="bms_tcp"}</option>
						<option value="1">{lng p="bms_local"}</option>
					</select></td>
				<td><input type="text" name="milters[0][hostname]" value="" size="32" />
				<td><input type="text" name="milters[0][port]" value="0" size="6" />
				<td><select name="milters[0][default_action]">
						<option value="116">{lng p="bms_milter_tempfail"}</option>
						<option value="97">{lng p="bms_milter_accept"}</option>
						<option value="114">{lng p="bms_milter_reject"}</option>
					</select></td>
				<td>
					<input type="checkbox" name="milters[0][flags][]" value="2" id="milter0_flag2" checked="checked" />
						<label for="milter0_flag2">{lng p="bms_milter_nonauth"}</label><br />
					<input type="checkbox" name="milters[0][flags][]" value="4" id="milter0_flag4" checked="checked" />
						<label for="milter0_flag4">{lng p="bms_milter_auth"}</label>
				</td>
				<td><input type="text" name="milters[0][pos]" value="{$nextPos}" size="6" /></td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</fieldset>

	<p>
		<div style="float:left" class="buttons">
			<input class="button" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='{$pageURL}&action=msgqueue&sid={$sid}';" />
		</div>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
