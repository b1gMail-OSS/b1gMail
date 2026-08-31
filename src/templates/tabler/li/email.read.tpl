<div id="contentHeader" class="bm-mail-read-page-header">
	<div class="left">
		<a class="btn btn-sm btn-ghost-secondary" href="email.php?folder={$folderID}{$sessionUrlSuffix}">
			<i class="ti ti-{if $folderInfo.type == 'inbox'}inbox{elseif $folderInfo.type == 'outbox'}send{elseif $folderInfo.type == 'drafts'}file-pencil{elseif $folderInfo.type == 'spam'}ban{elseif $folderInfo.type == 'trash'}trash{elseif $folderInfo.type == 'intellifolder'}folder{else}folder{/if} icon" aria-hidden="true"></i>
			{$folderInfo.title}
		</a>
	</div>
	<div class="right">
		{if empty($folderInfo.readonly)}<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="moveMail('{$mailID}');">
			<i class="ti ti-arrows-move icon" aria-hidden="true"></i>
			{lng p="move"}
		</button>{/if}
	</div>
</div>

<div class="scrollContainer withBottomBar{if !empty($smarty.get.openConversationView)}AndLayer{/if} bm-mail-read-scroll" id="mailReadScrollContainer">
	{hook id="email.read.tpl:head"}

	<div class="previewMailHeader bm-mail-read-header" id="mailHeader">
		<div class="bm-mail-read-header-top">
			<h1 class="bm-mail-subject">{text value=$subject}</h1>
		</div>
		<dl class="bm-mail-meta-list bm-mail-meta-list-read">
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

			{hook id="email.read.tpl:metaTable"}
		</dl>
	</div>

	<div id="bigFormToolbar" class="bm-mail-toolbar">

		{if $prevID}<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="document.location.href='email.read.php?id={$prevID}{$sessionUrlSuffix}';">
			<i class="ti ti-chevron-left icon" aria-hidden="true"></i>
		</button>{/if}

		<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="mailReply({$mailID},false);">
			<i class="ti ti-arrow-back-up icon" aria-hidden="true"></i>
			{lng p="reply"}
		</button>

		<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="mailReply({$mailID},true);">
			<i class="ti ti-arrows-double-ne-sw icon" aria-hidden="true"></i>
			{lng p="replyall"}
		</button>

		<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="document.location.href='email.compose.php?forward={$mailID}{$sessionUrlSuffix}';">
			<i class="ti ti-arrow-forward-up icon" aria-hidden="true"></i>
			{lng p="forward"}
		</button>

		<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="document.location.href='email.compose.php?redirect={$mailID}{$sessionUrlSuffix}';">
			<i class="ti ti-mail-forward icon" aria-hidden="true"></i>
			{lng p="redirect"}
		</button>

		<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="document.location.href='email.read.php?action=download&id={$mailID}{$sessionUrlSuffix}';">
			<i class="ti ti-download icon" aria-hidden="true"></i>
			{lng p="download"}
		</button>

		<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="printMail({$mailID});">
			<i class="ti ti-printer icon" aria-hidden="true"></i>
			{lng p="print"}
		</button>

		{if empty($folderInfo.readonly)}<button type="button" class="btn btn-sm btn-ghost-danger" onclick="{if $folderID==-5}if(confirm('{lng p="realdel"}')) {/if} document.location.href='email.php?do=deleteMail&id={$mailID}&folder={$folderID}{$sessionUrlSuffix}';">
			<i class="ti ti-trash icon" aria-hidden="true"></i>
			{lng p="delete"}
		</button>{/if}

		{hook id="email.read.tpl:afterButtons"}

		{if $nextID}<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="document.location.href='email.read.php?id={$nextID}{$sessionUrlSuffix}';">
			<i class="ti ti-chevron-right icon" aria-hidden="true"></i>
		</button>{/if}

	</div>



<div class="pad bm-mail-read-body">

{hook id="email.read.tpl:beforeText"}
{if isset($calendarInviteCard)}
{include file="li/email.calendar.invite.tpl"}
{/if}
<div class="bm-mail-alerts">
{include file="li/email.mailnotes.tpl"}
		{hook id="email.read.tpl:mailNotes"}
	</div>

	<iframe name="mailFrame" width="100%" style="height:200px;" id="textArea" src="about:blank" class="mailHTMLText" frameborder="no"></iframe>
	<textarea id="textArea_raw" style="display:none;">{text allowEmpty=true value=$text allowDoubleEnc=true}</textarea>

	<script>
	<!--
		initEMailTextArea(EBID('textArea_raw').value);
		if(typeof bmMailCalendarInviteInit === 'function')
			bmMailCalendarInviteInit();
	//-->
	</script>

{hook id="email.read.tpl:afterText"}

<div id="afterText">
{hook id="email.read.tpl:foot"}

<form id="quoteForm" action="email.compose.php?reply={$mailID}{$sessionUrlSuffix}" method="post">
	{csrffield}
	<input type="hidden" name="text" id="quoteText" value="" />
</form>

</div>

</div></div>

