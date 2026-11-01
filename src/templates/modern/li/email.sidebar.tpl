{hook id="email.sidebar.tpl:head"}

<div class="sidebarHeading bm-email-sidebar-heading">
	<span>{lng p="email"}</span>
</div>
<div class="contentMenuIcons">
	<a href="{sessionurl file='email.compose.php'}"><i class="fa fa-envelope-o" aria-hidden="true"></i> {lng p="sendmail"}</a><br />
	<a href="{sessionurl file='email.folders.php'}"><i class="fa fa-folder-open-o" aria-hidden="true"></i> {lng p="folderadmin"}</a><br />
	{hook id="email.sidebar.tpl:email"}
</div>

{if isset($folderFavorites) && $folderFavorites}
<div class="sidebarHeading">{lng p="folderfavorites"}</div>
<div class="contentMenuIcons">
{foreach from=$folderFavorites key=favID item=folder}
	<a href="{sessionurl file='email.php' params="folder={$favID}"}"><i class="fa fa-star" aria-hidden="true"></i> {text value=$folder.title cut=22 escape=true noentities=true}{if isset($folder.unread) && $folder.unread>0} <b>({$folder.unread})</b>{/if}</a><br />
{/foreach}
</div>
{/if}

<div class="sidebarHeading bm-mailbox-heading" title="{text value=$mailboxEmail escape=true}">{text value=$mailboxEmail}</div>
<div class="contentMenuIcons" id="folderList">
</div>
<script>
<!--
	var folderFavoriteIDs = [{foreach from=$folderFavoriteIDs item=fid name=ff}{$fid}{if !$smarty.foreach.ff.last},{/if}{/foreach}];
	{include file="li/email.folderlist.tpl"}
	function bmRenderEmailFolderList()
	{
		EBID('folderList').innerHTML = bmEmailFolderTreesHtml();
		enableFolderDragTargets();
	}
	bmRenderEmailFolderList();
//-->
</script>

{hook id="email.sidebar.tpl:foot"}
