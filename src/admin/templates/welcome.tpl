{if $adminRow.type==0||$adminRow.privileges.overview}
<div class="row row-cards mb-3">
	<div class="{if $canViewLogs}col-lg-4{else}col-lg-6{/if}">
		<div class="card h-100 d-flex flex-column" data-bm-mail-card data-bm-ranges="{$mailReceiveRanges|escape:'html'}">
			<div class="card-body">
				<div class="d-flex align-items-center">
					<div class="subheader">{lng p="stat_receive"}</div>
					<div class="ms-auto lh-1">
						<div class="dropdown">
							<button type="button" class="dropdown-toggle text-secondary border-0 bg-transparent p-0" data-bs-toggle="dropdown" data-bm-range-label aria-expanded="false">{lng p="last_7_days"}</button>
							<div class="dropdown-menu dropdown-menu-end">
								<button type="button" class="dropdown-item active" data-bm-range="7" aria-current="true">{lng p="last_7_days"}</button>
								<button type="button" class="dropdown-item" data-bm-range="30">{lng p="last_30_days"}</button>
								<button type="button" class="dropdown-item" data-bm-range="90">{lng p="last_3_months"}</button>
							</div>
						</div>
					</div>
				</div>
				<div class="d-flex align-items-baseline">
					<div class="h1 mb-0 me-2" data-bm-sum>{$mailReceiveSum7}</div>
					<div class="me-auto">
						<span class="d-inline-flex align-items-center lh-1{if $mailReceivePct7 === null} d-none{/if}{if $mailReceivePct7 !== null && $mailReceivePct7 >= 0} text-green{elseif $mailReceivePct7 !== null} text-red{/if}" data-bm-pct>{if $mailReceivePct7 !== null}{if $mailReceivePct7 > 0}+{/if}{$mailReceivePct7}% <i class="fa-solid {if $mailReceivePct7 !== null && $mailReceivePct7 >= 0}fa-arrow-up{else}fa-arrow-down{/if} ms-1"></i>{/if}</span>
					</div>
				</div>
			</div>
			<div class="bm-welcome-chart mt-auto">
				<span class="sparkline bm-welcome-area-chart text-primary" data-bs-toggle="sparkline" data-bs-type="line" data-bs-fill="auto" data-bs-spot="none" data-bs-min="0" data-bs-height="40" data-bs-width="320" data-bs-values="{$mailReceiveValues7}"></span>
			</div>
		</div>
	</div>
	<div class="{if $canViewLogs}col-lg-4{else}col-lg-6{/if}">
		<div class="card h-100 d-flex flex-column" data-bm-mail-card data-bm-ranges="{$mailSendRanges|escape:'html'}">
			<div class="card-body">
				<div class="d-flex align-items-center">
					<div class="subheader">{lng p="stat_send"}</div>
					<div class="ms-auto lh-1">
						<div class="dropdown">
							<button type="button" class="dropdown-toggle text-secondary border-0 bg-transparent p-0" data-bs-toggle="dropdown" data-bm-range-label aria-expanded="false">{lng p="last_7_days"}</button>
							<div class="dropdown-menu dropdown-menu-end">
								<button type="button" class="dropdown-item active" data-bm-range="7" aria-current="true">{lng p="last_7_days"}</button>
								<button type="button" class="dropdown-item" data-bm-range="30">{lng p="last_30_days"}</button>
								<button type="button" class="dropdown-item" data-bm-range="90">{lng p="last_3_months"}</button>
							</div>
						</div>
					</div>
				</div>
				<div class="d-flex align-items-baseline">
					<div class="h1 mb-0 me-2" data-bm-sum>{$mailSendSum7}</div>
					<div class="me-auto">
						<span class="d-inline-flex align-items-center lh-1{if $mailSendPct7 === null} d-none{/if}{if $mailSendPct7 !== null && $mailSendPct7 >= 0} text-green{elseif $mailSendPct7 !== null} text-red{/if}" data-bm-pct>{if $mailSendPct7 !== null}{if $mailSendPct7 > 0}+{/if}{$mailSendPct7}% <i class="fa-solid {if $mailSendPct7 !== null && $mailSendPct7 >= 0}fa-arrow-up{else}fa-arrow-down{/if} ms-1"></i>{/if}</span>
					</div>
				</div>
			</div>
			<div class="bm-welcome-chart mt-auto">
				<span class="sparkline bm-welcome-area-chart text-green" data-bs-toggle="sparkline" data-bs-type="line" data-bs-fill="auto" data-bs-spot="none" data-bs-min="0" data-bs-height="40" data-bs-width="320" data-bs-values="{$mailSendValues7}"></span>
			</div>
		</div>
	</div>
	{if $canViewLogs}
	<div class="col-lg-4">
		<div class="card h-100">
			<div class="card-body">
				<p class="mb-3">{lng p="today"}: <strong>{$logTodayTotal}</strong> {lng p="entries"}</p>
				<div class="progress progress-separated mb-3">
					<div class="progress-bar bg-azure{if $logTodayInfo==0} d-none{/if}" role="progressbar" style="width: {$logInfoPct}%" aria-label="{lng p="info"}"></div>
					<div class="progress-bar bg-yellow{if $logTodayWarning==0} d-none{/if}" role="progressbar" style="width: {$logWarningPct}%" aria-label="{lng p="warning"}"></div>
					<div class="progress-bar bg-red{if $logTodayError==0} d-none{/if}" role="progressbar" style="width: {$logErrorPct}%" aria-label="{lng p="error"}"></div>
				</div>
				<div class="legend-list">
					<a href="{sessionurl file='logs.php' params="start={$logTodayStart}&end={$logTodayEnd}&prio%5B2%5D=1"}" class="legend legend-azure">
						<span class="legend-dot"></span>
						{lng p="info"}<span class="legend-value"> {$logTodayInfo}</span>
					</a>
					<a href="{sessionurl file='logs.php' params="start={$logTodayStart}&end={$logTodayEnd}&prio%5B1%5D=1"}" class="legend legend-yellow">
						<span class="legend-dot"></span>
						{lng p="warning"}<span class="legend-value"> {$logTodayWarning}</span>
					</a>
					<a href="{sessionurl file='logs.php' params="start={$logTodayStart}&end={$logTodayEnd}&prio%5B4%5D=1"}" class="legend legend-red">
						<span class="legend-dot"></span>
						{lng p="error"}<span class="legend-value"> {$logTodayError}</span>
					</a>
				</div>
			</div>
		</div>
	</div>
	{/if}
