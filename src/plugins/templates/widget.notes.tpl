<div class="innerWidget notePreview" id="notePreview">
	{lng p="clicknote"}
</div>
<div class="innerWidget" style="max-height: 79px; overflow-y: auto; border-top: 1px solid #DDDDDD;">
{foreach from=$bmwidget_notes_items key=noteID item=note}
	<a href="javascript:previewNote('{$sid}', '{$noteID}');">
	<i class="fa fa-sticky-note-o" aria-hidden="true"></i>
	{text value=$note.text cut=30}</a><br />
{/foreach}
</div>