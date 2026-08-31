{include file="li/dialog.head.tpl" title=$title dialogBodyClass="bm-dialog-openfile bm-dialog-modal-sections"}

<div class="bm-openfile-dialog">
	<form action="{$formAction}" enctype="multipart/form-data" method="post" class="bm-dialog-form">
		{csrffield}
		<div class="modal-body">
			<p class="text-secondary bm-dialog-intro mb-3">{$text}</p>

			{include file="li/file.selector.tpl" name=$fieldName multiple=$multiple sid=$sid hasWebdisk=$hasWebdisk}

			{if $bar}
			<div class="mt-3">
				{progressBar value=$bar.value max=$bar.max width=100}
			</div>
			{/if}
		</div>

		<div class="modal-footer bm-openfile-dialog-footer">
			<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()">{lng p="cancel"}</button>
			<button type="submit" class="btn btn-primary">
				<i class="ti ti-check icon" aria-hidden="true"></i>
				{lng p="ok"}
			</button>
		</div>
	</form>
</div>

{if isset($fileSource) && $fileSource == 'webdisk'}
<script>
<!--
	registerLoadAction(function()
	{
		var sel = document.querySelector('.bm-file-selector[data-name="{$fieldName}"] .bm-file-selector-source');
		if(sel)
		{
			sel.value = 'webdisk';
			changeFileSelectorSource(sel, '{$fieldName}');
		}
	});
//-->
</script>
{/if}

{include file="li/dialog.foot.tpl"}
