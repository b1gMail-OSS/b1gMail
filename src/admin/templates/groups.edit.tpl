<form method="post" action="groups.php?{if $create}action=create&create=true{else}do=edit&id={$group.id}&save=true{/if}&sid={$sid}" onsubmit="spin(this)">
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="common"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="title"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="titel" value="{if isset($group.titel)}{text value=$group.titel allowEmpty=true}{/if}" placeholder="{lng p="title"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="htmlview"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="soforthtml"{if $group.soforthtml=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="monthasset"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="sms_monat" value="{$group.sms_monat}" placeholder="{lng p="monthasset"}">
							<span class="input-group-text">{lng p="credits"}</span>
						</div>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lng p="storage"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="email"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="storage" value="{$group.storage/1024/1024}" placeholder="{lng p="email"}">
							<span class="input-group-text">MB</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="webdisk"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="webdisk" value="{$group.webdisk/1024/1024}" placeholder="{lng p="webdisk"}">
							<span class="input-group-text">MB</span>
						</div>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lng p="limits"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="emailin"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="maxsize" value="{$group.maxsize/1024}" placeholder="{lng p="emailin"}">
							<span class="input-group-text">KB</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="emailout"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="anlagen" value="{$group.anlagen/1024}" placeholder="{lng p="emailout"}">
							<span class="input-group-text">KB</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="wdtraffic"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="traffic" value="{if $group.traffic>0}{$group.traffic/1024/1024}{else}{$group.traffic}{/if}" placeholder="{lng p="wdtraffic"}">
							<span class="input-group-text">MB</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="wdspeed"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="wd_member_kbs" value="{$group.wd_member_kbs}" placeholder="{lng p="wdspeed"}">
							<span class="input-group-text">KB/s</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="sharespeed"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="wd_open_kbs" value="{$group.wd_open_kbs}" placeholder="{lng p="sharespeed"}">
							<span class="input-group-text">KB/s</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="maxrecps"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="max_recps" value="{$group.max_recps}" placeholder="{lng p="maxrecps"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="sendlimit"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="send_limit_count" value="{$group.send_limit_count}" placeholder="{lng p="emailsin"}">
							<span class="input-group-text">{lng p="emailsin"}</span>
							<input type="text" class="form-control" name="send_limit_time" value="{$group.send_limit_time}" placeholder="{lng p="minutes"}">
							<span class="input-group-text">{lng p="minutes"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="ownpop3"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="ownpop3" value="{$group.ownpop3}" placeholder="{lng p="ownpop3"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="ownpop3interval"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="ownpop3_interval" value="{$group.ownpop3_interval}" placeholder="{lng p="ownpop3interval"}">
							<span class="input-group-text">{lng p="seconds"}</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="selfpop3_check"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="selfpop3_check"{if $group.selfpop3_check=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="aliases"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="aliase" value="{$group.aliase}" placeholder="{lng p="aliases"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="smspre"}</label>
					<div class="col-sm-8">
						<textarea class="form-control" name="sms_pre" placeholder="{lng p="smspre"}">{text value=$group.sms_pre allowEmpty=true}</textarea>
						<small>{lng p="sepby"}</small>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="smsvalidation"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="smsvalidation"{if $group.smsvalidation=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="allownewsoptout"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="allow_newsletter_optout"{if $group.allow_newsletter_optout=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="mail_send_code"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="mail_send_code"{if $group.mail_send_code=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="sms_send_code"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="sms_send_code"{if $group.sms_send_code=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="abuseprotect"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="abuseprotect"{if $group.abuseprotect=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="services"}</legend>

				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="autoresponder"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="responder"{if $group.responder=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="forward"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="forward"{if $group.forward=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="ads"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="ads"{if $group.ads=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						&nbsp;
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="mail2sms"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="mail2sms"{if $group.mail2sms=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="ownfrom"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="sms_ownfrom"{if $group.sms_ownfrom=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="mobileaccess"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="wap"{if $group.wap=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="sync"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="syncml"{if $group.syncml=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="wdshare"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="share"{if $group.share=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="webdav"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="webdav"{if !$davSupport} disabled="disabled"{else}{if $group.webdav=='yes'} checked="checked"{/if}{/if}>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="organizerdav"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="organizerdav"{if !$davSupport} disabled="disabled"{else}{if $group.organizerdav=='yes'} checked="checked"{/if}{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						&nbsp;
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="organizer"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="organizer"{if $group.organizer=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						&nbsp;
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="smtp"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="smtp"{if $group.smtp=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="pop3"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="pop3"{if $group.pop3=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="imap"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="imap"{if $group.imap=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						&nbsp;
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="smime"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="smime"{if !$smimeSupport} disabled="disabled"{else}{if $group.smime=='yes'} checked="checked"{/if}{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="issue_certificates"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="issue_certificates"{if !$smimeSupport} disabled="disabled"{else}{if $group.issue_certificates=='yes'} checked="checked"{/if}{/if}>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="upload_certificates"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="upload_certificates"{if !$smimeSupport} disabled="disabled"{else}{if $group.upload_certificates=='yes'} checked="checked"{/if}{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="sender_aliases"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="sender_aliases"{if $group.sender_aliases=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="ftsearch"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="ftsearch"{if !$ftsSupport} disabled="disabled"{else}{if $group.ftsearch=='yes'} checked="checked"{/if}{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="notifications"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="notifications"{if $group.notifications=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="deliverystatus"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="maildeliverystatus"{if $group.maildeliverystatus=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="auto_save_drafts"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="auto_save_drafts"{if $group.auto_save_drafts=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lng p="bmtoolbox"}</legend>

				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="tbx_enable"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="checker"{if $group.checker=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						&nbsp;
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="tbx_webdisk"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="tbx_webdisk"{if $group.tbx_webdisk=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<label class="col-sm-6 col-form-check-label">{lng p="tbx_smsmanager"}</label>
							<div class="col-sm-6">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" name="tbx_smsmanager"{if $group.tbx_smsmanager=='yes'} checked="checked"{/if}>
								</label>
							</div>
						</div>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lng p="aliasdomains"}</legend>

				<div class="mb-3 row">
					<div class="col-sm-12">
						<textarea class="form-control" name="saliase" placeholder="{lng p="aliasdomains"}">{text value=$group.saliase allowEmpty=true}</textarea>
						<small>{lng p="sepby"}</small>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lng p="misc"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="creditprice"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="sms_price_per_credit" value="{$group.sms_price_per_credit}" placeholder="{lng p="creditprice"}">
							<span class="input-group-text">(1/100 {$currency})</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="smsfrom"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="sms_from" value="{if isset($group.sms_from)}{text value=$group.sms_from allowEmpty=true}{/if}" placeholder="{lng p="smsfrom"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="smssig"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="sms_sig" value="{if isset($group.sms_sig)}{text value=$group.sms_sig allowEmpty=true}{/if}" placeholder="{lng p="smssig"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="mailsig"}</label>
					<div class="col-sm-8">
						<textarea class="form-control" name="signatur" placeholder="{lng p="mailsig"}">{text value=$group.signatur allowEmpty=true}</textarea>
					</div>
				</div>
				{foreach from=$groupOptions key=fieldKey item=fieldInfo}
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{$fieldInfo.desc}</label>
						<div class="col-sm-8">
							{if $fieldInfo.type==16}
								<textarea class="form-control" name="{$fieldKey}">{text value=$fieldInfo.value allowEmpty=true}</textarea>
							{elseif $fieldInfo.type==8}
								{foreach from=$fieldInfo.options item=optionValue key=optionKey}
									<div class="form-check">
										<input type="radio" class="form-check-input" name="{$fieldKey}" id="{$fieldKey}_{$optionKey}" value="{$optionKey}"{if $fieldInfo.value==$optionKey} checked="checked"{/if} />
										<span class="form-check-label">{text value=$optionValue}</span>
									</div>
								{/foreach}
							{elseif $fieldInfo.type==4}
								<select name="{$fieldKey}" class="form-select">
									{foreach from=$fieldInfo.options item=optionValue key=optionKey}
										<option value="{$optionKey}"{if $fieldInfo.value==$optionKey} selected="selected"{/if}>{text value=$optionValue}</option>
									{/foreach}
								</select>
							{elseif $fieldInfo.type==2}
								<div class="form-check">
									<input type="checkbox" class="form-check-input" name="{$fieldKey}" value="1"{if $fieldInfo.value} checked="checked"{/if} />
								</div>
							{elseif $fieldInfo.type==1}
								<input type="text" class="form-control" name="{$fieldKey}" value="{if isset($fieldInfo.value)}{text value=$fieldInfo.value allowEmpty=true}{/if}" />
							{/if}
						</div>
					</div>
				{/foreach}
			</fieldset>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div style="float: left;">{lng p="action"}:&nbsp;</div>
			<div style="float: left;">
				<div class="btn-group btn-group-sm">
					<select name="groupAction" id="groupAction" class="form-select form-select-sm">
						<optgroup label="{lng p="actions"}">
							<option value="newsletter.php?toGroup={$group.id}&sid={$sid}">{lng p="sendmail"}</option>
							<option value="groups.php?singleAction=delete&singleID={$group.id}&sid={$sid}">{lng p="delete"}</option>
						</optgroup>
					</select>
					<input type="button" name="executeMassAction" value="{lng p="ok"}" onclick="executeAction('groupAction');" class="btn btn-sm btn-dark-lt" />
				</div>
			</div>
		</div>
		<div class="col-md-6 text-end">
			<input class="btn btn-primary" type="submit" value=" {lng p="save"} " />
		</div>
	</div>
</form>