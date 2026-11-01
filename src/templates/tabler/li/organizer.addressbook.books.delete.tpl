{capture assign="dialogTitleText"}{lng p="delete"}{/capture}
{include file="li/dialog.head.tpl" dialogTitle=$dialogTitleText dialogBodyClass="bm-dialog-organizer-collection" dialogOnLoad="documentLoader()"}

<form method="post" target="_top" class="bm-organizer-collection-form" action="{sessionurl file='organizer.addressbook.php' params="action=books&do=delete&id={$bookItem.id}&csrf_token={$csrfToken}"}">
	{csrffield}
	<div class="modal-body">
		<p class="mb-1">{lng p="realdel"}</p>
		<p class="bm-organizer-collection-name mb-0">{text value=$bookItem.title}</p>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()">{lng p="cancel"}</button>
		<button type="submit" class="btn btn-danger">{lng p="delete"}</button>
	</div>
</form>

{include file="li/dialog.foot.tpl"}
