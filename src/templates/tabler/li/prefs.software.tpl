<div class="bm-prefs-page bm-prefs-page-software">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-download icon icon-sm" aria-hidden="true"></i>
		{lng p="software"}
	</div>
</div>

<div class="scrollContainer bm-prefs-body bm-prefs-list-body">
<div class="card bm-prefs-table-card bm-prefs-software-card">
<div class="card-body bm-prefs-software-body">
	<p class="bm-prefs-software-intro text-secondary mb-4">{$introText}</p>

	<div class="row g-4 bm-prefs-software-downloads">
		{if $releaseFiles.win && $releaseFiles.mac}{assign var="softwareColClass" value="col-12 col-lg-6"}{else}{assign var="softwareColClass" value="col-12 col-xl-8"}{/if}
		{if $releaseFiles.win}
		<div class="{$softwareColClass}">
			<div class="card bm-prefs-software-item h-100">
				<div class="card-body d-flex flex-column">
					<div class="d-flex align-items-start gap-3 mb-3">
						<div class="bm-prefs-software-icon flex-shrink-0" aria-hidden="true">
							<i class="ti ti-brand-windows icon"></i>
						</div>
						<div class="flex-fill min-w-0">
							<h3 class="h4 card-title mb-1">Windows <span class="fs-6 fw-normal text-secondary">({lng p="version"} {$verNo})</span></h3>
							<p class="text-secondary mb-0">{lng p="software_win"}</p>
						</div>
					</div>
					<div class="mt-auto pt-1">
						<button type="button" class="btn btn-primary btn-sm" onclick="document.location.href='{sessionurl file='prefs.php' params='action=software&do=download&os=win'}';">
							<i class="ti ti-download icon icon-sm me-1" aria-hidden="true"></i>
							{lng p="download"}
							<span class="text-white text-opacity-75 ms-1">({size bytes=$fileSizes.win})</span>
						</button>
					</div>
				</div>
			</div>
		</div>
		{/if}

		{if $releaseFiles.mac}
		<div class="{$softwareColClass}">
			<div class="card bm-prefs-software-item h-100">
				<div class="card-body d-flex flex-column">
					<div class="d-flex align-items-start gap-3 mb-3">
						<div class="bm-prefs-software-icon flex-shrink-0" aria-hidden="true">
							<i class="ti ti-brand-apple icon"></i>
						</div>
						<div class="flex-fill min-w-0">
							<h3 class="h4 card-title mb-1">Mac <span class="fs-6 fw-normal text-secondary">({lng p="version"} {$verNo})</span></h3>
							<p class="text-secondary mb-0">{lng p="software_mac"}</p>
						</div>
					</div>
					<div class="mt-auto pt-1">
						<button type="button" class="btn btn-primary btn-sm" onclick="document.location.href='{sessionurl file='prefs.php' params='action=software&do=download&os=mac'}';">
							<i class="ti ti-download icon icon-sm me-1" aria-hidden="true"></i>
							{lng p="download"}
							<span class="text-white text-opacity-75 ms-1">({size bytes=$fileSizes.mac})</span>
						</button>
					</div>
				</div>
			</div>
		</div>
		{/if}
	</div>
</div>
</div>
</div>
</div>
