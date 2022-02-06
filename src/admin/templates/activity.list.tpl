<form action="activity.php?sid={$sid}" method="post" onsubmit="spin(this)" name="f1">
<input type="hidden" name="page" id="page" value="{$pageNo}" />
<input type="hidden" name="sortBy" id="sortBy" value="{$sortBy}" />
<input type="hidden" name="sortOrder" id="sortOrder" value="{$sortOrder}" />
<input type="hidden" name="singleAction" id="singleAction" value="" />
<input type="hidden" name="singleID" id="singleID" value="" />

<fieldset>
	<legend>{lng p="users"}</legend>

	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'user_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th><a href="javascript:updateSort('id');">{lng p="id"}
				{if $sortBy=='id'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th><a href="javascript:updateSort('email');">{lng p="email"}
				{if $sortBy=='email'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="80"><a href="javascript:updateSort('mailspace_used');">{lng p="email"}
				{if $sortBy=='mailspace_used'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="80"><a href="javascript:updateSort('diskspace_used');">{lng p="webdisk"}
				{if $sortBy=='diskspace_used'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="80"><a href="javascript:updateSort('traffic');">{lng p="wdtrafficshort"}
				{if $sortBy=='traffic'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="80"><a href="javascript:updateSort('received_mails');">{lng p="receivedmails"}
				{if $sortBy=='received_mails'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="80"><a href="javascript:updateSort('sent_mails');">{lng p="sentmails"}
				{if $sortBy=='sent_mails'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="110">&nbsp;</th>
		</tr>

		{foreach from=$users item=user}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/user_{$user.statusImg}.png" border="0" width="16" height="16" alt="" /></td>
			<td align="center"><input type="checkbox" name="user_{$user.id}" /></td>
			<td>{$user.id}</td>
			<td><a href="users.php?do=edit&id={$user.id}&sid={$sid}">{email value=$user.email cut=30}</a></td>
			<td>{progressBar value=$user.mailspace_used max=$user.mailspace_max width=75}</td>
			<td>{progressBar value=$user.diskspace_used max=$user.diskspace_max width=75}</td>
			<td>{progressBar value=$user.traffic max=$user.traffic_max width=75}</td>
			<td>{$user.received_mails}</td>
			<td>{$user.sent_mails}</td>
			<td>
				<a href="users.php?do=edit&id={$user.id}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="javascript:singleAction('{if $user.gesperrt=='no'}lock{elseif $user.gesperrt=='yes'}unlock{elseif $user.gesperrt=='locked'}activate{elseif $user.gesperrt=='delete'}recover{/if}', '{$user.id}');"><img src="{$tpldir}images/{if $user.gesperrt=='no'}lock{elseif $user.gesperrt=='yes'}unlock{elseif $user.gesperrt=='locked'}unlock{elseif $user.gesperrt=='delete'}recover{/if}.png" border="0" alt="{if $user.gesperrt=='no'}{lng p="lock"}{elseif $user.gesperrt=='yes'}{lng p="unlock"}{elseif $user.gesperrt=='locked'}{lng p="activate"}{elseif $user.gesperrt=='delete'}{lng p="recover"}{/if}" width="16" height="16" /></a>
				<a href="javascript:singleAction('delete', '{$user.id}');"><img src="{$tpldir}images/{if $user.gesperrt=='delete'}delete{else}trash{/if}.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
				<!--<a href="javascript:singleAction('mail', '{$user.id}');"><img src="{$tpldir}images/ico_sendmail.png" border="0" alt="{lng p="sendmail"}" width="16" height="16" /></a>-->
				<a href="users.php?do=login&id={$user.id}&sid={$sid}" target="_blank" onclick="return confirm('{lng p="loginwarning"}');"><img src="{$tpldir}images/login.png" border="0" alt="{lng p="login"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}

		<tr>
			<td class="footer" colspan="10">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" class="smallInput">
						<option value="-">------------</option>

						<optgroup label="{lng p="actions"}">
							<option value="delete">{lng p="delete"}</option>
							<option value="lock">{lng p="lock"}</option>
							<option value="unlock">{lng p="unlock"}</option>
							<option value="restore">{lng p="restore"}</option>
							<!--<option value="mail">{lng p="sendmail"}</option>-->
						</optgroup>

						<optgroup label="{lng p="move"}">
						{foreach from=$groups item=group key=groupID}
							<option value="moveto_{$groupID}">{lng p="moveto"} &quot;{text value=$group.title cut=25}&quot;</option>
						{/foreach}
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
