<fieldset>
	<legend>{lng p="domains"}</legend>

	<form action="prefs.common.php?action=domains&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th width="25" style="text-align:center;"><a href="javascript:invertSelection2(document.forms.f1,'domains[','[del]');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="domain"}</th>
						<th style="width: 100px; text-align:center;">{lng p="login"}</th>
						<th style="width: 100px; text-align:center;">{lng p="signup"}</th>
						<th style="width: 100px; text-align:center;">{lng p="aliases"}</th>
						<th style="width: 80px;">{lng p="pos"}</th>
						<th style="width: 35px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$domains item=domain}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td><input type="checkbox" name="domains[{$domain.domain}][del]" /></td>
							<td>{domain value=$domain.domain}</td>
							<td style="text-align:center;"><input type="checkbox" name="domains[{$domain.domain}][in_login]"{if $domain.in_login} checked="checked"{/if} /></td>
							<td style="text-align:center;"><input type="checkbox" name="domains[{$domain.domain}][in_signup]"{if $domain.in_signup} checked="checked"{/if} /></td>
							<td style="text-align:center;"><input type="checkbox" name="domains[{$domain.domain}][in_aliases]"{if $domain.in_aliases} checked="checked"{/if} /></td>
							<td><input type="text" name="domains[{$domain.domain}][pos]" value="{if isset($domain.pos)}{text value=$domain.pos allowEmpty=true}{/if}" size="6" /></td>
							<td>
								<a href="prefs.common.php?action=domains&delete={$domain.urlDomain}&sid={$sid}" class="btn btn-sm" onclick="return confirm('{lng p="realdel"}');"><i class="fa-regular fa-trash-can"></i></a>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer">
				<div class="row">
					<div class="col-md-6">
						<div style="float: left;">{lng p="action"}:&nbsp;</div>
						<div style="float: left;">
							<div class="btn-group btn-group-sm">
								<select name="massAction" class="form-select form-select-sm">
									<option value="-">------------</option>
									<optgroup label="{lng p="actions"}">
										<option value="delete">{lng p="delete"}</option>
									</optgroup>
								</select>
								<input type="submit" name="executeMassAction" value="{lng p="execute"}" class="btn btn-sm btn-dark-lt" />
							</div>
						</div>
					</div>
					<div class="col-md-6 text-end">
						<input type="submit" name="save" value="{lng p="save"}" class="btn btn-sm btn-primary" />
					</div>
				</div>
			</div>
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="adddomain"}</legend>

	<form action="prefs.common.php?action=domains&add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="row">
			<div class="col-md-4">
				<div class="mb-3">
					<label class="form-label">{lng p="domain"}</label>
					<input type="text" class="form-control" id="username" name="domain" placeholder="{lng p="domain"}">
				</div>
			</div>
			<div class="col-md-2">
				<div class="mb-3">
					<label class="form-label">{lng p="pos"}</label>
					<input type="number" class="form-control" id="pos" name="pos" value="0" size="6" placeholder="{lng p="pos"}">
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3">
					<label class="form-label">{lng p="show_at"}</label>
					<div class="form-selectgroup">
						<label class="form-selectgroup-item">
							<input type="checkbox" name="in_login" class="form-selectgroup-input" checked="checked">
							<span class="form-selectgroup-label">{lng p="login"}</span>
						</label>
						<label class="form-selectgroup-item">
							<input type="checkbox" name="in_login" class="form-selectgroup-input" checked="checked">
							<span class="form-selectgroup-label">{lng p="signup"}</span>
						</label>
						<label class="form-selectgroup-item">
							<input type="checkbox" name="in_aliases" class="form-selectgroup-input" checked="checked">
							<span class="form-selectgroup-label">{lng p="aliases"}</span>
						</label>
					</div>
				</div>
			</div>
		</div>
		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>
