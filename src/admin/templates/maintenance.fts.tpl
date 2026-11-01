<fieldset>
	<legend>{lng p="buildindex"}</legend>

	<div class="alert alert-warning">{lng p="heavyop"}</div>

	<div id="buildForm">
		<p>{lng p="buildindex_desc"}</p>

		<div class="text-end">
			<div class="input-group input-group-sm bm-ops-action">
				<span class="input-group-text">{lng p="opsperpage"}</span>
				<input type="text" class="form-control" id="buildPerPage" value="50">
				<button type="button" class="btn btn-primary" onclick="buildIndex()">{lng p="execute"}</button>
			</div>
		</div>
	</div>
</fieldset>

<fieldset>
	<legend>{lng p="optimizeindex"}</legend>

	<div class="alert alert-warning">{lng p="heavyop"}</div>

	<div id="optimizeForm">
		<p>{lng p="optimizeindex_desc"}</p>

		<div class="text-end">
			<div class="input-group input-group-sm bm-ops-action">
				<span class="input-group-text">{lng p="opsperpage"}</span>
				<input type="text" class="form-control" id="optimizePerPage" value="5">
				<button type="button" class="btn btn-primary" onclick="optimizeIndex()">{lng p="execute"}</button>
			</div>
		</div>
	</div>
</fieldset>
