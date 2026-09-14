{capture assign="dialogTitleText"}{if $shareType=='calendar'}{lng p="calendarshare"}{elseif $shareType=='mailbox'}{lng p="sharemailbox"}{elseif $shareType=='mailfolder' || $shareType=='wdfolder'}{lng p="sharefolder"}{elseif $shareType=='webdisk'}{lng p="sharewebdisk"}{elseif $shareType=='tasklist'}{lng p="sharetodolist"}{elseif $shareType=='task'}{lng p="sharetask"}{elseif $shareType=='note'}{lng p="sharenote"}{else}{lng p="addressbookshare"}{/if}{/capture}
{if !isset($shareRequestAction)}{assign var="shareRequestAction" value="share"}{/if}
{include file="li/dialog.head.tpl" dialogTitle=$dialogTitleText dialogBodyClass="bm-dialog-organizer-collection bm-dialog-organizer-share" dialogOnLoad="documentLoader()"}

<div class="bm-organizer-share-outer">
<form method="post" class="bm-organizer-collection-form bm-organizer-share-wrap" action="{sessionurl file=$shareAction params="action={$shareRequestAction}&id={$shareItem.id}"}">
	{csrffield}
	<input type="hidden" name="id" value="{$shareItem.id}" />
	{if isset($shareKind) && $shareKind != ''}<input type="hidden" name="kind" value="{$shareKind}" />{/if}
	{if $shareType=='calendar' || $shareType=='mailbox' || (isset($shareSuccess) && $shareSuccess != '') || (isset($shareError) && $shareError != '')}
	<div class="modal-body">
		{if $shareType=='calendar'}
		<p class="text-secondary small mb-0">{lng p="sharenotifyhint"}</p>
		{/if}

		{if $shareType=='mailbox'}
		<div class="alert alert-warning mb-0" role="alert">
			<div class="d-flex">
				<div>
					<svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01"/><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/></svg>
				</div>
				<div>
					<h4 class="alert-heading">{lng p="sharemailboxwarn_title"}</h4>
					<div class="alert-description">{lng p="sharemailboxwarn_desc"}</div>
				</div>
			</div>
		</div>
		{/if}

		{if isset($shareSuccess) && $shareSuccess != ''}
		<div class="alert alert-success py-2 mb-0{if $shareType=='calendar' || $shareType=='mailbox'} mt-3{/if}">{$shareSuccess}</div>
		{/if}
		{if isset($shareError) && $shareError != ''}
		<div class="alert alert-danger py-2 mb-0{if $shareType=='calendar' || $shareType=='mailbox' || (isset($shareSuccess) && $shareSuccess != '')} mt-3{/if}">{$shareError}</div>
		{/if}
	</div>
	{/if}
	{if $shareType=='wdfolder'}
	<div class="modal-body pt-3 pb-2">
		<div class="d-flex align-items-center gap-2 mb-2">
			<i class="ti ti-users icon" aria-hidden="true"></i>
			<span class="fw-semibold text-secondary text-uppercase small">{lng p="shareinternally"|default:"Interne Freigabe"}</span>
		</div>
		<div class="text-secondary small">{lng p="sharewithhint"}</div>
	</div>
	{/if}
	<div class="bm-organizer-share-table-wrap">
		<table class="table table-vcenter table-sm mb-0 bm-organizer-share-table">
			<thead>
				<tr>
					<th class="bm-organizer-share-col-email">{lng p="recipient"}</th>
					<th class="bm-organizer-share-col-access">{lng p="shareaccess"}</th>
					{if $shareType=='calendar'}
					<th class="bm-organizer-share-col-flag">{lng p="sharenotify_push"}</th>
					<th class="bm-organizer-share-col-flag">{lng p="email"}</th>
					{/if}
					<th class="bm-organizer-share-col-action"></th>
				</tr>
			</thead>
			<tbody>
				{if $shareType=='calendar'}
				<tr>
					<td class="bm-organizer-share-col-email">{lng p="sharenotifyme"}</td>
					<td class="bm-organizer-share-col-access">{lng p="access_write"}</td>
					<td class="bm-organizer-share-col-flag">
						{include file="li/form-check.tpl" compact=true wrapClass="mb-0 bm-organizer-share-check" id="owner_notify_push" name="owner_notify_push" value="1" checked=$ownerNotifyPush}
					</td>
					<td class="bm-organizer-share-col-flag">
						{include file="li/form-check.tpl" compact=true wrapClass="mb-0 bm-organizer-share-check" id="owner_notify_email" name="owner_notify_email" value="1" checked=$ownerNotifyEmail}
					</td>
					<td class="bm-organizer-share-col-action"></td>
				</tr>
				{/if}
				{if $shareList}
				{foreach from=$shareList key=shareID item=shareRow}
				<tr>
					<td class="bm-organizer-share-col-email">{text value=$shareRow.email}</td>
					<td class="bm-organizer-share-col-access">{if $shareRow.access=='write'}{lng p="access_write"}{else}{lng p="access_read"}{/if}</td>
					{if $shareType=='calendar'}
					<td class="bm-organizer-share-col-flag">
						{include file="li/form-check.tpl" compact=true wrapClass="mb-0 bm-organizer-share-check" id="share_notify_push_{$shareID}" name="share_notify_push[{$shareID}]" value="1" checked=$shareRow.notify_push}
					</td>
					<td class="bm-organizer-share-col-flag">
						{include file="li/form-check.tpl" compact=true wrapClass="mb-0 bm-organizer-share-check" id="share_notify_email_{$shareID}" name="share_notify_email[{$shareID}]" value="1" checked=$shareRow.notify_email}
					</td>
					{/if}
					<td class="bm-organizer-share-col-action">
						{* F2: revoking a share must be a POST — otherwise a
						CSRF <a href> from any third-party page can trigger
						it. `formaction` keeps the click bound to the
						surrounding <form method="post">, `formnovalidate`
						bypasses the required-email invite input. *}
						<button type="submit" formnovalidate="formnovalidate"
							formaction="{sessionurl file=$shareAction params="action={$shareRequestAction}&id={$shareItem.id}&do=remove&share={$shareID}"}"
							class="btn btn-sm btn-ghost-danger btn-icon"
							title="{lng p="delete"}" aria-label="{lng p="delete"}">{include file="li/icon.tpl" faIcon="fa-trash-o"}</button>
					</td>
				</tr>
				{/foreach}
				{/if}
				<tr class="bm-organizer-share-invite-row">
					<td class="bm-organizer-share-col-email">
						<input type="email" class="form-control form-control-sm" name="email" id="shareEmail" placeholder="{lng p="email"}" autocomplete="off" />
					</td>
					<td class="bm-organizer-share-col-access">
						<select class="form-select form-select-sm" name="access" id="shareAccess">
							<option value="read">{lng p="access_read"}</option>
							<option value="write">{lng p="access_write"}</option>
						</select>
					</td>
					{if $shareType=='calendar'}
					<td class="bm-organizer-share-col-flag">
						{include file="li/form-check.tpl" compact=true wrapClass="mb-0 bm-organizer-share-check" id="notify_push" name="notify_push" value="1"}
					</td>
					<td class="bm-organizer-share-col-flag">
						{include file="li/form-check.tpl" compact=true wrapClass="mb-0 bm-organizer-share-check" id="notify_email" name="notify_email" value="1"}
					</td>
					{/if}
					<td class="bm-organizer-share-col-action">
						<button type="submit" name="do" value="add" class="btn btn-sm btn-ghost-primary btn-icon" title="{lng p="shareinvite"}" aria-label="{lng p="shareinvite"}">{include file="li/icon.tpl" faIcon="fa-user-plus"}</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	{if !($shareType=='wdfolder' && !empty($publicShareAvailable))}
	<div class="modal-footer">
		<p class="bm-organizer-share-hint">{lng p="sharewithhint"}</p>
		<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()">{lng p="close"}</button>
		{if $shareType=='calendar'}
		<button type="submit" name="do" value="saveNotify" class="btn btn-primary" formnovalidate="formnovalidate">{lng p="save"}</button>
		{/if}
	</div>
	{/if}
</form>

{if $shareType=='wdfolder' && !empty($publicShareAvailable)}
{include file="li/webdisk.share.public.section.tpl"}
{/if}
</div><!-- /.bm-organizer-share-outer -->

{include file="li/dialog.foot.tpl"}
