<div class="container">
	<div class="page-header"><h1>{lng p="signup"}</h1></div>

	<p>{if $signupText}{$signupText}{else}{lng p="signuptxt"}{/if} {if $code}{lng p="signuptxt_code"}{/if}</p>

	<div class="row"><div class="col-md-8"><form action="{if $ssl_signup_enable}{$ssl_url}{/if}index.php?action=signup" method="post" id="signupForm">
		<input type="hidden" name="do" value="createAccount" />
		<input type="hidden" name="transPostVars" value="true" />
		<input type="hidden" name="codeID" value="{$codeID}" />
	
		{if $errorStep}<div class="alert alert-danger" role="alert"><strong>{lng p="error"}:</strong> {$errorInfo}</div>{/if}

		{hook id="nli:signup.tpl:formStart"}

		<div class="panel-group" id="signup">

		{hook id="nli:signup.tpl:panelGroupStart"}

		<div class="panel panel-primary">
			<div class="panel-heading panel-title">
				<span class="glyphicon glyphicon-user"></span>
				<a data-toggle="collapse" data-parent="#signup" href="#signup-account">{lng p="wishaddressandpw"}</a>
			</div>
			<div class="panel-collapse collapse in" id="signup-account">
				<div class="panel-body">
					{if $f_anrede!="n"}
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label" for="salutation">
									{lng p="salutation"}
									{if $f_anrede=="p"}<span class="required">{lng p="required"}</span>{/if}
								</label>
								<select{if $f_anrede=="p"} required="required"{/if} class="form-control" name="salutation" id="salutation">
									<option value="">&nbsp;</option>
									<option value="herr"{if $_safePost.salutation=='herr'} selected="selected"{/if}>{lng p="mr"}</option>
									<option value="frau"{if $_safePost.salutation=='frau'} selected="selected"{/if}>{lng p="mrs"}</option>
								</select>
							</div>
						</div>
					</div>
					{/if}
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" for="firstname">
									{lng p="firstname"}
									<span class="required">{lng p="required"}</span>
								</label>
								<input type="text" class="form-control" required="true" name="firstname" id="firstname" value="{$_safePost.firstname}" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" for="surname">
									{lng p="surname"}
									<span class="required">{lng p="required"}</span>
								</label>
								<input type="text" class="form-control" required="true" name="surname" id="surname" value="{$_safePost.surname}" />
							</div>
						</div>
					</div>

					<hr />

					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="email_local">{lng p="wishaddress"}</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
									<input type="text" name="email_local" id="email_local" class="form-control" required="true" value="{$_safePost.email_local}" />
									<div class="input-group-btn">
										<input type="hidden" name="email_domain" id="email_domain" data-bind="email-domain" value="{if $_safePost.email_domain}{$_safePost.email_domain}{else}{domain value=$domainListSignup[0]}{/if}" />
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span data-bind="label">@{if !$_safePost.email_domain}{domain value=$domainListSignup[0]}{else}{$_safePost.email_domain}{/if}</span> <span class="caret"></span></button>
										<ul class="dropdown-menu dropdown-menu-right domainMenu" role="menu">
											{foreach from=$domainListSignup item=domain key=key}<li{if (!$_safePost.email_domain&&$key==0)||$_safePost.email_domain==$domain} class="active"{/if}><a href="#">@{domain value=$domain}</a></li>{/foreach}
										</ul>
									</div>
								</div>
							</div>

							<div class="alert alert-info" style="display:none;" role="alert" id="email_alert"></div>
						</div>
						<div class="col-md-6">
							
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" for="pass1">
									{lng p="password"}
									<span class="required">{lng p="required"}, {$minPassText}</span>
								</label>
								<input type="password" data-min-length="{$minPassLength}" class="form-control" required="true" autocomplete="off" name="pass1" id="pass1" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" for="pass2">
									{lng p="repeat"}
									<span class="required">{lng p="required"}</span>
								</label>
								<input type="password" class="form-control" required="true" autocomplete="off" name="pass2" id="pass2" />
							</div>
						</div>
					</div>

					{if !$errorStep}<button class="btn btn-success pull-right" data-role="next-block" type="button">
						{lng p="next"} <span class="glyphicon glyphicon-chevron-right"></span>
					</button>{/if}
				</div>
			</div>
		</div>

		{if $f_strasse!="n"}
		<div class="panel panel-default">
			<div class="panel-heading panel-title">
				<span class="glyphicon glyphicon-home"></span>
				<a data-toggle="collapse" data-parent="#signup" href="#signup-address">{lng p="address"}</a>
			</div>
			<div class="panel-collapse collapse" id="signup-address">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label class="control-label" for="street">
									{lng p="street"}
									{if $f_strasse=="p"}<span class="required">{lng p="required"}</span>{/if}
								</label>
								<input type="text" class="form-control"{if $f_strasse=="p"} required="true"{/if} name="street" id="street" value="{$_safePost.street}" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label" for="no">
									{lng p="nr"}
									{if $f_strasse=="p"}<span class="required">{lng p="required"}</span>{/if}
								</label>
								<input type="text" class="form-control"{if $f_strasse=="p"} required="true"{/if} name="no" id="no" value="{$_safePost.no}" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label" for="zip">
									{lng p="zip"}
									{if $f_strasse=="p"}<span class="required">{lng p="required"}</span>{/if}
								</label>
								<input type="text" class="form-control"{if $f_strasse=="p"} required="true"{/if} name="zip" id="zip" value="{$_safePost.zip}" />
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
								<label class="control-label" for="city">
									{lng p="city"}
									{if $f_strasse=="p"}<span class="required">{lng p="required"}</span>{/if}
								</label>
								<input type="text" class="form-control"{if $f_strasse=="p"} required="true"{/if} name="city" id="city" value="{$_safePost.city}" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label" for="country">
									{lng p="country"}
									{if $f_strasse=="p"}<span class="required">{lng p="required"}</span>{/if}
								</label>
								<select class="form-control" name="country" id="country">
									{foreach from=$countryList item=country key=id}
									<option value="{$id}"{if (!$_safePost.country && $id==$defaultCountry) || ($_safePost.country==$id)} selected="selected"{/if}>{$country}</option>
									{/foreach}
								</select>
							</div>
						</div>
					</div>

					{if !$errorStep&&($f_safecode!='n'||$code||$profilfelder||$f_telefon!='n'||$f_fax!='n'||$f_mail2sms_nummer!='n'||$f_alternativ!='n')}<button class="btn btn-success pull-right" data-role="next-block" type="button">
						{lng p="next"} <span class="glyphicon glyphicon-chevron-right"></span>
					</button>{/if}
				</div>
			</div>
		</div>
		{/if}

		{if $f_telefon!='n'||$f_fax!='n'||$f_mail2sms_nummer!='n'||$f_alternativ!='n'}
		<div class="panel panel-default">
			<div class="panel-heading panel-title">
				<span class="glyphicon glyphicon-earphone"></span>
				<a data-toggle="collapse" data-parent="#signup" href="#signup-contact">{lng p="contactinfo"}</a>
			</div>
			<div class="panel-collapse collapse" id="signup-contact">
				<div class="panel-body">
					{if $f_telefon!='n'||$f_fax!='n'||$f_mail2sms_nummer!='n'}
					<div class="row">
						{if $f_telefon!='n'}
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" for="phone">
									{lng p="phone"}
									{if $f_telefon=='p'}<span class="required">{lng p="required"}</span>{/if}
								</label>
								<input type="text" class="form-control"{if $f_telefon=='p'} required="true"{/if} name="phone" id="phone" value="{$_safePost.phone}" />
							</div>
						</div>
						{/if}
						{if $f_fax!='n'}
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" for="fax">
									{lng p="fax"}
									{if $f_fax=='p'}<span class="required">{lng p="required"}</span>{/if}
								</label>
								<input type="text" class="form-control"{if $f_fax=='p'} required="true"{/if} name="fax" id="fax" value="{$_safePost.fax}" />
							</div>
						</div>
						{/if}
						{if $f_mail2sms_nummer!='n'}
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" for="mail2sms_nummer">
									{lng p="mobile"}
									{if $f_mail2sms_nummer=='p'}<span class="required">{lng p="required"}</span>{/if}
								</label>
								<input type="text" class="form-control"{if $f_mail2sms_nummer=='p'} required="true"{/if} name="mail2sms_nummer" id="mail2sms_nummer" value="{$_safePost.mail2sms_nummer}" />
							</div>
						</div>
						{/if}
					</div>
					{/if}

					{if $f_alternativ!='n'}
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" for="altmail">
									{lng p="altmail2"}
									{if $f_alternativ=='p'}<span class="required">{lng p="required"}</span>{/if}
								</label>
								<input type="email" class="form-control"{if $f_alternativ=='p'} required="true"{/if} name="altmail" id="altmail" value="{$_safePost.altmail}" />
							</div>
						</div>
					</div>
					{/if}

					{if !$errorStep&&($f_safecode!='n'||$code||$profilfelder)}<button class="btn btn-success pull-right" data-role="next-block" type="button">
						{lng p="next"} <span class="glyphicon glyphicon-chevron-right"></span>
					</button>{/if}
				</div>
			</div>
		</div>
		{/if}

		{if $profilfelder}
		<div class="panel panel-default">
			<div class="panel-heading panel-title">
				<span class="glyphicon glyphicon-list-alt"></span>
				<a data-toggle="collapse" data-parent="#signup" href="#signup-misc">{lng p="misc"}</a>
			</div>
			<div class="panel-collapse collapse" id="signup-misc">
				<div class="panel-body">
					{foreach from=$profilfelder item=feld}
					{assign var=fieldID value=$feld.id}
					{assign var=fieldName value="field_$fieldID"}
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								{if $feld.typ!=2}<label class="control-label" for="{$fieldName}">
									{$feld.feld}
									{if $feld.pflicht}<span class="required">{lng p="required"}</span>{/if}
								</label>{/if}

								{if $feld.typ==1}
								<input{if $feld.pflicht} required="true"{/if} class="form-control" name="{$fieldName}" id="{$fieldName}" value="{$_safePost[$fieldName]}" type="text" />
								{elseif $feld.typ==2}
								<label class="control-label">
									<input type="checkbox" name="{$fieldName}" id="{$fieldName}"{if $_safePost[$fieldName]} checked="checked"{/if} />
									{$feld.feld}
								</label>
								{elseif $feld.typ==4}
								<select class="form-control" name="{$fieldName}" id="{$fieldName}">
									{foreach from=$feld.extra item=item}
									<option value="{$item}"{if $_safePost[$fieldName]==$item} selected="selected"{/if}>{$item}</option>
									{/foreach}
								</select>
								{elseif $feld.typ==8}
									{foreach from=$feld.extra item=item}
									<div class="radio">
										<label>
											<input type="radio" id="{$fieldName}_{$item}" name="{$fieldName}" value="{$item}"{if $_safePost[$fieldName]==$item} checked="checked"{/if} />
											{$item}
										</label> 
									</div>
									{/foreach}
								{else if $feld.typ==32}
									<div>{if $feld.pflicht}{assign var="all_extra" value='required="true"'}{else}{assign var="all_extra" value=""}{/if}{if $dateFields[$fieldName]}
									{html_select_date time=$dateFields[$fieldName] year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix="$fieldName" field_order="DMY" class="form-control" style="width:auto;display:inline-block;" all_extra="$all_extra"}
									{else}
									{html_select_date time="---" year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix="$fieldName" field_order="DMY" class="form-control" style="width:auto;display:inline-block;" all_extra="$all_extra"}
									{/if}</div>
								{/if}
							</div>
						</div>
					</div>
					{/foreach}

					{if !$errorStep&&($f_safecode!='n'||$code)}<button class="btn btn-success pull-right" data-role="next-block" type="button">
						{lng p="next"} <span class="glyphicon glyphicon-chevron-right"></span>
					</button>{/if}
				</div>
			</div>
		</div>
		{/if}

		{if $code}
		<div class="panel panel-default">
			<div class="panel-heading panel-title">
				<span class="glyphicon glyphicon-barcode"></span>
				<a data-toggle="collapse" data-parent="#signup" href="#signup-code">{lng p="code"}</a>
			</div>
			<div class="panel-collapse collapse" id="signup-code">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" for="code">
									{lng p="code"}
								</label>
								<input type="text" class="form-control" value="{$_safePost.code}" name="code" id="code" data-toggle="tooltip" data-placement="bottom" title="{lng p="signuptxt_code"}" />
							</div>
						</div>
					</div>

					{if !$errorStep&&($f_safecode!='n')}<button class="btn btn-success pull-right" data-role="next-block" type="button">
						{lng p="next"} <span class="glyphicon glyphicon-chevron-right"></span>
					</button>{/if}
				</div>
			</div>
		</div>
		{/if}

		{if $f_safecode!='n'}
		<div class="panel panel-default">
			<div class="panel-heading panel-title">
				<span class="glyphicon glyphicon-flag"></span>
				<a data-toggle="collapse" data-parent="#signup" href="#signup-finish">{lng p="completesignup"}</a>
			</div>
			<div class="panel-collapse collapse" id="signup-finish">
				<div class="panel-body">
					<div class="row">
						{if $captchaInfo.hasOwnInput}
						<div class="col-md-12">
							<div class="form-group" id="captchaContainer">
								<label class="control-label">
									{lng p="safecode"}
									<span class="required">{lng p="required"}</span>
								</label>
								{$captchaHTML}
							</div>
						</div>
						{else}
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label" for="safecode">
									{lng p="safecode"}
									<span class="required">{lng p="required"}</span>
								</label>
								<input type="text" class="form-control" required="true" name="safecode" id="safecode" />
							</div>
						</div>
						<div class="col-md-6" id="captchaContainer">
							{$captchaHTML}
						</div>
						{/if}
					</div>
				</div>
			</div>
		</div>
		{/if}

		</div>

		<div class="alert alert-info">
			<span class="glyphicon glyphicon-info-sign"></span>
			{lng p="iprecord"}
		</div>

		<div class="form-group">
			<label class="control-label">
				<input type="checkbox" name="tos" value="true"{if $_safePost.tos=='true'} checked="checked"{/if} />
				{lng p="accepttos"}
				<a href="#" data-toggle="modal" data-target="#tosModal">{lng p="tos"}</a>
			</label>
			<button type="submit" id="signupSubmit" class="btn btn-success pull-right" data-loading-text="{lng p="pleasewait"}">
				<span class="glyphicon glyphicon-ok"></span> {lng p="submit"}
			</button>
		</div>
	</form></div></div>
