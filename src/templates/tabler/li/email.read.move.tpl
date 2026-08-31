{capture assign="dialogTitleText"}{lng p="move"}{/capture}
{include file="li/dialog.head.tpl" dialogTitle=$dialogTitleText dialogBodyClass="bm-dialog-move-mail" dialogOnLoad="documentLoader();bmMoveMailDialogInit();"}

<div class="bm-dialog-page bm-move-mail-dialog">
	<div class="bm-move-mail-list-wrap">
		<div class="list-group list-group-flush bm-move-mail-list" role="listbox" aria-label="{lng p="movemailto"}">
		{foreach from=$moveFolderList item=folder}
			<a href="email.read.php?action=move&id={$mailID}&dest={$folder.id}{$sessionUrlSuffix}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 bm-move-mail-item" style="--bm-depth: {$folder.depth};" role="option">
				<i class="ti {if $folder.icon == 'inbox'}ti-inbox{elseif $folder.icon == 'outbox'}ti-send{elseif $folder.icon == 'drafts'}ti-file-pencil{elseif $folder.icon == 'spam'}ti-ban{elseif $folder.icon == 'trash'}ti-trash{elseif $folder.icon == 'intellifolder'}ti-folder{else}ti-folder{/if} icon text-secondary flex-shrink-0" aria-hidden="true"></i>
				<span class="bm-move-mail-label text-truncate">{text value=$folder.text}</span>
			</a>
		{/foreach}
		</div>
	</div>

	<div class="bm-dialog-actions">
		<button type="button" class="btn btn-ghost-secondary bm-move-mail-cancel" onclick="parent.hideOverlay()">{lng p="cancel"}</button>
	</div>
</div>

<script>
<!--
function bmDialogFitFrame()
{
	try {
		var frame = window.frameElement;
		if(!frame || !window.parent)
			return;

		var parentWin = window.parent,
			parentH = parentWin.innerHeight || parentWin.document.documentElement.clientHeight || 600,
			isMobile = parentWin.matchMedia && parentWin.matchMedia('(max-width: 575.98px)').matches,
			docH = Math.ceil(document.documentElement.scrollHeight),
			cap, nextH;

		if(isMobile)
		{
			cap = Math.floor(parentH * 0.92) - 8;
			nextH = Math.min(Math.max(docH, 120), cap);
		}
		else
		{
			cap = Math.floor(parentH * 0.85) - 56;
			nextH = Math.min(Math.max(docH, 200), cap);
		}

		frame.style.setProperty('height', nextH + 'px', 'important');
		frame.style.setProperty('min-height', Math.min(nextH, isMobile ? 160 : 200) + 'px', 'important');
	} catch(e) {}
}

function bmMoveMailDialogInit()
{
	bmDialogFitFrame();
	window.addEventListener('resize', bmDialogFitFrame);
}
//-->
</script>

{include file="li/dialog.foot.tpl"}
