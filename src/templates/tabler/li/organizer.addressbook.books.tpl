<div class="bm-organizer-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-address-book icon icon-sm" aria-hidden="true"></i>
			{lng p="addressbooks"}
		</div>
		<div class="right">
			<button type="button" class="btn btn-sm btn-primary" onclick="return organizerOpenOverlay('{sessionurl file='organizer.addressbook.php' params='action=books&do=addForm'}', '{lng p="addaddressbook"|escape:'javascript'}', 420, 190);">
				<i class="ti ti-plus icon icon-sm me-1"></i>{lng p="add"}
			</button>
		</div>
	</div>
	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter">
				<thead>
					<tr>
						<th>{lng p="title"}</th>
						<th style="width:80px;">{lng p="default"}</th>
						<th style="width:80px;"></th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$addressbooks key=bookID item=abook}
				<tr>
					<td>{text value=$abook.title}</td>
					<td>{if $abook.is_default}{lng p="yes"}{/if}</td>
					<td class="text-nowrap">
						<a href="#" onclick="return organizerOpenOverlay('{sessionurl file='organizer.addressbook.php' params="action=books&do=edit&id={$bookID}"}', '{lng p="editaddressbook"|escape:'javascript'}', 420, 190);"><i class="ti ti-pencil"></i></a>
						{if $abook.is_default}
						<span class="bm-organizer-action-disabled" title="{lng p="nodeletdefault"}" aria-label="{lng p="nodeletdefault"}" aria-disabled="true"><i class="ti ti-trash"></i></span>
						{else}
						<a href="#" onclick="return organizerOpenOverlay('{sessionurl file='organizer.addressbook.php' params="action=books&do=deleteForm&id={$bookID}"}', '{lng p="delete"|escape:'javascript'}', 400, 150);"><i class="ti ti-trash"></i></a>
						{/if}
					</td>
				</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>
