<div class="bm-prefs-page bm-prefs-page-pacc-cancel">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-id icon icon-sm" aria-hidden="true"></i>
		{lng p="cancelmembership"}
	</div>
</div>

<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">

<div class="d-flex gap-3">
	<div class="flex-fill">
		<div class="alert alert-info alert-dismissible" role="alert">
			<div class="alert-icon">
				<!-- Download SVG icon from http://tabler.io/icons/icon/info-circle -->
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2">
					<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
					<path d="M12 9h.01"></path>
					<path d="M11 12h1v4h1"></path>
				</svg>
			</div>
			<div>
				<div class="alert-description">{lng p="pacc_cancelwarning"}</div>
			</div>
		</div>

		<table class="listTable mt-3">
			<tr>
				<th class="listTableHead" colspan="2">{lng p="pacc_activesubscription"}</th>
			</tr>
			<tr>
				<td class="listTableLeft">{lng p="pacc_package"}:</td>
				<td class="listTableRight">
					<i class="ti ti-package icon icon-sm text-secondary me-1" aria-hidden="true"></i>
					{text value=$activeSubscription.package.titel}
				</td>
			</tr>
			<tr>
				<td class="listTableLeft">{lng p="pacc_lastpayment"}:</td>
				<td class="listTableRight">
					{date timestamp=$activeSubscription.letzte_zahlung}
				</td>
			</tr>
			<tr>
				<td class="listTableLeft">{lng p="pacc_validuntil"}:</td>
				<td class="listTableRight">
					{if $activeSubscription.ablauf<=1}({lng p="unlimited"}){else}{date timestamp=$activeSubscription.ablauf}{/if}
				</td>
			</tr>
		</table>

		<div class="mt-3 d-flex flex-wrap gap-2">
			<button type="button" class="btn btn-outline-secondary" onclick="history.back();">&laquo; {lng p="back"}</button>
			<button type="button" class="btn btn-primary" onclick="document.location.href='prefs.php?action=membership&do=cancelAccount&paccContinue=true{$sessionUrlSuffixHtml}';" disabled="disabled" id="cancelButton">
				{lng p="pacc_next"} (30)
			</button>
		</div>
	</div>
</div>

<script type="text/javascript">
<!--
	{literal}var i = 30;

	function cancelTimer()
	{
		i--;

		if(i==0)
		{
			EBID('cancelButton').textContent = '{/literal}{lng p="pacc_next"}{literal} >>';
			EBID('cancelButton').disabled = false;
		}
		else
		{
			EBID('cancelButton').textContent = '{/literal}{lng p="pacc_next"}{literal} (' + i + ')';
			window.setTimeout('cancelTimer()', 1000);
		}
	}

	window.setTimeout('cancelTimer()', 1000);{/literal}
//-->
</script>

</div></div>
</div>
