<fieldset>
	<legend>{lng p="tempfiles"}</legend>

	<form action="{sessionurl file='optimize.php' params="action=filesystem&do=cleanupTempFiles"}" method="post" onsubmit="spin(this)">
		{csrffield}
		<p>{lng p="tempdesc"}</p>

		<div class="row">
			<label class="col-sm-2 col-form-label">{lng p="count"}</label>
			<div class="col-sm-10">
				<div class="form-control-plaintext">{$tempFileCount}</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="size"}</label>
			<div class="col-sm-10">
				<div class="form-control-plaintext">{size bytes=$tempFileSize}</div>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="cleanup"}" />
		</div>
	</form>
</fieldset>

{if $haveSQLite3}
	<fieldset>
		<legend>{lng p="rebuildblobstor"}</legend>

		<div class="alert alert-warning">{lng p="heavyop"}</div>

		<div id="buildForm">
			<p>{lng p="rebuildblobstor_desc"}</p>

			<div class="mb-3 row">
				<div class="col-sm-12">
					<label class="form-check">
						<input class="form-check-input" type="radio" id="rebuild_email" name="rebuild" value="email" checked="checked">
						<span class="form-check-label">{lng p="rbbs_email"}</span>
					</label>
					<label class="form-check">
						<input class="form-check-input" type="radio" id="rebuild_webdisk" name="rebuild" value="webdisk">
						<span class="form-check-label">{lng p="rbbs_webdisk"}</span>
					</label>
				</div>
			</div>

			<div class="text-end">
				<div class="input-group input-group-sm bm-ops-action">
					<span class="input-group-text">{lng p="opsperpage"}</span>
					<input type="text" class="form-control" id="buildPerPage" value="50">
					<button type="button" class="btn btn-primary" onclick="rebuildBlobStor()">{lng p="execute"}</button>
				</div>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="userdbvacuum"}</legend>

		<div class="alert alert-warning">{lng p="heavyop"}</div>

		<div id="vacuumForm">
			<p>{lng p="userdbvacuum_desc"}</p>

			<div class="text-end">
				<div class="input-group input-group-sm bm-ops-action">
					<span class="input-group-text">{lng p="opsperpage"}</span>
					<input type="text" class="form-control" id="vacuumPerPage" value="5">
					<button type="button" class="btn btn-primary" onclick="vacuumBlobStor()">{lng p="execute"}</button>
				</div>
			</div>
		</div>
	</fieldset>
{/if}
