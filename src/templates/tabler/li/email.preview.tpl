<div class="previewMailHeader bm-mail-read-header">
	<div class="bm-mail-read-header-top">
		<div class="bm-mail-read-header-main">
			<button type="button" class="btn btn-sm btn-ghost-secondary bm-mail-meta-toggle" onclick="advancedOptions('mailHeaders', 'right', 'bottom', '{$tpldir}');" aria-expanded="{if $narrow}true{else}false{/if}" aria-controls="advanced_mailHeaders_body">
				<i class="ti icon ti-chevron-{if $narrow}down{else}right{/if}" id="advanced_mailHeaders_arrow" aria-hidden="true"></i>
			</button>
			<h1 class="bm-mail-subject">{text value=$subject}</h1>
		</div>
		<div class="bm-mail-read-header-actions">
			<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="currentID={$mailID};showMailMenu(event,this);">
				<i class="ti ti-dots-vertical icon" aria-hidden="true"></i>
				{lng p="actions"}
			</button>
		</div>
	</div>

	<div class="bm-mail-meta-compact" id="advanced_mailHeaders_body2" style="display:{if $narrow}none{/if};">
		<span class="bm-mail-meta-item">
			<span class="bm-mail-meta-label">{lng p="from2"}</span>
			{addressList list=$fromAddresses short=true}
		</span>
		<span class="bm-mail-meta-item">
			<span class="bm-mail-meta-label">{lng p="to2"}</span>
			{addressList list=$toAddresses short=true}
		</span>
		<span class="bm-mail-meta-item bm-mail-meta-date">
			<i class="ti ti-clock icon" aria-hidden="true"></i>
			{date timestamp=$date nice=true}
		</span>
		{if $attachments}
		<button type="button" class="btn btn-sm btn-ghost-secondary bm-mail-meta-attach" onclick="advancedOptions('mailHeaders', 'right', 'bottom', '{$tpldir}');" title="{lng p="attachments"}">
			<i class="ti ti-paperclip icon" aria-hidden="true"></i>
			{$attachments|@count}
		</button>
		{/if}
	</div>

	<div class="bm-mail-meta-detail" id="advanced_mailHeaders_body" style="display:{if !$narrow}none{/if};">
		<dl class="bm-mail-meta-list">
			<div class="bm-mail-meta-row">
				<dt>{lng p="from"}</dt>
				<dd>{addressList list=$fromAddresses}</dd>
			</div>
			<div class="bm-mail-meta-row">
				<dt>{lng p="to"}</dt>
				<dd>{addressList list=$toAddresses}</dd>
			</div>
			{if $ccAddresses}
			<div class="bm-mail-meta-row">
				<dt>{lng p="cc"}</dt>
				<dd>{addressList list=$ccAddresses}</dd>
			</div>
			{/if}
			{if $replyToAddresses}
			<div class="bm-mail-meta-row">
				<dt>{lng p="replyto"}</dt>
				<dd>{addressList list=$replyToAddresses}</dd>
			</div>
			{/if}
			{if $priority!=0}
			<div class="bm-mail-meta-row">
				<dt>{lng p="priority"}</dt>
				<dd>
					{if $priority==1}<i class="ti ti-alert-triangle icon text-warning" aria-hidden="true"></i>{/if}
					{lng p="prio_$priority"}
				</dd>
			</div>
			{/if}
			<div class="bm-mail-meta-row">
				<dt>{lng p="date"}</dt>
				<dd>{date timestamp=$date elapsed=true}</dd>
			</div>

			{if $smimeStatus!=0&&!($smimeStatus&1)}
			<div class="bm-mail-meta-row">
				<dt>{lng p="security"}</dt>
				<dd class="bm-mail-meta-security">
					{if $smimeStatus&2}
					<span class="text-danger">
						<img src="{$tpldir}images/li/mailico_signed_bad.png" width="16" height="16" border="0" alt="" align="absmiddle" />
						{lng p="badsigned"}
					</span>
					{/if}
					{if $smimeStatus&4}
					<img src="{$tpldir}images/li/mailico_signed_ok.png" width="16" height="16" border="0" alt="" align="absmiddle" />
					<a href="javascript:void(0);" onclick="showCertificate('{$smimeCertificateHash}');">{lng p="signed"}</a>
					{/if}
					{if $smimeStatus&8}
					<img src="{$tpldir}images/li/mailico_signed_noverify.png" width="16" height="16" border="0" alt="" align="absmiddle" />
					<a href="javascript:void(0);" onclick="showCertificate('{$smimeCertificateHash}');" class="text-warning">{lng p="noverifysigned"}</a>
					{/if}
					{if $smimeStatus&64}
					<img src="{$tpldir}images/li/mailico_encrypted_error.png" width="16" height="16" border="0" alt="" align="absmiddle" />
					<span class="text-danger">{lng p="decryptionfailed"}</span>
					{/if}
					{if $smimeStatus&128}
					<img src="{$tpldir}images/li/mailico_encrypted.png" width="16" height="16" border="0" alt="" align="absmiddle" /> {lng p="encrypted"}
					{/if}
				</dd>
			</div>
			{/if}

			{if $deliveryStatus}
			<div class="bm-mail-meta-row">
				<dt>{lng p="deliverystatus"}</dt>
				<dd>
					{if $deliveryStatus.exception}<i class="ti ti-alert-triangle icon text-warning" aria-hidden="true"></i>
					{elseif $deliveryStatus.allDelivered}<i class="ti ti-check icon text-success" aria-hidden="true"></i>
					{else}<i class="ti ti-refresh icon" aria-hidden="true"></i>{/if}
					<a href="javascript:showDeliveryStatus({$mailID});">{$deliveryStatus.statusText}</a>
				</dd>
			</div>
			{/if}
		</dl>

		{if $notes}
		<div class="bm-mail-notes-section">
			<div class="bm-mail-attachments-label">{lng p="notes"}</div>
			<div class="bm-mail-notes-box">{text value=$notes allowEmpty=true}</div>
		</div>
		{/if}
	</div>
