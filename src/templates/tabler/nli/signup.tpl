{include file="nli/signup.page.open.tpl"}
<h1 class="mb-3">{lng p="signup"}</h1>

<p class="text-secondary mb-4">{if $signupText}{$signupText}{else}{lng p="signuptxt"}{/if} {if $code}{lng p="signuptxt_code"}{/if}</p>

{if !$nliCompactLayout|default:false}<form action="{if $ssl_signup_enable}{$ssl_url}{/if}index.php?action=signup" method="post" id="signupForm">{/if}
		<input type="hidden" name="do" value="createAccount" />
		<input type="hidden" name="transPostVars" value="true" />
		<input type="hidden" name="codeID" value="{$codeID}" />
	
		{if isset($errorStep)}<div class="alert alert-danger" role="alert"><strong>{lng p="error"}:</strong> {$errorInfo}</div>{/if}

		{hook id="nli:signup.tpl:formStart"}

		{if isset($_safePost.email_domain) && $_safePost.email_domain != ''}{assign var="signupEmailDomain" value=$_safePost.email_domain}{else}{assign var="signupEmailDomain" value=$domainListSignup[0]}{/if}

		<div class="accordion bm-signup-wizard" id="signup">

		{hook id="nli:signup.tpl:panelGroupStart"}

		<div class="accordion-item bm-signup-step bm-signup-step-active" data-signup-target="signup-account" data-signup-email="1">
			<div class="accordion-header bm-signup-step-head" id="signup-head-account">
				<span class="badge bg-primary text-primary-fg bm-signup-step-badge">1</span>
				<i class="ti ti-user me-2 text-primary" aria-hidden="true"></i>
				<span class="bm-signup-step-title fw-semibold">{lng p="wishaddressandpw"}</span>
				<i class="ti ti-check bm-signup-step-check ms-auto text-success" aria-hidden="true"></i>
			</div>
			<div id="signup-account" class="accordion-collapse collapse show" aria-labelledby="signup-head-account" data-bs-parent="#signup">
				<div class="accordion-body">
					{if $f_anrede!="n"}
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-label{if $f_anrede=="p"} required{/if}" for="salutation">{lng p="salutation"}</label>
								<select{if $f_anrede=="p"} required="required"{/if} class="form-control" name="salutation" id="salutation">
									<option value="">&nbsp;</option>
									<option value="herr"{if isset($_safePost.salutation) && $_safePost.salutation=='herr'} selected="selected"{/if}>{lng p="mr"}</option>
									<option value="frau"{if isset($_safePost.salutation) && $_safePost.salutation=='frau'} selected="selected"{/if}>{lng p="mrs"}</option>
								</select>
							</div>
						</div>
					</div>
					{/if}
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label required" for="firstname">{lng p="firstname"}</label>
								<input type="text" class="form-control" required="true" name="firstname" id="firstname" value="{if isset($_safePost.firstname)}{$_safePost.firstname}{/if}" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label required" for="surname">{lng p="surname"}</label>
								<input type="text" class="form-control" required="true" name="surname" id="surname" value="{if isset($_safePost.surname)}{$_safePost.surname}{/if}" />
							</div>
						</div>
					</div>

					<div class="row bm-signup-section-email">
						<div class="col-12">
							<div class="mb-3">
								<label class="form-label required" for="email_local">{lng p="wishaddress"}</label>
								<div class="input-group nli-domain-group">
									<span class="input-group-text text-muted"><i class="ti ti-mail" aria-hidden="true"></i></span>
									<input type="text" name="email_local" id="email_local" class="form-control" required="true" autocomplete="username" value="{if isset($_safePost.email_local)}{$_safePost.email_local}{/if}" />
									<input type="hidden" name="email_domain" id="email_domain" data-bind="email-domain" value="{domain value=$signupEmailDomain}" />
									<button type="button" class="btn btn-outline-secondary dropdown-toggle nli-domain-btn" data-bs-toggle="dropdown" aria-expanded="false"><span data-bind="label">@{domain value=$signupEmailDomain}</span></button>
									<ul class="dropdown-menu dropdown-menu-end domainMenu">
										{foreach from=$domainListSignup item=domain key=key}<li{if (empty($_safePost.email_domain) && $key==0) || (isset($_safePost.email_domain) && $_safePost.email_domain==$domain)} class="active"{/if}><a class="dropdown-item" href="#">@{domain value=$domain}</a></li>{/foreach}
									</ul>
								</div>
							</div>
							<div class="alert alert-info d-none mb-3" role="alert" id="email_alert"></div>
							{if $signupSuggestions}
							<div id="email_suggestions_wrap" class="d-none mt-3">
								<div id="email_suggestions_content" class="bm-email-suggestions"></div>
							</div>
							{/if}
						</div>
					</div>

					<div class="row bm-signup-section-password g-3">
						<div class="col-md-6">
							<div class="form-group mb-0">
								<label class="form-label required" for="pass1">
									{lng p="password"}
									<span class="form-label-description">{$minPassText}</span>
								</label>
								<input type="password" data-min-length="{$minPassLength}" class="form-control" required="true" autocomplete="new-password" name="pass1" id="pass1"{if isset($_safePost.pass1)} value="{$_safePost.pass1}"{/if} />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group mb-0">
								<label class="form-label required" for="pass2">{lng p="repeat"}</label>
								<input type="password" class="form-control" required="true" autocomplete="new-password" name="pass2" id="pass2"{if isset($_safePost.pass2)} value="{$_safePost.pass2}"{/if} />
							</div>
						</div>
					</div>

				</div>
				<div class="card-footer bm-signup-step-footer py-3">
					{include file="nli/signup.step-nav.tpl"}
				</div>
			</div>
		</div>

		{if $f_strasse!="n"}
		<div class="accordion-item bm-signup-step" data-signup-target="signup-address">
			<div class="accordion-header bm-signup-step-head" id="signup-head-address">
				<span class="badge bg-primary text-primary-fg bm-signup-step-badge">2</span>
				<i class="ti ti-home me-2 text-primary" aria-hidden="true"></i>
				<span class="bm-signup-step-title fw-semibold">{lng p="address"}</span>
				<i class="ti ti-check bm-signup-step-check ms-auto text-success" aria-hidden="true"></i>
			</div>
			<div id="signup-address" class="accordion-collapse collapse" aria-labelledby="signup-head-address" data-bs-parent="#signup">
				<div class="accordion-body">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label class="form-label{if isset($f_strasse) && $f_strasse=="p"} required{/if}" for="street">{lng p="street"}</label>
								<input type="text" class="form-control"{if isset($f_strasse) && $f_strasse=="p"} required="true"{/if} name="street" id="street" value="{if isset($_safePost.street)}{$_safePost.street}{/if}" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-label{if isset($f_strasse) && $f_strasse=="p"} required{/if}" for="no">{lng p="nr"}</label>
								<input type="text" class="form-control"{if isset($f_strasse) && $f_strasse=="p"} required="true"{/if} name="no" id="no" value="{if isset($_safePost.no)}{$_safePost.no}{/if}" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-label{if isset($f_strasse) && $f_strasse=="p"} required{/if}" for="zip">{lng p="zip"}</label>
								<input type="text" class="form-control"{if isset($f_strasse) && $f_strasse=="p"} required="true"{/if} name="zip" id="zip" value="{if isset($_safePost.zip)}{$_safePost.zip}{/if}" />
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
								<label class="form-label{if isset($f_strasse) && $f_strasse=="p"} required{/if}" for="city">{lng p="city"}</label>
								<input type="text" class="form-control"{if isset($f_strasse) && $f_strasse=="p"} required="true"{/if} name="city" id="city" value="{if isset($_safePost.city)}{$_safePost.city}{/if}" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="form-label{if isset($f_strasse) && $f_strasse=="p"} required{/if}" for="country">{lng p="country"}</label>
								<select class="form-control" name="country" id="country">
									{foreach from=$countryList item=country key=id}
									<option value="{$id}"{if (!$_safePost.country && $id==$defaultCountry) || ($_safePost.country==$id)} selected="selected"{/if}>{$country}</option>
									{/foreach}
								</select>
							</div>
						</div>
					</div>

				</div>
				<div class="card-footer bm-signup-step-footer py-3">
					{include file="nli/signup.step-nav.tpl"}
				</div>
			</div>
		</div>
		{/if}

		{if $f_telefon!='n'||$f_fax!='n'||$f_mail2sms_nummer!='n'||$f_alternativ!='n'}
		<div class="accordion-item bm-signup-step" data-signup-target="signup-contact">
			<div class="accordion-header bm-signup-step-head" id="signup-head-contact">
				<span class="badge bg-primary text-primary-fg bm-signup-step-badge">3</span>
				<i class="ti ti-phone me-2 text-primary" aria-hidden="true"></i>
				<span class="bm-signup-step-title fw-semibold">{lng p="contactinfo"}</span>
				<i class="ti ti-check bm-signup-step-check ms-auto text-success" aria-hidden="true"></i>
			</div>
			<div id="signup-contact" class="accordion-collapse collapse" aria-labelledby="signup-head-contact" data-bs-parent="#signup">
				<div class="accordion-body">
					{if $f_telefon!='n'||$f_fax!='n'||$f_mail2sms_nummer!='n'}
					<div class="row">
						{if $f_telefon!='n'}
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label{if isset($f_telefon) && $f_telefon=='p'} required{/if}" for="phone">{lng p="phone"}</label>
								<input type="text" class="form-control"{if isset($f_telefon) && $f_telefon=='p'} required="true"{/if} name="phone" id="phone" value="{if isset($_safePost.phone)}{$_safePost.phone}{/if}" />
							</div>
						</div>
						{/if}
						{if isset($f_fax) && $f_fax!='n'}
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label{if $f_fax=='p'} required{/if}" for="fax">{lng p="fax"}</label>
								<input type="text" class="form-control"{if isset($f_fax) && $f_fax=='p'} required="true"{/if} name="fax" id="fax" value="{if isset($_safePost.fax)}{$_safePost.fax}{/if}" />
							</div>
						</div>
						{/if}
						{if isset($f_mail2sms_nummer) && $f_mail2sms_nummer!='n'}
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label{if $f_mail2sms_nummer=='p'} required{/if}" for="mail2sms_nummer">{lng p="mobile"}</label>
								<input type="text" class="form-control"{if $f_mail2sms_nummer=='p'} required="true"{/if} name="mail2sms_nummer" id="mail2sms_nummer" value="{if isset($_safePost.mail2sms_nummer)}{$_safePost.mail2sms_nummer}{/if}" />
							</div>
						</div>
						{/if}
					</div>
					{/if}

					{if isset($f_alternativ) && $f_alternativ!='n'}
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label{if $f_alternativ=='p'} required{/if}" for="altmail">{lng p="altmail2"}</label>
								<input type="email" class="form-control"{if $f_alternativ=='p'} required="true"{/if} name="altmail" id="altmail" value="{if isset($_safePost.altmail)}{$_safePost.altmail}{/if}" />
							</div>
						</div>
					</div>
					{/if}

				</div>
				<div class="card-footer bm-signup-step-footer py-3">
					{include file="nli/signup.step-nav.tpl"}
				</div>
			</div>
		</div>
		{/if}

		{if $profilfelder}
		<div class="accordion-item bm-signup-step" data-signup-target="signup-misc">
			<div class="accordion-header bm-signup-step-head" id="signup-head-misc">
				<span class="badge bg-primary text-primary-fg bm-signup-step-badge">4</span>
				<i class="ti ti-list-details me-2 text-primary" aria-hidden="true"></i>
				<span class="bm-signup-step-title fw-semibold">{lng p="misc"}</span>
				<i class="ti ti-check bm-signup-step-check ms-auto text-success" aria-hidden="true"></i>
			</div>
			<div id="signup-misc" class="accordion-collapse collapse" aria-labelledby="signup-head-misc" data-bs-parent="#signup">
				<div class="accordion-body">
					{foreach from=$profilfelder item=feld}
					{assign var=fieldID value=$feld.id}
					{assign var=fieldName value="field_$fieldID"}
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								{if isset($feld.typ) && $feld.typ!=2}<label class="form-label{if $feld.pflicht} required{/if}" for="{$fieldName}">{$feld.feld}</label>{/if}

								{if isset($feld.typ) && $feld.typ==1}
								<input{if $feld.pflicht} required="true"{/if} class="form-control" name="{$fieldName}" id="{$fieldName}" value="{if isset($_safePost[$fieldName])}{$_safePost[$fieldName]}{/if}" type="text" />
								{elseif isset($feld.typ) && $feld.typ==2}
								<label class="form-check">
									<input type="checkbox" class="form-check-input" name="{$fieldName}" id="{$fieldName}"{if $_safePost[$fieldName]} checked="checked"{/if} />
									<span class="form-check-label">{$feld.feld}</span>
								</label>
								{elseif isset($feld.typ) && $feld.typ==4}
								<select class="form-control" name="{$fieldName}" id="{$fieldName}">
									{foreach from=$feld.extra item=item}
									<option value="{$item}"{if isset($_safePost[$fieldName]) && $_safePost[$fieldName]==$item} selected="selected"{/if}>{$item}</option>
									{/foreach}
								</select>
								{elseif isset($feld.typ) && $feld.typ==8}
									{foreach from=$feld.extra item=item}
									<div class="radio">
										<label>
											<input type="radio" id="{$fieldName}_{$item}" name="{$fieldName}" value="{$item}"{if isset($_safePost[$fieldName]) && $_safePost[$fieldName]==$item} checked="checked"{/if} />
											{$item}
										</label> 
									</div>
									{/foreach}
								{else if isset($feld.typ) && $feld.typ==32}
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

				</div>
				<div class="card-footer bm-signup-step-footer py-3">
					{include file="nli/signup.step-nav.tpl"}
				</div>
			</div>
		</div>
		{/if}

		{if $code}
		<div class="accordion-item bm-signup-step" data-signup-target="signup-code">
			<div class="accordion-header bm-signup-step-head" id="signup-head-code">
				<span class="badge bg-primary text-primary-fg bm-signup-step-badge">5</span>
				<i class="ti ti-ticket me-2 text-primary" aria-hidden="true"></i>
				<span class="bm-signup-step-title fw-semibold">{lng p="code"}</span>
				<i class="ti ti-check bm-signup-step-check ms-auto text-success" aria-hidden="true"></i>
			</div>
			<div id="signup-code" class="accordion-collapse collapse" aria-labelledby="signup-head-code" data-bs-parent="#signup">
				<div class="accordion-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label" for="code">{lng p="code"}</label>
								<input type="text" class="form-control" value="{if isset($_safePost.code)}{$_safePost.code}{/if}" name="code" id="code" data-bs-toggle="tooltip" data-placement="bottom" title="{lng p="signuptxt_code"}" />
							</div>
						</div>
					</div>

				</div>
				<div class="card-footer bm-signup-step-footer py-3">
					{include file="nli/signup.step-nav.tpl"}
				</div>
			</div>
		</div>
		{/if}

		{if isset($f_safecode) && $f_safecode!='n'}
		<div class="accordion-item bm-signup-step" data-signup-target="signup-finish">
			<div class="accordion-header bm-signup-step-head" id="signup-head-finish">
				<span class="badge bg-primary text-primary-fg bm-signup-step-badge">6</span>
				<i class="ti ti-shield-check me-2 text-primary" aria-hidden="true"></i>
				<span class="bm-signup-step-title fw-semibold">{lng p="completesignup"}</span>
				<i class="ti ti-check bm-signup-step-check ms-auto text-success" aria-hidden="true"></i>
			</div>
			<div id="signup-finish" class="accordion-collapse collapse" aria-labelledby="signup-head-finish" data-bs-parent="#signup">
				<div class="accordion-body">
					<div class="row">
						{if $captchaInfo.hasOwnInput}
						<div class="col-md-12">
							<div class="form-group" id="captchaContainer">
								<label class="form-label required">{lng p="safecode"}</label>
								{$captchaHTML}
							</div>
						</div>
						{else}
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label required" for="safecode">{lng p="safecode"}</label>
								<input type="text" class="form-control" required="true" name="safecode" id="safecode" />
							</div>
						</div>
						<div class="col-md-6" id="captchaContainer">
							{$captchaHTML}
						</div>
						{/if}
					</div>
				</div>
				<div class="card-footer bm-signup-step-footer py-3">
					{include file="nli/signup.step-nav.tpl"}
				</div>
			</div>
		</div>
		{/if}

		</div>

