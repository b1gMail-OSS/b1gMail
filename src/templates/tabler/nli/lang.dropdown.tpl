<div class="dropdown{if $langDropdownDropup|default:false} dropup{/if}">
	<a href="#" class="{$langDropdownClass|default:'btn btn-sm btn-link text-secondary'} dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
		<i class="ti ti-world me-1" aria-hidden="true"></i>
		{foreach from=$languageList key=langKey item=langInfo}{if $langInfo.active}{$langInfo.title}{/if}{/foreach}
	</a>
	<ul class="dropdown-menu{if $langDropdownMenuEnd|default:false} dropdown-menu-end{/if}">
		{foreach from=$languageList key=langKey item=langInfo}
		<li{if $langInfo.active} class="active"{/if}>
			<a class="dropdown-item" href="index.php?action=switchLanguage&amp;lang={$langKey}{if !empty($smarty.get.action)}&amp;target={text value=$smarty.get.action}{/if}">{$langInfo.title}</a>
		</li>
		{/foreach}
	</ul>
</div>
