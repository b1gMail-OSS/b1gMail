{assign var="_pluginTabFound" value=false}
{foreach from=$pageTabs key=tabID item=tab}
{if $tabID != 'start' && $tabID != 'email' && $tabID != 'sms' && $tabID != 'organizer' && $tabID != 'webdisk' && $tabID != 'prefs'}
{assign var="_pluginTabFound" value=true}
{/if}
{/foreach}
{if $_pluginTabFound}
<div class="sidebarHeading">{lng p="misc"}</div>
<div class="contentMenuIcons bm-plugin-nav">
{foreach from=$pageTabs key=tabID item=tab}
{if $tabID != 'start' && $tabID != 'email' && $tabID != 'sms' && $tabID != 'organizer' && $tabID != 'webdisk' && $tabID != 'prefs'}
<a href="{$tab.link}"{if $activeTab==$tabID} class="active"{/if} title="{$tab.text|escape}">
	{include file="li/tab-icon.tpl" tab=$tab}
	{$tab.text}
</a>
{/if}
{/foreach}
</div>
{/if}
