<fieldset>
	<legend>{lng p="buildindex"}</legend>

	<div class="alert alert-warning">{lng p="heavyop"}</div>

	<div id="buildForm">
		<p>{lng p="buildindex_desc"}</p>

		<div style="float: right;"><input class="btn btn-sm btn-warning" type="button" value="{lng p="execute"}" onclick="buildIndex()" /></div>
		<div style="float: right;"><input type="text" class="form-control form-control-sm" id="buildPerPage" value="50" size="5" />&nbsp; </div>
		<div style="float: right;">{lng p="opsperpage"}&nbsp; </div>
	</div>
</fieldset>

<fieldset>
	<legend>{lng p="optimizeindex"}</legend>

	<div class="alert alert-warning">{lng p="heavyop"}</div>

	<div id="optimizeForm">
		<p>{lng p="optimizeindex_desc"}</p>

		<div style="float: right;"><input class="btn btn-sm btn-warning" type="button" value="{lng p="execute"}" onclick="optimizeIndex()" /></div>
		<div style="float: right;"><input type="text" class="form-control form-control-sm" id="optimizePerPage" value="5" size="5" />&nbsp; </div>
		<div style="float: right;">{lng p="opsperpage"}&nbsp; </div>
	</div>
</fieldset>
