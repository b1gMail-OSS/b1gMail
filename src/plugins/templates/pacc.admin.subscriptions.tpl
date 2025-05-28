<form action="{$pageURL}&action=subscriptions&filter=true&sid={$sid}" method="post" onsubmit="spin(this)" name="f1">
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
						<th style="width: 25px; text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'subscriber[');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th><a href="javascript:updateSort('benutzer');">{lng p="user"}
								{if $sortBy=='benutzer'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th><a href="javascript:updateSort('paket');">{lng p="pacc_package"}
								{if $sortBy=='paket'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th style="width: 175px;"><a href="javascript:updateSort('letzte_zahlung');">{lng p="pacc_lastpayment"}
								{if $sortBy=='letzte_zahlung'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th style="width: 175px;"><a href="javascript:updateSort('ablauf');">{lng p="pacc_expiration"}
								{if $sortBy=='ablauf'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
						<th style="width: 45px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$subscriptions item=subscription}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center"><input type="checkbox" name="subscriber[{$subscription.id}]" /></td>
							<td><a href="users.php?do=edit&id={$subscription.user.id}&sid={$sid}">{email value=$subscription.user.email cut=25}</a><br />
								<small>{text value=$subscription.user.nachname cut=20}, {text value=$subscription.user.vorname cut=20}</small></td>
							<td><a href="{$pageURL}&action=packages&do=edit&id={$subscription.package.id}&sid={$sid}">{if $subscription.package.deleted}<font color="#666666">{/if}{text value=$subscription.package.title cut=20}{if $subscription.package.deleted}</font>{/if}</a></td>
							<td>{date timestamp=$subscription.lastPayment nice=true}</td>
							<td>{if $subscription.expiration==-1}({lng p="unlimited"}){else}{date timestamp=$subscription.expiration nice=true}{/if}</td>
							<td>
								<a href="javascript:singleAction('cancel', '{$subscription.id}');" onclick="return confirm('{lng p="pacc_realcancel"}');" title="{lng p="pacc_cancelsubscr"}" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
							</td>
						</tr>
					{/foreach}
				</table>
			</div>
			<div class="card-footer">
				<div style="float: left;">{lng p="action"}:&nbsp;</div>
				<div style="float: left;">
					<div class="btn-group btn-group-sm">
						<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="cancel">{lng p="pacc_cancelsubscr"}</option>
								<option value="extend">{lng p="pacc_extendsubscr"}</option>
							</optgroup>
						</select>
						<input type="submit" name="executeMassAction" value="{lng p="execute"}" class="btn btn-sm btn-dark-lt" />
					</div>
				</div>
				<div class="text-end">
					{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"javascript:updatePage(.s);\">.t</a></span> "}&nbsp;
				</div>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="filter"}</legend>

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
			<input class="btn btn-sm btn-primary" type="submit" value=" {lng p="apply"} " />
		</div>
	</fieldset>
</form>