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

				<div class="scrollContainer withBottomBar noSelect bm-webdisk-content{if empty($folderContent) && !isset($upload)} bm-webdisk-empty{else} bm-webdisk-has-items{/if}" id="wdDnDArea">
					<div id="wdDnDNote" class="bm-webdisk-dnd-note">
						<i class="ti ti-upload icon icon-sm me-1" aria-hidden="true"></i>
						{lng p="dragfileshere"}
					</div>
					{if isset($upload)}
					<div class="card bm-webdisk-upload-card">
						<div class="card-header">
							<h3 class="card-title mb-0">{lng p="uploadfiles"}</h3>
						</div>
						<div class="card-body">
							{assign var="i" value=0}
							{section name=file loop=$upload}
							<div class="mb-2">
								<input type="file" class="form-control form-control-sm" name="file{$i}" />
							</div>
							{assign var="i" value=$i+1}
							{/section}
							<div class="d-flex align-items-center gap-2 mt-3">
								<i class="ti ti-loader-2 icon icon-sm fa-spin" style="display:none;" id="progressBar" aria-hidden="true"></i>
								<button id="sbButton" class="btn btn-sm btn-primary" type="button" onclick="EBID('wdAction').name='action';EBID('wdAction').value='uploadFiles';EBID('progressBar').style.display='';this.disabled=true;document.forms.f1.submit();">{lng p="ok"}</button>
							</div>
						</div>
					</div>
					{elseif $isShared}
					<div class="alert alert-info bm-webdisk-share-note">
						<div class="small mb-2">{lng p="sharednote"}</div>
						<div class="d-flex flex-wrap align-items-center gap-2">
							<i class="ti ti-share icon icon-sm" aria-hidden="true"></i>
							<a target="_blank" href="{$shareURL}" class="text-break">{$shareURL}</a>
							<button type="button" class="btn btn-sm btn-outline-primary ms-auto" onclick="document.forms.mailForm.submit();return(false);">
								<i class="ti ti-mail icon icon-sm me-1" aria-hidden="true"></i>
								{lng p="sendmail2"}
							</button>
						</div>
					</div>
					{/if}

					{if $viewMode=='icons'}
					<div id="wdContentDiv" class="bm-webdisk-icons-grid">
						{foreach from=$folderContent item=item}
						<div class="bm-webdisk-icon-item card{if $item.type==1} bm-webdisk-icon-item-folder{/if}">
							<a id="wli_{$item.type}_{$item.id}"
								class="webdiskItem bm-webdisk-item card-body"
								title="{text value=$item.title}">
								<span class="bm-webdisk-item-icon-wrap">
									{assign var='wdicons_size_class' value='bm-webdisk-icon-lg' scope='global'}
									{assign var='wdicons_additionalparam' value='draggable="true"' scope='global'}
									{assign var='wdicons_imgattr' value='' scope='global'}
									{include file="li/webdisk.icons.tpl"}
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
							<tr id="wli_{$item.type}_{$item.id}">
								<td class="text-center">
									{assign var='wdicons_size_class' value='bm-webdisk-icon-sm' scope='global'}
									{assign var='wdicons_additionalparam' value='draggable="true"' scope='global'}
									{assign var='wdicons_imgattr' value='' scope='global'}
									{include file="li/webdisk.icons.tpl"}
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

			{if !isset($smarty.post.inline)}
			<script src="./clientlib/dndupload.js?{fileDateSig file="../../clientlib/dndupload.js"}" type="text/javascript"></script>
			<script>
			{if $hotkeys}
				registerLoadAction('registerWebdiskFolderHotkeyHandler()');
			{/if}
				initDnDUpload(EBID('mainContent'), 'webdisk.php?sid='+currentSID+'&folder={$folderID}&action=dndUpload', function() {literal}{{/literal} document.location.href='webdisk.php?sid='+currentSID+'&folder={$folderID}'; {literal}}{/literal});
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
