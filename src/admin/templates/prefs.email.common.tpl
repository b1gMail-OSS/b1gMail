<form action="prefs.email.php?save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="common"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="storein"}</label>
			<div class="col-sm-10">
				<select name="blobstorage_provider" class="form-select">
					<option value="0"{if $bm_prefs.blobstorage_provider==0} selected="selected"{/if}>{lng p="filesystem"} ({lng p="separatefiles"})</option>
					<option value="1"{if $bm_prefs.blobstorage_provider==1} selected="selected"{/if}{if !$bsUserDBAvailable} disabled="disabled"{/if}>{lng p="filesystem"} ({lng p="userdb"})</option>
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="blobcompress"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="blobstorage_compress"{if $bm_prefs.blobstorage_compress=='yes'} checked="checked"{/if}>
					<span class="form-check-label">{lng p="onlyfor"} &quot;{lng p="filesystem"} ({lng p="userdb"})&quot;</span>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="ftsearch"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="fts_bg_indexing"{if $bm_prefs.fts_bg_indexing=='yes'} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="exturl"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="exturl_warning"{if $bm_prefs.exturl_warning=='yes'} checked="checked"{/if}>
				</label>
			</div>
		</div>
	</fieldset>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>
