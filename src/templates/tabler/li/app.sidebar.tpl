<div class="contentMenuIcons">
	{foreach from=$pageTabs key=tabID item=tab}
	<a href="{$tab.link}"{if $activeTab==$tabID} class="active"{/if}>{include file="li/icon.tpl" faIcon=$tab.faIcon} {$tab.text}</a>
	{/foreach}
</div>
