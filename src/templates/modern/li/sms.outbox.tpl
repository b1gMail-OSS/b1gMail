<div id="contentHeader">
	<div class="left">
		<i class="fa fa-commenting" aria-hidden="true"></i> {lng p="smsoutbox"}
	</div>
</div>

<form name="f1" method="post" action="sms.php?action=outbox&do=action&sid={$sid}">

<div class="scrollContainer withBottomBar">
	
<table class="bigTable">
	<tr>
		<th width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'sms');" /></th>
		<th>
			<a href="sms.php?action=outbox&sid={$sid}&sort=from&order={$sortOrderInv}">{lng p="from"}</a>
			{if $sortColumn=='from'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
		</th>
		<th>
			<a href="sms.php?action=outbox&sid={$sid}&sort=to&order={$sortOrderInv}">{lng p="to"}</a>
			{if $sortColumn=='to'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
		</th>
		<th width="160">
			<a href="sms.php?action=outbox&sid={$sid}&sort=date&order={$sortOrderInv}">{lng p="date"}</a>
			{if $sortColumn=='date'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
		</th>
		<th width="35">&nbsp;</th>
	</tr>
	
	{if $outbox}
	<tbody class="listTBody">
	{foreach from=$outbox key=smsID item=sms}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	{assign value=$task.priority var=prio}
	{assign value=$task.akt_status var=status}
	<tr>
		<td class="{$class}" nowrap="nowrap"><input type="checkbox" id="sms_{$sms.id}" name="sms_{$sms.id}" /></td>
		<td class="{if $sortColumn=='from'}listTableTDActive{else}{$class}{/if}">&nbsp;<a href="javascript:toggleGroup({$sms.id});"><img id="groupImage_{$sms.id}" src="{$tpldir}images/{if $smarty.request.show==$sms.id}contract{else}expand{/if}.png" width="11" height="11" border="0" alt="" align="absmiddle" /></a>&nbsp;{text value=$sms.from}</td>
		<td class="{if $sortColumn=='to'}listTableTDActive{else}{$class}{/if}">&nbsp;<a href="sms.php?to={text value=$sms.to}&sid={$sid}">{text value=$sms.to}</a></td>
		<td class="{if $sortColumn=='date'}listTableTDActive{else}{$class}{/if}">&nbsp;{date timestamp=$sms.date nice=true}</td>
		<td class="{$class}">
			<a onclick="return confirm('{lng p="realdel"}');" href="sms.php?action=outbox&do=delete&id={$sms.id}&sid={$sid}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		</td>
	</tr>
	<tbody id="group_{$sms.id}" style="display:{if $smarty.request.show!=$sms.id}none{/if}">
	<tr>
		<td colspan="5" class="listTableTDText">{text value=$sms.text}</td>
	</tr>
	</tbody>
	{/foreach}
	</tbody>
	{/if}
	
	<tr>
		<td colspan="5" class="listTableFoot">
			<table cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td align="left">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

</div>

<div id="contentFooter">
	<div class="left">
		<select class="smallInput" name="do2">
			<option value="-">------ {lng p="selaction"} ------</option>
			<option value="delete">{lng p="delete"}</option>
		</select>
		<input class="smallInput" type="submit" value="{lng p="ok"}" />
	</div>
</div>

</form>
