{capture assign="dialogTitleText"}{if $calendarItem}{lng p="editcalendar"}{else}{lng p="addcalendar"}{/if}{/capture}
{include file="li/dialog.head.tpl" dialogTitle=$dialogTitleText dialogBodyClass="bm-dialog-organizer-collection" dialogOnLoad="documentLoader()"}

<form method="post" target="_top" class="bm-organizer-collection-form" action="{sessionurl file='organizer.calendar.php' params="action=calendars&do={if $calendarItem}save&id={$calendarItem.id}{else}add{/if}"}" onsubmit="return organizerCollectionCheck(this);">
	{csrffield}
	<div class="modal-body">
		<div class="mb-3">
			<label class="form-label required" for="title">{lng p="title"}</label>
			<input type="text" class="form-control" name="title" id="title" value="{if isset($calendarItem.title)}{text value=$calendarItem.title allowEmpty=true}{/if}" autofocus />
		</div>
		<div class="mb-0">
			<label class="form-label">{lng p="color"}</label>
			<div class="bm-organizer-color-picks">
				{section name=c loop=6}
				<label class="bm-organizer-color-pick">
					<input type="radio" name="color" value="{$smarty.section.c.index}"{if (!$calendarItem && $smarty.section.c.index==0) || (isset($calendarItem.color) && $calendarItem.color==$smarty.section.c.index)} checked="checked"{/if} />
					<span style="{if $smarty.section.c.index==0}background:#3D81EB;{elseif $smarty.section.c.index==1}background:#5DB747;{elseif $smarty.section.c.index==2}background:#FA514A;{elseif $smarty.section.c.index==3}background:#FD9530;{elseif $smarty.section.c.index==4}background:#C358BF;{else}background:#8A74D3;{/if}"></span>
				</label>
				{/section}
			</div>
		</div>
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
