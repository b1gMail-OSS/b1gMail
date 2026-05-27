<div class="bm-folder-admin">
	<div id="contentHeader" class="contentHeader bm-folder-admin-header">
		<div class="left">
			<i class="ti ti-folders icon icon-sm" aria-hidden="true"></i>
			{lng p="folderadmin"}
		</div>
		<div class="right">
			<button class="btn btn-sm btn-outline-primary" onclick="document.location.href='email.folders.php?action=addFolder&sid={$sid}';" type="button">
				<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>{lng p="addfolder"}
			</button>
		</div>
	</div>

	<form name="f1" method="post" action="email.folders.php?action=action&sid={$sid}" class="card bm-folder-admin-card">
		<div class="table-responsive">
			<table class="table table-vcenter table-hover card-table bm-folder-admin-table" id="folderAdminTable">
				<thead>
				<tr>
					<th class="bm-folder-col-check"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'folder');" aria-label="{lng p="selaction"}" /></label></th>
					<th class="bm-folder-col-title">
						<a class="bm-folder-sort-link" href="email.folders.php?sid={$sid}&sort=titel&order={$sortOrderInv}">{lng p="title"}</a>
						{if $sortColumn=='titel'}<i class="ti ti-arrow-{if $sortOrder=='fa-arrow-down'}down{else}up{/if} icon icon-sm ms-1 text-primary" aria-hidden="true"></i>{/if}
					</th>
					<th class="bm-folder-col-parent d-none d-md-table-cell">
						<a class="bm-folder-sort-link" href="email.folders.php?sid={$sid}&sort=parent&order={$sortOrderInv}">{lng p="parentfolder"}</a>
						{if $sortColumn=='parent'}<i class="ti ti-arrow-{if $sortOrder=='fa-arrow-down'}down{else}up{/if} icon icon-sm ms-1 text-primary" aria-hidden="true"></i>{/if}
					</th>
					<th class="bm-folder-col-size d-none d-lg-table-cell">{lng p="size"}</th>
					<th class="bm-folder-col-status">{lng p="status"}</th>
					<th class="bm-folder-col-subscribed">
						<a class="bm-folder-sort-link" href="email.folders.php?sid={$sid}&sort=subscribed&order={$sortOrderInv}">{lng p="subscribed"}</a>
						{if $sortColumn=='subscribed'}<i class="ti ti-arrow-{if $sortOrder=='fa-arrow-down'}down{else}up{/if} icon icon-sm ms-1 text-primary" aria-hidden="true"></i>{/if}
					</th>
					<th class="bm-folder-col-actions"></th>
				</tr>
				</thead>

				{if !empty($sysFolderList)}
				<tr class="bm-folder-section-row">
					<td colspan="7">
						<button type="button" class="bm-folder-section-toggle" onclick="toggleGroup('sys');">
							<i class="ti ti-chevron-down icon icon-sm" id="groupImage_sys" aria-hidden="true"></i>
							{lng p="sysfolders"}
						</button>
					</td>
				</tr>
				<tbody id="group_sys" class="bm-folder-section-body">
				{foreach from=$sysFolderList key=folderID item=folder}
				<tr class="bm-folder-row">
					<td class="bm-folder-col-check"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" disabled="disabled" aria-hidden="true" /></label></td>
					<td class="bm-folder-col-title{if $sortColumn=='titel'} bm-folder-col-sorted{/if}">
						<a class="bm-folder-title-link" href="email.php?sid={$sid}&folder={$folderID}">
							<span class="bm-folder-title-icon"><i class="ti {if $folder.type == 'inbox'}ti-inbox{elseif $folder.type == 'outbox'}ti-send{elseif $folder.type == 'drafts'}ti-file-pencil{elseif $folder.type == 'spam'}ti-ban{elseif $folder.type == 'trash'}ti-trash{else}ti-folder{/if} icon" aria-hidden="true"></i></span>
							<span class="bm-folder-title-text">
								<span class="bm-folder-name">{text value=$folder.titel cut=40}</span>
								{if isset($folder.parent)}<span class="bm-folder-parent d-md-none">{text value=$folder.parent cut=20}</span>{/if}
							</span>
						</a>
					</td>
					<td class="bm-folder-col-parent d-none d-md-table-cell">{if isset($folder.parent)}{text value=$folder.parent cut=20}{/if}</td>
					<td class="bm-folder-col-size d-none d-lg-table-cell text-secondary">{size bytes=$folder.size}</td>
					<td class="bm-folder-col-status">
						<div class="bm-folder-stats">
							<span class="badge bg-secondary-lt bm-folder-stat" title="{lng p="all"}"><i class="ti ti-mail icon icon-sm" aria-hidden="true"></i>{$folder.allMails}</span>
							<span class="badge bg-azure-lt bm-folder-stat" title="{lng p="unread"}"><i class="ti ti-mail-opened icon icon-sm" aria-hidden="true"></i>{$folder.unreadMails}</span>
							<span class="badge bg-yellow-lt bm-folder-stat" title="{lng p="flagged"}"><i class="ti ti-flag icon icon-sm" aria-hidden="true"></i>{$folder.flaggedMails}</span>
						</div>
					</td>
					<td class="bm-folder-col-subscribed"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" checked="checked" disabled="disabled" aria-hidden="true" /></label></td>
					<td class="bm-folder-col-actions">
						<a href="email.folders.php?action=editFolder&id={$folderID}&sid={$sid}" class="btn btn-sm btn-ghost-secondary btn-icon" title="{lng p="edit"}" aria-label="{lng p="edit"}"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
					</td>
				</tr>
				{/foreach}
				</tbody>
				{/if}

				{if $theFolderList}
				<tr class="bm-folder-section-row">
					<td colspan="7">
						<button type="button" class="bm-folder-section-toggle" onclick="toggleGroup('own');">
							<i class="ti ti-chevron-down icon icon-sm" id="groupImage_own" aria-hidden="true"></i>
							{lng p="ownfolders"}
						</button>
					</td>
				</tr>
				<tbody id="group_own" class="bm-folder-section-body">
				{foreach from=$theFolderList key=folderID item=folder}
				<tr class="bm-folder-row">
					<td class="bm-folder-col-check"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="folder_{$folderID}" name="folder_{$folderID}" aria-label="{text value=$folder.titel cut=40}" /></label></td>
					<td class="bm-folder-col-title{if $sortColumn=='titel'} bm-folder-col-sorted{/if}">
						<a class="bm-folder-title-link" href="email.php?sid={$sid}&folder={$folderID}">
							<span class="bm-folder-title-icon"><i class="ti {if $folder.intelligent==1}ti-folder-cog{else}ti-folder{/if} icon" aria-hidden="true"></i></span>
							<span class="bm-folder-title-text">
								<span class="bm-folder-name">{text value=$folder.titel cut=40}</span>
								{if isset($folder.parent)}<span class="bm-folder-parent d-md-none">{text value=$folder.parent cut=20}</span>{/if}
							</span>
						</a>
					</td>
					<td class="bm-folder-col-parent d-none d-md-table-cell{if $sortColumn=='parent'} bm-folder-col-sorted{/if}">{if isset($folder.parent)}{text value=$folder.parent cut=20}{/if}</td>
					<td class="bm-folder-col-size d-none d-lg-table-cell text-secondary">{if $folder.intelligent}-{else}{size bytes=$folder.size}{/if}</td>
					<td class="bm-folder-col-status">
						<div class="bm-folder-stats">
							<span class="badge bg-secondary-lt bm-folder-stat" title="{lng p="all"}"><i class="ti ti-mail icon icon-sm" aria-hidden="true"></i>{$folder.allMails}</span>
							<span class="badge bg-azure-lt bm-folder-stat" title="{lng p="unread"}"><i class="ti ti-mail-opened icon icon-sm" aria-hidden="true"></i>{$folder.unreadMails}</span>
							<span class="badge bg-yellow-lt bm-folder-stat" title="{lng p="flagged"}"><i class="ti ti-flag icon icon-sm" aria-hidden="true"></i>{$folder.flaggedMails}</span>
						</div>
					</td>
					<td class="bm-folder-col-subscribed{if $sortColumn=='subscribed'} bm-folder-col-sorted{/if}"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" {if $folder.subscribed==1}checked="checked" {/if} onchange="updateFolderSubscription('{$folderID}', this, '{$sid}')" aria-label="{lng p="subscribed"}" /></label></td>
					<td class="bm-folder-col-actions">
						<div class="btn-list flex-nowrap justify-content-end">
							<a href="email.folders.php?action=editFolder&id={$folderID}&sid={$sid}" class="btn btn-sm btn-ghost-secondary btn-icon" title="{lng p="edit"}" aria-label="{lng p="edit"}"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
							<a onclick="return confirm('{lng p="realdel"}');" href="email.folders.php?action=deleteFolder&id={$folderID}&sid={$sid}" class="btn btn-sm btn-ghost-secondary btn-icon text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
						</div>
					</td>
				</tr>
				{/foreach}
				</tbody>
				{/if}

				{if $sharedFolderList}
				<tr class="bm-folder-section-row">
					<td colspan="7">
						<button type="button" class="bm-folder-section-toggle" onclick="toggleGroup('shared');">
							<i class="ti ti-chevron-down icon icon-sm" id="groupImage_shared" aria-hidden="true"></i>
							{lng p="sharedfolders"}
						</button>
					</td>
				</tr>
				<tbody id="group_shared" class="bm-folder-section-body">
				{foreach from=$sharedFolderList key=folderID item=folder}
				<tr class="bm-folder-row">
					<td class="bm-folder-col-check"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="folder_{$folderID}" name="folder_{$folderID}" aria-label="{text value=$folder.titel cut=40}" /></label></td>
					<td class="bm-folder-col-title{if $sortColumn=='titel'} bm-folder-col-sorted{/if}">
						<a class="bm-folder-title-link" href="email.php?sid={$sid}&folder={$folderID}">
							<span class="bm-folder-title-icon"><i class="ti ti-share-3 icon" aria-hidden="true"></i></span>
							<span class="bm-folder-title-text">
								<span class="bm-folder-name">{text value=$folder.titel cut=40}{if $folder.readonly} <span class="text-secondary fw-normal">({lng p="readonly"})</span>{/if}</span>
								{if $folder.parent}<span class="bm-folder-parent d-md-none">{text value=$folder.parent cut=20}</span>{/if}
							</span>
						</a>
					</td>
					<td class="bm-folder-col-parent d-none d-md-table-cell{if $sortColumn=='parent'} bm-folder-col-sorted{/if}">{text value=$folder.parent cut=20}</td>
					<td class="bm-folder-col-size d-none d-lg-table-cell text-secondary">{size bytes=$folder.size}</td>
					<td class="bm-folder-col-status">
						<div class="bm-folder-stats">
							<span class="badge bg-secondary-lt bm-folder-stat" title="{lng p="all"}"><i class="ti ti-mail icon icon-sm" aria-hidden="true"></i>{$folder.allMails}</span>
							<span class="badge bg-azure-lt bm-folder-stat" title="{lng p="unread"}"><i class="ti ti-mail-opened icon icon-sm" aria-hidden="true"></i>{$folder.unreadMails}</span>
							<span class="badge bg-yellow-lt bm-folder-stat" title="{lng p="flagged"}"><i class="ti ti-flag icon icon-sm" aria-hidden="true"></i>{$folder.flaggedMails}</span>
						</div>
					</td>
					<td class="bm-folder-col-subscribed{if $sortColumn=='subscribed'} bm-folder-col-sorted{/if}"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" {if $folder.subscribed==1}checked="checked" {/if} disabled="disabled" aria-hidden="true" /></label></td>
					<td class="bm-folder-col-actions"></td>
				</tr>
				{/foreach}
				</tbody>
				{/if}
			</table>
		</div>

		<div class="card-footer bm-folder-admin-footer">
			<div class="bm-folder-admin-footer-row">
				<div class="input-group input-group-sm bm-folder-action-group">
					<select class="form-select" name="do" aria-label="{lng p="selaction"}">
						<option value="-">{lng p="selaction"}</option>
						<option value="delete">{lng p="delete"}</option>
					</select>
					<button type="submit" class="btn btn-primary btn-sm bm-folder-footer-ok" aria-label="{lng p="ok"}">
						<i class="ti ti-check bm-folder-footer-ok-icon" aria-hidden="true"></i>
						<span class="bm-folder-footer-ok-text">{lng p="ok"}</span>
					</button>
				</div>
			</div>
		</div>
	</form>
</div>
