<fieldset>
	<legend>{lng p="pop3gateway"}</legend>
	
	<div id="form">
		<p>{lng p="pop3fetch_desc"}</p>

		<div style="float: right;"><input class="btn btn-sm btn-warning" type="button" value="{lng p="execute"}" onclick="fetchPOP3()" /></div>
		<div style="float: right;"><input type="text" class="form-control form-control-sm" name="perpage" id="perpage" value="5" size="5" />&nbsp; </div>
		<div style="float: right;">{lng p="opsperpage"}&nbsp; </div>
	</div>
</fieldset>