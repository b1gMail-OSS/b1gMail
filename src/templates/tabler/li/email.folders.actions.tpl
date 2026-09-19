{assign var="folderActionShare" value=0}
{assign var="folderActionEdit" value=0}
{assign var="folderActionDelete" value=0}
{assign var="folderActionLeave" value=0}
{if $folderActions=='sys'}
	{assign var="folderActionShare" value=1}
	{assign var="folderActionEdit" value=1}
{elseif $folderActions=='own'}
	{if $folder.intelligent!=1}{assign var="folderActionShare" value=1}{/if}
	{assign var="folderActionEdit" value=1}
	{assign var="folderActionDelete" value=1}
{elseif $folderActions=='shared'}
	{if $folder.can_leave}{assign var="folderActionLeave" value=1}{/if}
{/if}
<div class="btn-group btn-group-sm" role="group" aria-label="{lng p="actions"}">
	<a href="{sessionurl file='email.folders.php' params="action=toggleFavorite&id={$folderID}"}" class="btn btn-icon{if isset($folderFavoriteMap[$folderID])} text-yellow{/if}" title="{if isset($folderFavoriteMap[$folderID])}{lng p="removefromfavorites"}{else}{lng p="addtofavorites"}{/if}" aria-label="{if isset($folderFavoriteMap[$folderID])}{lng p="removefromfavorites"}{else}{lng p="addtofavorites"}{/if}"><i class="ti {if isset($folderFavoriteMap[$folderID])}ti-star-filled{else}ti-star{/if} icon" aria-hidden="true"></i></a>
	{if $canShareMail}
	{if $folderActionShare}
	<a href="#" class="btn btn-icon" title="{lng p="sharefolder"}" aria-label="{lng p="sharefolder"}" onclick="openOverlay('{sessionurl file='email.folders.php' params="action=share&id={$folderID}"}', '{lng p="sharefolder"|escape:'javascript'}', 520, 360, true); return false;"><i class="ti ti-share icon" aria-hidden="true"></i></a>
	{else}
	<span class="btn btn-icon disabled" aria-disabled="true" title="{lng p="sharefolder"}"><i class="ti ti-share icon" aria-hidden="true"></i></span>
	{/if}
	{/if}
	{if $folderActionEdit}
	<a href="{sessionurl file='email.folders.php' params="action=editFolder&id={$folderID}"}" class="btn btn-icon" title="{lng p="edit"}" aria-label="{lng p="edit"}"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
	{else}
	<span class="btn btn-icon disabled" aria-disabled="true" title="{lng p="edit"}"><i class="ti ti-pencil icon" aria-hidden="true"></i></span>
	{/if}
	{if $folderActionDelete}
	<a onclick="return confirm('{lng p="realdel"}');" href="{sessionurl file='email.folders.php' params="action=deleteFolder&id={$folderID}&csrf_token={$csrfToken}"}" class="btn btn-icon text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
	{elseif $folderActionLeave}
	<a href="#" class="btn btn-icon text-danger" title="{lng p="shareleave"}" aria-label="{lng p="shareleave"}" onclick="openOverlay('{sessionurl file='email.folders.php' params="action=leaveshare&id={$folderID}"}', '{lng p="shareleave"|escape:'javascript'}', 480, 260, true); return false;"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
	{else}
	<span class="btn btn-icon disabled" aria-disabled="true" title="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></span>
	{/if}
</div>
