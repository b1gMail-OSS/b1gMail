<div class="bm-prefs-page bm-prefs-page-common">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-settings icon icon-sm" aria-hidden="true"></i>
		{lng p="common"}
	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=common&do=save&sid={$sid}">
<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">
{if $allownewsoptout!='yes'&&$newsletter_optin=='yes'}
<input type="hidden" name="newsletter_optin" value="true" />
{/if}
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="common"}</th>
		</tr>
	
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-cogs" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="common"}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="preferred_language">{lng p="language"}:</label></td>
			<td class="listTableRight">
				<select name="preferred_language" id="preferred_language">
					<option value="">({lng p="auto"})</option>
				{foreach from=$availableLanguages key=lang item=langInfo}
					<option value="{$lang}"{if $preferred_language==$lang} selected="selected"{/if}>{$langInfo.title}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="c_firstday">{lng p="weekstart"}:</label></td>
			<td class="listTableRight">
				<select name="c_firstday" id="c_firstday">
				{foreach from=$fullWeekdays item=dayName key=dayKey}
					<option value="{$dayKey}"{if $dayKey==$c_firstday} selected="selected"{/if}>{$dayName}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="datumsformat">{lng p="dateformat"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="datumsformat" id="datumsformat" value="{if isset($datumsformat)}{text value=$datumsformat}{/if}" style="width:250px;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="hotkeys">{lng p="hotkeys"}?</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="hotkeys" name="hotkeys" checked=$hotkeys labelKey="enable"}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="search_details_default">{lng p="search"}:</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="search_details_default" name="search_details_default" checked=$searchDetailsDefault labelKey="details_default"}
			</td>
		</tr>

		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-bell-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="notifications"}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="notify_sound">{lng p="notify_sound"}?</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="notify_sound" name="notify_sound" checked=$notifySound labelKey="enable"}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="notify_types">{lng p="notify_types"}:</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" inline=true id="notify_email" name="notify_email" checked=$notifyEMail labelKey="notify_email"}
				{include file="li/form-check.tpl" inline=true id="notify_birthday" name="notify_birthday" checked=$notifyBirthday labelKey="notify_birthday"}
			</td>
		</tr>

		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-inbox" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="email"}</td>
		</tr>
		{if $allownewsoptout=='yes'}
		<tr>
			<td class="listTableLeft"><label for="newsletter_optin">{lng p="newsletter"}?</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="newsletter_optin" name="newsletter_optin" checked=($newsletter_optin=='yes') labelKey="subscribe"}
			</td>
		</tr>
		{/if}
		<tr>
			<td class="listTableLeft"><label for="in_refresh">{lng p="inboxrefresh"}:</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" wrapClass="d-inline-flex me-2 mb-0" id="in_refresh_active" name="in_refresh_active" checked=($in_refresh>0)}
				{lng p="every"} <input type="text" name="in_refresh" id="in_refresh" value="{$in_refresh}" size="4" onkeypress="EBID('in_refresh_active').checked=true;" /> {lng p="seconds"}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="preview">{lng p="preview"}?</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="preview" name="preview" checked=($preview=='yes') labelKey="enable"}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="soforthtml">{lng p="plaintextcourier"}:</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="plaintext_courier" name="plaintext_courier" checked=($plaintext_courier=='yes') labelKey="usecourier"}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="soforthtml">{lng p="insthtmlview"}?</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="soforthtml" name="soforthtml" checked=($soforthtml=='yes') labelKey="enable"}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="conversation_view">{lng p="conversationview"}?</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="conversation_view" name="conversation_view" checked=($conversation_view=='yes') labelKey="enable"}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="autosend_dn">{lng p="mailconfirmation"}?</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="autosend_dn" name="autosend_dn" checked=$autosend_dn labelKey="autosend"}
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-reply" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="composeprefs"}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="absendername">{lng p="sendername"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="absendername" id="absendername" value="{if isset($absendername)}{text value=$absendername allowEmpty=true}{/if}" style="width:350px;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="defaultSender">{lng p="defaultsender"}:</label></td>
			<td class="listTableRight">
				<select name="defaultSender" id="defaultSender">
				{foreach from=$possibleSenders item=senderName key=senderKey}
					<option value="{$senderKey}"{if $senderKey==$defaultSender} selected="selected"{/if}>{email value=$senderName}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="re">{lng p="defaults"} (1):</label></td>
			<td class="listTableRight">
				<i class="fa fa-id-card-o" aria-hidden="true"></i> {include file="li/form-check.tpl" inline=true name="composeDefaults[attachVCard]" id="attachVCard" checked=isset($composeDefaults.attachVCard) labelKey="attachvc"}
				&nbsp;
				<i class="fa fa-certificate" aria-hidden="true"></i> {include file="li/form-check.tpl" inline=true name="composeDefaults[certMail]" id="certMail" checked=isset($composeDefaults.certMail) labelKey="certmail"}
				&nbsp;
				<i class="fa fa-bullhorn" aria-hidden="true"></i> {include file="li/form-check.tpl" inline=true name="composeDefaults[mailConfirmation]" id="mailConfirmation" checked=isset($composeDefaults.mailConfirmation) labelKey="mailconfirmation"}

			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="re">{lng p="defaults"} (2):</label></td>
			<td class="listTableRight">
				<i class="fa fa-inbox" aria-hidden="true"></i> <label for="savecopy">{lng p="savecopy"}:</label>
					<select name="composeDefaults[savecopy]" id="savecopy">
					{foreach from=$dropdownFolderList key=dFolderID item=dFolderTitle}
						<option value="{$dFolderID}" style="font-family:courier;"{if (!$composeDefaults.savecopy&&$composeDefaults.savecopy!=='0'&&$dFolderID==-2)||$composeDefaults.savecopy==$dFolderID} selected="selected"{/if}>{$dFolderTitle}</option>
					{/foreach}
					</select>
				&nbsp;
				<i class="fa fa-flag" aria-hidden="true"></i>
					<select name="composeDefaults[priority]" id="priority">
						<option value="1"{if $composeDefaults.priority==1} selected="selected"{/if}>{lng p="prio_1"}</option>
						<option value="0"{if !$composeDefaults.priority||$composeDefaults.priority==0} selected="selected"{/if}>{lng p="prio_0"}</option>
						<option value="-1"{if $composeDefaults.priority==-1} selected="selected"{/if}>{lng p="prio_-1"}</option>
					</select>
				{if $signatures}
				&nbsp;
					<i class="fa fa-quote-right" aria-hidden="true"></i>
					<select name="composeDefaults[signature]" id="signature">
						<option value="0">-</option>
					{foreach from=$signatures item=signature}
						<option value="{$signature.id}"{if $composeDefaults.signature==$signature.id} selected="selected"{/if}>{text value=$signature.titel cut=15}</option>
					{/foreach}
					</select>
				{/if}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="re">{lng p="retext"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="re" id="re" value="{text allowEmpty=true value=$re}" style="width:80px;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="fwd">{lng p="fwdtext"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="fwd" id="fwd" value="{text allowEmpty=true value=$fwd}" style="width:80px;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="fwd">{lng p="atreply"}:</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="reply_quote" name="reply_quote" checked=($reply_quote=='yes') labelKey="insertquote"}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="attcheck">{lng p="attcheck"}:</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="attcheck" name="attcheck" checked=($attcheck=='yes') labelKey="attcheck_desc"}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="linesep">{lng p="linesep"}:</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="linesep" name="linesep" checked=$linesep labelKey="linesep_desc"}
			</td>
		</tr>
		{if $draftAutoSaveAllowed}
		<tr>
			<td class="listTableLeft"><label for="auto_save_drafts">{lng p="auto_save_drafts"}:</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" wrapClass="d-inline-flex me-2 mb-0" id="auto_save_drafts" name="auto_save_drafts" checked=$autoSaveDrafts}
				{lng p="every"} <input type="text" name="auto_save_drafts_interval" id="auto_save_drafts_interval" value="{$autoSaveDraftsInterval}" size="4" onkeypress="EBID('auto_save_drafts').checked=true;" /> {lng p="seconds"}
			</td>
		</tr>
		{/if}
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-folder-open-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="webdisk"}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="webdisk_hidehidden">{lng p="hiddenelements"}:</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="webdisk_hidehidden" name="webdisk_hidehidden" checked=$webdisk_hidehidden labelKey="hide"}
			</td>
		</tr>

		{if $smimeAllowed}
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-key" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="security"}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="smimeSign">{lng p="sign"}:</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="smimeSign" name="smimeSign" checked=$smimeSign labelKey="enablebydefault"}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="smimeEncrypt">{lng p="encrypt"}:</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="smimeEncrypt" name="smimeEncrypt" checked=$smimeEncrypt labelKey="enablebydefault"}
			</td>
		</tr>
		{/if}
		
		{if $mail2smsAllowed||$forwardingAllowed}<tr>
			<td class="listTableLeftDesc"><i class="fa fa-folder-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="receiveprefs"}</td>
		</tr>{/if}
		{if $mail2smsAllowed}<tr>
			<td class="listTableLeft"><label for="mail2sms">{lng p="mail2sms"}:</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" id="mail2sms" name="mail2sms" checked=($mail2sms=='yes') labelKey="enable"}
			</td>
		</tr>{/if}
		{if $forwardingAllowed}<tr>
			<td class="listTableLeft"><label for="forward_to">{lng p="forwarding"}?</label></td>
			<td class="listTableRight">
				{include file="li/form-check.tpl" wrapClass="d-inline-flex me-2 mb-0" id="forward" name="forward" checked=($forward=='yes')}
				{lng p="to2"} <input type="email" name="forward_to" id="forward_to" value="{email value=$forward_to}" style="width:200px;" onkeypress="EBID('forward').checked=true;" /><br />
				{include file="li/form-check.tpl" id="forward_delete" name="forward_delete" checked=($forward_delete=='yes') labelKey="deleteforwarded"}
			</td>
		</tr>{/if}
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="btn btn-primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</div></div>
</form>
</div>
