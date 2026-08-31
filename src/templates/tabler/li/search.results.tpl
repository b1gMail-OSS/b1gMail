{if isset($results)}
{foreach from=$results item=resultCat}
{assign var=first value=true}
{foreach from=$resultCat.results item=result}
	{if !empty($result.extLink)}
	<a class="list-group-item list-group-item-action" href="{$result.extLink}" target="_blank" rel="noopener noreferrer" onclick="hideSearchPopup(true);"{if isset($result.excerpt)} title="{text value=$result.excerpt allowEmpty=true stripTags=true}"{/if}>
	{else}
	<a class="list-group-item list-group-item-action" href="{$result.link}" onclick="hideSearchPopup(true);"{if isset($result.excerpt)} title="{text value=$result.excerpt allowEmpty=true stripTags=true}"{/if}>
	{/if}
		<span class="d-flex align-items-center gap-2">
			<span class="text-secondary small text-end" style="min-width:4.5rem;">{if $first}{assign var=first value=false}{text value=$resultCat.title}{/if}</span>
			<span class="text-truncate">
				<i class="fa {if !empty($result.icon)}{$result.icon}{else}{$resultCat.icon}{/if}" aria-hidden="true"></i>
				{text value=$result.title cut=25}
			</span>
		</span>
	</a>
{/foreach}
{/foreach}
	<a class="list-group-item list-group-item-action" href="{sessionurl file='search.php' params="q={$q}"}" onclick="hideSearchPopup(true);">
		<i class="fa fa-search" aria-hidden="true"></i>
		{lng p="details"}...
	</a>
{else}
	<div class="list-group-item text-secondary">{lng p="nothingfound"}</div>
{/if}
