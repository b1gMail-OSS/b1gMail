<div id="contentHeader">
	<div class="left">
		<i class="fa fa-folder-o" aria-hidden="true"></i> {lng p="folderadmin"}
	</div>
</div>

<form name="f1" method="post" action="email.folders.php?action=action&sid={$sid}">

<div class="scrollContainer withBottomBar">
<table class="bigTable">
	<thead>
	<tr>
		<th width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'folder');" /></th>
		<th class="listTableHead">
			<a href="email.folders.php?sid={$sid}&sort=titel&order={$sortOrderInv}">{lng p="title"}</a>
			{if $sortColumn=='titel'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="160">
			<a href="email.folders.php?sid={$sid}&sort=parent&order={$sortOrderInv}">{lng p="parentfolder"}</a>
			{if $sortColumn=='parent'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="70">
			{lng p="size"}
		</th>
		<th width="140">
			{lng p="status"}
		</th>
		<th width="70">
			<a href="email.folders.php?sid={$sid}&sort=subscribed&order={$sortOrderInv}">{lng p="subscribed"}</a>
			{if $sortColumn=='subscribed'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="55">&nbsp;</th>
	</tr>
	</thead>
	
	{if $sysFolderList}
	<tr>
		<td colspan="7" class="folderGroup">
			<a style="display:block;" href="javascript:toggleGroup('sys');">&nbsp;<img id="groupImage_sys" src="{$tpldir}images/contract.png" width="11" height="11" border="0" align="absmiddle" alt="" />
			&nbsp;{lng p="sysfolders"}</a>
		</td>
	</tr>
	<tbody id="group_sys" style="display:;">
	{foreach from=$sysFolderList key=folderID item=folder}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}" nowrap="nowrap"><input type="checkbox" disabled="disabled" /></td>
		<td class="{$class}" nowrap="nowrap">&nbsp;<a href="email.php?sid={$sid}&folder={$folderID}"><i class="fa {if $folder.type == 'inbox'}fa-inbox{elseif $folder.type == 'outbox'}fa-inbox{elseif $folder.type == 'drafts'}fa-envelope{elseif $folder.type == 'spam'}fa-ban{elseif $folder.type == 'trash'}fa-trash-o{elseif $folder.type == 'intellifolder'}fa-folder{else}fa-folder-o{/if}" aria-hidden="true"></i> {text value=$folder.titel cut=25}</a></td>
		<td class="{$class}" nowrap="nowrap">&nbsp;{text value=$folder.parent cut=15}</td>
		<td class="{$class}" nowrap="nowrap" style="text-align:center;">
			{size bytes=$folder.size}
		</td>
		<td class="{$class}" nowrap="nowrap" style="text-align:center;">
			<table>
				<tr>
					<td width="45" align="left"><i class="fa fa-envelope-o"></i>
						{$folder.allMails}</td>
					<td width="45" align="left"><i class="fa fa-envelope"></i>
						{$folder.unreadMails}</td>
					<td width="45" align="left"><i class="fa fa-flag-o"></i>
						{$folder.flaggedMails}</td>
				</tr>
			</table>
		</td>
		<td class="{$class}" nowrap="nowrap"><center><input type="checkbox" checked="checked" disabled="disabled" /></center></td>
		<td class="{$class}" nowrap="nowrap">
			<a href="email.folders.php?action=editFolder&id={$folderID}&sid={$sid}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
		</td>
	</tr>
	{/foreach}
	</tbody>
	{/if}
	
	{if $theFolderList}
	<tr>
		<td colspan="7" class="folderGroup">
			<a style="display:block;" href="javascript:toggleGroup('own');">&nbsp;<img id="groupImage_own" src="{$tpldir}images/contract.png" width="11" height="11" border="0" align="absmiddle" alt="" />
			&nbsp;{lng p="ownfolders"}</a>
		</td>
	</tr>
	<tbody id="group_own" style="display:;">
	{foreach from=$theFolderList key=folderID item=folder}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}" nowrap="nowrap"><input type="checkbox" id="folder_{$folderID}" name="folder_{$folderID}" /></td>
		<td class="{if $sortColumn=='titel'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">&nbsp;<a href="email.php?sid={$sid}&folder={$folderID}">{if $folder.intelligent==1}<i class="fa fa-folder" aria-hidden="true"></i>{else}<i class="fa fa-folder-o" aria-hidden="true"></i>{/if} {text value=$folder.titel cut=25}</a></td>
		<td class="{if $sortColumn=='parent'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">&nbsp;{text value=$folder.parent cut=15}</td>
		<td class="{$class}" nowrap="nowrap" style="text-align:center;">
			{if $folder.intelligent}-{else}{size bytes=$folder.size}{/if}
		</td>
		<td class="{$class}" nowrap="nowrap" style="text-align:center;">
			<table>
				<tr>
					<td width="45" align="left"><i class="fa fa-envelope-o"></i>
						{$folder.allMails}</td>
					<td width="45" align="left"><i class="fa fa-envelope"></i>
						{$folder.unreadMails}</td>
					<td width="45" align="left"><i class="fa fa-flag-o"></i>
						{$folder.flaggedMails}</td>
				</tr>
			</table>
		</td>
		<td class="{if $sortColumn=='subscribed'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap"><center><input type="checkbox" {if $folder.subscribed==1}checked="checked" {/if} onchange="updateFolderSubscription('{$folderID}', this, '{$sid}')" /></center></td>
		<td class="{$class}" nowrap="nowrap">
			<a href="email.folders.php?action=editFolder&id={$folderID}&sid={$sid}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
			<a onclick="return confirm('{lng p="realdel"}');" href="email.folders.php?action=deleteFolder&id={$folderID}&sid={$sid}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		</td>
	</tr>
	{/foreach}
	</tbody>
	{/if}

	{if $sharedFolderList}
	<tr>
		<td colspan="7" class="folderGroup">
			<a style="display:block;" href="javascript:toggleGroup('shared');">&nbsp;<img id="groupImage_shared" src="{$tpldir}images/contract.png" width="11" height="11" border="0" align="absmiddle" alt="" />
			&nbsp;{lng p="sharedfolders"}</a>
		</td>
	</tr>
	<tbody id="group_shared" style="display:;">
	{foreach from=$sharedFolderList key=folderID item=folder}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}" nowrap="nowrap"><input type="checkbox" id="folder_{$folderID}" name="folder_{$folderID}" /></td>
		<td class="{if $sortColumn=='titel'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">&nbsp;<a href="email.php?sid={$sid}&folder={$folderID}"><i class="fa fa-share-square-o" aria-hidden="true"></i> {text value=$folder.titel cut=25}</a>
			{if $folder.readonly}<small>({lng p="readonly"})</small>{/if}</td>
		<td class="{if $sortColumn=='parent'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">&nbsp;{text value=$folder.parent cut=15}</td>
		<td class="{$class}" nowrap="nowrap" style="text-align:center;">
			{size bytes=$folder.size}
		</td>
		<td class="{$class}" nowrap="nowrap" style="text-align:center;">
			<table>
				<tr>
					<td width="45" align="left"><i class="fa fa-envelope-o"></i>
						{$folder.allMails}</td>
					<td width="45" align="left"><i class="fa fa-envelope"></i>
						{$folder.unreadMails}</td>
					<td width="45" align="left"><i class="fa fa-flag-o"></i>
						{$folder.flaggedMails}</td>
				</tr>
			</table>
		</td>
		<td class="{if $sortColumn=='subscribed'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap"><center><input type="checkbox" {if $folder.subscribed==1}checked="checked" {/if} disabled="disabled" /></center></td>
		<td class="{$class}" nowrap="nowrap">
			&nbsp;
		</td>
	</tr>
	{/foreach}
	</tbody>
	{/if}
</table>

</div>

<div id="contentFooter">
	<div class="left">
		<select class="smallInput" name="do">
			<option value="-">------ {lng p="selaction"} ------</option>
			<option value="delete">{lng p="delete"}</option>
		</select>
		<input class="smallInput" type="submit" value="{lng p="ok"}" />
	</div>
	
	<div class="right">
		<button class="primary" onclick="document.location.href='email.folders.php?action=addFolder&sid={$sid}';" type="button">
			<i class="fa fa-plus-circle"></i>
			{lng p="addfolder"}
		</button>
	</div>
</div>

</form>
