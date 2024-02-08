<form action="{$pageURL}&sid={$sid}&action=msgqueue&do=deliveryRules&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="bms_deliveryrules"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th>{lng p="type"}</th>
						<th>{lng p="field"}</th>
						<th>{lng p="bms_rule"}</th>
						<th>{lng p="bms_target"}</th>
						<th>{lng p="bms_param"}</th>
						<th>{lng p="options"}</th>
						<th style="width: 80px;">{lng p="pos"}</th>
						<th>{lng p="delete"}</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$rules item=rule key=ruleID}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td>
								<select name="rules[{$ruleID}][mail_type]" class="form-select">
									<option value="0"{if $rule.mail_type==0} selected="selected"{/if}>{lng p="bms_inbound"}</option>
									<option value="1"{if $rule.mail_type==1} selected="selected"{/if}>{lng p="bms_outbound"}</option>
								</select>
							</td>
							<td>
								<select name="rules[{$ruleID}][rule_subject]" class="form-select">
									<option value="1"{if $rule.rule_subject==1} selected="selected"{/if}>{lng p="bms_sender"}</option>
									<option value="2"{if $rule.rule_subject==2} selected="selected"{/if}>{lng p="bms_recipient"}</option>
									<option value="0"{if $rule.rule_subject==0} selected="selected"{/if}>{lng p="bms_recpdomain"}</option>
								</select>
							</td>
							<td><input type="text" class="form-control" name="rules[{$ruleID}][rule]" value="{if isset($rule.rule)}{text value=$rule.rule allowEmpty=true}{/if}" size="16" /></td>
							<td>
								<select name="rules[{$ruleID}][target]" class="form-select">
									<option value="0"{if $rule.target==0} selected="selected"{/if}>{lng p="bms_target_0"}</option>
									<option value="1"{if $rule.target==1} selected="selected"{/if}>{lng p="bms_redirecttosendmail"}</option>
									<option value="2"{if $rule.target==2} selected="selected"{/if}>{lng p="bms_redirecttosmtprelay"}</option>
									<option value="3"{if $rule.target==3} selected="selected"{/if}>{lng p="bms_target_3"}</option>
								</select>
							</td>
							<td><input type="text" class="form-control" name="rules[{$ruleID}][target_param]" value="{if isset($rule.target_param)}{text value=$rule.target_param allowEmpty=true}{/if}" size="16" /></td>
							<td>
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="rules[{$ruleID}][flags][]" value="1" id="rule{$ruleID}_flag1"{if $rule.flags&1} checked="checked"{/if}>
									<span class="form-check-label">{lng p="bms_flag_ci"}</span>
								</label>
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="rules[{$ruleID}][flags][]" value="2" id="rule{$ruleID}_flag2"{if $rule.flags&2} checked="checked"{/if}>
									<span class="form-check-label">{lng p="bms_flag_regexp"}</span>
								</label>
							</td>
							<td><input type="text" class="form-control" name="rules[{$ruleID}][pos]" value="{$rule.pos}" size="6" /></td>
							<td><input type="checkbox" class="form-check-input" name="rules[{$ruleID}][delete]" /></td>
						</tr>
					{/foreach}

					{cycle name=class values="td1,td2" assign=class}
					<tr class="{$class}">
						<td>
							<select name="rules[0][mail_type]" class="form-select">
								<option value="0">{lng p="bms_inbound"}</option>
								<option value="1">{lng p="bms_outbound"}</option>
							</select>
						</td>
						<td>
							<select name="rules[0][rule_subject]" class="form-select">
								<option value="1">{lng p="bms_sender"}</option>
								<option value="2">{lng p="bms_recipient"}</option>
								<option value="0">{lng p="bms_recpdomain"}</option>
							</select>
						</td>
						<td><input type="text" class="form-control" name="rules[0][rule]" value="" size="16" /></td>
						<td>
							<select name="rules[0][target]" class="form-select">
								<option value="0">{lng p="bms_target_0"}</option>
								<option value="1">{lng p="bms_redirecttosendmail"}</option>
								<option value="2">{lng p="bms_redirecttosmtprelay"}</option>
								<option value="3">{lng p="bms_target_3"}</option>
							</select>
						</td>
						<td><input type="text" class="form-control" name="rules[0][target_param]" value="" size="16" /></td>
						<td>
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="rules[0][flags][]" value="1" id="rule0_flag1">
								<span class="form-check-label">{lng p="bms_flag_ci"}</span>
							</label>
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="rules[0][flags][]" value="2" id="rule0_flag2">
								<span class="form-check-label">{lng p="bms_flag_regexp"}</span>
							</label>
						</td>
						<td><input type="text" class="form-control" name="rules[0][pos]" value="{$nextPos}" size="6" /></td>
						<td>&nbsp;</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</fieldset>

	<div class="row">
	<div class="col-md-6">
		<input class="btn btn-primary" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='{$pageURL}&action=msgqueue&sid={$sid}';" />
	</div>
	<div class="col-md-6 text-end">
		<input class="btn btn-primary" type="submit" value=" {lng p="save"} " />
	</div>
	</div>
</form>
