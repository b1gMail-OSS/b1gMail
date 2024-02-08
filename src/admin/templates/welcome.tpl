<h1>{lng p="overview"}</h1>
{if $adminRow.type==0||$adminRow.privileges.overview}
<div class="row row-cards">
	<div class="col-md-8">
		<div class="row row-cards">
			<div class="col-md-6">
				<div class="card" style="margin-bottom: 20px;">
					<div class="card-header"><strong>{lng p="users"}</strong></div>
					<table class="table table-vcenter table-striped card-table">
						<tbody>
						<tr>
							<td style="width: 150px;"><a href="users.php?sid={$sid}">{lng p="users"}</a></td>
							<td>{$userCount}</td>
						</tr>
						<tr>
							<td><a href="users.php?filter=true&statusNotActivated=true&allGroups=true&sid={$sid}">{lng p="notactivated"}</a></td>
							<td>{$notActivatedUserCount}</td>
						</tr>
						<tr>
							<td><a href="users.php?filter=true&statusLocked=true&allGroups=true&sid={$sid}">{lng p="locked"}</a></td>
							<td>{$lockedUserCount}</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card" style="margin-bottom: 20px;">
					<div class="card-header"><strong>b1gMail</strong></div>
					<table class="table table-vcenter table-striped card-table">
						<tbody>
						<tr>
							<td style="width: 150px;">{lng p="version"}</td>
							<td>{$version}</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="row row-cards">
			<div class="col-md-6">
				<div class="card" style="margin-bottom: 20px;">
					<div class="card-header"><strong>{lng p="email"}</strong></div>
					<table class="table table-vcenter table-striped card-table">
						<tbody>
						<tr>
							<td style="width: 150px;">{lng p="emailsize"}</td>
							<td>{if $emailSize!==false}{size bytes=$emailSize}{else}-{/if}</td>
						</tr>
						<tr>
							<td>{lng p="emails"}</td>
							<td>{$emailCount}</td>
						</tr>
						<tr>
							<td>{lng p="folders"}</td>
							<td>{$folderCount}</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card" style="margin-bottom: 20px;">
					<div class="card-header"><strong>{lng p="webserver"}</strong></div>
					<table class="table table-vcenter table-striped card-table">
						<tbody>
						<tr>
							<td style="width: 150px;">{lng p="phpversion"}</td>
							<td>{$phpVersion}</td>
						</tr>
						<tr>
							<td>{lng p="webserver"}</td>
							<td>{$webserver}</td>
						</tr>
						<tr>
							<td>{lng p="load"}</td>
							<td>{$loadAvg}</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="row row-cards">
			<div class="col-md-6">
				<div class="card" style="margin-bottom: 20px;">
					<div class="card-header"><strong>{lng p="webdisk"}</strong></div>
					<table class="table table-vcenter table-striped card-table">
						<tbody>
						<tr>
							<td style="width: 150px;">{lng p="disksize"}</td>
							<td>{if $diskSize!==false}{size bytes=$diskSize}{else}-{/if}</td>
						</tr>
						<tr>
							<td>{lng p="files"}</td>
							<td>{$diskFileCount}</td>
						</tr>
						<tr>
							<td>{lng p="folders"}</td>
							<td>{$diskFolderCount}</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card" style="margin-bottom: 20px;">
					<div class="card-header"><strong>{lng p="db"}</strong></div>
					<table class="table table-vcenter table-striped card-table">
						<tbody>
						<tr>
							<td style="width: 150px;">{lng p="mysqlversion"}</td>
							<td>{$mysqlVersion}</td>
						</tr>
						<tr>
							<td>{lng p="tables"}</td>
							<td>{$tableCount}</td>
						</tr>
						<tr>
							<td>{lng p="dbsize"}</td>
							<td>{size bytes=$dbSize}</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		{/if}

		<div class="card" style="margin-bottom: 20px;">
			<div class="card-header"><strong>{lng p="notes"}</strong></div>
			<form action="welcome.php?sid={$sid}&do=saveNotes" method="post" onsubmit="spin(this)">
				<textarea class="form-control" style="border: 0px; min-height: 78px; padding-left: 24px;" name="notes" placeholder="{lng p="notes"}">{text value=$notes allowEmpty=true}</textarea>
				<div class="card-footer text-end">
					<input type="submit" value="{lng p="save"}" class="btn btn-sm btn-primary" />
				</div>
			</form>
		</div>

		{if $showActivation}
		<div class="card" style="margin-bottom: 20px;">
			<div class="card-header"><strong>{lng p="activatepayment"}</strong></div>
			<div class="card-body">
				<p>{lng p="activate_desc"}</p>
				<div id="activationResult">&nbsp;</div>
				<div class="row">
					<div class="col-md-6 mb-3">
						<label class="form-label">{lng p="vkcode"}</label>
						<input type="text" class="form-control" name="vkCode" id="vkCode" value="VK-" onkeypress="return handleActivatePaymentInput(event, 0);" placeholder="{lng p="vkcode"}" autocomplete="off">
					</div>
					<div class="col-md-6 mb-3">
						<label class="form-label">{lng p="amount"}</label>
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="amount" id="amount" onkeypress="return handleActivatePaymentInput(event, 1);" placeholder="{lng p="amount"}" autocomplete="off">
							<span class="input-group-text">{text value=$bm_prefs.currency}</span>
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer text-end">
				<input type="button" id="activateButton" value="{lng p="activate"}" onclick="activatePayment()" class="btn btn-sm btn-primary" />
			</div>
		</div>
		{/if}
	</div>
</div>

{if $adminRow.type == 0 && $notices|@count > 0}
	</div>
	<div class="card-footer" style="padding: 10px 0px 0px 0px;">
		<h3 style="margin: 10px 30px 10px 30px;"><strong>{lng p="notices"}</strong></h3>
		<table class="table" style="margin-bottom: 0px;">
			{foreach from=$notices item=notice}
				<tr>
					<td class="align-top text-end" style="width: 60px;">
						{if $notice.type == 'debug'}
							<i class="fa-solid fa-bug text-danger"></i>
						{elseif $notice.type == 'info'}
							<i class="fa-solid fa-circle-info text-info"></i>
						{elseif $notice.type == 'warning'}
							<i class="fa-solid fa-triangle-exclamation text-warning"></i>
						{elseif $notice.type == 'error'}
							<i class="fa-regular fa-circle-xmark text-red"></i>
						{else}
							<i class="fa-solid fa-puzzle-piece text-cyan"></i>
						{/if}
					</td>
					<td class="align-top">{$notice.text}</td>
					<td class="align-top" style="width: 60px;">{if isset($notice.link)}<a href="{$notice.link}sid={$sid}"><i class="fa-solid fa-square-arrow-up-right"></i></a>{else}&nbsp;{/if}</td>
				</tr>
			{/foreach}
		</table>
	</div>
{/if}
