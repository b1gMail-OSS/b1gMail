{if $templatePrefs.loginStyle=='msp'}
	{include file="nli/login.msp.tpl"}
{elseif $templatePrefs.loginStyle=='center' || $templatePrefs.loginStyle=='minimal'}
	{include file="nli/login.center.tpl"}
{else}
	{include file="nli/login.cover.tpl"}
{/if}
