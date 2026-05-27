<div class="bm-mail-attachments">
{foreach from=$attachments item=attachment key=attID}
	{if $attachment.mimetype=='message/rfc822'||$attachment.filetype=='.eml'}
		{assign var=attHref value="javascript:showAttachedMail({$mailID}, '{$attID}', '{text value=$attachment.filename cut=45 escape=true}');"}
	{elseif $attachment.mimetype=='application/zip'||$attachment.filetype=='.zip'}
		{assign var=attHref value="javascript:showAttachedZIP({$mailID}, '{$attID}', '{text value=$attachment.filename cut=45 escape=true}');"}
	{else}
		{assign var=attHref value="email.read.php?id={$mailID}&action=downloadAttachment&attachment={$attID}{if $attachment.viewable}&view=true{/if}&sid={$sid}"}
	{/if}
	{if isset($selectable) && $selectable}
	<div class="bm-mail-attachment-chip bm-mail-attachment-chip-selectable">
		<label class="form-check mb-0">
			<input type="checkbox" class="form-check-input bm-mail-attachment-check" name="att[]" id="att_{$attID}" value="{$attID}" />
		</label>
		<a class="bm-mail-attachment-chip-link" href="{$attHref}"{if $attachment.mimetype!='message/rfc822'&&$attachment.filetype!='.eml'&&$attachment.mimetype!='application/zip'&&$attachment.filetype!='.zip'} target="_blank"{/if} title="{text value=$attachment.filename escape=true}">
			<span class="bm-mail-attachment-icon"><i class="ti ti-paperclip icon" aria-hidden="true"></i></span>
			<span class="bm-mail-attachment-meta">
				<span class="bm-mail-attachment-name">{text value=$attachment.filename cut=40}</span>
				<span class="bm-mail-attachment-size">{size bytes=$attachment.size}</span>
			</span>
		</a>
	</div>
	{else}
	<a class="bm-mail-attachment-chip" href="{$attHref}"{if $attachment.mimetype!='message/rfc822'&&$attachment.filetype!='.eml'&&$attachment.mimetype!='application/zip'&&$attachment.filetype!='.zip'} target="_blank"{/if} title="{text value=$attachment.filename escape=true}">
		<span class="bm-mail-attachment-icon"><i class="ti ti-paperclip icon" aria-hidden="true"></i></span>
		<span class="bm-mail-attachment-meta">
			<span class="bm-mail-attachment-name">{text value=$attachment.filename cut=40}</span>
			<span class="bm-mail-attachment-size">{size bytes=$attachment.size}</span>
		</span>
	</a>
	{/if}
{/foreach}
</div>
