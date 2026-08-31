{capture assign="dialogTitleText"}{lng p="browse"}{/capture}
{include file="li/dialog.head.tpl" dialogTitle=$dialogTitleText dialogBodyClass="bm-dialog-webdisk bm-dialog-modal-sections" dialogOnLoad="dialogInit('{$sid}');documentLoader()"}

<div class="bm-webdisk-picker-dialog">
	<input type="hidden" name="fileid" id="fileid" />

	<div class="modal-body">
		{if $type=='save'}
		<div class="mb-3">
			<label class="form-label" for="filename">{lng p="saveas"}</label>
			<input type="text" class="form-control" name="filename" id="filename" value="{if isset($filename)}{text value=$filename allowEmpty=true}{/if}" />
		</div>
		{else}
		<input type="hidden" name="filename" id="filename" />
		{/if}
		<div class="fileList bm-webdisk-file-list" id="fileList" role="region" aria-label="{lng p="webdisk"}">
			<div class="bm-dialog-loading text-center py-5">
				<i class="ti ti-loader-2 icon icon-lg text-secondary bm-spin" aria-hidden="true"></i>
			</div>
		</div>
	</div>

	<div class="modal-footer bm-webdisk-picker-footer">
		<div class="bm-webdisk-picker-footer-left">
			{if $type=='save'}
			<button type="button" class="btn btn-sm btn-outline-secondary" onclick="createFolder()">
				<i class="ti ti-folder-plus icon" aria-hidden="true"></i>
				{lng p="createfolder"}
			</button>
			{/if}
		</div>
		<div class="bm-webdisk-picker-footer-right">
			<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()">{lng p="cancel"}</button>
			<button type="button" class="btn btn-primary" onclick="close{if $type=='save'}Save{else}Open{/if}Dialog('{$smarty.request.field}'{if $params}, '{$params}'{/if})">
				<i class="ti ti-check icon" aria-hidden="true"></i>
				{lng p="ok"}
			</button>
		</div>
	</div>
</div>

{include file="li/dialog.foot.tpl"}
