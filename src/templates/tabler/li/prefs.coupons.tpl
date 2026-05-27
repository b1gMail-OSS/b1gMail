<div class="bm-prefs-page bm-prefs-page-coupons">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-ticket icon icon-sm" aria-hidden="true"></i>
		{lng p="coupons"}
	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=coupons&do=redeem&sid={$sid}">
<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="redeemcoupon"}</th>
		</tr>
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				{lng p="prefs_d_coupons"}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="code">{lng p="code"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="code" id="code" value="" style="width:250px;" />
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="btn btn-primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</div></div>
</form>
</div>
