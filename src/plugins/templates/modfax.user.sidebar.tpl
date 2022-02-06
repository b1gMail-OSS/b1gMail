{if $_tplname=='modern'}
<div class="sidebarHeading">{lng p="modfax_fax"}</div>
<div class="contentMenuIcons">
	<a href="start.php?action=faxPlugin&sid={$sid}"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> {lng p="modfax_send"}</a><br />
	<a href="start.php?action=faxPlugin&do=outbox&sid={$sid}"><i class="fa fa-archive" aria-hidden="true"></i> {lng p="modfax_outbox"}</a><br />
	<a href="prefs.php?action=membership&sid={$sid}"><i class="fa fa-id-card-o" aria-hidden="true"></i> {lng p="accbalance"}</a><br />
</div>
{else}
<div class="sidebarHeading"> &nbsp; {lng p="modfax_fax"}</div>
<div class="contentMenuIcons">
	&nbsp;<a href="start.php?action=faxPlugin&sid={$sid}"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> {lng p="modfax_send"}</a><br />
	&nbsp;<a href="start.php?action=faxPlugin&do=outbox&sid={$sid}"><i class="fa fa-archive" aria-hidden="true"></i> {lng p="modfax_outbox"}</a><br />
	&nbsp;<a href="prefs.php?action=membership&sid={$sid}"><i class="fa fa-id-card-o" aria-hidden="true"></i> {lng p="accbalance"}</a><br />
</div>
{/if}
