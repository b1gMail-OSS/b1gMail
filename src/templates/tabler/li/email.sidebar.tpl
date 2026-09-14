{hook id="email.sidebar.tpl:head"}

<div class="sidebarHeading bm-email-sidebar-heading">
	<span>{lng p="email"}</span>
</div>
<div class="contentMenuIcons">
	<a href="{sessionurl file='email.compose.php'}">{include file="li/icon.tpl" faIcon="fa-envelope-o"} {lng p="sendmail"}</a><br />
	<a href="{sessionurl file='email.folders.php'}">{include file="li/icon.tpl" faIcon="fa-folder-open-o"} {lng p="folderadmin"}</a><br />
	{hook id="email.sidebar.tpl:email"}
</div>

{if isset($folderFavorites) && $folderFavorites}
<div class="sidebarHeading bm-folder-fav-heading">{lng p="folderfavorites"}</div>
<div class="contentMenuIcons bm-folder-favorites">
{foreach from=$folderFavorites key=favID item=folder}
	<a href="{sessionurl file='email.php' params="folder={$favID}"}" class="bm-folder-fav{if isset($currentFolderID) && $currentFolderID==$favID} current{/if}" data-folder-id="{$favID}">
		<i class="ti {if $folder.type == 'inbox'}ti-inbox{elseif $folder.type == 'outbox'}ti-send{elseif $folder.type == 'drafts'}ti-file-pencil{elseif $folder.type == 'spam'}ti-ban{elseif $folder.type == 'trash'}ti-trash{elseif $folder.type == 'sharedfolder'}ti-folder-share{elseif $folder.type == 'intellifolder'}ti-folder{else}ti-folder{/if} icon icon-1" aria-hidden="true"></i>
		<span class="bm-folder-fav-label">{text value=$folder.title cut=22 escape=true noentities=true}</span>
		<span class="bm-folder-count bm-folder-fav-count" data-folder-count-id="{$favID}"{if empty($folder.unread) || $folder.unread<=0} style="display:none;"{/if}>{if !empty($folder.unread)}{$folder.unread}{else}0{/if}</span>
	</a>
{/foreach}
</div>
{/if}

<div class="sidebarHeading bm-mailbox-heading" title="{text value=$mailboxEmail escape=true}">{text value=$mailboxEmail}</div>
<div class="bm-folder-tree" id="folderList">
</div>
<script>
<!--
	var folderFavoriteIDs = [{foreach from=$folderFavoriteIDs item=fid name=ff}{$fid}{if !$smarty.foreach.ff.last},{/if}{/foreach}];
	{include file="li/email.folderlist.tpl"}
	function bmUpdateFolderFavoriteCounts()
	{
		if(typeof bmFolderUnreadCounts !== 'object' || bmFolderUnreadCounts === null)
			return;
		var elems = document.querySelectorAll('.bm-folder-fav-count');
		for(var i = 0; i < elems.length; i++)
		{
			var id = elems[i].getAttribute('data-folder-count-id');
			if(id === null || typeof bmFolderUnreadCounts[id] === 'undefined')
				continue;
			var cnt = parseInt(bmFolderUnreadCounts[id], 10);
			if(isNaN(cnt) || cnt <= 0)
			{
				elems[i].textContent = '0';
				elems[i].style.display = 'none';
			}
			else
			{
				elems[i].textContent = cnt;
				elems[i].style.display = '';
			}
		}
	}
	function bmRenderEmailFolderList()
	{
		EBID('folderList').innerHTML = bmEmailFolderTreesHtml();
		enableFolderDragTargets();
		bmUpdateFolderFavoriteCounts();
	}
	bmRenderEmailFolderList();
//-->
</script>

{hook id="email.sidebar.tpl:foot"}