</div>

<div class="modal fade" id="tosModal" tabindex="-1" role="dialog" aria-labelledby="tosLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{lng p="close"}</span></button>
				<h4 class="modal-title" id="tosLabel">{lng p="tos"}</h4>
			</div>
			<div class="modal-body" style="max-height:400px;overflow-y:auto;">
				{$tos_html}
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">{lng p="close"}</button>
			</div>
		</div>
	</div>
</div>

{if $signupSuggestions}<div class="modal fade" id="suggestionsModal" tabindex="-1" role="dialog" aria-labelledby="suggestionsLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{lng p="close"}</span></button>
				<h4 class="modal-title" id="suggestionsLabel">{lng p="suggestions"}</h4>
			</div>
			<div class="modal-body" id="suggestionsBody"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">
					<span class="glyphicon glyphicon-remove"></span>
					{lng p="nothanks"}
				</button>
			</div>
		</div>
	</div>
</div>{/if}

<script src="{$tpldir}js/nli.signup.js?{fileDateSig file="js/nli.signup.js"}"></script>
<script>
<!--
	$(document).ready(function() {ldelim}
	{if $errorStep}
	{foreach from=$invalidFields item=field}
	markFieldAsInvalid('{$field}');
	{/foreach}
	{if $f_safecode!='n'}
	markFieldAsInvalid('safecode');
	{/if}
	markFieldAsInvalid('pass1');
	markFieldAsInvalid('pass2');
	checkEMailAvailability();
	{else}
	if($('#salutation').length) $('#salutation').focus();
	else if($('#firstname').length) $('#firstname').focus();
	{/if}
	{rdelim});
//-->
</script>
