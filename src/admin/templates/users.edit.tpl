<form method="post" action="users.php?do=edit&id={$user.id}&save=true&sid={$sid}" onsubmit="spin(this)">

{if $msg}
<center style="margin:1em;">
	<div class="note">
		{$msg}
	</div>
</center>
{/if}

<table width="100%" cellspacing="2" cellpadding="0">
	<tr>
		<td valign="top" width="50%">
			<fieldset>
				<legend>{lng p="profile"}</legend>

				<table width="100%">
					<tr>
						<td class="td1" width="160">{lng p="email"}:</td>
						<td class="td2"><input type="text" name="email" value="{email value=$user.email}" style="width:85%;" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="salutation"}:</td>
						<td class="td2"><select name="anrede">
								<option value="">&nbsp;</option>
								<option value="herr"{if $user.anrede=='herr'} selected="selected"{/if}>{lng p="mr"}</option>
								<option value="frau"{if $user.anrede=='frau'} selected="selected"{/if}>{lng p="mrs"}</option>
							</select></td>
					</tr>
					<tr>
						<td class="td1">{lng p="firstname"}:</td>
						<td class="td2"><input type="text" name="vorname" value="{text value=$user.vorname allowEmpty=true}" style="width:85%;" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="lastname"}:</td>
						<td class="td2"><input type="text" name="nachname" value="{text value=$user.nachname allowEmpty=true}" style="width:85%;" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="streetno"}:</td>
						<td class="td2"><input type="text" name="strasse" value="{text value=$user.strasse allowEmpty=true}" style="width:55%;" />
										<input type="text" name="hnr" value="{text value=$user.hnr allowEmpty=true}" style="width:15%;" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="zipcity"}:</td>
						<td class="td2"><input type="text" name="plz" value="{text value=$user.plz allowEmpty=true}" style="width:20%;" />
										<input type="text" name="ort" value="{text value=$user.ort allowEmpty=true}" style="width:50%;" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="country"}:</td>
						<td class="td2"><select name="land">
						{foreach from=$countries item=countryName key=countryID}
							<option value="{$countryID}"{if $countryID==$user.land} selected="selected"{/if}>{text value=$countryName}</option>
						{/foreach}
						</select></td>
					</tr>
					<tr>
						<td class="td1">{lng p="tel"}:</td>
						<td class="td2"><input type="text" name="tel" value="{text value=$user.tel allowEmpty=true}" style="width:85%;" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="fax"}:</td>
						<td class="td2"><input type="text" name="fax" value="{text value=$user.fax allowEmpty=true}" style="width:85%;" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="cellphone"}:</td>
						<td class="td2"><input type="text" name="mail2sms_nummer" value="{text value=$user.mail2sms_nummer allowEmpty=true}" style="width:85%;" /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="altmail"}:</td>
						<td class="td2"><input type="text" name="altmail" value="{email value=$user.altmail}" style="width:85%;" /></td>
					</tr>

					{foreach from=$profileFields item=profileField}
					{assign var=fieldID value=$profileField.id}
					<tr>
						<td class="td1">{$profileField.title}:</td>
						<td class="td2">
							{if $profileField.type==1}
								<input type="text" name="field_{$profileField.id}" value="{text value=$profileField.value allowEmpty=true}" style="width:85%;" />
							{elseif $profileField.type==2}
								<input type="checkbox" name="field_{$profileField.id}"{if $profileField.value} checked="checked"{/if} />
							{elseif $profileField.type==4}
								<select name="field_{$profileField.id}">
								{foreach from=$profileField.extra item=item}
									<option value="{text value=$item allowEmpty=true}"{if $profileField.value==$item} selected="selected"{/if}>{text value=$item allowEmpty=true}</option>
								{/foreach}
								</select>
							{elseif $profileField.type==8}
								{foreach from=$profileField.extra item=item}
									<input type="radio" id="field_{$profileField.id}_{$item}" name="field_{$profileField.id}" value="{text value=$item allowEmpty=true}"{if $profileField.value==$item} checked="checked"{/if} />
									<label for="field_{$profileField.id}_{$item}"><b>{$item}</b></label> &nbsp;
								{/foreach}
							{elseif $profileField.type==32}
								{if $profileField.value}
								{html_select_date time=$profileField.value year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix="field_$fieldID" field_order="DMY"}
								{else}
								{html_select_date time="---" year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix="field_$fieldID" field_order="DMY"}
								{/if}
							{/if}
						</td>
					</tr>
					{/foreach}
				</table>
				{if $historyCount}
				<br /><div class="note">
					{$historyCount} {lng p="oldcontacts"}
					<a href="users.php?do=contactHistory&id={$user.id}&sid={$sid}">{lng p="show"} &raquo;</a>
				</div>
				{/if}
			</fieldset>

			<fieldset>
				<legend>{lng p="common"}</legend>

				<table width="100%">
					<tr>
						<td class="td1" width="160">{lng p="group"}:</td>
						<td class="td2"><select name="gruppe">
						{foreach from=$groups item=groupItem}
							<option value="{$groupItem.id}"{if $groupItem.id==$group.id} selected="selected"{/if}>{text value=$groupItem.title}</option>
						{/foreach}
						</select></td>
					</tr>
					<tr>
						<td class="td1">{lng p="status"}:</td>
						<td class="td2"><select name="gesperrt">
							<option value="no"{if $user.gesperrt=='no'} selected="selected"{/if}>{lng p="active"}</option>
							<option value="yes"{if $user.gesperrt=='yes'} selected="selected"{/if}>{lng p="locked"}</option>
							<option value="locked"{if $user.gesperrt=='locked'} selected="selected"{/if}>{lng p="notactivated"}</option>
							<option value="delete"{if $user.gesperrt=='delete'} selected="selected"{/if}>{lng p="deleted"}</option>
						</select></td>
					</tr>
					<tr>
						<td class="td1">{lng p="assets"}:</td>
						<td class="td2"><a href="users.php?do=transactions&id={$user.id}&sid={$sid}">{$staticBalance} {lng p="credits"}</a></td>
					</tr>
					<tr>
						<td class="td1">{lng p="newpassword"}:</td>
						<td class="td2"><input type="text" name="passwort" value="" style="width:85%;" /></td>
					</tr>
				</table>
			</fieldset>

			<fieldset>
				<legend>{lng p="addservices"}</legend>

				<table width="100%">
					<tr>
						<td class="td1" width="160">{lng p="mailspace_add"}:</td>
						<td class="td2"><input type="text" name="mailspace_add" value="{$user.mailspace_add/1024/1024}" size="8" /> MB</td>
					</tr>
					<tr>
						<td class="td1">{lng p="diskspace_add"}:</td>
						<td class="td2"><input type="text" name="diskspace_add" value="{$user.diskspace_add/1024/1024}" size="8" /> MB</td>
					</tr>
					<tr>
						<td class="td1">{lng p="traffic_add"}:</td>
						<td class="td2"><input type="text" name="traffic_add" value="{$user.traffic_add/1024/1024}" size="8" /> MB</td>
					</tr>
				</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset>
				<legend>{lng p="usage"}</legend>

				<table>
					<tr>
						<td class="td1" width="160">{lng p="email"}:</td>
						<td class="td2">
							{$emailMails} {lng p="emails"}, {$emailFolders} {lng p="folders"}
							{progressBar value=$user.mailspace_used max=$group.storage width=200}
							<small>{size bytes=$user.mailspace_used} / {size bytes=$group.storage} {lng p="used"}</small>
						</td>
					</tr>
					<tr>
						<td class="td1">{lng p="webdisk"}:</td>
						<td class="td2">
							{$diskFiles} {lng p="files"}, {$diskFolders} {lng p="folders"}
							{progressBar value=$user.diskspace_used max=$group.webdisk width=200}
							<small>{size bytes=$user.diskspace_used} / {size bytes=$group.webdisk} {lng p="used"}</small>
						</td>
					</tr>
					<tr>
						<td class="td1">{lng p="wdtraffic"}:</td>
						<td class="td2">
							{if $group.traffic>0}{progressBar value=$user.traffic_down+$user.traffic_up max=$group.traffic width=200}{/if}
							<small>{size bytes=$user.traffic_down+$user.traffic_up}{if $group.traffic>0} / {size bytes=$group.traffic}{/if} {lng p="used2"}</small>
						</td>
					</tr>

					{if $group.sms_monat>0}
					<tr>
						<td class="td1">{lng p="monthasset"}:</td>
						<td class="td2">
							{progressBar value=$usedMonthSMS max=$group.sms_monat width=200}
							<small>{$usedMonthSMS} / {$group.sms_monat} {lng p="credits"} {lng p="used2"}</small>
						</td>
					</tr>
					{/if}

					<tr>
						<td class="td1">{lng p="abuseprotect"}:</td>
						<td class="td2">
							<img src="{$tpldir}/images/indicator_{$abuseIndicator}.png" alt="" border="0" align="absmiddle" />
								<a href="abuse.php?do=show&userid={$user.id}&sid={$sid}">
								{if $abuseIndicator!='grey'}
									{$abusePoints}
									{lng p="points"}
								{else}
									({lng p="disabled"})
								{/if}
								</a>
						</td>
					</tr>
				</table>
			</fieldset>

			<fieldset>
				<legend>{lng p="misc"}</legend>

				<table>
					<tr>
						<td class="td1" width="160">{lng p="lastlogin"}:</td>
						<td class="td2">{date timestamp=$user.lastlogin nice=true nozero=true}</td>
					</tr>
					<tr>
						<td class="td1"><small>{lng p="ip"}:</small></td>
						<td class="td2"><small>{text value=$user.ip}</small></td>
					</tr>
					<tr>
						<td class="td1">{lng p="regdate"}:</td>
						<td class="td2">{date timestamp=$user.reg_date nice=true nozero=true}</td>
					</tr>
					<tr>
						<td class="td1"><small>{lng p="ip"}:</small></td>
						<td class="td2"><small>{text value=$user.reg_ip}</small></td>
					</tr>
					<tr>
						<td class="td1">{lng p="lastpop3"}:</td>
						<td class="td2">{date timestamp=$user.last_pop3 nice=true nozero=true}</td>
					</tr>
					<tr>
						<td class="td1">{lng p="lastsmtp"}:</td>
						<td class="td2">{date timestamp=$user.last_smtp nice=true nozero=true}</td>
					</tr>
					<tr>
						<td class="td1">{lng p="lastimap"}:</td>
						<td class="td2">{date timestamp=$user.last_imap nice=true nozero=true}</td>
					</tr>
				</table>
			</fieldset>

			<fieldset>
				<legend>{lng p="notes"}</legend>
				<textarea style="width:100%;height:150px;" name="notes">{text value=$user.notes allowEmpty=true}</textarea>
			</fieldset>

			<fieldset class="collapsed">
				<legend><a href="javascript:;" onclick="toggleFieldset(this)">{lng p="prefs"}</a></legend>
				<div class="content">
					<table width="100%">
						<tr>
							<td class="td1" width="160">{lng p="re"}/{lng p="fwd"}:</td>
							<td class="td2"><input type="text" name="re" value="{text value=$user.re allowEmpty=true}" style="width:35%;" />
											<input type="text" name="fwd" value="{text value=$user.fwd allowEmpty=true}" style="width:35%;" /></td>
						</tr>
						<tr>
							<td class="td1">{lng p="mail2sms"}:</td>
							<td class="td2"><select name="mail2sms">
								<option value="yes"{if $user.mail2sms=='yes'} selected="selected"{/if}>{lng p="yes"}</option>
								<option value="no"{if $user.mail2sms=='no'} selected="selected"{/if}>{lng p="no"}</option>
							</select></td>
						</tr>
						<tr>
							<td class="td1">{lng p="forward"}:</td>
							<td class="td2"><select name="forward">
								<option value="yes"{if $user.forward=='yes'} selected="selected"{/if}>{lng p="yes"}</option>
								<option value="no"{if $user.forward=='no'} selected="selected"{/if}>{lng p="no"}</option>
							</select></td>
						</tr>
						<tr>
							<td class="td1">{lng p="forwardto"}:</td>
							<td class="td2"><input type="text" name="forward_to" value="{email value=$user.forward_to}" style="width:85%;" /></td>
						</tr>
						<tr>
							<td class="td1">{lng p="newsletter"}:</td>
							<td class="td2"><select name="newsletter_optin">
								<option value="yes"{if $user.newsletter_optin=='yes'} selected="selected"{/if}>{lng p="yes"}</option>
								<option value="no"{if $user.newsletter_optin=='no'} selected="selected"{/if}>{lng p="no"}</option>
							</select></td>
						</tr>
						<tr>
							<td class="td1">{lng p="dateformat"}:</td>
							<td class="td2"><input type="text" name="datumsformat" value="{text value=$user.datumsformat allowEmpty=true}" style="width:85%;" /></td>
						</tr>
						<tr>
							<td class="td1">{lng p="sendername"}:</td>
							<td class="td2"><input type="text" name="absendername" value="{text value=$user.absendername allowEmpty=true}" style="width:85%;" /></td>
						</tr>
					</table>
				</div>
			</fieldset>

			<fieldset class="collapsed">
				<legend><a href="javascript:;" onclick="toggleFieldset(this)">{lng p="aliasdomains"}</a></legend>
				<div class="content">
					<textarea style="width:100%;height:80px;" name="saliase">{text value=$user.saliase allowEmpty=true}</textarea>
					<small>{lng p="sepby"}</small>
				</div>
			</fieldset>

			<fieldset class="{if $showAliases}un{/if}collapsed">
				<legend><a href="javascript:;" onclick="toggleFieldset(this)">{lng p="aliases"}</a></legend>
				<div class="content">
					<table class="list">
						<tr>
							<th width="20">&nbsp;</th>
							<th>{lng p="alias"}</th>
							<th width="130">{lng p="type"}</th>
							<th width="28">&nbsp;</th>
						</tr>

						{foreach from=$aliases item=alias}
						{cycle values="td1,td2" name="class" assign="class"}
						<tr class="{$class}">
							<td><img src="{$tpldir}images/alias.png" border="0" alt="" width="16" height="16" /></td>
							<td>{email value=$alias.email cut=30}</td>
							<td>{$alias.type}</td>
							<td><a href="users.php?do=edit&id={$user.id}&deleteAlias={$alias.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a></td>
						</tr>
						{/foreach}
					</table>
				</div>
			</fieldset>

			<fieldset class="{if $showPayments}un{/if}collapsed">
				<legend><a href="javascript:;" onclick="toggleFieldset(this)">{lng p="payments"}</a> ({lng p="max"} 15)</legend>
				<div class="content">
					<table class="list">
						<tr>
							<th width="20">&nbsp;</th>
							<th>{lng p="orderno"}</th>
							<th width="135">{lng p="amount"}</th>
							<th width="145">{lng p="date"}</th>
							<th width="65">&nbsp;</th>
						</tr>

						{foreach from=$payments item=payment}
						{cycle values="td1,td2" name="class" assign="class"}
						<tr class="{$class}">
							<td align="center"><img src="templates/images/{if $payment.status==1}yes{else}no{/if}.png" border="0" alt="" width="16" height="16" /></td>
							<td>{text value=$payment.invoiceNo}<br /><small>{text value=$payment.customerNo}</small></td>
							<td>
								<div style="float:left;">
									{$payment.amount}<br /><small>{$payment.method}</small>
								</div>
								{if $payment.paymethod<0}
								<div style="float:right;">
									<a href="payments.php?do=details&orderid={$payment.orderid}&sid={$sid}" title="{lng p="details"}"><img src="{$tpldir}images/ico_prefs_payments.png" border="0" alt="{lng p="details"}" width="16" height="16" /></a>
								</div>
								{/if}
							</td>
							<td>{date timestamp=$payment.created nice=true}</td>
							<td>
								{if $payment.hasInvoice}<a href="javascript:void(0);" onclick="openWindow('payments.php?action=showInvoice&orderID={$payment.orderid}&sid={$sid}','invoice_{$payment.orderid}',640,480);" title="{lng p="showinvoice"}"><img src="{$tpldir}images/file.png" border="0" alt="{lng p="showinvoice"}" width="16" height="16" /></a>{/if}
								{if $payment.status==0}<a href="{if $payment.paymethod<0}payments.php?do=details&orderid={$payment.orderid}&sid={$sid}{else}users.php?do=edit&id={$user.id}&activatePayment={$payment.orderid}&sid={$sid}{/if}" title="{lng p="activatepayment"}"><img src="{$tpldir}images/unlock.png" border="0" alt="{lng p="activatepayment"}" width="16" height="16" /></a>{/if}
								<a href="users.php?do=edit&id={$user.id}&deletePayment={$payment.orderid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" title="{lng p="delete"}"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
							</td>
						</tr>
						{/foreach}
					</table>
				</div>
			</fieldset>
		</td>
	</tr>
