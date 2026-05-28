<div class="modal modal-blur fade bm-wd-preview-modal" id="wdPreviewModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-centered bm-wd-preview-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-truncate me-3" id="wdPreviewTitle">&nbsp;</h5>
				<button type="button" class="btn-close" id="wdPreviewClose" data-bs-dismiss="modal" aria-label="{lng p="close"}"></button>
			</div>
			<div class="bm-wd-preview-editor-pane" id="wdPreviewEditorPane" style="display:none;">
				<div class="bm-wd-preview-editor-loading" id="wdPreviewEditorLoading">
					<i class="ti ti-loader-2 icon icon-lg fa-spin" aria-hidden="true"></i>
				</div>
				<textarea class="bm-wd-preview-editor" id="wdPreviewEditor" spellcheck="false" style="display:none;"></textarea>
				<div class="bm-wd-preview-markdown-preview" id="wdPreviewMarkdownPreview" style="display:none;"></div>
			</div>
			<div class="modal-body bm-wd-preview-body p-0" id="wdPreviewGalleryPane">
				<button type="button" class="bm-wd-preview-nav bm-wd-preview-prev" id="wdPreviewPrev" title="{lng p="wd_preview_prev"}">
					<i class="ti ti-chevron-left icon" aria-hidden="true"></i>
				</button>
				<div class="bm-wd-preview-stage" id="wdPreviewStage">
					<div class="bm-wd-preview-loading" id="wdPreviewLoading">
						<i class="ti ti-loader-2 icon icon-lg fa-spin" aria-hidden="true"></i>
					</div>
					<img class="bm-wd-preview-image" id="wdPreviewImage" alt="" style="display:none;" />
					<canvas class="bm-wd-preview-pdf" id="wdPreviewPdfCanvas" style="display:none;"></canvas>
					<video class="bm-wd-preview-video" id="wdPreviewVideo" controls playsinline style="display:none;"></video>
					<audio class="bm-wd-preview-audio" id="wdPreviewAudio" controls style="display:none;"></audio>
					<iframe class="bm-wd-preview-text" id="wdPreviewText" title="" style="display:none;"></iframe>
				</div>
				<button type="button" class="bm-wd-preview-nav bm-wd-preview-next" id="wdPreviewNext" title="{lng p="wd_preview_next"}">
					<i class="ti ti-chevron-right icon" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-footer flex-wrap">
				<span class="text-secondary small" id="wdPreviewCounter"></span>
				<div class="bm-wd-preview-text-actions ms-auto" id="wdPreviewTextToolbar" style="display:none;">
					<span class="text-secondary small me-2" id="wdPreviewTextStatus"></span>
					<div class="btn-group btn-group-sm me-2" id="wdPreviewMarkdownModes" style="display:none;">
						<button type="button" class="btn btn-outline-secondary" id="wdPreviewMdModeEdit">Editor</button>
						<button type="button" class="btn btn-outline-secondary active" id="wdPreviewMdModeSplit">Split</button>
						<button type="button" class="btn btn-outline-secondary" id="wdPreviewMdModePreview">Vorschau</button>
					</div>
					<button type="button" class="btn btn-sm btn-primary" id="wdPreviewSave">
						<i class="ti ti-device-floppy icon icon-sm me-1" aria-hidden="true"></i>{lng p="save"}
					</button>
				</div>
				<div class="input-group input-group-sm bm-wd-preview-pdf-pages ms-auto" id="wdPreviewPdfToolbar" style="display:none;" role="group" aria-label="{lng p="wd_preview_pdf_pages"}">
					<button type="button" class="btn btn-outline-secondary" id="wdPreviewPdfPrevPage" title="{lng p="wd_preview_pdf_prev"}">
						<i class="ti ti-chevron-up icon" aria-hidden="true"></i>
					</button>
					<span class="input-group-text bm-wd-preview-pdf-page-label" id="wdPreviewPdfPageLabel">1 / 1</span>
					<button type="button" class="btn btn-outline-secondary" id="wdPreviewPdfNextPage" title="{lng p="wd_preview_pdf_next"}">
						<i class="ti ti-chevron-down icon" aria-hidden="true"></i>
					</button>
				</div>
				<a class="btn btn-sm btn-outline-primary" id="wdPreviewDownload" href="#" target="_blank" rel="noopener">
					<i class="ti ti-download icon icon-sm me-1" aria-hidden="true"></i>{lng p="download"}
				</a>
			</div>
		</div>
	</div>
</div>
