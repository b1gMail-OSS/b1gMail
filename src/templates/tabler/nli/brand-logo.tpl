{assign var=_bmLogoSurface value=$logoSurface|default:'auto'}
{assign var=_bmLogoHeight value=$logoHeight|default:'32'}
{assign var=_bmLogoClass value=$logoClass|default:'navbar-brand-image'}
{if $_bmLogoSurface == 'dark'}
<img src="{if $templateLogoDarkUrl}{$templateLogoDarkUrl}{else}{$tpldir}images/logo.png{/if}" height="{$_bmLogoHeight}" alt="{$service_title}" class="{$_bmLogoClass} {if $templateLogoDarkIsCustom}bm-logo-custom{else}bm-logo-default{/if}" />
{elseif $_bmLogoSurface == 'light'}
<img src="{if $templateLogoUrl}{$templateLogoUrl}{else}{$tpldir}images/logo.png{/if}" height="{$_bmLogoHeight}" alt="{$service_title}" class="{$_bmLogoClass} {if $templateLogoIsCustom}bm-logo-custom{else}bm-logo-default{/if}" />
{else}
<span class="bm-logo-pair d-inline-flex align-items-center">
	<img src="{if $templateLogoUrl}{$templateLogoUrl}{else}{$tpldir}images/logo.png{/if}" height="{$_bmLogoHeight}" alt="{$service_title}" class="{$_bmLogoClass} bm-logo-light {if $templateLogoIsCustom}bm-logo-custom{else}bm-logo-default{/if}" />
	<img src="{if $templateLogoDarkUrl}{$templateLogoDarkUrl}{else}{$tpldir}images/logo.png{/if}" height="{$_bmLogoHeight}" alt="" class="{$_bmLogoClass} bm-logo-dark {if $templateLogoDarkIsCustom}bm-logo-custom{else}bm-logo-default{/if}" />
</span>
{/if}
