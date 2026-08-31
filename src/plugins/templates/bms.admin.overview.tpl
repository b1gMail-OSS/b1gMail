<fieldset class="mb-4">
	<legend class="h4 mb-3">b1gMailServer</legend>

	<div class="card">
		<div class="card-body">
			<div class="row align-items-center g-3">
				<div class="col-auto">
					<img src="../plugins/templates/images/bms_logo.png" alt="" width="32" height="32" class="flex-shrink-0 object-fit-contain" />
				</div>
				<div class="col-md-5">
					<div class="text-secondary small">{lng p="version"} ({lng p="bms_adminplugin"})</div>
					<div class="fw-medium">{$adminVersion}</div>
				</div>
				<div class="col-md-5">
					<div class="text-secondary small">{lng p="version"} ({lng p="bms_core"})</div>
					<div class="fw-medium">{if $coreVersion}{$coreVersion}{else}<em>({lng p="unknown"})</em>{/if}</div>
				</div>
			</div>
		</div>
	</div>
</fieldset>

<fieldset class="mb-4">
	<legend class="h4 mb-3">{lng p="overview"}</legend>

	<div class="row g-3">
		<div class="col-md-6">
			<div class="card h-100">
				<div class="card-body">
					<div class="d-flex gap-3 align-items-start">
						<img src="../plugins/templates/images/bms_common.png" alt="" width="32" height="32" class="flex-shrink-0 align-self-start object-fit-contain" />
						<div class="flex-grow-1">
							<div class="row g-2">
								<div class="col-6 text-secondary small">{lng p="bms_pop3today"}</div>
								<div class="col-6">{$pop3Today}</div>
								<div class="col-6 text-secondary small">{lng p="bms_imaptoday"}</div>
								<div class="col-6">{$imapToday}</div>
								<div class="col-6 text-secondary small">{lng p="bms_smtptoday"}</div>
								<div class="col-6">{$smtpToday}</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card h-100">
				<div class="card-body">
					<div class="d-flex gap-3 align-items-start">
						<img src="../plugins/templates/images/bms_stats.png" alt="" width="32" height="32" class="flex-shrink-0 align-self-start object-fit-contain" />
						<div class="flex-grow-1">
							<div class="row g-2">
								<div class="col-6 text-secondary small">{lng p="bms_pop3traffic"}</div>
								<div class="col-6">{size bytes=$pop3Traffic}</div>
								<div class="col-6 text-secondary small">{lng p="bms_imaptraffic"}</div>
								<div class="col-6">{size bytes=$imapTraffic}</div>
								<div class="col-6 text-secondary small">{lng p="bms_smtptraffic"}</div>
								<div class="col-6">{size bytes=$smtpTraffic}</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card h-100">
				<div class="card-body">
					<div class="d-flex gap-3 align-items-start">
						<img src="../plugins/templates/images/bms_queue.png" alt="" width="32" height="32" class="flex-shrink-0 align-self-start object-fit-contain" />
						<div class="flex-grow-1">
							<div class="row g-2">
								<div class="col-6 text-secondary small">{lng p="bms_queueentries"}</div>
								<div class="col-6">{$queueEntries}</div>
								<div class="col-6 text-secondary small">{lng p="bms_inbound"}</div>
								<div class="col-6">{$queueInbound}</div>
								<div class="col-6 text-secondary small">{lng p="bms_outbound"}</div>
								<div class="col-6">{$queueOutbound}</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card h-100">
				<div class="card-body">
					<div class="d-flex gap-3 align-items-start">
						<img src="../plugins/templates/images/bms_features.png" alt="" width="32" height="32" class="flex-shrink-0 align-self-start object-fit-contain" />
						<div class="flex-grow-1">
							<div class="row g-2 align-items-center">
								<div class="col-8 text-secondary small">{lng p="bms_queue"}?</div>
								<div class="col-4">
									{if $queueRunning}<span class="badge bg-success-lt">{lng p="bms_running"} ({$threadCount} {lng p="bms_threads"})</span>{else}<span class="badge bg-danger-lt">{lng p="bms_not_running"}</span>{/if}
								</div>
								<div class="col-8 text-secondary small">{lng p="bms_feature_tls"}?</div>
								<div class="col-4">{if ($bms_prefs.core_features&1)!=0}<i class="ti ti-check text-success"></i>{else}<i class="ti ti-x text-danger"></i>{/if}</div>
								<div class="col-8 text-secondary small">{lng p="bms_feature_sig"}?</div>
								<div class="col-4">{if ($bms_prefs.core_features&2)!=0}<i class="ti ti-check text-success"></i>{else}<i class="ti ti-x text-danger"></i>{/if}</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</fieldset>

{if $notices|@count > 0}
<fieldset class="mb-4">
	<legend class="h4 mb-3">{lng p="notices"}</legend>

	<div class="card">
		<div class="list-group list-group-flush" id="noticeTable">
		{foreach from=$notices item=notice}
			<div class="list-group-item">
				<div class="d-flex gap-3 align-items-start">
					<img src="{$tpldir}images/{$notice.type}.png" width="16" height="16" alt="" class="flex-shrink-0 align-self-start object-fit-contain mt-1" />
					<div class="flex-grow-1">{$notice.text}</div>
					{if $notice.link}
					<a href="{sessionurl file=$notice.link}" class="btn btn-sm btn-ghost-secondary" title="{lng p="show"}"><i class="ti ti-arrow-right"></i></a>
					{/if}
				</div>
			</div>
		{/foreach}
		</div>
	</div>
</fieldset>
{/if}
