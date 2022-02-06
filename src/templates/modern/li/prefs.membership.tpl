<div id="contentHeader">
	<div class="left">
		<i class="fa fa-id-card-o" aria-hidden="true"></i>
		{lng p="membership"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form action="prefs.php?action=membership&do=changePW&sid={$sid}" method="post">
<h2>{lng p="changepw"}</h2>
{if $errorStep}
<div class="note">
	{$errorInfo}
</div>
<br />
{/if}
<table class="listTable">
	<tr>
		<th class="listTableHead" colspan="2"> {lng p="changepw"}</th>
	</tr>
	<tr>
		<td class="listTableLeft">{lng p="password"}:</td>
		<td class="listTableRight">
			<input type="password" name="pass1" value="" size="35" />
		</td>
	</tr>
	<tr>
		<td class="listTableLeft">{lng p="repeat"}:</td>
		<td class="listTableRight">
			<input type="password" name="pass2" value="" size="35" />
		</td>
	</tr>
	<tr>
		<td class="listTableLeft">&nbsp;</td>
		<td class="listTableRight">
			<input type="submit" class="primary" value=" {lng p="save"} " />
			<input type="reset" value=" {lng p="reset"} " />
		</td>
	</tr>
</table>
</form>

<h2>{lng p="accbalance"}</h2>
<table class="listTable">
	<tr>
		<th class="listTableHead" colspan="2"> {lng p="accbalance"}</th>
	</tr>
	<tr>
		<td class="listTableLeft">{lng p="accbalance"}:</td>
		<td class="listTableRight">
			{$accBalance} {lng p="credits"}
		</td>
	</tr>
	<tr>
		<td class="listTableLeft">&nbsp;</td>
		<td class="listTableRight">
			{if $allowCharge}<input type="button" class="primary" value=" {lng p="charge"} " onclick="document.location.href='prefs.php?action=membership&do=chargeAccount&sid={$sid}';" />{/if}
			<input type="button" value="{lng p="statement"}" onclick="showStatement()" />
		</td>
	</tr>
</table>

{if $workgroups}
<h2>{lng p="wgmembership"}</h2>
<table class="listTable">
	<tr>
		<th class="listTableHead" width="65%">
			{lng p="workgroup"}
			<i class="fa fa-arrow-up" aria-hidden="true"></i>
		</th>
		<th class="listTableHead">{lng p="email"}</th>
	</tr>
	
	{foreach from=$workgroups item=workgroup}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="listTableTDActive">&nbsp;<a href="javascript:toggleGroup({$workgroup.id});"><img id="groupImage_{$workgroup.id}" src="{$tpldir}images/expand.png" width="11" height="11" border="0" alt="" align="absmiddle" /></a>&nbsp;<i class="fa fa-users" aria-hidden="true"></i> {text value=$workgroup.title} ({$workgroup.memberCount})</td>
		<td class="{$class}">&nbsp;<a href="email.compose.php?to={$workgroup.email}&sid={$sid}">{text value=$workgroup.email}</a></td>
	</tr>
	
	<!-- members -->
	<tbody id="group_{$workgroup.id}" class="wgTableTB" style="display:none;">
	{foreach from=$workgroup.members item=member}
		<tr>
			<td class="wgTableMemberTD"><i class="fa fa-user-o" aria-hidden="true"></i> {text value=$member.nachname}, {text value=$member.vorname}</td>
			<td class="wgTableTD">&nbsp;<a href="email.compose.php?to={$member.email}&sid={$sid}">{$member.email}</a></td>
		</tr>
	{/foreach}
	</tbody>
	{/foreach}
</table>
{/if}

{if $regDate||$allowCancel}
<h2>{lng p="membership"}</h2>
<table class="listTable">
	<tr>
		<th class="listTableHead" colspan="2"> {lng p="membership"}</th>
	</tr>
	{if $regDate}
	<tr>
		<td class="listTableLeft">{lng p="membersince"}:</td>
		<td class="listTableRight">
			{date timestamp=$regDate dayonly=true}
		</td>
	</tr>
	{/if}
	{if $allowCancel}
	<tr>
		<td class="listTableLeft">&nbsp;</td>
		<td class="listTableRight">
			<input type="button" value=" {lng p="cancelmembership"} " onclick="document.location.href='prefs.php?action=membership&do=cancelAccount&sid={$sid}';" />
		</td>
	</tr>
	{/if}
</table>
{/if}

</div></div>
