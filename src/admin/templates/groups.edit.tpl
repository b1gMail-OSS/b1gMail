<form method="post" action="groups.php?{if $create}action=create&create=true{else}do=edit&id={$group.id}&save=true{/if}&sid={$sid}" onsubmit="spin(this)">
<table width="100%" cellspacing="2" cellpadding="0">
	<tr>
		<td valign="top" width="50%">
			<fieldset>
				<legend>{lng p="common"}</legend>
				
				<table width="100%">
					<tr>
						<td class="td1" width="160">{lng p="title"}:</td>
						<td class="td2"><input type="text" name="titel" value="{text value=$group.titel allowEmpty=true}" style="width:85%;" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="htmlview"}?</td>
						<td class="td2"><input type="checkbox" name="soforthtml"{if $group.soforthtml=='yes'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="monthasset"}:</td>
						<td class="td2"><input type="text" name="sms_monat" value="{$group.sms_monat}" size="8" /> {lng p="credits"}</td>
					</tr>
				</table>
			</fieldset>
			
			<fieldset>
				<legend>{lng p="storage"}</legend>
				
				<table width="100%">
					<tr>
						<td class="td1" width="160">{lng p="email"}:</td>
						<td class="td2"><input type="text" name="storage" value="{$group.storage/1024/1024}" size="8" /> MB</td>
					</tr>
					<tr>
						<td class="td1">{lng p="webdisk"}:</td>
						<td class="td2"><input type="text" name="webdisk" value="{$group.webdisk/1024/1024}" size="8" /> MB</td>
					</tr>
				</table>
			</fieldset>
			
			<fieldset>
				<legend>{lng p="limits"}</legend>
				
				<table width="100%">
					<tr>
						<td class="td1" width="160">{lng p="emailin"}:</td>
						<td class="td2"><input type="text" name="maxsize" value="{$group.maxsize/1024}" size="8" /> KB</td>
					</tr>
					<tr>
						<td class="td1">{lng p="emailout"}:</td>
						<td class="td2"><input type="text" name="anlagen" value="{$group.anlagen/1024}" size="8" /> KB</td>
					</tr>
					<tr>
						<td class="td1">{lng p="wdtraffic"}:</td>
						<td class="td2"><input type="text" name="traffic" value="{if $group.traffic>0}{$group.traffic/1024/1024}{else}{$group.traffic}{/if}" size="8" /> MB</td>
					</tr>
					<tr>
						<td class="td1">{lng p="wdspeed"}:</td>
						<td class="td2"><input type="text" name="wd_member_kbs" value="{$group.wd_member_kbs}" size="8" /> KB/s</td>
					</tr>
					<tr>
						<td class="td1">{lng p="sharespeed"}:</td>
						<td class="td2"><input type="text" name="wd_open_kbs" value="{$group.wd_open_kbs}" size="8" /> KB/s</td>
					</tr>
					<tr>
						<td class="td1" width="220">{lng p="maxrecps"}:</td>
						<td class="td2"><input type="text" name="max_recps" value="{$group.max_recps}" size="8" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="sendlimit"}:</td>
						<td class="td2"><input type="text" name="send_limit_count" value="{$group.send_limit_count}" size="8" />
										{lng p="emailsin"}
										<input type="text" name="send_limit_time" value="{$group.send_limit_time}" size="8" />
										{lng p="minutes"}</td>
					</tr>
					<tr>
						<td class="td1">{lng p="ownpop3"}:</td>
						<td class="td2"><input type="text" name="ownpop3" value="{$group.ownpop3}" size="8" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="ownpop3interval"}:</td>
						<td class="td2"><input type="text" name="ownpop3_interval" value="{$group.ownpop3_interval}" size="8" /> {lng p="seconds"}</td>
					</tr>
					<tr>
						<td class="td1">{lng p="selfpop3_check"}?</td>
						<td class="td2"><input type="checkbox" name="selfpop3_check"{if $group.selfpop3_check=='yes'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="aliases"}:</td>
						<td class="td2"><input type="text" name="aliase" value="{$group.aliase}" size="8" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="smspre"}:</td>
						<td class="td2">
							<textarea style="width:100%;height:80px;" name="sms_pre">{text value=$group.sms_pre allowEmpty=true}</textarea>
							<small>{lng p="sepby"}</small>
						</td>
					</tr>
					<tr>
						<td class="td1">{lng p="smsvalidation"}?</td>
						<td class="td2"><input type="checkbox" name="smsvalidation"{if $group.smsvalidation=='yes'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="allownewsoptout"}?</td>
						<td class="td2"><input name="allow_newsletter_optout"{if $group.allow_newsletter_optout=='yes'} checked="checked"{/if} type="checkbox" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="mail_send_code"}?</td>
						<td class="td2"><input name="mail_send_code"{if $group.mail_send_code=='yes'} checked="checked"{/if} type="checkbox" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="sms_send_code"}?</td>
						<td class="td2"><input name="sms_send_code"{if $group.sms_send_code=='yes'} checked="checked"{/if} type="checkbox" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="abuseprotect"}?</td>
						<td class="td2"><input name="abuseprotect"{if $group.abuseprotect=='yes'} checked="checked"{/if} type="checkbox" /></td>
					</tr>
				</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset>
				<legend>{lng p="services"}</legend>
				
				<table width="100%">
					<tr>
						<td class="td1" width="150">{lng p="autoresponder"}?</td>
						<td class="td2"><input type="checkbox" name="responder"{if $group.responder=='yes'} checked="checked"{/if} /></td>
						<td class="td1" width="150">{lng p="forward"}?</td>
						<td class="td2"><input type="checkbox" name="forward"{if $group.forward=='yes'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="ads"}?</td>
						<td class="td2"><input type="checkbox" name="ads"{if $group.ads=='yes'} checked="checked"{/if} /></td>
						<td class="td2" colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="td1">{lng p="mail2sms"}?</td>
						<td class="td2"><input type="checkbox" name="mail2sms"{if $group.mail2sms=='yes'} checked="checked"{/if} /></td>
						<td class="td1">{lng p="ownfrom"}?</td>
						<td class="td2"><input type="checkbox" name="sms_ownfrom"{if $group.sms_ownfrom=='yes'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="mobileaccess"}?</td>
						<td class="td2"><input type="checkbox" name="wap"{if $group.wap=='yes'} checked="checked"{/if} /></td>
						<td class="td1">{lng p="sync"}?</td>
						<td class="td2"><input type="checkbox" name="syncml"{if $group.syncml=='yes'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="wdshare"}?</td>
						<td class="td2"><input type="checkbox" name="share"{if $group.share=='yes'} checked="checked"{/if} /></td>
						<td class="td1">{lng p="webdav"}?</td>
						<td class="td2"><input type="checkbox" name="webdav"{if !$davSupport} disabled="disabled"{else}{if $group.webdav=='yes'} checked="checked"{/if}{/if} /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="organizerdav"}?</td>
						<td class="td2"><input type="checkbox" name="organizerdav"{if !$davSupport} disabled="disabled"{else}{if $group.organizerdav=='yes'} checked="checked"{/if}{/if} /></td>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="td1">{lng p="smtp"}?</td>
						<td class="td2"><input type="checkbox" name="smtp"{if $group.smtp=='yes'} checked="checked"{/if} /></td>
						<td class="td1">{lng p="pop3"}?</td>
						<td class="td2"><input type="checkbox" name="pop3"{if $group.pop3=='yes'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="imap"}?</td>
						<td class="td2"><input type="checkbox" name="imap"{if $group.imap=='yes'} checked="checked"{/if} /></td>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="td1">{lng p="smime"}?</td>
						<td class="td2"><input type="checkbox" name="smime"{if !$smimeSupport} disabled="disabled"{else}{if $group.smime=='yes'} checked="checked"{/if}{/if} /></td>
						<td class="td1">{lng p="issue_certificates"}?</td>
						<td class="td2"><input type="checkbox" name="issue_certificates"{if !$smimeSupport} disabled="disabled"{else}{if $group.issue_certificates=='yes'} checked="checked"{/if}{/if} /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="upload_certificates"}?</td>
						<td class="td2"><input type="checkbox" name="upload_certificates"{if !$smimeSupport} disabled="disabled"{else}{if $group.upload_certificates=='yes'} checked="checked"{/if}{/if} /></td>
						<td class="td1">{lng p="sender_aliases"}?</td>
						<td class="td2"><input type="checkbox" name="sender_aliases"{if $group.sender_aliases=='yes'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="ftsearch"}?</td>
						<td class="td2"><input type="checkbox" name="ftsearch"{if !$ftsSupport} disabled="disabled"{else}{if $group.ftsearch=='yes'} checked="checked"{/if}{/if} /></td>
						<td class="td1">{lng p="notifications"}?</td>
						<td class="td2"><input type="checkbox" name="notifications"{if $group.notifications=='yes'} checked="checked"{/if} /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="deliverystatus"}?</td>
						<td class="td2"><input type="checkbox" name="maildeliverystatus"{if $group.maildeliverystatus=='yes'} checked="checked"{/if} /></td>
						<td class="td1">{lng p="auto_save_drafts"}?</td>
						<td class="td2"><input type="checkbox" name="auto_save_drafts"{if $group.auto_save_drafts=='yes'} checked="checked"{/if} /></td>
					</tr>
				</table>
			</fieldset>
			
			<fieldset>
				<legend>{lng p="bmtoolbox"}</legend>
				
				<table width="100%">
					<tr>
						<td class="td1" width="150">{lng p="tbx_enable"}?</td>
						<td class="td2"><input type="checkbox" name="checker"{if $group.checker=='yes'} checked="checked"{/if} /></td>
						<td class="td2" width="150">&nbsp;</td>
						<td class="td2">&nbsp;</td>
					</tr>
					<tr>
						<td class="td1">{lng p="tbx_webdisk"}?</td>
						<td class="td2"><input type="checkbox" name="tbx_webdisk"{if $group.tbx_webdisk=='yes'} checked="checked"{/if} /></td>
						<td class="td1">{lng p="tbx_smsmanager"}?</td>
						<td class="td2"><input type="checkbox" name="tbx_smsmanager"{if $group.tbx_smsmanager=='yes'} checked="checked"{/if} /></td>
					</tr>
				</table>
			</fieldset>
			
			<fieldset>
				<legend>{lng p="aliasdomains"}</legend>
				
				<textarea style="width:100%;height:80px;" name="saliase">{text value=$group.saliase allowEmpty=true}</textarea>
				<small>{lng p="sepby"}</small>
			</fieldset>
			
			<fieldset>
				<legend>{lng p="misc"}</legend>
				
				<table width="100%">
					<tr>
						<td class="td1" width="160">{lng p="creditprice"}:</td>
						<td class="td2"><input type="text" name="sms_price_per_credit" value="{$group.sms_price_per_credit}" size="6" /> (1/100 {$currency})</td>
					</tr>
					<tr>
						<td class="td1" width="160">{lng p="smsfrom"}:</td>
						<td class="td2"><input type="text" name="sms_from" value="{text value=$group.sms_from allowEmpty=true}" style="width:85%;" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="smssig"}:</td>
						<td class="td2"><input type="text" name="sms_sig" value="{text value=$group.sms_sig allowEmpty=true}" style="width:85%;" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="mailsig"}:</td>
						<td class="td2"><textarea style="width:100%;height:80px;" name="signatur">{text value=$group.signatur allowEmpty=true}</textarea></td>
					</tr>
					
					{foreach from=$groupOptions key=fieldKey item=fieldInfo}
					<tr>
						<td class="td1">{$fieldInfo.desc}</td>
						<td class="td2">
							{if $fieldInfo.type==16}
								<textarea style="width:100%;height:80px;" name="{$fieldKey}">{text value=$fieldInfo.value allowEmpty=true}</textarea></td>
							{elseif $fieldInfo.type==8}
								{foreach from=$fieldInfo.options item=optionValue key=optionKey}
								<input type="radio" name="{$fieldKey}" id="{$fieldKey}_{$optionKey}" value="{$optionKey}"{if $fieldInfo.value==$optionKey} checked="checked"{/if} />
									<label for="{$fieldKey}_{$optionKey}">{text value=$optionValue}</label>
								{/foreach}
							{elseif $fieldInfo.type==4}
								<select name="{$fieldKey}">
								{foreach from=$fieldInfo.options item=optionValue key=optionKey}
									<option value="{$optionKey}"{if $fieldInfo.value==$optionKey} selected="selected"{/if}>{text value=$optionValue}</option>
								{/foreach}									
								</select>
							{elseif $fieldInfo.type==2}
								<input type="checkbox" name="{$fieldKey}" value="1"{if $fieldInfo.value} checked="checked"{/if} />
							{elseif $fieldInfo.type==1}
								<input type="text" style="width:85%;" name="{$fieldKey}" value="{text value=$fieldInfo.value allowEmpty=true}" />
							{/if}
					</tr>
					{/foreach}
				</table>
			</fieldset>
		</td>
	</tr>
</table>
<p>
	{if !$create}<div style="float:left" class="buttons">
		&nbsp;{lng p="action"}:
		<select name="groupAction" id="groupAction">
			<optgroup label="{lng p="actions"}">
				<option value="newsletter.php?toGroup={$group.id}&sid={$sid}">{lng p="sendmail"}</option>
				<option value="groups.php?singleAction=delete&singleID={$group.id}&sid={$sid}">{lng p="delete"}</option>
			</optgroup>
		</select>
	</div>
	<div style="float:left">
		<input class="button" type="button" value=" {lng p="ok"} " onclick="executeAction('groupAction');" />
	</div>{/if}
	<div style="float:right" class="buttons">
		<input class="button" type="submit" value=" {lng p="save"} " />
	</div>
</p>
</form>
<br /><br />
