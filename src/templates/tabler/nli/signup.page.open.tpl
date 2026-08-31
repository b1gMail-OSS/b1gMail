{if !isset($nliCompactLayout)}{include file="nli/layout.vars.tpl" scope=parent}{/if}
{if $nliCompactLayout}
<div class="page-body nli-msp-layout">
	<div class="container-xl py-4">
		<div class="text-center mb-4">
			{include file="nli/login.brand.tpl"}
		</div>
		<div class="card bm-signup-card">
			{include file="nli/msp.tabs.tpl"}
			<form action="{if $ssl_signup_enable}{$nliUrlSignupSsl}{else}{$nliUrlSignup}{/if}" method="post" id="signupForm" class="bm-signup-form">
				{csrffield}
			<div class="card-body">
{else}
{include file="nli/page.open.tpl"}
{/if}
