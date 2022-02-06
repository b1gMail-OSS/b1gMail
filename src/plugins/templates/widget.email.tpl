<div class="innerWidget">
<table cellspacing="0" width="100%" style="table-layout:fixed;">
	{foreach from=$bmwidget_email_items item=folder key=folderID}
<tr>
	<td width="20" align="center">
	<i class="fa {if $folder.icon == 'inbox'}fa-inbox{elseif $folder.icon == 'outbox'}fa-inbox{elseif $folder.icon == 'drafts'}fa-envelope{elseif $folder.icon == 'spam'}fa-ban{elseif $folder.icon == 'trash'}fa-trash-o{elseif $folder.icon == 'intellifolder'}fa-folder{else}fa-folder-o{/if}" aria-hidden="true"></i>
	</td>
	<td style="text-overflow:ellipsis;overflow:hidden;"><a href="email.php?folder={$folderID}&sid={$sid}">{$folder.text}</a></td>
	<td align="left" width="50"><i class="fa fa-envelope-o"></i> {$folder.allMails}</td>
	<td align="left" width="45"><i class="fa fa-flag-o"></i> {if $folder.flaggedMails>0}<b>{$folder.flaggedMails}</b>{else}-{/if}</td>
	<td align="left" width="45"><i class="fa fa-envelope"></i> {if $folder.unreadMails>0}<b>{$folder.unreadMails}</b>{else}-{/if}</td>
</tr>
	{/foreach}
</table>
</div>