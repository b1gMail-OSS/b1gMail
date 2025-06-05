<form method="post" action="users.php?do=edit&id={$user.id}&save=true&sid={$sid}" onsubmit="spin(this)">

	{if isset($msg)}
		<div class="alert alert-info">{$msg}</div>
	{/if}

	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="profile"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="email"}</label>
					<div class="col-sm-8">
						<input type="email" class="form-control" name="email" value="{email value=$user.email}" placeholder="{lng p="email"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="salutation"}</label>
					<div class="col-sm-8">
						<select name="anrede" class="form-select">
							<option value="">&nbsp;</option>
							<option value="herr"{if $user.anrede=='herr'} selected="selected"{/if}>{lng p="mr"}</option>
							<option value="frau"{if $user.anrede=='frau'} selected="selected"{/if}>{lng p="mrs"}</option>
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="firstname"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="vorname" value="{if isset($user.vorname)}{text value=$user.vorname allowEmpty=true}{/if}" placeholder="{lng p="firstname"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="lastname"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="nachname" value="{if isset($user.nachname)}{text value=$user.nachname allowEmpty=true}{/if}" placeholder="{lng p="lastname"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="company"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="company" value="{if isset($user.company)}{text value=$user.company allowEmpty=true}{/if}" placeholder="{lng p="company"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="streetno"}</label>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="strasse" value="{if isset($user.strasse)}{text value=$user.strasse allowEmpty=true}{/if}" placeholder="{lng p="streetno"}">
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" name="hnr" value="{if isset($user.hnr)}{text value=$user.hnr allowEmpty=true}{/if}" placeholder="{lng p="streetno"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="zipcity"}</label>
					<div class="col-sm-2">
						<input type="text" class="form-control" name="plz" value="{if isset($user.plz)}{text value=$user.plz allowEmpty=true}{/if}" placeholder="{lng p="zip"}">
					</div>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="ort" value="{if isset($user.ort)}{text value=$user.ort allowEmpty=true}{/if}" placeholder="{lng p="city"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="country"}</label>
					<div class="col-sm-8">
						<select name="land" class="form-select">
							{foreach from=$countries item=countryName key=countryID}
								<option value="{$countryID}"{if $countryID==$user.land} selected="selected"{/if}>{text value=$countryName}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="tel"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="tel" value="{if isset($user.tel)}{text value=$user.tel allowEmpty=true}{/if}" placeholder="{lng p="tel"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="fax"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="fax" value="{if isset($user.fax)}{text value=$user.fax allowEmpty=true}{/if}" placeholder="{lng p="fax"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="cellphone"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="mail2sms_nummer" value="{if isset($user.mail2sms_nummer)}{text value=$user.mail2sms_nummer allowEmpty=true}{/if}" placeholder="{lng p="cellphone"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="altmail"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="altmail" value="{email value=$user.altmail}" placeholder="{lng p="altmail"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="taxid"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="taxid" value="{if isset($user.taxid)}{text value=$user.taxid}{/if}" placeholder="{lng p="taxid"}">
					</div>
				</div>
				{foreach from=$profileFields item=profileField}
					{assign var=fieldID value=$profileField.id}
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{$profileField.title}</label>
						<div class="col-sm-8">
							{if $profileField.type==1}
								<input type="text" class="form-control" name="field_{$profileField.id}" value="{if isset($profileField.value)}{text value=$profileField.value allowEmpty=true}{/if}" style="width:85%;" />
							{elseif $profileField.type==2}
								<div class="form-check">
									<input type="checkbox" class="form-check-input" name="field_{$profileField.id}"{if $profileField.value} checked="checked"{/if} />
								</div>
							{elseif $profileField.type==4}
								<select name="field_{$profileField.id}" class="form-select">
									{foreach from=$profileField.extra item=item}
										<option value="{text value=$item allowEmpty=true}"{if $profileField.value==$item} selected="selected"{/if}>{text value=$item allowEmpty=true}</option>
									{/foreach}
								</select>
							{elseif $profileField.type==8}
								{foreach from=$profileField.extra item=item}
									<div class="form-check">
										<input type="radio" class="form-check-input" id="field_{$profileField.id}_{$item}" name="field_{$profileField.id}" value="{if isset($item)}{text value=$item allowEmpty=true}{/if}"{if $profileField.value==$item} checked="checked"{/if} />
										<span class="form-check-label">{$item}</span> &nbsp;
									</div>
								{/foreach}
							{elseif $profileField.type==32}
								{if $profileField.value}
									{html_select_date time=$profileField.value year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix="field_$fieldID" field_order="DMY"}
								{else}
									{html_select_date time="---" year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix="field_$fieldID" field_order="DMY"}
								{/if}
							{/if}
						</div>
					</div>
				{/foreach}
				{if $historyCount}
					<div class="alert alert-warning">
						{$historyCount} {lng p="oldcontacts"}
						<a href="users.php?do=contactHistory&id={$user.id}&sid={$sid}">{lng p="show"} &raquo;</a>
					</div>
				{/if}
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="usage"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="email"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							{$emailMails} {lng p="emails"}, {$emailFolders} {lng p="folders"}
							{progressBar value=$user.mailspace_used max=$group.storage width=200}
							<small>{size bytes=$user.mailspace_used} / {size bytes=$group.storage} {lng p="used"}</small>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="webdisk"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							{$diskFiles} {lng p="files"}, {$diskFolders} {lng p="folders"}
							{progressBar value=$user.diskspace_used max=$group.webdisk width=200}
							<small>{size bytes=$user.diskspace_used} / {size bytes=$group.webdisk} {lng p="used"}</small>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="wdtraffic"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							{if $group.traffic>0}{progressBar value=$user.traffic_down+$user.traffic_up max=$group.traffic width=200}{/if}
							<small>{size bytes=$user.traffic_down+$user.traffic_up}{if $group.traffic>0} / {size bytes=$group.traffic}{/if} {lng p="used2"}</small>
						</div>
					</div>
				</div>
				{if $group.sms_monat>0}
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{lng p="monthasset"}</label>
						<div class="col-sm-8">
							<div class="form-control-plaintext">
								{progressBar value=$usedMonthSMS max=$group.sms_monat width=200}
								<small>{$usedMonthSMS} / {$group.sms_monat} {lng p="credits"} {lng p="used2"}</small>
							</div>
						</div>
					</div>
				{/if}
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="abuseprotect"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							<i class="fa-regular fa-circle text-{$abuseIndicator}"></i>
							<a href="abuse.php?do=show&userid={$user.id}&sid={$sid}">
								{if $abuseIndicator!='grey'}
									{$abusePoints}
									{lng p="points"}
								{else}
									({lng p="disabled"})
								{/if}
							</a>
						</div>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lng p="misc"}</legend>

				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="lastlogin"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							{date timestamp=$user.lastlogin nice=true nozero=true}
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label"><small>{lng p="ip"}:</small></label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							<small>{text value=$user.ip}</small>
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="regdate"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							{date timestamp=$user.reg_date nice=true nozero=true}
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label"><small>{lng p="ip"}:</small></label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							<small>{text value=$user.reg_ip}</small>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="lastpop3"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							{date timestamp=$user.last_pop3 nice=true nozero=true}
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="lastsmtp"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							{date timestamp=$user.last_smtp nice=true nozero=true}
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="lastimap"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							{date timestamp=$user.last_imap nice=true nozero=true}
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="common"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="group"}</label>
					<div class="col-sm-8">
						<select name="gruppe" class="form-select">
							{foreach from=$groups item=groupItem}
								<option value="{$groupItem.id}"{if $groupItem.id==$group.id} selected="selected"{/if}>{text value=$groupItem.title}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="status"}</label>
					<div class="col-sm-8">
						<select name="gesperrt" class="form-select">
							<option value="no"{if $user.gesperrt=='no'} selected="selected"{/if}>{lng p="active"}</option>
							<option value="yes"{if $user.gesperrt=='yes'} selected="selected"{/if}>{lng p="locked"}</option>
							<option value="locked"{if $user.gesperrt=='locked'} selected="selected"{/if}>{lng p="notactivated"}</option>
							<option value="delete"{if $user.gesperrt=='delete'} selected="selected"{/if}>{lng p="deleted"}</option>
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="assets"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							<a href="users.php?do=transactions&id={$user.id}&sid={$sid}">{$staticBalance} {lng p="credits"}</a>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="newpassword"}</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" name="passwort" value="" placeholder="{lng p="newpassword"}">
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="notes"}</legend>

				<textarea class="form-control" style="min-height: 150px;" name="notes">{text value=$user.notes allowEmpty=true}</textarea>
			</fieldset>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="addservices"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="mailspace_add"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="mailspace_add" value="{$user.mailspace_add/1024/1024}" placeholder="{lng p="mailspace_add"}">
							<span class="input-group-text">MB</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="diskspace_add"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="diskspace_add" value="{$user.diskspace_add/1024/1024}" placeholder="{lng p="diskspace_add"}">
							<span class="input-group-text">MB</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="traffic_add"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="traffic_add" value="{$user.traffic_add/1024/1024}" placeholder="{lng p="traffic_add"}">
							<span class="input-group-text">MB</span>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<div class="accordion accordion-flush" id="prefs">
				<div class="accordion-item">
					<div class="accordion-header" id="heading-prefs">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-prefs" aria-expanded="false">{lng p="prefs"}</button>
					</div>
					<div id="collapse-prefs" class="accordion-collapse collapse" data-bs-parent="#prefs" style="">
						<div class="accordion-body pt-0">

							<div class="mb-3 row">
								<label class="col-sm-4 col-form-label">{lng p="re"}/{lng p="fwd"}</label>
								<div class="col-sm-4">
									<input type="text" class="form-control" name="re" value="{if isset($user.re)}{text value=$user.re allowEmpty=true}{/if}" placeholder="{lng p="re"}">
								</div>
								<div class="col-sm-4">
									<input type="text" class="form-control" name="fwd" value="{if isset($user.fwd)}{text value=$user.fwd allowEmpty=true}{/if}" placeholder="{lng p="fwd"}">
								</div>
							</div>
							<div class="mb-3 row">
								<label class="col-sm-4 col-form-label">{lng p="mail2sms"}</label>
								<div class="col-sm-8">
									<select name="mail2sms" class="form-select">
										<option value="yes"{if $user.mail2sms=='yes'} selected="selected"{/if}>{lng p="yes"}</option>
										<option value="no"{if $user.mail2sms=='no'} selected="selected"{/if}>{lng p="no"}</option>
									</select>
								</div>
							</div>
							<div class="mb-3 row">
								<label class="col-sm-4 col-form-label">{lng p="forward"}</label>
								<div class="col-sm-8">
									<select name="forward" class="form-select">
										<option value="yes"{if $user.forward=='yes'} selected="selected"{/if}>{lng p="yes"}</option>
										<option value="no"{if $user.forward=='no'} selected="selected"{/if}>{lng p="no"}</option>
									</select>
								</div>
							</div>
							<div class="mb-3 row">
								<label class="col-sm-4 col-form-label">{lng p="forwardto"}</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="forward_to" value="{email value=$user.forward_to}" placeholder="{lng p="forwardto"}">
								</div>
							</div>
							<div class="mb-3 row">
								<label class="col-sm-4 col-form-label">{lng p="newsletter"}</label>
								<div class="col-sm-8">
									<select name="newsletter_optin" class="form-select">
										<option value="yes"{if $user.newsletter_optin=='yes'} selected="selected"{/if}>{lng p="yes"}</option>
										<option value="no"{if $user.newsletter_optin=='no'} selected="selected"{/if}>{lng p="no"}</option>
									</select>
								</div>
							</div>
							<div class="mb-3 row">
								<label class="col-sm-4 col-form-label">{lng p="dateformat"}</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="datumsformat" value="{if isset($user.datumsformat)}{text value=$user.datumsformat allowEmpty=true}{/if}" placeholder="{lng p="dateformat"}">
								</div>
							</div>
							<div class="mb-3 row">
								<label class="col-sm-4 col-form-label">{lng p="sendername"}</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="absendername" value="{if isset($user.absendername)}{text value=$user.absendername allowEmpty=true}{/if}" placeholder="{lng p="sendername"}">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="accordion accordion-flush" id="aliasdomains">
				<div class="accordion-item">
					<div class="accordion-header" id="heading-aliasdomains">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-aliasdomains" aria-expanded="false">{lng p="aliasdomains"}</button>
					</div>
					<div id="collapse-aliasdomains" class="accordion-collapse collapse" data-bs-parent="#aliasdomains" style="">
						<div class="accordion-body pt-0">
							<div class="mb-3 row">
								<div class="col-sm-12">
									<textarea class="form-control" name="saliase">{text value=$user.saliase allowEmpty=true}</textarea>
									<small>{lng p="sepby"}</small>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="accordion accordion-flush" id="aliases">
				<div class="accordion-item">
					<div class="accordion-header" id="heading-aliases">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-aliases" aria-expanded="false">{lng p="aliases"}</button>
					</div>
					<div id="collapse-aliases" class="accordion-collapse collapse" data-bs-parent="#aliases" style="">
						<div class="accordion-body pt-0">
							<div class="table-responsive card">
								<div class="card">
									<div class="table-responsive">
										<table class="table table-vcenter table-striped">
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
													<td><a href="users.php?do=edit&id={$user.id}&deleteAlias={$alias.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a></td>
												</tr>
											{/foreach}
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="accordion accordion-flush" id="payments">
				<div class="accordion-item">
					<div class="accordion-header" id="heading-payments">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-payments" aria-expanded="false">{lng p="payments"}</a> ({lng p="max"} 15)</button>
					</div>
					<div id="collapse-payments" class="accordion-collapse collapse" data-bs-parent="#payments" style="">
						<div class="accordion-body pt-0">
							<div class="table-responsive card">
								<div class="card">
									<div class="table-responsive">
										<table class="table table-vcenter table-striped">
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
													<td align="center">{if $payment.status==1}<i class="fa-regular fa-square-check text-green"></i>{else}<i class="fa-solid fa-square-xmark text-red"></i>{/if}</td>
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
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div style="float: left;">{lng p="action"}:&nbsp;</div>
			<div style="float: left;">
				<div class="btn-group btn-group-sm">
					<select name="massAction" class="form-select form-select-sm">
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
					<input type="button" name="executeMassAction" value="{lng p="ok"}" onclick="executeAction('userAction');" class="btn btn-sm btn-dark-lt" />
				</div>
			</div>
		</div>
		<div class="col-md-6 text-end">
			<input class="btn btn-primary" type="submit" value=" {lng p="save"} " />
		</div>
	</div>
</form>