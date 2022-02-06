<br />
<table width="95%">
	<tr>
		<td valign="top" width="64" align="center"><i class="fa fa-info-circle fa-4x" aria-hidden="true"></i></td>
		<td valign="top">
			<b>{lng p="sendmail"}</b>
			<br /><br />{lng p="mailsent"}
			<br /><br />
			<input type="button" value="&laquo; {lng p="back"}" class="primary" onclick="document.location.href='email.php?sid={$sid}';" />
			<br /><br /><br />
		</td>
	</tr>
	
	{if $addrMails}
	<tr>
		<td valign="top" width="64" align="center"><i class="fa fa-address-book-o fa-4x" aria-hidden="true"></i></td>
		<td valign="top">
			<b>{lng p="addressbook"}</b>
			<br /><br />
			{lng p="addraddtext"}
			<br /><br />
			<form action="organizer.addressbook.php?action=quickAdd&sid={$sid}" method="post" onsubmit="return ajaxFormSubmit(this);">
				{*<table class="listTable">
					<tr>
						<th class="listTableHead" width="24">&nbsp;</th>
						<th class="listTableHead">{lng p="email"}</th>
						<th class="listTableHead" width="25%">{lng p="firstname"}</th>
						<th class="listTableHead" width="25%">{lng p="surname"}</th>
						<th class="listTableHead" width="25%">{lng p="company"}</th>
					</tr>
					
					{foreach from=$addrMails item=item key=i}
					{cycle values="listTableTR,listTableTR2" assign="class"}
					<tr class="{$class}">
						<td><input type="checkbox" name="addr[{$i}][email]" value="{text value=$item.email}" id="addr_{$i}" checked="checked" /></td>
						<td><label for="addr_{$i}">{text value=$item.email cut=35}</label></td>
						<td><input type="text" name="addr[{$i}][firstname]" value="{text value=$item.firstname allowEmpty=true}" style="width:95%;" /></td>
						<td><input type="text" name="addr[{$i}][lastname]" value="{text value=$item.lastname allowEmpty=true}" style="width:95%;" /></td>
						<td><input type="text" name="addr[{$i}][company]" style="width:95%;" /></td>
					</tr>
					{/foreach}
				</table>*}
				
				{foreach from=$addrMails item=item key=i}
				<table class="listTable">
					<tr>
						<td class="listTableHead" colspan="2">
							<input type="checkbox" name="addr[{$i}][email]" value="{text value=$item.email}" id="addr_{$i}" checked="checked" />
							<label for="addr_{$i}">{text value=$item.email}</label>
						</td>
					</tr>
					<tr>
						<td class="listTableLeft"><label>{lng p="firstname"}:</label></td>
						<td class="listTableRight">
							<input type="text" name="addr[{$i}][firstname]" value="{text value=$item.firstname allowEmpty=true}" size="30" />
						</td>
					</tr>
					<tr>
						<td class="listTableLeft"><label>{lng p="surname"}:</label></td>
						<td class="listTableRight">
							<input type="text" name="addr[{$i}][lastname]" value="{text value=$item.lastname allowEmpty=true}" size="30" />
						</td>
					</tr>
					<tr>
						<td class="listTableLeft"><label>{lng p="company"}:</label></td>
						<td class="listTableRight">
							<input type="text" name="addr[{$i}][company]" size="30" />
						</td>
					</tr>
					{if $groups}<tr>
						<td class="listTableLeft"><label>{lng p="groupmember"}:</label></td>
						<td class="listTableRight">
							{foreach from=$groups item=group key=groupID}
								<input type="checkbox" id="group_{$i}_{$groupID}" name="addr[{$i}][groups][]" value="{$groupID}" />
								<label for="group_{$i}_{$groupID}">{text value=$group.title cut=18}</label><br />
							{/foreach}
						</td>
					</tr>{/if}
				</table><br />
				{/foreach}
				
				<input type="submit" class="primary" value=" {lng p="save"} " />
			</form>
		</td>
	</tr>
	{/if}
</table>