{if $attachments || isset($vcards)}
<div class="contentBottomLayer bm-mail-bottom-layer" id="bottomLayer_attachments" style="display:none;">
	<div class="bm-mail-bottom-layer-header">
		<span class="bm-mail-bottom-layer-title">
			<i class="ti ti-paperclip icon" aria-hidden="true"></i>
			{lng p="attachments"}{if $attachments} ({$attachments|@count}){/if}
		</span>
		<button type="button" class="btn btn-sm btn-ghost-secondary btn-icon" onclick="readMailHideBottomLayers()" aria-label="{lng p="close"}">
			<i class="ti ti-x icon" aria-hidden="true"></i>
		</button>
	</div>

	{if $attachments}
	<form name="attachmentsForm" method="get" action="email.read.php" class="bm-mail-attachments-panel-form">
	<input type="hidden" name="id" value="{$mailID}" />
	<input type="hidden" name="sid" value="{$sid}" />
	{/if}

	<div class="bm-mail-attachments-panel-body">
		{if $attachments}
		{include file="li/email.attachments.chips.tpl" selectable=true}
		{/if}

		{if isset($vcards)}
		<div class="bm-mail-vcards">
		{foreach from=$vcards item=card key=key}
			<div class="card bm-mail-vcard-card">
				<div class="card-body">
					<div class="row g-2 align-items-start">
						<div class="col-auto">
							<span class="avatar bg-primary-lt text-primary">
								<i class="ti ti-address-book icon" aria-hidden="true"></i>
							</span>
						</div>
						<div class="col">
							<dl class="row bm-mail-vcard-dl mb-0">
								<dt class="col-sm-4">{lng p="firstname"}</dt>
								<dd class="col-sm-8">{if $card.vorname}{text value=$card.vorname}{else}-{/if}</dd>
								<dt class="col-sm-4">{lng p="surname"}</dt>
								<dd class="col-sm-8">{if $card.nachname}{text value=$card.nachname}{else}-{/if}</dd>
								<dt class="col-sm-4">{lng p="company"}</dt>
								<dd class="col-sm-8">{if $card.firma}{text value=$card.firma}{else}-{/if}</dd>
								<dt class="col-sm-4">{lng p="email"}</dt>
								<dd class="col-sm-8">{if $card.email}{email value=$card.email}{else}-{/if}</dd>
							</dl>
						</div>
						<div class="col-12 col-md-auto d-flex flex-wrap gap-1">
							<a class="btn btn-sm btn-primary" href="email.read.php?id={$mailID}&action=importVCF&attachment={$key}{$sessionUrlSuffix}">
								<i class="ti ti-upload icon" aria-hidden="true"></i>
								{lng p="importvcf"}
							</a>
							<a class="btn btn-sm btn-ghost-secondary" href="email.read.php?id={$mailID}&action=downloadAttachment&attachment={$key}{$sessionUrlSuffix}">
								<i class="ti ti-download icon" aria-hidden="true"></i>
								{lng p="download"}
							</a>
						</div>
					</div>
				</div>
			</div>
		{/foreach}
		</div>
		{/if}
	</div>

	{if $attachments}
	<div class="bm-mail-bottom-layer-footer bm-mail-attachments-bulk-footer">
		<label class="form-check mb-0">
			<input class="form-check-input" type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.attachmentsForm, 'att');" />
			<span class="form-check-label">{lng p="all"}</span>
		</label>
		<div class="input-group input-group-sm bm-mail-attachments-bulk-actions">
			<select class="form-select" name="do">
				<option value="-">------ {lng p="selaction"} ------</option>
				<option value="downloadAttachments">{lng p="download"}</option>
				{hook id="email.read.tpl:attachSelect"}
			</select>
			<button class="btn btn-primary" type="submit">{lng p="ok"}</button>
		</div>
	</div>
	</form>
	{/if}
</div>
{/if}

{if $conversationView}
<div class="contentBottomLayer" id="bottomLayer_conversation" style="display:{if empty($smarty.get.openConversationView)}none{/if};">
	<div class="contentHeader">
		<div class="left">
			<i class="fa fa-comment"></i>
			{lng p="conversation"}
		</div>
		<div class="right">
			<button onclick="readMailHideBottomLayers()">
				<i class="fa fa-close"></i>
			</button>
		</div>
	</div>

	<div class="bigForm">
		<iframe id="conversationIFrame" style="width:100%;height:100%;" src="email.read.php?action=showThread&id={$mailID}{$sessionUrlSuffix}" border="0" frameborder="0"></iframe>
	</div>
</div>
{/if}

