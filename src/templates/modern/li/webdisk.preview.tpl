<div class="modal fade bm-wd-preview-modal" id="wdPreviewModal" tabindex="-1" aria-hidden="true" style="display:none;">
	<div class="modal-dialog bm-wd-preview-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="wdPreviewTitle">&nbsp;</h5>
				<button type="button" class="close" id="wdPreviewClose" aria-label="{lng p="close"}"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="bm-wd-preview-editor-pane" id="wdPreviewEditorPane" style="display:none;">
				<div class="bm-wd-preview-editor-loading" id="wdPreviewEditorLoading">
					<i class="fa fa-spinner fa-spin fa-fw fa-2x" aria-hidden="true"></i>
				</div>
				<textarea class="bm-wd-preview-editor" id="wdPreviewEditor" spellcheck="false" style="display:none;"></textarea>
				<div class="bm-wd-preview-markdown-preview" id="wdPreviewMarkdownPreview" style="display:none;"></div>
			</div>
			<div class="modal-body bm-wd-preview-body p-0" id="wdPreviewGalleryPane">
				<button type="button" class="bm-wd-preview-nav bm-wd-preview-prev" id="wdPreviewPrev" title="{lng p="wd_preview_prev"}">
					<i class="fa fa-chevron-left" aria-hidden="true"></i>
				</button>
				<div class="bm-wd-preview-stage" id="wdPreviewStage">
					<div class="bm-wd-preview-loading" id="wdPreviewLoading">
						<i class="fa fa-spinner fa-spin fa-fw fa-2x" aria-hidden="true"></i>
					</div>
					<img class="bm-wd-preview-image" id="wdPreviewImage" alt="" style="display:none;" />
					<canvas class="bm-wd-preview-pdf" id="wdPreviewPdfCanvas" style="display:none;"></canvas>
					<video class="bm-wd-preview-video" id="wdPreviewVideo" controls playsinline style="display:none;"></video>
					<audio class="bm-wd-preview-audio" id="wdPreviewAudio" controls style="display:none;"></audio>
					<iframe class="bm-wd-preview-text" id="wdPreviewText" title="" style="display:none;"></iframe>
				</div>
				<button type="button" class="bm-wd-preview-nav bm-wd-preview-next" id="wdPreviewNext" title="{lng p="wd_preview_next"}">
					<i class="fa fa-chevron-right" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-footer">
				<span class="bm-wd-preview-counter" id="wdPreviewCounter"></span>
				<div class="bm-wd-preview-text-actions" id="wdPreviewTextToolbar" style="display:none;">
					<span id="wdPreviewTextStatus"></span>
					<span id="wdPreviewMarkdownModes" style="display:none;">
						<button type="button" id="wdPreviewMdModeEdit">Editor</button>
						<button type="button" class="primary" id="wdPreviewMdModeSplit">Split</button>
						<button type="button" id="wdPreviewMdModePreview">Vorschau</button>
					</span>
					<button type="button" class="primary" id="wdPreviewSave">
						<i class="fa fa-save" aria-hidden="true"></i> {lng p="save"}
					</button>
				</div>
				<div class="bm-wd-preview-pdf-pages" id="wdPreviewPdfToolbar" style="display:none;">
					<button type="button" id="wdPreviewPdfPrevPage" title="{lng p="wd_preview_pdf_prev"}">
						<i class="fa fa-chevron-up" aria-hidden="true"></i>
					</button>
					<span id="wdPreviewPdfPageLabel">1 / 1</span>
					<button type="button" id="wdPreviewPdfNextPage" title="{lng p="wd_preview_pdf_next"}">
						<i class="fa fa-chevron-down" aria-hidden="true"></i>
					</button>
				</div>
				<a id="wdPreviewDownload" href="#" target="_blank" rel="noopener">
					<i class="fa fa-download" aria-hidden="true"></i> {lng p="download"}
				</a>
			</div>
		</div>
	</div>
</div>
