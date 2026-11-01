{capture assign="dialogTitleText"}{lng p="shareleave"}{/capture}
{include file="li/dialog.head.tpl" dialogTitle=$dialogTitleText dialogBodyClass="bm-dialog-organizer-collection" dialogOnLoad="documentLoader()"}

<form method="post" target="_top" class="bm-organizer-collection-form" action="{sessionurl file=$leaveAction params="action=leaveshare&id={$leaveItem.id}{if isset($leaveKind) && $leaveKind != ''}&kind={$leaveKind}{/if}"}">
	{csrffield}
	<input type="hidden" name="id" value="{$leaveItem.id}" />
	{if isset($leaveKind) && $leaveKind != ''}<input type="hidden" name="kind" value="{$leaveKind}" />{/if}
	<input type="hidden" name="do" value="leave" />
	<div class="modal-body">
		<p class="mb-1">{lng p="shareleaveq"}</p>
		<p class="bm-organizer-collection-name mb-0">{text value=$leaveItem.title}</p>
		{if isset($leaveItem.owner_email) && $leaveItem.owner_email != ''}
		<p class="text-secondary small mb-0">{text value=$leaveItem.owner_email}</p>
		{/if}
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()">{lng p="cancel"}</button>
		<button type="submit" class="btn btn-danger">{lng p="shareleave"}</button>
	</div>
</form>

{include file="li/dialog.foot.tpl"}
