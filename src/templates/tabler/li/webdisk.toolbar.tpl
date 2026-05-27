<div class="col-12 bm-li-email-toolbar py-0">
	<div class="d-flex flex-wrap align-items-center gap-2 gap-md-3 w-100">
		{hook id="webdisk.toolbar.tpl:firstColumn"}

		<div class="bm-li-toolbar-item d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0">
			<span class="bm-li-toolbar-label" aria-label="{lng p="space"}">
				<i class="icon ti ti-database icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text">{lng p="space"}</span>
			</span>
			<div class="bm-li-toolbar-progress">
				{progressBar value=$spaceUsed max=$spaceLimit width=120}
			</div>
			<span class="bm-li-toolbar-meta">{size bytes=$spaceUsed} / {size bytes=$spaceLimit}</span>
		</div>

		{if $trafficLimit>0}
		<div class="bm-li-toolbar-divider d-none d-md-block" aria-hidden="true"></div>
		<div class="bm-li-toolbar-item d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0">
			<span class="bm-li-toolbar-label" aria-label="{lng p="traffic"}">
				<i class="icon ti ti-arrows-exchange icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text">{lng p="traffic"}</span>
			</span>
			<div class="bm-li-toolbar-progress">
				{progressBar value=$trafficUsed max=$trafficLimit width=120}
			</div>
			<span class="bm-li-toolbar-meta">{size bytes=$trafficUsed} / {size bytes=$trafficLimit}</span>
		</div>
		{/if}

		<div class="bm-li-toolbar-divider d-none d-md-block" aria-hidden="true"></div>
		<div class="bm-li-toolbar-item d-flex align-items-center gap-2 ms-md-auto">
			<span class="bm-li-toolbar-label" aria-label="{lng p="viewmode"}">
				<i class="icon ti ti-layout-grid icon-1" aria-hidden="true"></i>
				<span class="bm-li-toolbar-label-text">{lng p="viewmode"}</span>
			</span>
			<select class="form-select form-select-sm bm-li-toolbar-select" onchange="updateWebdiskViewMode(this, '{$folderID}', '{$sid}')">
				<option value="icons"{if $viewMode=="icons"} selected="selected"{/if}>{lng p="icons"}</option>
				<option value="list"{if $viewMode=="list"} selected="selected"{/if}>{lng p="list"}</option>
			</select>
		</div>

		{hook id="webdisk.toolbar.tpl:lastColumn"}
	</div>
</div>
