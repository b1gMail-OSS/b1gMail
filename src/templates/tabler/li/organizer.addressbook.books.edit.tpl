<div class="bm-organizer-page bm-organizer-form-page">
	<form name="f2" method="post" action="{sessionurl file='organizer.addressbook.php' params="action=books&do={if $bookItem}save&id={$bookItem.id}{else}add{/if}"}" class="bm-organizer-form" onsubmit="return(checkGroupForm(this));">
		{csrffield}
		<div id="contentHeader" class="bm-compose-header">
			<div class="left">
				<span class="bm-compose-header-title">{if $bookItem}{lng p="editaddressbook"}{else}{lng p="addaddressbook"}{/if}</span>
			</div>
		</div>
		<div class="bm-organizer-form-body">
			<div class="mb-3">
				<label class="form-label required" for="title">{lng p="title"}</label>
				<input type="text" class="form-control" name="title" id="title" value="{if isset($bookItem.title)}{text value=$bookItem.title allowEmpty=true}{/if}" />
			</div>
			<button type="submit" class="btn btn-primary">{lng p="ok"}</button>
		</div>
	</form>
</div>
