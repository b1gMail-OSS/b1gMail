<form name="f1" method="post" action="{sessionurl file='sms.php' params='action=sendSMS'}" class="bm-compose-form" onreset="return askReset();">
	{csrffield}

<div id="contentHeader" class="contentHeader bm-compose-header">
	<div class="left">
		<i class="ti ti-message-plus icon icon-sm" aria-hidden="true"></i>
		{lng p="sendsms"}
	</div>
	<div class="right bm-compose-header-tools">
		<select name="smsType" id="smsType" class="form-select form-select-sm bm-compose-control" onchange="smsTypeChanged()">
		{foreach from=$smsTypes key=typeID item=type}
			<option value="{$typeID}"{if $type.default} selected="selected"{/if}>{text value=$type.title} ({$type.price} {lng p="credits"})</option>
		{/foreach}
		</select>
	</div>
</div>

<div class="bigForm withBottomBar bm-compose-body">
	<div class="bm-compose-fields" id="composeHeader">
		<div class="bm-compose-row" id="ownFromTR">
			<label class="bm-compose-label{if $ownFrom} required{/if}" for="from">{lng p="from"}</label>
			<div class="bm-compose-field">
				{if !$ownFrom}
				<input type="text" class="form-control form-control-sm bm-compose-control" value="{text value=$smsFrom}" readonly="readonly" tabindex="-1" aria-readonly="true" />
				{else}
				<div class="bm-sms-mobile-nr">{mobileNr name="from" value=$smsFrom size="100%"}</div>
				{/if}
			</div>
		</div>

		<div class="bm-compose-row">
			<label class="bm-compose-label required" for="to">{lng p="to"}</label>
			<div class="bm-compose-field" id="addrDiv_to">
				<div class="input-group input-group-sm">
					{mobileNr name="to" size="100%" value=$smsTo}
					<button type="button" class="btn btn-outline-secondary" onclick="openCellphoneAddressbook('{$sid}')">
						<i class="ti ti-address-book icon" aria-hidden="true"></i>
						<span class="d-none d-md-inline">{lng p="fromaddr"}</span>
					</button>
				</div>
			</div>
		</div>
	</div>

	<div id="composeText" class="bm-compose-editor">
		<textarea class="composeTextarea" name="smsText" id="smsText" style="width:100%;height:100%;" onkeyup="updateMaxChars(this)"></textarea>
	</div>
</div>

{if isset($captchaInfo)}
<div id="safecodeFooter" class="bm-compose-captcha"{if $captchaInfo.heightHint} style="min-height:{$captchaInfo.heightHint};"{/if}>
	<div class="d-flex flex-wrap align-items-center justify-content-center gap-3">
		<label class="form-label mb-0" for="safecode">{lng p="safecode"}</label>
		<div id="captchaContainer">{$captchaHTML}</div>
		{if !$captchaInfo.hasOwnInput}
		<input type="text" class="form-control form-control-sm bm-compose-safecode-input" name="safecode" id="safecode" />
		{/if}
		{if $captchaInfo.showNotReadable}<small class="text-secondary">{lng p="notreadable"}</small>{/if}
	</div>
</div>
{/if}

<div id="contentFooter" class="bm-compose-footer">
	<div class="left d-flex flex-wrap align-items-center gap-2">
		<div class="alert alert-warning mb-0 py-1 px-2 small" id="priceWarning" style="display:none;" role="alert"></div>
	</div>
	<div class="center bm-sms-chars-footer text-secondary small">
		<div class="d-inline-flex flex-wrap align-items-center justify-content-center gap-2">
			<span class="text-nowrap">{lng p="chars"}</span>
			<div class="bm-sms-chars-bar">{progressBar value=0 max=1 width=100 name="charCountBar"}</div>
			<span class="text-nowrap"><span id="charCount">0</span> / <span id="maxChars">0</span></span>
		</div>
	</div>
	<div class="right d-flex flex-wrap align-items-center gap-2">
		<button type="reset" class="btn btn-sm btn-ghost-secondary">{lng p="reset"}</button>
		<button type="button" class="btn btn-sm btn-primary" id="sendButton" onclick="if(!checkSMSComposeForm()) return(false); {if $captchaInfo&&!$captchaInfo.hasOwnAJAXCheck}checkSafeCode('{$captchaInfo.failAction}');{else}document.forms.f1.submit();{/if}">
			<i class="ti ti-send icon" aria-hidden="true"></i>
			{lng p="sendsms2"}
		</button>
	</div>
</div>

</form>

<script type="text/javascript">
<!--
	var accountBalance = {$accBalance},
		smsTypePrices = [],
		smsTypeFlags = [],
		smsTypeLengths = [];
	{foreach from=$smsTypes item=type key=typeID}
	smsTypePrices[{$typeID}] = {$type.price};
	smsTypeFlags[{$typeID}] = {$type.flags};
	smsTypeLengths[{$typeID}] = {$type.maxlength};
	{/foreach}
	registerLoadAction(smsTypeChanged);
	registerLoadAction(smsComposeSizer);
//-->
</script>
