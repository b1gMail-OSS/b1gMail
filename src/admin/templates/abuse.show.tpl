<fieldset>
	<legend>{lng p="user"}: {email value=$userRow.email}</legend>

	<form action="abuse.php?do=show&userid={$userID}&sid={$sid}" method="post">
	<table width="100%">
		<tr>
			<td class="td1">{lng p="status"}:</td>
			<td class="td2"><img src="{$tpldir}images/user_{$userStatusImg}.png" border="0" width="16" height="16" alt="" align="absmiddle" />
				{if $userRow.gesperrt=='no'}{lng p="active"}
				{elseif $userRow.gesperrt=='yes'}{lng p="locked"}
				{elseif $userRow.gesperrt=='locked'}{lng p="notactivated"}
				{elseif $userRow.gesperrt=='delete'}{lng p="deleted"}{/if}
			</td>
			<td class="td1">{lng p="group"}:</td>
			<td class="td2"><img src="{$tpldir}images/ico_group.png" border="0" width="16" height="16" alt="" align="absmiddle" />
							<a href="groups.php?do=edit&id={$groupRow.id}&sid={$sid}">{text value=$groupRow.titel}</a></td>
		</tr>
		<tr>
			<td class="td1">{lng p="email"}:</td>
			<td class="td2">
				<small>{$emailMails} {lng p="emails"}, {$emailFolders} {lng p="folders"}</small>
				{progressBar value=$userRow.mailspace_used max=$groupRow.storage width=200}
				<small>{size bytes=$userRow.mailspace_used} / {size bytes=$groupRow.storage} {lng p="used"}</small>
			</td>

			<td class="td1">{lng p="receivedmails"}:<br />{lng p="sentmails"}:</td>
			<td class="td2">{$userRow.received_mails}<br />{$userRow.sent_mails}</td>
		</tr>
		<tr>
			<td class="td1" width="120">{lng p="webdisk"}:</td>
			<td class="td2">
				<small>{$diskFiles} {lng p="files"}, {$diskFolders} {lng p="folders"}</small>
				{progressBar value=$userRow.diskspace_used max=$groupRow.webdisk width=200}
				<small>{size bytes=$userRow.diskspace_used} / {size bytes=$groupRow.webdisk} {lng p="used"}</small>
			</td>
			<td class="td1" width="120">{lng p="wdtraffic"}:</td>
			<td class="td2">
				{if $groupRow.traffic>0}{progressBar value=$userRow.traffic_down+$userRow.traffic_up max=$groupRow.traffic width=200}{/if}
				<small>{size bytes=$userRow.traffic_down+$userRow.traffic_up}{if $groupRow.traffic>0} / {size bytes=$groupRow.traffic}{/if} {lng p="used2"}</small>
			</td>
		</tr>

		{if $groupRow.sms_monat>0}
		<tr>
			<td class="td1">{lng p="monthasset"}:</td>
			<td class="td2">
				{progressBar value=$usedMonthSMS max=$groupRow.sms_monat width=200}
				<small>{$usedMonthSMS} / {$groupRow.sms_monat} {lng p="credits"} {lng p="used2"}</small>
			</td>
			<td colspan="2"></td>
		</tr>
		{/if}

		<tr>
			<td class="td1">{lng p="lastlogin"}:</td>
			<td class="td2">{date timestamp=$userRow.lastlogin nice=true nozero=true}</td>
			<td class="td1">{lng p="ip"}:</td>
			<td class="td2">{text value=$userRow.ip}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="regdate"}:</td>
			<td class="td2">{date timestamp=$userRow.reg_date nice=true nozero=true}</td>
			<td class="td1">{lng p="ip"}:</td>
			<td class="td2">{text value=$userRow.reg_ip}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="lastpop3"}:</td>
			<td class="td2">{date timestamp=$userRow.last_pop3 nice=true nozero=true}</td>
			<td class="td1">{lng p="lastsmtp"}:</td>
			<td class="td2">{date timestamp=$userRow.last_smtp nice=true nozero=true}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="lastimap"}:</td>
			<td class="td2">{date timestamp=$userRow.last_imap nice=true nozero=true}</td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<td class="td1">{lng p="notes"}:</td>
			<td class="td2" colspan="3">
				<textarea style="width:100%;height:80px;" name="notes">{text value=$userRow.notes allowEmpty=true}</textarea>
			</td>
		</tr>
	</table>

	<div align="center" style="margin-top:0.5em;">
		<button class="button" type="submit" name="save"><img src="{$tpldir}images/ico_save.png" align="absmiddle" border="0" alt="" width="16" height="16" />
			{lng p="save"}</button>
		<button class="button" onclick="document.location.href='users.php?do=edit&id={$userID}&sid={$sid}';return(false);"><img src="{$tpldir}images/user_action.png" align="absmiddle" border="0" alt="" width="16" height="16" />
				{lng p="profile"}</button>
		<button class="button" onclick="window.singleAction('{if $userRow.gesperrt=='no'}lock{elseif $userRow.gesperrt=='yes'}unlock{elseif $userRow.gesperrt=='locked'}activate{elseif $userRow.gesperrt=='delete'}recover{/if}User', '{$userRow.id}');return(false);"><img src="{$tpldir}images/{if $userRow.gesperrt=='no'}lock{elseif $userRow.gesperrt=='yes'}unlock{elseif $userRow.gesperrt=='locked'}unlock{elseif $userRow.gesperrt=='delete'}recover{/if}.png" align="absmiddle" border="0" alt="" width="16" height="16" />
				{if $userRow.gesperrt=='no'}{lng p="lock"}{elseif $userRow.gesperrt=='yes'}{lng p="unlock"}{elseif $userRow.gesperrt=='locked'}{lng p="activate"}{elseif $userRow.gesperrt=='delete'}{lng p="recover"}{/if}</button>
		<button class="button" onclick="window.singleAction('deleteUser', '{$userRow.id}');return(false);"><img src="{$tpldir}images/{if $userRow.gesperrt=='delete'}delete{else}trash{/if}.png" align="absmiddle" border="0" alt="" width="16" height="16" />
				{lng p="delete"}</button>
		<button class="button" onclick="if(confirm('{lng p="loginwarning"}')) window.open('users.php?do=login&id={$userID}&sid={$sid}');return(false);"><img src="{$tpldir}images/login.png" align="absmiddle" border="0" alt="" width="16" height="16" />
				{lng p="login"}</button>
	</div>
	</form>
