<div class="bm-webdisk-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header bm-webdisk-header">
		<div class="left">
			<i class="ti ti-cloud icon icon-sm" aria-hidden="true"></i>
			{if $currentPath}
			<a href="#" onclick="switchWebdiskFolder(0); return false;" class="bm-webdisk-breadcrumb-root">{lng p="webdisk"}</a>{foreach from=$currentPath key=pathKey item=folder name=pathLoop} <span class="bm-webdisk-breadcrumb-sep" aria-hidden="true">&raquo;</span> {if $smarty.foreach.pathLoop.last}<span class="bm-webdisk-breadcrumb-current">{text value=$folder.title}</span>{else}<a href="#" onclick="switchWebdiskFolder({$folder.id}); return false;" class="bm-webdisk-breadcrumb-link">{text value=$folder.title}</a>{/if}{/foreach}
			{else}
			{lng p="webdisk"}
			{/if}
		</div>
		<div class="right bm-webdisk-header-actions">
			<button type="button" class="btn btn-primary btn-sm" onclick="webdiskOpenUploadModal(); return false;">
				<i class="ti ti-upload icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="uploadfiles"}
			</button>
		</div>
	</div>

	<div class="bm-webdisk-split">
		<div class="bm-webdisk-main">
			{hook id="webdisk.folder.tpl:head"}

			{if $isShared}
			<form action="email.compose.php?sid={$sid}" method="post" name="mailForm">
				<input type="hidden" name="subject" value="{if isset($shareMailSubject)}{text value=$shareMailSubject allowEmpty=true}{/if}" />
				<textarea name="text" style="display:none">{if isset($shareMail)}{text value=$shareMail allowEmpty=true}{/if}</textarea>
			</form>
			{/if}

			<form enctype="multipart/form-data" action="webdisk.php?folder={$folderID}&sid={$sid}" method="post" name="f1" onsubmit="transferSelectedWebdiskItems();" class="bm-webdisk-form">
				<input type="hidden" name="" value="" id="wdAction" />
				<input type="hidden" name="massAction" value="" id="wdMassAction" />
				<input type="hidden" name="selectedWebdiskItems" id="selectedWebdiskItems" value="" />

				<div class="bm-webdisk-alerts" id="wdAlerts">
					{include file="li/webdisk.alerts.tpl"}
					<div id="webdiskPageAlert" class="d-none" role="alert" aria-live="polite"></div>
				</div>

				<div class="scrollContainer withBottomBar noSelect bm-webdisk-content{if empty($folderContent)} bm-webdisk-empty{else} bm-webdisk-has-items{/if}" id="wdDnDArea">
					<div id="wdDnDNote" class="bm-webdisk-dnd-note">
						<i class="ti ti-upload icon icon-sm me-1" aria-hidden="true"></i>
						{lng p="dragfileshere"}
					</div>
					{if $isShared}
					<div class="alert alert-info bm-webdisk-share-note">
						<div class="bm-webdisk-share-layout">
							<div class="bm-webdisk-share-left">
								<div class="small">{lng p="sharednote"}</div>
								<div class="bm-webdisk-share-link-row d-flex align-items-center gap-1 mt-2">
								<i class="ti ti-share icon icon-sm" aria-hidden="true"></i>
								<a target="_blank" href="{$shareURL}" class="text-break">{$shareURL}</a>
								</div>
							</div>
							<div class="bm-webdisk-share-actions-row d-flex flex-wrap align-items-center gap-2">
								<button type="button" class="btn btn-sm btn-outline-danger" onclick="webdiskStopShareFolder({$folderID}); return false;">
									<i class="ti ti-share-off icon icon-sm me-1" aria-hidden="true"></i>
									{lng p="stopsharing"}
								</button>
								<button type="button" class="btn btn-sm btn-outline-primary" onclick="document.forms.mailForm.submit();return(false);">
									<i class="ti ti-mail icon icon-sm me-1" aria-hidden="true"></i>
									{lng p="sendmail2"}
								</button>
							</div>
						</div>
					</div>
					{/if}

					{if $viewMode=='icons'}
					<div id="wdContentDiv" class="bm-webdisk-icons-grid">
						{foreach from=$folderContent item=item}
						<div class="bm-webdisk-icon-item card{if $item.type==1} bm-webdisk-icon-item-folder{/if}">
							<a id="wli_{$item.type}_{$item.id}"
								class="webdiskItem bm-webdisk-item card-body"
								title="{text value=$item.title}"{if $item.type==2 && $item.viewable} data-viewable="1"{/if}>
								<span class="bm-webdisk-item-icon-wrap{if $item.thumbnail} bm-webdisk-item-icon-wrap--thumb{/if}">
									{if $item.thumbnail}
									<img class="bm-webdisk-thumb" src="webdisk.php?action=thumbnail&amp;id={$item.id}&amp;sid={$sid}" alt="" loading="lazy" draggable="false" />
									{else}
									{assign var='wdicons_size_class' value='bm-webdisk-icon-lg' scope='global'}
									{assign var='wdicons_additionalparam' value='draggable="true"' scope='global'}
									{assign var='wdicons_imgattr' value='' scope='global'}
									{include file="li/webdisk.icons.tpl"}
									{/if}
								</span>
								<span id="wd_{$item.type}_{$item.id}" class="bm-webdisk-item-title" draggable="false">{text value=$item.title cut=20}</span>
								<small class="bm-webdisk-item-meta" draggable="false">{if $item.type==1}{lng p="folder"}{else}{size bytes=$item.size}{/if}</small>
							</a>
						</div>
						{/foreach}
					</div>
					{else}
					<div class="table-responsive">
						<table class="table table-vcenter table-hover card-table bm-organizer-table" id="wdContentTable">
							<thead>
							<tr>
								<th style="width:2.5rem;">&nbsp;</th>
								<th>{lng p="filename"}</th>
								<th style="width:9.375rem;">{lng p="created"}</th>
								<th style="width:6rem;">{lng p="size"}</th>
								<th style="width:7rem;">{lng p="type"}</th>
							</tr>
							</thead>
							<tbody>
							{foreach from=$folderContent item=item}
							<tr id="wli_{$item.type}_{$item.id}"{if $item.type==2 && $item.viewable} data-viewable="1"{/if}>
								<td class="text-center">
									{if $item.thumbnail}
									<img class="bm-webdisk-thumb bm-webdisk-thumb--list" src="webdisk.php?action=thumbnail&amp;id={$item.id}&amp;sid={$sid}" alt="" loading="lazy" draggable="false" width="32" height="32" />
									{else}
									{assign var='wdicons_size_class' value='bm-webdisk-icon-sm' scope='global'}
									{assign var='wdicons_additionalparam' value='draggable="true"' scope='global'}
									{assign var='wdicons_imgattr' value='' scope='global'}
									{include file="li/webdisk.icons.tpl"}
									{/if}
								</td>
								<td nowrap="nowrap" style="cursor:default;" id="wd_{$item.type}_{$item.id}">{text value=$item.title}</td>
								<td nowrap="nowrap">{date timestamp=$item.created nice=true}</td>
								<td nowrap="nowrap">{if $item.type==1}-{else}{size bytes=$item.size}{/if}</td>
								<td nowrap="nowrap">{if $item.type==1}{lng p="folder"}{elseif $item.ext=='?'}{lng p="file"}{else}.{$item.ext}-{lng p="file"}{/if}</td>
							</tr>
							{/foreach}
							</tbody>
						</table>
					</div>
					{/if}
				</div>

				<div id="contentFooter" class="contentFooter bm-organizer-footer bm-webdisk-footer">
					<div class="bm-webdisk-footer-row">
					<div class="left bm-organizer-footer-actions bm-webdisk-footer-actions">
						<div class="input-group input-group-sm bm-organizer-action-group">
							<select class="form-select" id="massAction" aria-label="{lng p="selaction"}">
								<option value="-">------ {lng p="selaction"} ------</option>
								<option value="download">{lng p="download"}</option>
								<option value="delete">{lng p="delete"}</option>
								{hook id="webdisk.folder.tpl:select"}
							</select>
							<button type="button" class="btn btn-primary" onclick="EBID('wdMassAction').value=EBID('massAction').value;transferSelectedWebdiskItems();document.forms.f1.submit();">{lng p="ok"}</button>
						</div>
					</div>
					</div>
				</div>
			</form>

			{hook id="webdisk.folder.tpl:foot"}

			{include file="li/webdisk.preview.tpl"}
			{include file="li/webdisk.upload.modal.tpl"}

			{if !isset($smarty.post.inline)}
			<script src="./clientlib/dndupload.js?{fileDateSig file="../../clientlib/dndupload.js"}" type="text/javascript"></script>
			<script type="application/json" id="webdiskPreviewManifest">{$webdiskPreviewFilesJSON}</script>
			<script type="application/json" id="webdiskPreviewItems">{$webdiskPreviewItemsJSON}</script>
			<script>
				window.webdiskMaxUploadBytes = {$webdiskMaxUploadBytes};
				window.webdiskUploadRules = {$webdiskUploadRulesJSON};
			{if isset($uploadErrors) || isset($uploadSuccess)}
				registerLoadAction(function() {literal}{{/literal}
					var el = EBID('wdAlerts');
					if(el && typeof el.scrollIntoView === 'function')
						el.scrollIntoView({literal}{{/literal} behavior: 'smooth', block: 'nearest' {literal}}{/literal});
				{literal}}{/literal});
			{/if}
			{if $hotkeys}
				registerLoadAction('registerWebdiskFolderHotkeyHandler()');
			{/if}
				registerLoadAction('webdiskInitPreview()');
				registerLoadAction('webdiskEnsureUploadModalInBody()');
				initDnDUpload(EBID('mainContent'), 'webdisk.php?sid='+currentSID+'&folder={$folderID}&action=dndUpload', function() {literal}{{/literal} document.location.href='webdisk.php?sid='+currentSID+'&folder={$folderID}'; {literal}}{/literal}, webdiskDnDFileDone);
				currentWebdiskFolderID = {$folderID};
				var treeID = webdiskGetTreeIDbyFolderID({$folderID});
				if(treeID > 0) {
					webdisk_d.openTo(treeID, true);
				}
				initWDSel();
			</script>
			{/if}
		</div>

		<div id="rightSidebar" class="bm-webdisk-sidebar">
			{include file="li/webdisk.sidebar.tpl"}
		</div>

		<div id="folderLoading" style="display:none"><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></div>
	</div>
</div>
