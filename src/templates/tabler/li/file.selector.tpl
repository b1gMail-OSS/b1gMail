<div class="bm-file-selector" data-name="{$name}">
	<div class="input-group bm-file-selector-source-group">
		<span class="input-group-text text-secondary">
			<i class="ti ti-source-code icon" aria-hidden="true"></i>
		</span>
		<select class="form-select bm-file-selector-source" onchange="changeFileSelectorSource(this, '{$name}')">
			<option value="local">{lng p="localfile"}</option>
			{if $hasWebdisk}
			<option value="webdisk">{lng p="webdiskfile"}</option>
			{/if}
		</select>
	</div>

	<div id="fileSelector_local_{$name}" class="bm-file-selector-panel mt-2">
		<div class="input-group">
			<span class="input-group-text text-secondary">
				<i class="ti ti-upload icon" aria-hidden="true"></i>
			</span>
			<input type="file" class="form-control" id="localFile_{$name}" name="localFile_{$name}{if isset($multiple) && $multiple}[]{/if}"{if isset($multiple) && $multiple} multiple="multiple"{/if} />
		</div>
	</div>

	<div id="fileSelector_webdisk_{$name}" class="bm-file-selector-panel mt-2" style="display:none;">
		<input type="hidden" name="webdiskFile_{$name}_id" id="webdiskFile_{$name}_id" value="" />
		<div class="input-group input-group-flat">
			<span class="input-group-text text-secondary">
				<i class="ti ti-cloud icon" aria-hidden="true"></i>
			</span>
			<input type="text" class="form-control" id="webdiskFile_{$name}" name="webdiskFile_{$name}" readonly="readonly" placeholder="{lng p="webdiskfile"}" autocomplete="off" />
			<button type="button" class="btn btn-outline-primary" onclick="webdiskDialog('{$sid}', 'open', 'webdiskFile_{$name}')">
				<i class="ti ti-folder-open icon" aria-hidden="true"></i>
				<span class="d-none d-sm-inline ms-1">{lng p="browse"}</span>
			</button>
		</div>
		<div class="form-hint text-secondary mt-1" id="webdiskFile_{$name}_hint" style="display:none;"></div>
	</div>
</div>