</fieldset>

{if $sendStats}
<fieldset class="collapsed">
	<legend><a href="javascript:;" onclick="toggleFieldset(this)">{lng p="sendstats"}</a> ({lng p="last7d"})</legend>
	<div class="content">
		<table class="list" id="sendStatsTable">
			<tr>
				<th>{lng p="timeframe"}</th>
				<th width="20%">{lng p="mails"}</th>
				<th width="20%">&sum; {lng p="recipients"}</th>
			</tr>
			{foreach from=$sendStats item=stat}
			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}" data-time-stamp="{$stat.timeStamp}">
				<td><i class="fa fa-chevron-right" style="font-size:10px;" id="sendStats_{$stat.timeStamp}_chevron"></i>
					<a href="javascript:void(0);" onclick="expandStatsDay('send', {$userID}, {$stat.timeStamp});">
						{date timestamp=$stat.timeStamp dayonly=true}
					</a></td>
				<td>{$stat.mails}</td>
				<td>{$stat.recipients}</td>
			</tr>
			<tbody id="sendStats_{$stat.timeStamp}" style="display:none;"></tbody>
			{/foreach}
		</table>
	</div>
</fieldset>
{/if}

{if $recvStats}
<fieldset class="collapsed">
	<legend><a href="javascript:;" onclick="toggleFieldset(this)">{lng p="recvstats"}</a> ({lng p="last7d"})</legend>
	<div class="content">
		<table class="list" id="sendStatsTable">
			<tr>
				<th>{lng p="timeframe"}</th>
				<th width="20%">{lng p="mails"}</th>
				<th width="20%">&sum; {lng p="size"}</th>
			</tr>
			{foreach from=$recvStats item=stat}
			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}" data-time-stamp="{$stat.timeStamp}">
				<td><i class="fa fa-chevron-right" style="font-size:10px;" id="recvStats_{$stat.timeStamp}_chevron"></i>
					<a href="javascript:void(0);" onclick="expandStatsDay('recv', {$userID}, {$stat.timeStamp});">
						{date timestamp=$stat.timeStamp dayonly=true}
					</a></td>
				<td>{$stat.mails}</td>
				<td>{size bytes=$stat.size}</td>
			</tr>
			<tbody id="recvStats_{$stat.timeStamp}" style="display:none;"></tbody>
			{/foreach}
		</table>
	</div>
</fieldset>
{/if}

<form action="abuse.php?do=show&userid={$userID}&sid={$sid}" method="post" onsubmit="spin(this)" name="f1">
<input type="hidden" name="page" id="page" value="{$pageNo}" />
<input type="hidden" name="singleAction" id="singleAction" value="" />
<input type="hidden" name="singleID" id="singleID" value="" />

<fieldset>
	<legend>{lng p="points"}</legend>

	<table class="list">
		<tr>
			<th width="22">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'entries[]');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th width="80">{lng p="points"}</th>
			<th>{lng p="type"}</th>
			<th>{lng p="comment"}</th>
			<th width="150">{lng p="date"}
				<img src="{$tpldir}images/sort_desc.png" border="0" alt="" width="7" height="6" align="absmiddle" /></th>
			<th width="80">{lng p="sum"}</th>
			<th width="32">&nbsp;</th>
		</tr>

		{foreach from=$points item=entry}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}"{if $entry.expired} style="text-decoration:line-through;"{/if}>
			<td align="center"><img src="{$tpldir}images/indicator_{$entry.indicator}.png" border="0" width="16" height="16" alt="" /></td>
			<td align="center"><input type="checkbox" name="entries[]" value="{$entry.entryid}" /></td>
			<td>{$entry.points}</td>
			<td>{$entry.typeText}</td>
			<td>{text value=$entry.comment}</td>
			<td>{date timestamp=$entry.date nice=true}</td>
			<td>{$entry.sum}</td>
			<td>
				<a href="javascript:singleAction('delete', '{$entry.entryid}');" title="{lng p="delete"}"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}

		<tr>
			<td class="footer" colspan="8">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" class="smallInput">
						<option value="-">------------</option>

						<optgroup label="{lng p="actions"}">
							<option value="delete">{lng p="delete"}</option>
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
</fieldset>
</form>
