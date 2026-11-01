{if $paccMsg}
<div class="alert alert-{$paccMsg.type} mb-3" role="alert">{$paccMsg.text}</div>
{/if}

<form action="{sessionurl file='plugin.page.php' params="plugin={$paccPlugin}&do=subscriptions"}" method="post" onsubmit="spin(this)" name="f1">
	{csrffield}
	<input type="hidden" name="page" id="page" value="{$pageNo}" />
	<input type="hidden" name="sortBy" id="sortBy" value="{$sortBy}" />
	<input type="hidden" name="sortOrder" id="sortOrder" value="{$sortOrder}" />
	<input type="hidden" name="singleAction" id="singleAction" value="" />
	<input type="hidden" name="singleID" id="singleID" value="" />

	<fieldset>
		<legend>{lng p="pacc_subscriptions"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 25px; text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'subscriber[');">{acpicon file="dot.png" size="sm"}</a></th>
						<th><a href="javascript:updateSort('benutzer');">{lng p="user"}
								{if $sortBy=='benutzer'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th><a href="javascript:updateSort('paket');">{lng p="pacc_package"}
								{if $sortBy=='paket'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th style="width: 175px;"><a href="javascript:updateSort('letzte_zahlung');">{lng p="pacc_lastpayment"}
								{if $sortBy=='letzte_zahlung'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th style="width: 175px;"><a href="javascript:updateSort('ablauf');">{lng p="pacc_expiration"}
								{if $sortBy=='ablauf'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th style="width: 45px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$subscriptions item=subscription}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center"><input type="checkbox" name="subscriber[{$subscription.id}]" /></td>
							<td><a href="users.php?do=edit&id={$subscription.user.id}{$sessionUrlSuffixHtml}">{email value=$subscription.user.email cut=25}</a><br />
								<small>{text value=$subscription.user.nachname cut=20}, {text value=$subscription.user.vorname cut=20}</small></td>
							<td><a href="{sessionurl file='plugin.page.php' params="plugin={$paccPlugin}&do=packages&action=edit&id={$subscription.package.id}"}">{if $subscription.package.deleted}<span class="text-secondary">{/if}{text value=$subscription.package.title cut=20}{if $subscription.package.deleted}</span>{/if}</a></td>
							<td>{date timestamp=$subscription.lastPayment nice=true}</td>
							<td>{if $subscription.expiration==-1}({lng p="unlimited"}){else}{date timestamp=$subscription.expiration nice=true}{/if}</td>
							<td>
								<a href="javascript:singleAction('cancel', '{$subscription.id}');" onclick="return confirm('{lng p="pacc_realcancel"}');" title="{lng p="pacc_cancelsubscr"}" class="btn btn-outline-danger btn-sm"><i class="ti ti-trash"></i></a>
							</td>
						</tr>
					{/foreach}
				</table>
			</div>
			<div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2">
				<div class="input-group input-group-sm bm-mass-action">
					<span class="input-group-text">{lng p="action"}</span>
					<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="cancel">{lng p="pacc_cancelsubscr"}</option>
								<option value="extend">{lng p="pacc_extendsubscr"}</option>
							</optgroup>
						</select>
					<button type="submit" name="executeMassAction" value="1" class="btn btn-sm btn-primary">{lng p="execute"}</button>
				</div>
				<div class="text-end">
					{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"javascript:updatePage(.s);\">.t</a></span> "}&nbsp;
				</div>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="filter"}</legend>
		<input type="hidden" name="filter" value="1" />

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="pacc_packages"}</label>
			<div class="col-sm-10">
				{foreach from=$packages item=package key=packageID}
					<label class="form-check">
						<input class="form-check-input" type="checkbox" name="packages[{$packageID}]" id="package_{$packageID}"{if $package.checked} checked="checked"{/if}>
						<span class="form-check-label">{if $package.deleted}<p class="text-muted">{/if}{text value=$package.title}{if $package.deleted}</p>{/if}</span>
					</label>
				{/foreach}
			</div>
		</div>

		<div class="text-end">
			{lng p="perpage"}:
			<input type="text" name="perPage" value="{$perPage}" size="5" />
			<button type="submit" class="btn btn-sm btn-primary">{lng p="apply"}</button>
		</div>
	</fieldset>
</form>