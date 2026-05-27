<footer class="text-secondary mt-3 small nli-msp-footer">
	<div class="d-flex flex-wrap align-items-center justify-content-center gap-1 mb-1 nli-msp-footer-meta">
		<span>&copy; {$year} {$service_title}</span>
		<span class="text-secondary" aria-hidden="true">·</span>
		<span>powered by <a href="https://www.b1gmail.eu/" target="_blank" rel="noreferrer" class="text-secondary">b1gMail.eu</a></span>
	</div>
	<div class="d-flex flex-wrap align-items-center justify-content-center gap-1 nli-msp-footer-actions">
		{assign var="langDropdownClass" value="link-secondary py-0"}
		{assign var="langDropdownDropup" value=true}
		{include file="nli/lang.dropdown.tpl"}
		<span class="text-secondary" aria-hidden="true">·</span>
		<a href="{$mobileURL}" class="text-secondary">{lng p="mobilepda"}</a>
		<span class="text-secondary" aria-hidden="true">·</span>
		<a href="index.php?action=imprint" class="text-secondary">{lng p="contact"}</a>
		{foreach from=$pluginUserPages item=item}{if !$item.top|default:false}
		<span class="text-secondary" aria-hidden="true">·</span>
		<a href="{$item.link}" class="text-secondary">{$item.text}</a>
		{/if}{/foreach}
	</div>
</footer>