{if !$nliCompactLayout|default:false}
		<div id="signupFinishArea" class="bm-signup-finish mt-4">
			<div class="text-secondary small mb-3">
				<i class="ti ti-info-circle me-1" aria-hidden="true"></i>
				{lng p="iprecord"}
			</div>
			<p id="signupFinishHint" class="text-secondary small d-none mb-3">{lng p="completesignup"}</p>
			<div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
				<label class="form-check mb-0">
					<input type="checkbox" class="form-check-input" name="tos" value="true"{if isset($_safePost.tos) && $_safePost.tos=='true'} checked="checked"{/if} />
					<span class="form-check-label">
						{lng p="accepttos"}
						<a href="#" data-bs-toggle="modal" data-bs-target="#tosModal">{lng p="tos"}</a>
					</span>
				</label>
				<button type="submit" id="signupSubmit" class="btn btn-success">
					<i class="ti ti-check me-1" aria-hidden="true"></i>{lng p="submit"}
				</button>
			</div>
		</div>
	</form>
{/if}
{include file="nli/signup.page.close.tpl"}

<div class="modal modal-blur fade" id="tosModal" tabindex="-1" role="dialog" aria-labelledby="tosLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="visually-hidden">{lng p="close"}</span></button>
				<h4 class="modal-title" id="tosLabel">{lng p="tos"}</h4>
			</div>
			<div class="modal-body" style="max-height:400px;overflow-y:auto;">
				{$tos_html}
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-bs-dismiss="modal">{lng p="close"}</button>
			</div>
		</div>
	</div>
</div>

{if $signupSuggestions}<div class="modal modal-blur fade" id="suggestionsModal" tabindex="-1" aria-labelledby="suggestionsLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="suggestionsLabel">{lng p="suggestions"}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{lng p="close"}"></button>
			</div>
			<div class="modal-body" id="suggestionsBody"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">{lng p="nothanks"}</button>
			</div>
		</div>
	</div>
</div>{/if}

<script src="{$tpldir}js/nli.signup.js?{fileDateSig file="js/nli.signup.js"}"></script>
<script>
<!--
	$(document).ready(function() {ldelim}
	{if isset($errorStep)}
	{foreach from=$invalidFields item=field}
	markFieldAsInvalid('{$field}');
	{/foreach}
	checkEMailAvailability();
	var $errField = $('#signupForm .is-invalid').first();
	if($errField.length && $errField.closest('.bm-signup-step').length)
		bmSignupShowStep($errField.closest('.bm-signup-step'));
	{else}
	setTimeout(function() {ldelim}
		if($('#salutation').length) $('#salutation').focus();
		else if($('#firstname').length) $('#firstname').focus();
	{rdelim}, 150);
	{/if}
	{rdelim});
//-->
</script>
