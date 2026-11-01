{capture assign="dialogTitleText"}{if $bookItem}{lng p="editaddressbook"}{else}{lng p="addaddressbook"}{/if}{/capture}
{include file="li/dialog.head.tpl" dialogTitle=$dialogTitleText dialogBodyClass="bm-dialog-organizer-collection" dialogOnLoad="documentLoader()"}

<form method="post" target="_top" class="bm-organizer-collection-form" action="{sessionurl file='organizer.addressbook.php' params="action=books&do={if $bookItem}save&id={$bookItem.id}{else}add{/if}"}" onsubmit="return organizerCollectionCheck(this);">
	{csrffield}
	<div class="modal-body">
		<label class="form-label required" for="title">{lng p="title"}</label>
		<input type="text" class="form-control" name="title" id="title" value="{if isset($bookItem.title)}{text value=$bookItem.title allowEmpty=true}{/if}" autofocus />
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()">{lng p="cancel"}</button>
		<button type="submit" class="btn btn-primary">{lng p="ok"}</button>
	</div>
</form>
<script>
function organizerCollectionCheck(f) {
	var t = f.elements['title'];
	if(!t || String(t.value).replace(/^\s+|\s+$/g, '').length === 0) { alert(lang['fillin']); return false; }
	return true;
}
</script>

{include file="li/dialog.foot.tpl"}
