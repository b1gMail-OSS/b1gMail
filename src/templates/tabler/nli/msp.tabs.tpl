{assign var="nliAction" value=$smarty.request.action|default:''}
{if $page=='nli/login.tpl' || $page=='nli/login.smsvalidation.tpl' || $page=='nli/loginresult.tpl'}
	{assign var="nliAction" value='login'}
{elseif $page=='nli/signup.tpl' || $page=='nli/regdone.tpl'}
	{assign var="nliAction" value='signup'}
{elseif $page=='nli/faq.tpl'}
	{assign var="nliAction" value='faq'}
{elseif $page=='nli/tos.tpl'}
	{assign var="nliAction" value='tos'}
{elseif $page=='nli/imprint.tpl' || $page=='nli/contact.complete.tpl'}
	{assign var="nliAction" value='imprint'}
{elseif ($smarty.request.action|default:'') == 'faq'}
	{assign var="nliAction" value='faq'}
{elseif ($smarty.request.action|default:'') == 'support' || ($smarty.request.action|default:'') == 'ticket'}
	{assign var="nliAction" value='support'}
{elseif ($smarty.request.action|default:'') == 'blog'}
	{assign var="nliAction" value='blog'}
{elseif ($smarty.request.action|default:'') == 'newsPlugin'}
	{assign var="nliAction" value='newsPlugin'}
{/if}

<div class="card-header">
	<ul class="nav nav-pills card-header-pills">
		<li class="nav-item">
			<a class="nav-link{if $nliAction=='login'} active{/if}" href="{$nliUrlHome}">
				<i class="icon nav-link-icon icon-2 ti ti-home" aria-hidden="true"></i>
				{lng p="home"}
			</a>
		</li>
		{if $_regEnabled||(!$templatePrefs.hideSignup)}
		<li class="nav-item">
			<a class="nav-link{if $nliAction=='signup'} active{/if}" href="{if $ssl_signup_enable}{$nliUrlSignupSsl}{else}{$nliUrlSignup}{/if}">
				<i class="icon nav-link-icon icon-2 ti ti-user-plus" aria-hidden="true"></i>
				{lng p="signup"}
			</a>
		</li>
		{/if}
		<li class="nav-item">
			<a class="nav-link{if $nliAction=='faq'} active{/if}" href="{$nliUrlFaq}">
				<i class="icon nav-link-icon icon-2 ti ti-help" aria-hidden="true"></i>
				{lng p="faq"}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link{if $nliAction=='tos'} active{/if}" href="{$nliUrlTos}">
				<i class="icon nav-link-icon icon-2 ti ti-file-text" aria-hidden="true"></i>
				{lng p="tos"}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link{if $nliAction=='imprint'} active{/if}" href="{$nliUrlImprint}">
				<i class="icon nav-link-icon icon-2 ti ti-address-book" aria-hidden="true"></i>
				{lng p="contact"}
			</a>
		</li>
	{foreach from=$pluginUserPages item=item}{if !$item.top|default:false && !$item.footerOnly|default:false}
		<li class="nav-item">
			{assign var="_pluginLink" value=$item.link|default:''}
			<a class="nav-link{if $item.active|default:false || ($nliAction != '' && ($_pluginLink|replace:$nliAction:'' != $_pluginLink))} active{/if}" href="{$item.link}" title="{text value=$item.text}">
				{include file="li/icon.tpl" faIcon=$item.faIcon|default:'fa-puzzle-piece' iconClass='icon nav-link-icon icon-2'}
				{$item.text}
			</a>
		</li>
	{/if}{/foreach}
	</ul>
</div>
