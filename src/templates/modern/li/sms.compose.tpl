<div id="contentHeader">
	<div class="left">
		<i class="fa fa-commenting-o" aria-hidden="true"></i>
		{lng p="sendsms"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
	<form name="f1" method="post" action="sms.php?action=sendSMS&sid={$sid}">
		<table class="listTable">
			<tr>
				<th class="listTableHead" colspan="2"> {lng p="sendsms"}</th>
			</tr>
			<tr>
				<td class="listTableLeft">* <label for="type">{lng p="type"}:</label></td>
				<td class="listTableRight">
					<select name="type" id="type" onchange="smsTypeChanged()">
					{foreach from=$smsTypes key=typeID item=type}
						<option value="{$typeID}"{if $type.default} selected="selected"{/if}>{text value=$type.title} ({$type.price} {lng p="credits"})</option>
					{/foreach}
					</select>
				</td>
			</tr>
			<tr id="ownFromTR">
				<td class="listTableLeft">{if $ownFrom}* {/if}{lng p="from"}:</td>
				<td class="listTableRight">
					{if !$ownFrom}{text value=$smsFrom}{else}{mobileNr name="from" value=$smsFrom size="350px"}{/if}
				</td>
			</tr>
			<tr>
				<td class="listTableLeftDescBottomLine">* {lng p="to"}:</td>
				<td class="listTableRightDesc">
					<table cellspacing="0" cellpadding="0">
						<tr>
							<td width="364">
								{mobileNr name="to" size="350px" value=$smsTo}
							</td>
							<td>
								<span id="addrDiv_to">
									<a href="javascript:openCellphoneAddressbook('{$sid}')">
										<i class="fa fa-address-book-o" aria-hidden="true"></i>
										{lng p="fromaddr"}
									</a>
								</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
			<tr>
				<td class="listTableCompose" colspan="2">
					<textarea class="composeTextarea" name="smsText" id="smsText" style="width:100%;height:180px;" onkeyup="updateMaxChars(this)"></textarea>
				</td>
			</tr>
			
			<tr>
				<td class="listTableLeftDescTopLine">{lng p="chars"}:</td>
				<td class="listTableRightDesc">
					<div style="float:left">
						{progressBar value=0 max=1 width=100 name="charCountBar"}
					</div>
					<div style="float:left">
						&nbsp;
					</div>
					<div style="float:left">
						<span id="charCount">0</span> / <span id="maxChars">0</span>
					</div>
				</td>
			</tr>
			
			{if $captchaInfo}
			<tr>
				<td class="listTableLeft">&nbsp;</td>
				<td class="listTableRight" id="captchaContainer">
					{$captchaHTML}
				</td>
			</tr>
			{if !$captchaInfo.hasOwnInput}<tr>
				<td class="listTableLeft"><label for="safecode">{lng p="safecode"}:</label></td>
				<td class="listTableRight">
					<input type="text" size="20" style="text-align:center;width:212px;" name="safecode" id="safecode" />
				</td>
			</tr>{/if}
			{/if}
		
			<tr>
				<td class="listTableLeft">&nbsp;</td>
				<td class="listTableRight">
					<div class="note" id="priceWarning" style="display:none;"></div>
					<input type="button" class="primary" value="{lng p="sendsms2"}" id="sendButton" onclick="if(!checkSMSComposeForm()) return(false); {if $captchaInfo&&!$captchaInfo.hasOwnAJAXCheck}checkSafeCode('{$captchaInfo.failAction}');{else}document.forms.f1.submit();{/if}" />
					<input type="reset" value="{lng p="reset"}" onclick="return askReset();"/>
				</td>
			</tr>
		</table>
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
	//-->
	</script>
</div></div>
