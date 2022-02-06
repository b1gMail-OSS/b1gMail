<form action="users.php?do=transactions&id={$user.id}&filter=true&sid={$sid}" method="post" name="f1">
<input type="hidden" name="page" id="page" value="{$pageNo}" />
<input type="hidden" name="sortBy" id="sortBy" value="{$sortBy}" />
<input type="hidden" name="sortOrder" id="sortOrder" value="{$sortOrder}" />
<input type="hidden" name="singleAction" id="singleAction" value="" />
<input type="hidden" name="singleID" id="singleID" value="" />
{if $queryString}<input type="hidden" name="query" id="query" value="{text value=$queryString}" />{/if}

<fieldset>
	<legend>{lng p="transactions"} ({email value=$user.email}, #{$user.id})</legend>

	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection2(document.forms.f1,'transactions[', ']');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th width="64"><a href="javascript:updateSort('transactionid');">{lng p="id"}
				{if $sortBy=='transactionid'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th><a href="javascript:updateSort('description');">{lng p="description"}
				{if $sortBy=='description'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="100"><a href="javascript:updateSort('amount');">{lng p="credits"}
				{if $sortBy=='amount'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="120"><a href="javascript:updateSort('date');">{lng p="date"}
				{if $sortBy=='date'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="64">&nbsp;</th>
		</tr>

		{foreach from=$transactions item=tx}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}" style="{if $tx.status==2}text-decoration:line-through;color:#666;{/if}">
			<td align="center"><img src="{$tpldir}images/transaction.png" border="0" width="16" height="16" alt="" /></td>
			<td align="center"><input type="checkbox" name="transactions[]" value="{$tx.transactionid}" /></td>
			<td>{$tx.transactionid}</td>
			<td>{text value=$tx.description cut=45}</td>
			<td style="text-align:right;">{if $tx.amount<0}<span style="color:red;">{$tx.amount}</span>{else}{$tx.amount}{/if}</td>
			<td>{date nice=true timestamp=$tx.date}</td>
			<td>
				<a href="users.php?do=editTransaction&transactionid={$tx.transactionid}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="javascript:if(confirm('{lng p="realdel"}')) singleAction('delete', '{$tx.transactionid}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}

		<tr>
			<td class="footer" colspan="999">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" id="massAction" class="smallInput">
						<option value="-">------------</option>

						<optgroup label="{lng p="actions"}">
							<option value="delete">{lng p="delete"}</option>
							<option value="cancel">{lng p="cancel"}</option>
							<option value="uncancel">{lng p="uncancel"}</option>
						</optgroup>
					</select>&nbsp;
				</div>
				<div style="float:left;">
					<input type="submit" name="executeMassAction" value=" {lng p="execute"} " class="smallInput" />
				</div>
				<div style="float:right;padding-top:3px;">
					{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"javascript:updatePage(.s);\">.t</a></span> "}&nbsp;
				</div>
			</td>
		</tr>
	</table>

	<p>
		<div style="float:left;padding-top:0.5em;">
			{lng p="assets"}:
			{if $staticBalance<0}<span style="color:red;">{$staticBalance}</span>{else}{$staticBalance}{/if}
			{lng p="credits"}
		</div>

		<div style="float:right">
			{lng p="perpage"}:
			<input type="text" name="perPage" value="{$perPage}" size="5" />
			<input class="button" type="submit" value=" {lng p="apply"} " />
		</div>
	</p>
</fieldset>
</form>

<fieldset>
	<legend>{lng p="addtransaction"}</legend>

	<form action="users.php?do=transactions&add=true&id={$user.id}&sid={$sid}" method="post" onsubmit="spin(this);">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/add32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="description"}:</td>
				<td class="td2"><input type="text" style="width:85%;" required="required" name="description" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="credits"}:</td>
				<td class="td2"><input type="number" min="-999999" max="999999" step="1" name="amount" value="0" size="6" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="status"}:</td>
				<td class="td2"><select name="status">
					<option value="1">{lng p="booked"}</option>
					<option value="2">{lng p="cancelled"}</option>
				</select></td>
			</tr>
			<tr>
				<td>
					&nbsp;
				</td>
				<td align="right">
					<input class="button" type="submit" value=" {lng p="add"} " />
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<p>
	<div style="float:left" class="buttons">
		<input class="button" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='users.php?do=edit&id={$user.id}&sid={$sid}';" />
	</div>
</p>
