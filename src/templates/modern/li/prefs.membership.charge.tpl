<div id="contentHeader">
	<div class="left">
		<i class="fa fa-id-card-o" aria-hidden="true"></i>
		{lng p="charge"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form action="{sessionurl file='prefs.php' params='action=membership&do=chargeAccount'}" method="post">
	{csrffield}
<table class="listTable">
	<tr>
		<th class="listTableHead" colspan="2"> {lng p="charge"}</th>
	</tr>
	<tr>
		<td class="listTableLeft">&nbsp;</td>
		<td class="listTableRight">
			{lng p="charge_desc"}<br /><br />
			{if $minAmount}{$minAmount}<br /><br />{/if}
			{if $error}<div class="note">{$error}</div><br /><br />{/if}
		</td>
	</tr>
	<tr>
		<td class="listTableLeft">{lng p="charge2"}:</td>
		<td class="listTableRight">
			<input type="text" name="credits" value="{if $credits}{$credits}{else}{$minCredits}{/if}" size="8" />
			{$priceText}
		</td>
	</tr>
	<tr>
		<td class="listTableLeft">&nbsp;</td>
		<td class="listTableRight">
			<input type="submit" class="primary" value=" {lng p="ok"} &raquo; " />
		</td>
	</tr>
</table>
</form>

{if $credits}
<br />
<form action="{sessionurl file='prefs.php' params='action=membership&do=chargeAccount'}" method="post">
	{csrffield}
<input type="hidden" name="credits" value="{$credits}" />
<input type="hidden" name="submitOrder" value="true" />
{include file="li/payment.form.tpl"}
</form>
{/if}

</div></div>