</div>
<div class="row row-cards mb-3">
	<div class="col-sm-6 col-lg-3">
		<div class="card card-sm">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-auto">
						<span class="bg-primary text-white avatar avatar-square"><i class="fa-solid fa-users"></i></span>
					</div>
					<div class="col">
						<div class="fw-medium"><a href="{sessionurl file='users.php'}" class="text-reset text-decoration-none">{$userCount} {lng p="users"}</a></div>
						<div class="text-secondary"><a href="{sessionurl file='users.php' params="filter=true&statusNotActivated=true&allGroups=true"}" class="{if $notActivatedUserCount>0}text-warning{else}text-secondary{/if}">{$notActivatedUserCount} {lng p="notactivated"}</a>, <a href="{sessionurl file='users.php' params="filter=true&statusLocked=true&allGroups=true"}" class="{if $lockedUserCount>0}text-danger{else}text-secondary{/if}">{$lockedUserCount} {lng p="locked"}</a></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-lg-3">
		<div class="card card-sm">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-auto">
						<span class="bg-azure text-white avatar avatar-square"><i class="fa-solid fa-envelope"></i></span>
					</div>
					<div class="col">
						<div class="fw-medium">{$emailCount} {lng p="emails"}</div>
						<div class="text-secondary">{if $emailSize!==false}{size bytes=$emailSize}{else}-{/if}, {$folderCount} {lng p="folders"}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-lg-3">
		<div class="card card-sm">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-auto">
						<span class="bg-green text-white avatar avatar-square"><i class="fa-solid fa-hard-drive"></i></span>
					</div>
					<div class="col">
						<div class="fw-medium">{if $diskSize!==false}{size bytes=$diskSize}{else}-{/if} {lng p="webdisk"}</div>
						<div class="text-secondary">{$diskFileCount} {lng p="files"}, {$diskFolderCount} {lng p="folders"}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-lg-3">
		<div class="card card-sm">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-auto">
						<span class="bg-purple text-white avatar avatar-square"><i class="fa-solid fa-database"></i></span>
					</div>
					<div class="col">
						<div class="fw-medium">{size bytes=$dbSize} {lng p="db"}</div>
						<div class="text-secondary">{$tableCount} {lng p="tables"}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{/if}

