
<h1><i class="fa fa-address-book-o" aria-hidden="true"></i> {lng p="editgroup"}</h1>

<form name="f1" method="post" action="organizer.addressbook.php?action=saveGroup&id={$group.id}&sid={$sid}" onsubmit="return(checkGroupForm(this));">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="editgroup"}</th>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="title">{lng p="title"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="title" id="title" value="{text value=$group.title allowEmpty=true}" style="width:100%;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>
