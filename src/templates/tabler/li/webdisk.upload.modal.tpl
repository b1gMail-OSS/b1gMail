<div class="modal modal-blur fade bm-webdisk-upload-modal" id="webdiskUploadModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="ti ti-upload icon icon-sm me-1" aria-hidden="true"></i>
					{lng p="uploadfiles"}
				</h5>
				<button type="button" class="btn-close" id="webdiskUploadClose" data-bs-dismiss="modal" aria-label="{lng p="close"}"></button>
			</div>
			<form class="modal-body" id="webdiskUploadForm" method="post" enctype="multipart/form-data" action="webdisk.php?folder={$folderID}&amp;sid={$sid}" onsubmit="return webdiskSubmitUploadModal();">
				<input type="hidden" name="action" value="uploadFiles" />

				<p class="text-secondary small mb-3" id="webdiskUploadLimitsNote">
					{lng p="wd_upload_maxsize"}: <strong>{$webdiskMaxUploadSize}</strong>
				</p>

				{include file="li/webdisk.upload.limits.tpl"}

				<div class="mb-3">
					<label class="form-label" for="webdiskUploadFiles">{lng p="localfile"}</label>
					<div class="bm-file-selector bm-webdisk-upload-filepicker">
						<div class="bm-file-selector-panel">
							<input type="file" class="form-control" id="webdiskUploadFiles" name="files[]" multiple="multiple" onchange="webdiskUploadFilesSelected(this);" />
						</div>
						<div id="webdiskUploadFilesList" class="bm-webdisk-upload-files-list text-secondary small mt-2" aria-live="polite"></div>
					</div>
					<small class="text-secondary d-block mt-1">{lng p="wd_upload_files_note"}</small>
				</div>

				<div class="hr-text my-3" aria-hidden="true">{lng p="wd_upload_or"}</div>

				<div class="bm-webdisk-upload-dnd rounded border border-dashed text-center p-4 mb-0" id="wdUploadModalDnD">
					<i class="ti ti-drag-drop icon icon-lg text-secondary mb-2" aria-hidden="true"></i>
					<div class="fw-medium">{lng p="dragfileshere"}</div>
					<div class="text-secondary small mt-1">{lng p="wd_upload_dnd_note"}</div>
				</div>
			</form>
			<div class="modal-footer">
				<button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">{lng p="cancel"}</button>
				<button type="submit" form="webdiskUploadForm" class="btn btn-primary" id="webdiskUploadSubmitBtn">
					<i class="ti ti-upload icon icon-sm me-1" aria-hidden="true"></i>
					{lng p="uploadfiles"}
				</button>
			</div>
		</div>
	</div>
</div>
