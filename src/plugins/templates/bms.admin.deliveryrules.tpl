<form action="{$pageURL}&sid={$sid}&action=msgqueue&do=deliveryRules&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="bms_deliveryrules"}</legend>
		
		<table class="list">
			<tr>
				<th>{lng p="type"}</th>
				<th>{lng p="field"}</th>
				<th>{lng p="bms_rule"}</th>
				<th>{lng p="bms_target"}</th>
				<th>{lng p="bms_param"}</th>
				<th>{lng p="options"}</th>
				<th width="80">{lng p="pos"}</th>
				<th>{lng p="delete"}</th>
			</tr>
			
			{foreach from=$rules item=rule key=ruleID}
			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}">
				<td><select name="rules[{$ruleID}][mail_type]">
						<option value="0"{if $rule.mail_type==0} selected="selected"{/if}>{lng p="bms_inbound"}</option>
						<option value="1"{if $rule.mail_type==1} selected="selected"{/if}>{lng p="bms_outbound"}</option>
					</select></td>
				<td><select name="rules[{$ruleID}][rule_subject]">
						<option value="1"{if $rule.rule_subject==1} selected="selected"{/if}>{lng p="bms_sender"}</option>
						<option value="2"{if $rule.rule_subject==2} selected="selected"{/if}>{lng p="bms_recipient"}</option>
						<option value="0"{if $rule.rule_subject==0} selected="selected"{/if}>{lng p="bms_recpdomain"}</option>
					</select></td>
				<td><input type="text" name="rules[{$ruleID}][rule]" value="{text value=$rule.rule allowEmpty=true}" size="16" /></td>
				<td><select name="rules[{$ruleID}][target]">
						<option value="0"{if $rule.target==0} selected="selected"{/if}>{lng p="bms_target_0"}</option>
						<option value="1"{if $rule.target==1} selected="selected"{/if}>{lng p="bms_redirecttosendmail"}</option>
						<option value="2"{if $rule.target==2} selected="selected"{/if}>{lng p="bms_redirecttosmtprelay"}</option>
						<option value="3"{if $rule.target==3} selected="selected"{/if}>{lng p="bms_target_3"}</option>
					</select></td>
				<td><input type="text" name="rules[{$ruleID}][target_param]" value="{text value=$rule.target_param allowEmpty=true}" size="16" /></td>
				<td>
					<input type="checkbox" name="rules[{$ruleID}][flags][]" value="1" id="rule{$ruleID}_flag1"{if $rule.flags&1} checked="checked"{/if} />
						<label for="rule{$ruleID}_flag1">{lng p="bms_flag_ci"}</label><br />
					<input type="checkbox" name="rules[{$ruleID}][flags][]" value="2" id="rule{$ruleID}_flag2"{if $rule.flags&2} checked="checked"{/if} />
						<label for="rule{$ruleID}_flag2">{lng p="bms_flag_regexp"}</label>
				</td>
				<td><input type="text" name="rules[{$ruleID}][pos]" value="{$rule.pos}" size="6" /></td>
				<td><input type="checkbox" name="rules[{$ruleID}][delete]" /></td>
			</tr>
			{/foreach}
			
			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}">
				<td><select name="rules[0][mail_type]">
					<option value="0">{lng p="bms_inbound"}</option>
					<option value="1">{lng p="bms_outbound"}</option>
				</select></td>
				<td><select name="rules[0][rule_subject]">
						<option value="1">{lng p="bms_sender"}</option>
						<option value="2">{lng p="bms_recipient"}</option>
						<option value="0">{lng p="bms_recpdomain"}</option>
					</select></td>
				<td><input type="text" name="rules[0][rule]" value="" size="16" /></td>
				<td><select name="rules[0][target]">
						<option value="0">{lng p="bms_target_0"}</option>
						<option value="1">{lng p="bms_redirecttosendmail"}</option>
						<option value="2">{lng p="bms_redirecttosmtprelay"}</option>
						<option value="3">{lng p="bms_target_3"}</option>
					</select></td>
				<td><input type="text" name="rules[0][target_param]" value="" size="16" /></td>
				<td>
					<input type="checkbox" name="rules[0][flags][]" value="1" id="rule0_flag1" />
						<label for="rule0_flag1">{lng p="bms_flag_ci"}</label><br />
					<input type="checkbox" name="rules[0][flags][]" value="2" id="rule0_flag2" />
						<label for="rule0_flag2">{lng p="bms_flag_regexp"}</label>
				</td>
				<td><input type="text" name="rules[0][pos]" value="{$nextPos}" size="6" /></td>
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
