<div class="sidebarHeading">{lng p="sms"}</div>
<div class="contentMenuIcons bm-sms-sidebar">
	<a href="{sessionurl file='sms.php'}"{if $pageContent=='li/sms.compose.tpl'} class="active"{/if}><i class="ti ti-message-plus icon icon-sm me-1" aria-hidden="true"></i>{lng p="sendsms"}</a><br />
	<a href="{sessionurl file='sms.php' params='action=outbox'}"{if $pageContent=='li/sms.outbox.tpl'} class="active"{/if}><i class="ti ti-inbox icon icon-sm me-1" aria-hidden="true"></i>{lng p="smsoutbox"}</a><br />
	<a href="{sessionurl file='prefs.php' params='action=membership'}"><i class="ti ti-coins icon icon-sm me-1" aria-hidden="true"></i>{lng p="accbalance"}</a><br />
</div>
