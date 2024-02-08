{if $fileCache}
	<fieldset>
		<legend>{lng p="filecache"}</legend>

		<form action="optimize.php?action=cache&do=cleanupFileCache&sid={$sid}" method="post" onsubmit="spin(this)">
			<p>{lng p="filecachedesc"}</p>

			<div class="row">
				<label class="col-sm-2 col-form-label">{lng p="count"}</label>
				<div class="col-sm-10">
					<div class="form-control-plaintext">{$cacheFileCount}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-2 col-form-label">{lng p="count"}</label>
				<div class="col-sm-10">
					<div class="form-control-plaintext">{$cacheFileSize}</div>
				</div>
			</div>

			<div class="text-end">
				<input class="btn btn-sm btn-warning" type="submit" value=" {lng p="emptycache"} " />
			</div>
		</form>
	</fieldset>
{/if}

<fieldset>
	<legend>{lng p="rebuildcaches"}</legend>

	<div class="alert alert-warning">{lng p="heavyop"}</div>

	<div id="form">
		<p>{lng p="rebuild_desc"}</p>

		<div class="mb-3">
			<div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
				<label class="form-selectgroup-item flex-fill">
					<input class="form-selectgroup-input" type="radio" id="rebuild_usersizes" name="rebuild" value="usersizes" checked="checked">
					<div class="form-selectgroup-label d-flex align-items-center p-3">
						<div class="me-3"><span class="form-selectgroup-check"></span></div>
						<div>{lng p="usersizes_cache"}<br /><small>{lng p="usersizes_desc"}</small></div>
					</div>
				</label>
				<label class="form-selectgroup-item flex-fill">
					<input class="form-selectgroup-input" type="radio" id="rebuild_disksizes" name="rebuild" value="disksizes">
					<div class="form-selectgroup-label d-flex align-items-center p-3">
						<div class="me-3"><span class="form-selectgroup-check"></span></div>
						<div>{lng p="disksizes_cache"}<br /><small>{lng p="disksizes_desc"}</small></div>
					</div>
				</label>
				<label class="form-selectgroup-item flex-fill">
					<input class="form-selectgroup-input" type="radio" id="rebuild_mailsizes" name="rebuild" value="mailsizes">
					<div class="form-selectgroup-label d-flex align-items-center p-3">
						<div class="me-3"><span class="form-selectgroup-check"></span></div>
						<div>{lng p="emailsizes_cache"}<br /><small>{lng p="emailsizes_desc"}</small></div>
					</div>
				</label>
			</div>
		</div>

		<div style="float: right;"><input class="btn btn-sm btn-warning" type="submit" onclick="rebuildCaches()" value="{lng p="execute"}" /></div>
		<div style="float: right;"><input type="text" class="form-control form-control-sm" name="perpage" id="perpage" value="50" size="5" />&nbsp; </div>
		<div style="float: right;">{lng p="opsperpage"}&nbsp; </div>
	</div>
</fieldset>