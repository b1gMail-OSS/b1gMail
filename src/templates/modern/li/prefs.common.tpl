<div id="contentHeader">
	<div class="left">
		<i class="fa fa-cogs" aria-hidden="true"></i>
		{lng p="common"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
<form name="f1" method="post" action="prefs.php?action=common&do=save&sid={$sid}">
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
				<input type="text" name="datumsformat" id="datumsformat" value="{text value=$datumsformat}" style="width:250px;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="hotkeys">{lng p="hotkeys"}?</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="hotkeys" id="hotkeys"{if $hotkeys} checked="checked"{/if} />
					<label for="hotkeys">{lng p="enable"}</label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="search_details_default">{lng p="search"}:</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="search_details_default" id="search_details_default"{if $searchDetailsDefault} checked="checked"{/if} />
					<label for="search_details_default">{lng p="details_default"}</label>
			</td>
		</tr>

		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-bell-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="notifications"}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="notify_sound">{lng p="notify_sound"}?</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="notify_sound" id="notify_sound"{if $notifySound} checked="checked"{/if} />
					<label for="notify_sound">{lng p="enable"}</label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="notify_types">{lng p="notify_types"}:</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="notify_email" id="notify_email"{if $notifyEMail} checked="checked"{/if} />
					<label for="notify_email">{lng p="notify_email"}</label>
				<input type="checkbox" name="notify_birthday" id="notify_birthday"{if $notifyBirthday} checked="checked"{/if} />
					<label for="notify_birthday">{lng p="notify_birthday"}</label>
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
				<input type="checkbox" name="newsletter_optin" id="newsletter_optin"{if $newsletter_optin=='yes'} checked="checked"{/if} />
					<label for="newsletter_optin">{lng p="subscribe"}</label>
			</td>
		</tr>
		{/if}
		<tr>
			<td class="listTableLeft"><label for="in_refresh">{lng p="inboxrefresh"}:</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="in_refresh_active" id="in_refresh_active"{if $in_refresh>0} checked="checked"{/if} />
				{lng p="every"} <input type="text" name="in_refresh" id="in_refresh" value="{$in_refresh}" size="4" onkeypress="EBID('in_refresh_active').checked=true;" /> {lng p="seconds"}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="preview">{lng p="preview"}?</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="preview" id="preview"{if $preview=='yes'} checked="checked"{/if} />
					<label for="preview">{lng p="enable"}</label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="soforthtml">{lng p="plaintextcourier"}:</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="plaintext_courier" id="plaintext_courier"{if $plaintext_courier=='yes'} checked="checked"{/if} />
					<label for="plaintext_courier">{lng p="usecourier"}</label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="soforthtml">{lng p="insthtmlview"}?</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="soforthtml" id="soforthtml"{if $soforthtml=='yes'} checked="checked"{/if} />
					<label for="soforthtml">{lng p="enable"}</label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="conversation_view">{lng p="conversationview"}?</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="conversation_view" id="conversation_view"{if $conversation_view=='yes'} checked="checked"{/if} />
					<label for="conversation_view">{lng p="enable"}</label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="autosend_dn">{lng p="mailconfirmation"}?</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="autosend_dn" id="autosend_dn"{if $autosend_dn} checked="checked"{/if} />
					<label for="autosend_dn">{lng p="autosend"}</label>
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-reply" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="composeprefs"}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="absendername">{lng p="sendername"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="absendername" id="absendername" value="{text value=$absendername allowEmpty=true}" style="width:350px;" />
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
				<i class="fa fa-id-card-o" aria-hidden="true"></i> <input type="checkbox" name="composeDefaults[attachVCard]" id="attachVCard"{if $composeDefaults.attachVCard} checked="checked"{/if} /> <label for="attachVCard">{lng p="attachvc"}</label>
				&nbsp;
				<i class="fa fa-certificate" aria-hidden="true"></i> <input type="checkbox" name="composeDefaults[certMail]" id="certMail"{if $composeDefaults.certMail} checked="checked"{/if} /> <label for="certMail">{lng p="certmail"}</label>
				&nbsp;
				<i class="fa fa-bullhorn" aria-hidden="true"></i> <input type="checkbox" name="composeDefaults[mailConfirmation]" id="mailConfirmation"{if $composeDefaults.mailConfirmation} checked="checked"{/if} /> <label for="mailConfirmation">{lng p="mailconfirmation"}</label>

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
				<input type="checkbox" name="reply_quote" id="reply_quote"{if $reply_quote=='yes'} checked="checked"{/if} />
					<label for="reply_quote">{lng p="insertquote"}</label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="attcheck">{lng p="attcheck"}:</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="attcheck" id="attcheck"{if $attcheck=='yes'} checked="checked"{/if} />
					<label for="attcheck">{lng p="attcheck_desc"}</label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="linesep">{lng p="linesep"}:</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="linesep" id="linesep"{if $linesep} checked="checked"{/if} />
					<label for="linesep">{lng p="linesep_desc"}</label>
			</td>
		</tr>
		{if $draftAutoSaveAllowed}
		<tr>
			<td class="listTableLeft"><label for="auto_save_drafts">{lng p="auto_save_drafts"}:</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="auto_save_drafts" id="auto_save_drafts"{if $autoSaveDrafts} checked="checked"{/if} />
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
				<input type="checkbox" name="webdisk_hidehidden" id="webdisk_hidehidden"{if $webdisk_hidehidden} checked="checked"{/if} />
					<label for="webdisk_hidehidden">{lng p="hide"}</label>
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
				<input type="checkbox" name="smimeSign" id="smimeSign"{if $smimeSign} checked="checked"{/if} />
					<label for="smimeSign">{lng p="enablebydefault"}</label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="smimeEncrypt">{lng p="encrypt"}:</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="smimeEncrypt" id="smimeEncrypt"{if $smimeEncrypt} checked="checked"{/if} />
					<label for="smimeEncrypt">{lng p="enablebydefault"}</label>
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
				<input type="checkbox" name="mail2sms" id="mail2sms"{if $mail2sms=='yes'} checked="checked"{/if} />
					<label for="mail2sms">{lng p="enable"}</label>
			</td>
		</tr>{/if}
		{if $forwardingAllowed}<tr>
			<td class="listTableLeft"><label for="forward_to">{lng p="forwarding"}?</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="forward" id="forward"{if $forward=='yes'} checked="checked"{/if} />
					{lng p="to2"} <input type="text" name="forward_to" id="forward_to" value="{email value=$forward_to}" style="width:200px;" onkeypress="EBID('forward').checked=true;" /><br />
				<input type="checkbox" name="forward_delete" id="forward_delete"{if $forward_delete=='yes'} checked="checked"{/if} />
					<label for="forward_delete">{lng p="deleteforwarded"}</label>
			</td>
		</tr>{/if}
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>
</div></div>
