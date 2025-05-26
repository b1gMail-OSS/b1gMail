<fieldset>
	<legend>{lng p="archiving"}</legend>
		
	<form action="logs.php?action=archiving&do=archive&sid={$sid}" method="post" onsubmit="if(EBID('saveCopy').checked || confirm('{lng p="reallynotarc"}')) spin(this); else return(false);">
		<p>{lng p="logarc_desc"}</p>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="date"}</label>
			<div class="col-sm-10">
				{html_select_date prefix="date" start_year="-5" field_order="DMY" field_separator="."},
				{html_select_time prefix="date" display_seconds=false}
			</div>
		</div>

		<div style="float: right;"><input class="btn btn-sm" type="submit" onclick="rebuildCaches()" value="{lng p="execute"}" /></div>
		<div style="float: right;"><input type="text" class="form-control form-control-sm" name="perpage" id="perpage" value="50" size="5" />&nbsp; </div>
		<div style="float: right;">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="saveCopy" id="saveCopy" checked="checked">
				<span class="form-check-label">{lng p="savearc"}&nbsp; </span>
			</label>&nbsp;
		</div>
	</form>
</fieldset>
