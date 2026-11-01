<div id="contentHeader">
	<div class="left">
		<i class="fa fa-address-book-o" aria-hidden="true"></i>
		{lng p="addressbooks"}
	</div>
</div>

<div class="scrollContainer withBottomBar">
<table class="bigTable">
	<tr>
		<th class="listTableHead">{lng p="title"}</th>
		<th class="listTableHead" width="80">{lng p="default"}</th>
		<th class="listTableHead" width="55">&nbsp;</th>
	</tr>
	<tbody class="listTBody">
	{foreach from=$addressbooks key=bookID item=abook}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}">&nbsp;<i class="fa fa-address-book-o" aria-hidden="true"></i> {text value=$abook.title}</td>
		<td class="{$class}">{if $abook.is_default}{lng p="yes"}{/if}</td>
		<td class="{$class}" nowrap="nowrap">
			<a href="{sessionurl file='organizer.addressbook.php' params="action=books&do=edit&id={$bookID}"}" onclick="return organizerOpenOverlay(this.href, '{lng p="editaddressbook"|escape:'javascript'}', 420, 190);"><i class="fa fa-pencil" aria-hidden="true"></i></a>
			{if $abook.is_default}
			<span class="bm-organizer-action-disabled" title="{lng p="nodeletdefault"}"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
			{else}
			<a href="{sessionurl file='organizer.addressbook.php' params="action=books&do=deleteForm&id={$bookID}"}" onclick="return organizerOpenOverlay(this.href, '{lng p="delete"|escape:'javascript'}', 400, 150);"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
			{/if}
		</td>
	</tr>
	{/foreach}
	</tbody>
</table>
</div>

<div id="contentFooter">
	<div class="right">
		<button type="button" class="primary" onclick="return organizerOpenOverlay('{sessionurl file='organizer.addressbook.php' params='action=books&do=addForm'}', '{lng p="addaddressbook"|escape:'javascript'}', 420, 190);">
			<i class="fa fa-plus-circle"></i>
			{lng p="add"}
		</button>
	</div>
</div>
