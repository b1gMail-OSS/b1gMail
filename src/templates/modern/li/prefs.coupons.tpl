<div id="contentHeader">
	<div class="left">
		<i class="fa fa-id-badge" aria-hidden="true"></i>
		{lng p="coupons"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form name="f1" method="post" action="prefs.php?action=coupons&do=redeem&sid={$sid}">
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
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>

</div></div>
