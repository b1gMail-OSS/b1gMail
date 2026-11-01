<!DOCTYPE html>
<html>
<head>
	<title>{if $shareType=='calendar'}{lng p="calendarshare"}{elseif $shareType=='mailbox'}{lng p="sharemailbox"}{elseif $shareType=='mailfolder' || $shareType=='wdfolder'}{lng p="sharefolder"}{elseif $shareType=='webdisk'}{lng p="sharewebdisk"}{elseif $shareType=='tasklist'}{lng p="sharetodolist"}{elseif $shareType=='task'}{lng p="sharetask"}{elseif $shareType=='note'}{lng p="sharenote"}{else}{lng p="addressbookshare"}{/if}</title>
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	<link rel="shortcut icon" type="image/png" href="{$selfurl}res/favicon.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	<script src="{sessionurl file='clientlang.php'}"></script>
	<script src="{$selfurl}clientlib/overlay.js"></script>
	<script src="{$tpldir}js/common.js"></script>
	<script src="{$tpldir}js/loggedin.js"></script>
	<script src="{$tpldir}js/dialog.js"></script>
</head>
<body>
	{if !isset($shareRequestAction)}{assign var="shareRequestAction" value="share"}{/if}
	<form method="post" action="{sessionurl file=$shareAction params="action={$shareRequestAction}&id={$shareItem.id}"}">
		{csrffield}
		<input type="hidden" name="id" value="{$shareItem.id}" />
		{if isset($shareKind) && $shareKind != ''}<input type="hidden" name="kind" value="{$shareKind}" />{/if}
		{if $shareType=='calendar'}
		<p><small>{lng p="sharenotifyhint"}</small></p>
		{/if}
		{if $shareType=='mailbox'}
		<p class="warning" style="border:1px solid #d63; padding:8px; background:#fff8e6; color:#663">
			<strong>{lng p="sharemailboxwarn_title"}</strong><br />
			{lng p="sharemailboxwarn_desc"}
		</p>
		{/if}
		{if isset($shareSuccess) && $shareSuccess != ''}
		<p class="ok">{$shareSuccess}</p>
		{/if}
		{if isset($shareError) && $shareError != ''}
		<p class="warning">{$shareError}</p>
		{/if}
		<table class="listTable" width="100%" cellspacing="0" cellpadding="4">
			<tr>
				<th>{lng p="recipient"}</th>
				<th>{lng p="shareaccess"}</th>
				{if $shareType=='calendar'}
				<th align="center">{lng p="sharenotify_push"}</th>
				<th align="center">{lng p="email"}</th>
				{/if}
				<th></th>
			</tr>
			{if $shareType=='calendar'}
			<tr>
				<td>{lng p="sharenotifyme"}</td>
				<td>{lng p="access_write"}</td>
				<td align="center"><input type="checkbox" name="owner_notify_push" value="1"{if $ownerNotifyPush} checked="checked"{/if} /></td>
				<td align="center"><input type="checkbox" name="owner_notify_email" value="1"{if $ownerNotifyEmail} checked="checked"{/if} /></td>
				<td></td>
			</tr>
			{/if}
			{if $shareList}
			{foreach from=$shareList key=shareID item=shareRow}
			<tr>
				<td>{text value=$shareRow.email}</td>
				<td>{if $shareRow.access=='write'}{lng p="access_write"}{else}{lng p="access_read"}{/if}</td>
				{if $shareType=='calendar'}
				<td align="center"><input type="checkbox" name="share_notify_push[{$shareID}]" value="1"{if $shareRow.notify_push} checked="checked"{/if} /></td>
				<td align="center"><input type="checkbox" name="share_notify_email[{$shareID}]" value="1"{if $shareRow.notify_email} checked="checked"{/if} /></td>
				{/if}
				<td align="right">{* F2: POST-only via formaction. *}<button type="submit" formnovalidate="formnovalidate" formaction="{sessionurl file=$shareAction params="action={$shareRequestAction}&id={$shareItem.id}&do=remove&share={$shareID}"}" title="{lng p="delete"}" style="background:none;border:0;padding:0;cursor:pointer"><i class="fa fa-trash-o" aria-hidden="true"></i></button></td>
			</tr>
			{/foreach}
			{/if}
			<tr>
				<td><input type="text" name="email" id="shareEmail" style="width:100%;" /></td>
				<td>
					<select name="access" id="shareAccess">
						<option value="read">{lng p="access_read"}</option>
						<option value="write">{lng p="access_write"}</option>
					</select>
				</td>
				{if $shareType=='calendar'}
				<td align="center"><input type="checkbox" name="notify_push" value="1" /></td>
				<td align="center"><input type="checkbox" name="notify_email" value="1" /></td>
				{/if}
				<td align="right"><button type="submit" name="do" value="add" title="{lng p="shareinvite"}"><i class="fa fa-user-plus" aria-hidden="true"></i></button></td>
			</tr>
		</table>
		<p align="right">
			<small style="float:left;text-align:left;max-width:60%;">{lng p="sharewithhint"}</small>
			<input type="button" onclick="parent.hideOverlay()" value="{lng p="close"}" />
			{if $shareType=='calendar'}
			<button type="submit" name="do" value="saveNotify">{lng p="save"}</button>
			{/if}
		</p>
	</form>
</body>
</html>