{assign var=bmHasNotices value=false}
{if $adminRow.type==0 && $notices|@count > 0}{assign var=bmHasNotices value=true}{/if}
{assign var=bmHasVersion value=false}
{if $adminRow.type==0||$adminRow.privileges.overview}{assign var=bmHasVersion value=true}{/if}
{if $bmHasNotices || $bmHasVersion || $showActivation}
<div class="row row-cards mb-3">
	{if $bmHasNotices}
	<div class="{if $showActivation}col-lg-6{else}col-lg-8{/if}">
		<div class="card h-100">
			<div class="card-body bm-welcome-notices">
				<h2 class="card-title">{lng p="notices"}</h2>
				<div class="divide-y">
				{foreach from=$notices item=notice}
					<div class="row align-items-start">
						<div class="col-auto">
							<span class="avatar avatar-sm {if $notice.type == 'error' || $notice.type == 'debug'}bg-red-lt text-red{elseif $notice.type == 'warning'}bg-yellow-lt text-yellow{elseif $notice.type == 'info'}bg-azure-lt text-azure{else}bg-secondary-lt text-secondary{/if}">
								{if $notice.type == 'debug'}
									<i class="fa-solid fa-bug"></i>
								{elseif $notice.type == 'info'}
									<i class="fa-solid fa-circle-info"></i>
								{elseif $notice.type == 'warning'}
									<i class="fa-solid fa-triangle-exclamation"></i>
								{elseif $notice.type == 'error'}
									<i class="fa-regular fa-circle-xmark"></i>
								{else}
									<i class="fa-solid fa-puzzle-piece"></i>
								{/if}
							</span>
						</div>
						<div class="col bm-welcome-notice-text">{$notice.text}</div>
						{if isset($notice.link)}
						<div class="col-auto">
							<a href="{$notice.link}" class="btn btn-sm btn-icon btn-outline-secondary" aria-label="{lng p="notices"}"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
						</div>
						{/if}
					</div>
				{/foreach}
				</div>
			</div>
		</div>
	</div>
	{/if}
	{if $bmHasVersion}
	<div class="{if $bmHasNotices && $showActivation}col-lg-3{elseif $bmHasNotices}col-lg-4{elseif $showActivation}col-lg-6{else}col-12{/if}">
		<div class="card h-100">
			<div class="card-body">
				<div class="bm-welcome-meta{if $bmHasNotices} bm-welcome-meta-side{/if}">
					<div class="bm-welcome-meta-item">
						<div class="subheader">{lng p="version"}</div>
						<div class="bm-welcome-meta-value">{text value=$version}</div>
					</div>
					<div class="bm-welcome-meta-item">
						<div class="subheader">{lng p="phpversion"}</div>
						<div class="bm-welcome-meta-value">{text value=$phpVersion}</div>
					</div>
					<div class="bm-welcome-meta-item">
						<div class="subheader">{lng p="webserver"}</div>
						<div class="bm-welcome-meta-value">{text value=$webserver}</div>
					</div>
					<div class="bm-welcome-meta-item">
						<div class="subheader">{lng p="load"}</div>
						<div class="bm-welcome-meta-value">{$loadAvg}</div>
					</div>
					<div class="bm-welcome-meta-item bm-welcome-meta-item-wide">
						<div class="subheader">{lng p="mysqlversion"}</div>
						<div class="bm-welcome-meta-value">{text value=$mysqlVersion}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	{/if}
	{if $showActivation}
	<div class="{if $bmHasNotices && $bmHasVersion}col-lg-3{elseif $bmHasVersion || $bmHasNotices}col-lg-6{else}col-12{/if}">
		<div class="card h-100">
			<div class="card-body">
				<h2 class="card-title">{lng p="activatepayment"}</h2>
				<p class="text-secondary">{lng p="activate_desc"}</p>
				<div class="bm-welcome-activate{if $bmHasVersion || $bmHasNotices} bm-welcome-activate-stack{/if}">
					<div class="bm-welcome-activate-code">
						<label class="form-label" for="vkCode">{lng p="vkcode"}</label>
						<input type="text" class="form-control" name="vkCode" id="vkCode" value="VK-" onkeypress="return handleActivatePaymentInput(event, 0);" placeholder="{lng p="vkcode"}" autocomplete="off">
					</div>
					<div class="bm-welcome-activate-amount">
						<label class="form-label" for="amount">{lng p="amount"}</label>
						<div class="input-group">
							<input type="text" class="form-control" name="amount" id="amount" onkeypress="return handleActivatePaymentInput(event, 1);" placeholder="{lng p="amount"}" autocomplete="off">
							<span class="input-group-text">{text value=$bm_prefs.currency}</span>
						</div>
					</div>
					<input type="button" id="activateButton" value="{lng p="activate"}" onclick="activatePayment()" class="btn btn-primary" />
				</div>
				<div id="activationResult" class="bm-welcome-activate-result"></div>
			</div>
		</div>
	</div>
	{/if}
</div>
{/if}

{if $hasNotes}
<div class="row row-cards mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<h2 class="card-title">{lng p="notes"}</h2>
				<div class="bm-welcome-notes">{text value=$notes allowEmpty=true}</div>
			</div>
		</div>
	</div>
</div>
{/if}

<div class="modal modal-blur fade" id="bmNotesModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<form class="modal-content" action="{sessionurl file='welcome.php' params='do=saveNotes'}" method="post" onsubmit="spin(this)">
			{csrffield}
			<div class="modal-header">
				<h5 class="modal-title">{lng p="notes"}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
			</div>
			<div class="modal-body p-0">
				<textarea class="form-control bm-notes-field" rows="8" name="notes" placeholder="{lng p="notes"}">{text value=$notes allowEmpty=true}</textarea>
			</div>
			<div class="modal-footer">
				<input type="submit" value="{lng p="save"}" class="btn btn-primary" />
			</div>
		</form>
	</div>
</div>
