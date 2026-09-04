<div data-role="header" data-position="fixed">
	<h1>{$pageTitle}</h1>
	<a href="{sessionurl file='email.php' params='action=compose'}" data-icon="forward" data-iconpos="right" class="ui-btn-right">{lng p="sendmail"}</a>
</div>

<div data-role="content">
	<ul data-role="listview" data-filter="true" data-filter-placeholder="{lng p="search"}...">
	{foreach from=$folders item=folder key=folderID}
		<li>
			<a href="{sessionurl file='email.php' params="folder={$folderID}&id={$mailID}"}" data-transition="slide">
				<i class="fa {if $folder.type == 'inbox'}fa-inbox{elseif $folder.type == 'outbox'}fa-inbox{elseif $folder.type == 'drafts'}fa-envelope{elseif $folder.type == 'spam'}fa-ban{elseif $folder.type == 'trash'}fa-trash-o{elseif $folder.type == 'intellifolder'}fa-folder{else}fa-folder-o{/if}" aria-hidden="true"></i>
				{$folder.title}
				{if $folder.unread}<span class="ui-li-count">{$folder.unread}</span>{/if}
			</a>
		</li>
	{/foreach}
	</ul>
</div>