</table>
<p>
	<div style="float:left" class="buttons">
		{lng p="action"}:&nbsp;
		<select name="userAction" id="userAction">
			<optgroup label="{lng p="actions"}">
				{if $user.sms_validation_code!=''&&$user.gesperrt=='locked'}
				{if $regValidation=='email'&&$user.altmail!=''}
				<option value="users.php?do=edit&id={$user.id}&resendValidationEmail=true&sid={$sid}">{lng p="resend_val_email"}</option>
				{elseif $regValidation=='sms'&&$user.mail2sms_nummer!=''}
				<option value="users.php?do=edit&id={$user.id}&resendValidationSMS=true&sid={$sid}">{lng p="resend_val_sms"}</option>
				{/if}
				{/if}
				<option value="mailto:{email value=$user.email}">{lng p="sendmail"}</option>
				{if $user.altmail!=''}<option value="mailto:{email value=$user.altmail}">{lng p="sendmail"} ({lng p="altmail"})</option>{/if}
				<option value="popup;users.php?do=login&id={$user.id}&sid={$sid}">{lng p="login"}</option>
				<option value="users.php?singleAction=emptyTrash&singleID={$user.id}&sid={$sid}">{lng p="emptytrash"}</option>
				<option value="users.php?singleAction={if $user.gesperrt=='no'}lock{elseif $user.gesperrt=='yes'}unlock{elseif $user.gesperrt=='locked'}activate{elseif $user.gesperrt=='delete'}recover{/if}&singleID={$user.id}&sid={$sid}">{if $user.gesperrt=='no'}{lng p="lock"}{elseif $user.gesperrt=='yes'}{lng p="unlock"}{elseif $user.gesperrt=='locked'}{lng p="activate"}{elseif $user.gesperrt=='delete'}{lng p="restore"}{/if}</option>
				<option value="users.php?singleAction=delete&singleID={$user.id}&sid={$sid}">{lng p="delete"}</option>
			</optgroup>

			<optgroup label="{lng p="move"}">
			{foreach from=$groups item=groupItem key=groupID}
			{if $groupID!=$user.gruppe}
				<option value="users.php?do=edit&id={$user.id}&moveToGroup={$groupID}&sid={$sid}">{lng p="moveto"} &quot;{text value=$groupItem.title cut=25}&quot;</option>
			{/if}
			{/foreach}
			</optgroup>
		</select>
	</div>
	<div style="float:left;padding-left:0.5em;">
		<input class="button" type="button" value=" {lng p="ok"} " onclick="executeAction('userAction');" />
	</div>
	<div style="float:right" class="buttons">
		<input class="button" type="submit" value=" {lng p="save"} " />
	</div>
</p>
</form>