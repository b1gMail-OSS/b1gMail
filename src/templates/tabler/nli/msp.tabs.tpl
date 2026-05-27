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
{/if}

<div class="card-header">
	<ul class="nav nav-pills card-header-pills">
		<li class="nav-item">
			<a class="nav-link{if $nliAction=='login'} active{/if}" href="index.php">
				<i class="icon nav-link-icon icon-2 ti ti-home" aria-hidden="true"></i>
				{lng p="home"}
			</a>
		</li>
		{if $_regEnabled||(!$templatePrefs.hideSignup)}
		<li class="nav-item">
			<a class="nav-link{if $nliAction=='signup'} active{/if}" href="{if $ssl_signup_enable}{$ssl_url}{/if}index.php?action=signup">
				<i class="icon nav-link-icon icon-2 ti ti-user-plus" aria-hidden="true"></i>
				{lng p="signup"}
			</a>
		</li>
		{/if}
		<li class="nav-item">
			<a class="nav-link{if $nliAction=='faq'} active{/if}" href="index.php?action=faq">
				<i class="icon nav-link-icon icon-2 ti ti-help" aria-hidden="true"></i>
				{lng p="faq"}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link{if $nliAction=='tos'} active{/if}" href="index.php?action=tos">
				<i class="icon nav-link-icon icon-2 ti ti-file-text" aria-hidden="true"></i>
				{lng p="tos"}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link{if $nliAction=='imprint'} active{/if}" href="index.php?action=imprint">
				<i class="icon nav-link-icon icon-2 ti ti-address-book" aria-hidden="true"></i>
				{lng p="contact"}
			</a>
		</li>
	{foreach from=$pluginUserPages item=item}{if !$item.top|default:false}
		<li class="nav-item">
			{assign var="_pluginLink" value=$item.link|default:''}
			<a class="nav-link{if $item.active|default:false || ($nliAction != '' && ($_pluginLink|replace:$nliAction:'' != $_pluginLink))} active{/if}" href="{$item.link}" title="{text value=$item.text}">
				<i class="icon nav-link-icon icon-2 ti ti-link" aria-hidden="true"></i>
				{$item.text}
			</a>
		</li>
	{/if}{/foreach}
	</ul>
</div>
