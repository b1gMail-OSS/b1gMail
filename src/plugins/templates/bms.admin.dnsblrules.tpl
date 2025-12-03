<form action="{$pageURL}&sid={$sid}&action=smtp&do=dnsblRules&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="bms_dnsbl_rules"}</legend>

		<table class="list">
			<tr>
				<th>{lng p="bms_dnsbl"}</th>
				<th>{lng p="bms_matchips"}</th>
				<th>{lng p="bms_classification"}</th>
				<th>{lng p="type"}</th>
				<th>{lng p="delete"}</th>
			</tr>

			{foreach from=$dnsbls item=dnsbl}
			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}">
				<td><input type="text" name="dnsbls[{$dnsbl.id}][host]" value="{text value=$dnsbl.host allowEmpty=true}" size="32" /></td>
				<td><input type="text" name="dnsbls[{$dnsbl.id}][match_ips]" value="{text value=$dnsbl.match_ips allowEmpty=true}" size="32" /></td>
				<td><select name="dnsbls[{$dnsbl.id}][classification]">
					<option value="1"{if $dnsbl.classification==1} selected="selected"{/if}>{lng p="bms_origin_default"}</option>
					<option value="2"{if $dnsbl.classification==2} selected="selected"{/if}>{lng p="bms_origin_trusted"}</option>
					<option value="3"{if $dnsbl.classification==3} selected="selected"{/if}>{lng p="bms_origin_dialup"}</option>
					<option value="4"{if $dnsbl.classification==4} selected="selected"{/if}>{lng p="bms_origin_reject"}</option>
					<option value="5"{if $dnsbl.classification==5} selected="selected"{/if}>{lng p="bms_origin_nogrey"}</option>
					<option value="6"{if $dnsbl.classification==6} selected="selected"{/if}>{lng p="bms_origin_nogreyandban"}</option>
				</select></td>
				<td><select name="dnsbls[{$dnsbl.id}][type]">
					<option value="ipv4"{if $dnsbl.type=='ipv4'} selected="selected"{/if}>IPv4</option>
					<option value="ipv6"{if $dnsbl.type=='ipv6'} selected="selected"{/if}>IPv6</option>
					<option value="both"{if $dnsbl.type=='both'} selected="selected"{/if}>{lng p="both"}</option>
				</select></td>
				<td><input type="checkbox" name="dnsbls[{$dnsbl.id}][delete]" /></td>
			</tr>
			{/foreach}

			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}">
				<td><input type="text" name="dnsbls[0][host]" value="" size="32" /></td>
				<td><input type="text" name="dnsbls[0][match_ips]" value="" size="32" /></td>
				<td><select name="dnsbls[0][classification]">
					<option value="1">{lng p="bms_origin_default"}</option>
					<option value="2">{lng p="bms_origin_trusted"}</option>
					<option value="3">{lng p="bms_origin_dialup"}</option>
					<option value="4">{lng p="bms_origin_reject"}</option>
					<option value="5">{lng p="bms_origin_nogrey"}</option>
					<option value="6">{lng p="bms_origin_nogreyandban"}</option>
				</select></td>
				<td><select name="dnsbls[0][type]">
					<option value="ipv4">IPv4</option>
					<option value="ipv6">IPv6</option>
					<option value="both">{lng p="both"}</option>
				</select></td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</fieldset>

	<p>
		<div style="float:left" class="buttons">
			<input class="button" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='{$pageURL}&action=smtp&sid={$sid}';" />
		</div>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
