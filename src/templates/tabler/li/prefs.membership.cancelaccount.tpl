<div class="bm-prefs-page bm-prefs-page-membership">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-id icon icon-sm" aria-hidden="true"></i>
		{lng p="cancelmembership"}
	</div>
</div>

<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">

<form action="prefs.php?sid={$sid}" method="post">
<input type="hidden" name="action" value="membership" />
<input type="hidden" name="do" value="reallyCancelAccount" />
<input type="hidden" name="really" id="really" value="false" />

<table>
	<tr>
		<td valign="top" width="64" align="center"><i class="fa fa-info-circle fa-5x" aria-hidden="true"></i></td>
		<td valign="top">
			{lng p="canceltext"}
			<br /><br />
			<input type="button" value="&laquo; {lng p="back"}" onclick="history.back();" />
			<input type="submit" value=" {lng p="cancelmembership"} (30) " disabled="disabled" id="cancelButton" />
		</td>
	</tr>
</table>
</form>

<script>
<!--
	{literal}var i = 30;
	
	function cancelTimer()
	{
		i--;
	
		if(i==0)
		{
			EBID('cancelButton').value = '{/literal}{lng p="cancelmembership"}{literal}';
			EBID('cancelButton').disabled = false;
			EBID('cancelButton').className = 'primary';
			EBID('really').value = 'true';
		}
		else
		{
			EBID('cancelButton').value = '{/literal}{lng p="cancelmembership"}{literal} (' + i + ')';
			window.setTimeout('cancelTimer()', 1000);
		}
	}
	
	window.setTimeout('cancelTimer()', 1000);{/literal}
//-->
</script>

</div></div>
</div>
