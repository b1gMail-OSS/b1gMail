{if $_tplname=='modern'}
<div id="contentHeader">
	<div class="left">
		<i class="fa fa-id-card-o" aria-hidden="true"></i>
		{lng p="cancelmembership"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
{else}
<h1><i class="fa fa-id-card-o" aria-hidden="true"></i> {lng p="cancelmembership"}</h1>
{/if}

<table>
	<tr>
		<td valign="top" width="64" align="center"><i class="fa fa-info-circle fa-5x" aria-hidden="true"></i></td>
		<td valign="top">
			{lng p="pacc_cancelwarning"}
			<br /><br />
			<table class="listTable">
				<tr>
					<th class="listTableHead" colspan="2"> {lng p="pacc_activesubscription"}</th>
				</tr>
				<tr>
					<td class="listTableLeft">{lng p="pacc_package"}:</td>
					<td class="listTableRight">
						<i class="fa fa-archive" aria-hidden="true"></i>
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
			<br />
			<input type="button" value="&laquo; {lng p="back"}" onclick="history.back();" />
			<input type="button" class="primary" value=" {lng p="pacc_next"} (30) " onclick="document.location.href='prefs.php?action=membership&do=cancelAccount&paccContinue=true&sid={$sid}';" disabled="disabled" id="cancelButton" />
		</td>
	</tr>
</table>


<script type="text/javascript">
<!--
	{literal}var i = 30;

	function cancelTimer()
	{
		i--;

		if(i==0)
		{
			EBID('cancelButton').value = '{/literal}{lng p="pacc_next"}{literal} >>';
			EBID('cancelButton').disabled = false;
		}
		else
		{
			EBID('cancelButton').value = '{/literal}{lng p="pacc_next"}{literal} (' + i + ')';
			window.setTimeout('cancelTimer()', 1000);
		}
	}

	window.setTimeout('cancelTimer()', 1000);{/literal}
//-->
</script>

{if $_tplname=='modern'}
</div></div>
{/if}
