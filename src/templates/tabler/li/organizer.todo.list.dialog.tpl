{capture assign="dialogTitleText"}{lng p="edittasklist"}{/capture}
{include file="li/dialog.head.tpl" dialogTitle=$dialogTitleText dialogBodyClass="bm-dialog-organizer-collection" dialogOnLoad="documentLoader()"}

<form method="post" target="_top" class="bm-organizer-collection-form" action="{sessionurl file='organizer.todo.php' params="action=editList&do=save&id={$taskListItem.tasklistid}"}" onsubmit="return organizerCollectionCheck(this);">
	{csrffield}
	<div class="modal-body">
		<label class="form-label required" for="title">{lng p="title"}</label>
		<input type="text" class="form-control" name="title" id="title" value="{text value=$taskListItem.title allowEmpty=true}" autofocus />
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
