<fieldset>
	<legend>{lng p="domains"}</legend>

	<form action="{sessionurl file='prefs.common.php' params="action=domains"}" name="f1" method="post" onsubmit="spin(this)">
		{csrffield}
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th width="25" style="text-align:center;"><a href="javascript:invertSelection2(document.forms.f1,'domains[','[del]');">{acpicon file="dot.png" size="sm"}</a></th>
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
							<td class="text-center">
								<label class="form-check d-inline-block mb-0">
									<input class="form-check-input" type="checkbox" name="domains[{$domain.domain}][del]" />
								</label>
							</td>
							<td>{domain value=$domain.domain}</td>
							<td class="text-center">
								<label class="form-check d-inline-block mb-0">
									<input class="form-check-input" type="checkbox" name="domains[{$domain.domain}][in_login]"{if $domain.in_login} checked="checked"{/if} />
								</label>
							</td>
							<td class="text-center">
								<label class="form-check d-inline-block mb-0">
									<input class="form-check-input" type="checkbox" name="domains[{$domain.domain}][in_signup]"{if $domain.in_signup} checked="checked"{/if} />
								</label>
							</td>
							<td class="text-center">
								<label class="form-check d-inline-block mb-0">
									<input class="form-check-input" type="checkbox" name="domains[{$domain.domain}][in_aliases]"{if $domain.in_aliases} checked="checked"{/if} />
								</label>
							</td>
							<td><input type="text" name="domains[{$domain.domain}][pos]" value="{if isset($domain.pos)}{text value=$domain.pos allowEmpty=true}{/if}" size="6" /></td>
							<td>
								<a href="{sessionurl file='prefs.common.php' params="action=domains&delete={$domain.urlDomain}"}" class="btn btn-sm" onclick="return confirm('{lng p="realdel"}');"><i class="fa-regular fa-trash-can"></i></a>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2">
				<div class="input-group input-group-sm bm-mass-action">
					<span class="input-group-text">{lng p="action"}</span>
					<select name="massAction" class="form-select">
						<option value="-">------------</option>
						<optgroup label="{lng p="actions"}">
							<option value="delete">{lng p="delete"}</option>
						</optgroup>
					</select>
					<button type="submit" name="executeMassAction" class="btn btn-dark-lt" value="1">{lng p="execute"}</button>
				</div>
				<input type="submit" name="save" value="{lng p="save"}" class="btn btn-sm btn-primary" />
			</div>
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="adddomain"}</legend>

	<form action="{sessionurl file='prefs.common.php' params="action=domains&add=true"}" method="post" onsubmit="spin(this)">
		{csrffield}
		<div class="input-group">
			<input type="text" class="form-control" name="domain" placeholder="{lng p="domain"}" aria-label="{lng p="domain"}">
			<span class="input-group-text">{lng p="pos"}</span>
			<input type="number" class="form-control" name="pos" value="0" aria-label="{lng p="pos"}" style="flex: 0 0 5.5rem; width: 5.5rem;">
			<label class="input-group-text">
				<input class="form-check-input mt-0 me-2" type="checkbox" name="in_login" checked="checked">
				{lng p="login"}
			</label>
			<label class="input-group-text">
				<input class="form-check-input mt-0 me-2" type="checkbox" name="in_signup" checked="checked">
				{lng p="signup"}
			</label>
			<label class="input-group-text">
				<input class="form-check-input mt-0 me-2" type="checkbox" name="in_aliases" checked="checked">
				{lng p="aliases"}
			</label>
			<button class="btn btn-primary" type="submit">{lng p="add"}</button>
		</div>
	</form>
</fieldset>
