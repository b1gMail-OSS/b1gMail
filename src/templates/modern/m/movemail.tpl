<div id="leftTopButtons">
	<a class="hButton" href="main.php?action=read&id={$mailID}&sid={$sid}">
		<div class="hButtonLeftArrow"></div>&nbsp;&laquo;&nbsp;<div class="hButtonRight"></div>
	</a>
</div>

<div id="content" class="white noFooter">
	<ul>
	{foreach from=$folders item=folder key=folderID}
	{if !$folder.intelligent}
		<li>
			<a href="{if $folderID!=$currentFolder}main.php?action=moveMail&id={$mailID}&to={$folderID}&sid={$sid}{else}main.php?folder={$folderID}&sid={$sid}{/if}" class="high">
				<i class="fa {if $folder.type == 'inbox'}fa-inbox{elseif $folder.type == 'outbox'}fa-inbox{elseif $folder.type == 'drafts'}fa-envelope{elseif $folder.type == 'spam'}fa-ban{elseif $folder.type == 'trash'}fa-trash-o{elseif $folder.type == 'intellifolder'}fa-folder{else}fa-folder-o{/if}" aria-hidden="true"></i> 
				{if $folderID!=$currentFolder}{$folder.title}{else}<span style="color:grey;">{$folder.title}</span>{/if}
			</a>
		</li>
	{/if}
	{/foreach}
	</ul>
</div>
