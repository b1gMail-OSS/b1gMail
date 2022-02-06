<form action="{$pageURL}&action=subscriptions&filter=true&sid={$sid}" method="post" onsubmit="spin(this)" name="f1">
<input type="hidden" name="page" id="page" value="{$pageNo}" />
<input type="hidden" name="sortBy" id="sortBy" value="{$sortBy}" />
<input type="hidden" name="sortOrder" id="sortOrder" value="{$sortOrder}" />
<input type="hidden" name="singleAction" id="singleAction" value="" />
<input type="hidden" name="singleID" id="singleID" value="" />

<fieldset>
	<legend>{lng p="pacc_subscriptions"}</legend>

	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'subscriber[');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th><a href="javascript:updateSort('benutzer');">{lng p="user"}
				{if $sortBy=='benutzer'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th><a href="javascript:updateSort('paket');">{lng p="pacc_package"}
				{if $sortBy=='paket'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="175"><a href="javascript:updateSort('letzte_zahlung');">{lng p="pacc_lastpayment"}
				{if $sortBy=='letzte_zahlung'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="175"><a href="javascript:updateSort('ablauf');">{lng p="pacc_expiration"}
				{if $sortBy=='ablauf'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="45">&nbsp;</th>
		</tr>

		{foreach from=$subscriptions item=subscription}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="../plugins/templates/images/pacc_subscriptions.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center"><input type="checkbox" name="subscriber[{$subscription.id}]" /></td>
			<td><a href="users.php?do=edit&id={$subscription.user.id}&sid={$sid}">{email value=$subscription.user.email cut=25}</a><br />
				<small>{text value=$subscription.user.nachname cut=20}, {text value=$subscription.user.vorname cut=20}</small></td>
			<td><a href="{$pageURL}&action=packages&do=edit&id={$subscription.package.id}&sid={$sid}">{if $subscription.package.deleted}<font color="#666666">{/if}{text value=$subscription.package.title cut=20}{if $subscription.package.deleted}</font>{/if}</a></td>
			<td>{date timestamp=$subscription.lastPayment nice=true}</td>
			<td>{if $subscription.expiration==-1}({lng p="unlimited"}){else}{date timestamp=$subscription.expiration nice=true}{/if}</td>
			<td>
				<a href="javascript:singleAction('cancel', '{$subscription.id}');" onclick="return confirm('{lng p="pacc_realcancel"}');" title="{lng p="pacc_cancelsubscr"}"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="pacc_cancelsubscr"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}

		<tr>
			<td class="footer" colspan="7">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" class="smallInput">
						<option value="-">------------</option>

						<optgroup label="{lng p="actions"}">
							<option value="cancel">{lng p="pacc_cancelsubscr"}</option>
							<option value="extend">{lng p="pacc_extendsubscr"}</option>
						</optgroup>
					</select>&nbsp;
				</div>
				<div style="float:left;">
					<input class="button" type="submit" name="executeMassAction" value=" {lng p="execute"} " class="smallInput" />
				</div>
				<div style="float:right;padding-top:3px;">
					{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"javascript:updatePage(.s);\">.t</a></span> "}&nbsp;
				</div>
			</td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>{lng p="filter"}</legend>

	<table width="100%">
		<tr>
			<td width="40" valign="top" rowspan="2"><img src="{$tpldir}images/filter.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="80">{lng p="pacc_packages"}:</td>
			<td class="td2">
				{foreach from=$packages item=package key=packageID}
					<input type="checkbox" name="packages[{$packageID}]" id="package_{$packageID}"{if $package.checked} checked="checked"{/if} />
						<label for="package_{$packageID}"><b>{if $package.deleted}<font color="#666666">{/if}{text value=$package.title}{if $package.deleted}</font>{/if}</b></label><br />
				{/foreach}
			</td>
		</tr>
	</table>

	<p align="right">
		{lng p="perpage"}:
		<input type="text" name="perPage" value="{$perPage}" size="5" />
		<input class="button" type="submit" value=" {lng p="apply"} " />
	</p>
</fieldset>

</form>
