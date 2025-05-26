<fieldset>
	<legend>{lng p="tempfiles"}</legend>

	<form action="optimize.php?action=filesystem&do=cleanupTempFiles&sid={$sid}" method="post" onsubmit="spin(this)">
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
			<input class="btn btn-sm btn-warning" type="submit" value="{lng p="cleanup"}" />
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

			<div style="float: right;"><input class="btn btn-sm btn-warning" type="button" onclick="rebuildBlobStor()" value="{lng p="execute"}" /></div>
			<div style="float: right;"><input type="text" class="form-control form-control-sm" id="buildPerPage" value="50" size="5" />&nbsp; </div>
			<div style="float: right;">{lng p="opsperpage"}&nbsp; </div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="userdbvacuum"}</legend>

		<div class="alert alert-warning">{lng p="heavyop"}</div>

		<div id="vacuumForm">
			<p>{lng p="userdbvacuum_desc"}</p>

			<div style="float: right;"><input class="btn btn-sm btn-warning" type="submit" onclick="vacuumBlobStor()" value="{lng p="execute"}" /></div>
			<div style="float: right;"><input type="text" class="form-control form-control-sm" id="vacuumPerPage" value="5" size="5" />&nbsp; </div>
			<div style="float: right;">{lng p="opsperpage"}&nbsp; </div>
		</div>
	</fieldset>
{/if}
