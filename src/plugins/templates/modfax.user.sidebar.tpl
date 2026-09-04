{if $_tplname=='modern' || $_tplname=='tabler'}
<div class="sidebarHeading">{lng p="modfax_fax"}</div>
<div class="contentMenuIcons bm-fax-sidebar">
	<a href="{sessionurl file='start.php' params='action=faxPlugin'}"{if $faxDo=='compose'} class="active"{/if}><i class="ti ti-send icon icon-sm me-1" aria-hidden="true"></i>{lng p="modfax_send"}</a><br />
	<a href="{sessionurl file='start.php' params='action=faxPlugin&do=outbox'}"{if $faxDo=='outbox'} class="active"{/if}><i class="ti ti-archive icon icon-sm me-1" aria-hidden="true"></i>{lng p="modfax_outbox"}</a><br />
	<a href="{sessionurl file='prefs.php' params='action=membership'}"><i class="ti ti-coins icon icon-sm me-1" aria-hidden="true"></i>{lng p="accbalance"}</a><br />
</div>
{else}
<div class="sidebarHeading"> &nbsp; {lng p="modfax_fax"}</div>
<div class="contentMenuIcons">
	&nbsp;<a href="start.php?action=faxPlugin{$sessionUrlSuffixHtml}"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> {lng p="modfax_send"}</a><br />
	&nbsp;<a href="start.php?action=faxPlugin&do=outbox{$sessionUrlSuffixHtml}"><i class="fa fa-archive" aria-hidden="true"></i> {lng p="modfax_outbox"}</a><br />
	&nbsp;<a href="prefs.php?action=membership{$sessionUrlSuffixHtml}"><i class="fa fa-id-card-o" aria-hidden="true"></i> {lng p="accbalance"}</a><br />
</div>
{/if}