<div class="contentBottomLayer bm-mail-bottom-layer" id="bottomLayer_props" style="display:none;">
	<div class="bm-mail-bottom-layer-header">
		<span class="bm-mail-bottom-layer-title">
			<i class="ti ti-tags icon" aria-hidden="true"></i>
			{lng p="props"}
		</span>
		<button type="button" class="btn btn-sm btn-ghost-secondary btn-icon" onclick="readMailHideBottomLayers()" aria-label="{lng p="close"}">
			<i class="ti ti-x icon" aria-hidden="true"></i>
		</button>
	</div>

	<form method="post" action="email.read.php?id={$mailID}{$sessionUrlSuffix}" class="bm-mail-props-form">
		{csrffield}
	<input type="hidden" name="do" value="saveMeta" />

	<div class="bm-mail-props-body">
		<div class="row g-3">
			<div class="col-md-4">
				<label class="form-label">{lng p="color"}</label>
				<div class="bm-mail-color-grid" role="radiogroup">
					<label class="bm-mail-color-option">
						<input type="radio" class="form-check-input"{if $color==1} checked="checked"{/if} name="color" value="1" />
						<span class="bm-mail-color-swatch bm-mail-color-swatch-1" title=""></span>
					</label>
					<label class="bm-mail-color-option">
						<input type="radio" class="form-check-input"{if $color==2} checked="checked"{/if} name="color" value="2" />
						<span class="bm-mail-color-swatch bm-mail-color-swatch-2" title=""></span>
					</label>
					<label class="bm-mail-color-option">
						<input type="radio" class="form-check-input"{if $color==3} checked="checked"{/if} name="color" value="3" />
						<span class="bm-mail-color-swatch bm-mail-color-swatch-3" title=""></span>
					</label>
					<label class="bm-mail-color-option">
						<input type="radio" class="form-check-input"{if $color==4} checked="checked"{/if} name="color" value="4" />
						<span class="bm-mail-color-swatch bm-mail-color-swatch-4" title=""></span>
					</label>
					<label class="bm-mail-color-option">
						<input type="radio" class="form-check-input"{if $color==5} checked="checked"{/if} name="color" value="5" />
						<span class="bm-mail-color-swatch bm-mail-color-swatch-5" title=""></span>
					</label>
					<label class="bm-mail-color-option">
						<input type="radio" class="form-check-input"{if $color==6} checked="checked"{/if} name="color" value="6" />
						<span class="bm-mail-color-swatch bm-mail-color-swatch-6" title=""></span>
					</label>
					<label class="bm-mail-color-option bm-mail-color-option-none">
						<input type="radio" class="form-check-input"{if $color==0} checked="checked"{/if} name="color" value="0" />
						<span class="bm-mail-color-none-label">{lng p="none"}</span>
					</label>
				</div>
			</div>
			<div class="col-md-4">
				<label class="form-label">{lng p="flags"}</label>
				<div class="bm-mail-flags-list">
					<label class="form-check">
						<input class="form-check-input" type="checkbox" name="flags[1]" id="flags1"{if $smarty.post.do=='saveMeta'&&($flags&1)} checked="checked"{/if} />
						<span class="form-check-label"><i class="ti ti-mail icon" aria-hidden="true"></i> {lng p="unread"}</span>
					</label>
					<label class="form-check">
						<input class="form-check-input" type="checkbox" name="flags[16]" id="flags16"{if $flags&16} checked="checked"{/if} />
						<span class="form-check-label"><i class="ti ti-flag-filled icon" aria-hidden="true"></i> {lng p="marked"}</span>
					</label>
					<label class="form-check">
						<input class="form-check-input" type="checkbox" name="flags[4096]" id="flags4096"{if $flags&4096} checked="checked"{/if} />
						<span class="form-check-label"><i class="ti ti-circle-check icon" aria-hidden="true"></i> {lng p="done"}</span>
					</label>
				</div>
			</div>
			<div class="col-md-4">
				<label class="form-label" for="mailNotesField">{lng p="notes"}</label>
				<textarea class="form-control" id="mailNotesField" name="notes" rows="4">{text value=$notes allowEmpty=true}</textarea>
			</div>
		</div>
	</div>

	<div class="bm-mail-bottom-layer-footer">
		<button class="btn btn-primary" type="submit"{if isset($folderInfo.readonly)} disabled="disabled"{/if}>
			<i class="ti ti-check icon" aria-hidden="true"></i>
			{lng p="save"}
		</button>
	</div>

	</form>
</div>

<div id="contentFooter" class="contentFooter bm-mail-read-footer">
	<div class="left bm-mail-read-footer-actions">
		{if $attachments || isset($vcards)}
		<button type="button" class="btn btn-sm btn-ghost-secondary" data-bottom-layer="attachments" onclick="readMailShowBottomLayer('attachments');">
			<i class="ti ti-paperclip icon" aria-hidden="true"></i>
			{lng p="attachments"}{if $attachments} <span class="badge bg-primary-lt ms-1">{$attachments|@count}</span>{/if}
		</button>
		{/if}

		{if $conversationView}
		<button type="button" class="btn btn-sm btn-ghost-secondary" data-bottom-layer="conversation" onclick="readMailShowBottomLayer('conversation');">
			<i class="ti ti-messages icon" aria-hidden="true"></i>
			{lng p="conversation"}
		</button>
		{/if}

		<button type="button" class="btn btn-sm btn-ghost-secondary" data-bottom-layer="props" onclick="readMailShowBottomLayer('props');">
			<i class="ti ti-tags icon" aria-hidden="true"></i>
			{lng p="props"}
		</button>
	</div>
</div>

{include file="li/email.addressmenu.tpl"}
{include file="li/webdisk.preview.tpl"}
