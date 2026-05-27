<div class="col-12 bm-li-email-toolbar py-0">
	<div class="d-flex flex-wrap align-items-center gap-2 gap-md-3 w-100">
		{hook id="email.toolbar.tpl:firstColumn"}

		<div class="bm-li-toolbar-item d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0">
			<span class="bm-li-toolbar-label" aria-label="{lng p="space"}">
				<i class="icon ti ti-database icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text">{lng p="space"}</span>
			</span>
			<div class="bm-li-toolbar-progress">
				{progressBar value=$spaceUsed max=$spaceLimit width=120}
			</div>
			<span class="bm-li-toolbar-meta">{size bytes=$spaceUsed} / {size bytes=$spaceLimit} {lng p="used"}</span>
		</div>

		{if !empty($enablePreview)}
		<div class="bm-li-toolbar-divider d-none d-md-block" aria-hidden="true"></div>
		<div class="bm-li-toolbar-item d-flex align-items-center gap-2">
			<span class="bm-li-toolbar-label" aria-label="{lng p="preview"}">
				<i class="icon ti ti-layout-columns icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text">{lng p="preview"}</span>
			</span>
			<select class="form-select form-select-sm bm-li-preview-select" onchange="updatePreviewPosition(this)">
				<option value="bottom"{if !$narrow} selected="selected"{/if}>{lng p="bottom"}</option>
				<option value="right"{if $narrow} selected="selected"{/if}>{lng p="right"}</option>
			</select>
		</div>
		{/if}

		{hook id="email.toolbar.tpl:lastColumn"}
	</div>
</div>
