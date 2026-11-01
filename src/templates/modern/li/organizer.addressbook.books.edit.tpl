<div id="contentHeader">
	<div class="left">
		<i class="fa fa-address-book-o" aria-hidden="true"></i>
		{if $bookItem}{lng p="editaddressbook"}{else}{lng p="addaddressbook"}{/if}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
<form name="f2" method="post" action="{sessionurl file='organizer.addressbook.php' params="action=books&do={if $bookItem}save&id={$bookItem.id}{else}add{/if}"}" onsubmit="return(checkGroupForm(this));">
	{csrffield}
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2">{if $bookItem}{lng p="editaddressbook"}{else}{lng p="addaddressbook"}{/if}</th>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="title">{lng p="title"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="title" id="title" value="{if isset($bookItem.title)}{text value=$bookItem.title allowEmpty=true}{/if}" size="34" style="width:100%;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>
</div></div>