</div>

<div id="bigFormToolbar" class="bm-mail-toolbar">
	<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="mailReply({$mailID},false);">
		<i class="ti ti-arrow-back-up icon" aria-hidden="true"></i>
		{lng p="reply"}
	</button>
	<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="mailReply({$mailID},true);">
		<i class="ti ti-arrows-double-ne-sw icon" aria-hidden="true"></i>
		{lng p="replyall"}
	</button>
	<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="document.location.href='email.compose.php?sid={$sid}&forward={$mailID}';">
		<i class="ti ti-arrow-forward-up icon" aria-hidden="true"></i>
		{lng p="forward"}
	</button>
	<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="printMail({$mailID},'{$sid}');">
		<i class="ti ti-printer icon" aria-hidden="true"></i>
		{lng p="print"}
	</button>
	{if !isset($folderInfo.readonly)}<button type="button" class="btn btn-sm btn-ghost-danger" onclick="{if $folderID==-5}if(confirm('{lng p="realdel"}')) {/if} deleteMail({$mailID});">
		<i class="ti ti-trash icon" aria-hidden="true"></i>
		{lng p="delete"}
	</button>{/if}
	{hook id="email.preview.tpl:afterButtons"}
</div>

<div class="bm-mail-alerts">
{include file="li/email.mailnotes.tpl" preview=true}
</div>

<iframe width="100%" style="height:200px;" id="textArea" name="textArea" src="about:blank" class="mailHTMLText" frameborder="no"></iframe>
<textarea id="textArea_raw" style="display:none;">{text allowEmpty=true value=$text allowDoubleEnc=true}</textarea>

{if $attachments}
<div class="bm-mail-attachments-footer">
	<div class="bm-mail-attachments-label">{lng p="attachments"}</div>
	{include file="li/email.attachments.chips.tpl"}
</div>
{/if}

<form id="quoteForm" action="email.compose.php?sid={$sid}&reply={$mailID}" method="post">
	<input type="hidden" name="text" id="quoteText" value="" />
</form>
