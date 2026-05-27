<div class="sidebarHeading">{lng p="sms"}</div>
<div class="contentMenuIcons bm-sms-sidebar">
	<a href="sms.php?sid={$sid}"{if $pageContent=='li/sms.compose.tpl'} class="active"{/if}><i class="ti ti-message-plus icon icon-sm me-1" aria-hidden="true"></i>{lng p="sendsms"}</a><br />
	<a href="sms.php?action=outbox&sid={$sid}"{if $pageContent=='li/sms.outbox.tpl'} class="active"{/if}><i class="ti ti-inbox icon icon-sm me-1" aria-hidden="true"></i>{lng p="smsoutbox"}</a><br />
	<a href="prefs.php?action=membership&sid={$sid}"><i class="ti ti-coins icon icon-sm me-1" aria-hidden="true"></i>{lng p="accbalance"}</a><br />
</div>
