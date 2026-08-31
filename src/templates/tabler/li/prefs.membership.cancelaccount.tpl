<div class="bm-prefs-page bm-prefs-page-membership">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-id icon icon-sm" aria-hidden="true"></i>
		{lng p="cancelmembership"}
	</div>
</div>
dasfd
<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">

<form action="{sessionurl file='prefs.php'}" method="post">
	{csrffield}
<input type="hidden" name="action" value="membership" />
<input type="hidden" name="do" value="reallyCancelAccount" />
<input type="hidden" name="really" id="really" value="false" />

<div class="alert alert-info" role="alert">
	<div class="alert-icon">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2">
			<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
			<path d="M12 9h.01"></path>
			<path d="M11 12h1v4h1"></path>
		</svg>
	</div>
	<div>
		<div class="alert-description">{lng p="canceltext"}</div>
	</div>
</div>

<div class="d-flex flex-wrap gap-2 mt-3">
	<input type="button" class="btn btn-outline-secondary" value="&laquo; {lng p="back"}" onclick="history.back();" />
	<input type="submit" class="btn btn-danger" value=" {lng p="cancelmembership"} (30) " disabled="disabled" id="cancelButton" />
</div>
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
			EBID('cancelButton').className = 'btn btn-danger';
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
