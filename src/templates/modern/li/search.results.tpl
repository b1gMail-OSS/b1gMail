<table width="100%" cellspacing="0" cellpadding="0">
{if $results}
{foreach from=$results item=resultCat}
{assign var=first value=true}
{foreach from=$resultCat.results item=result}
	<tr>
		<td class="resultLeft" height="20" width="90">{if $first}{assign var=first value=false}{text value=$resultCat.title}{else}&nbsp;{/if}</td>
		<td class="resultRight" onclick="{if $result.extLink}window.open('{$result.extLink}');{else}document.location.href='{$result.link}sid={$sid}';{/if}parent.hideSearchPopup(true);" title="{text value=$result.excerpt allowEmpty=true stripTags=true}">
			<i class="fa {if $result.icon}{$result.icon}{else}{$resultCat.icon}{/if}" aria-hidden="true"></i>
			{text value=$result.title cut=25}
		</td>
	</tr>
{/foreach}
{/foreach}
	<tr>
		<td class="resultLeft" height="20" width="90" style="border-top:3px double #DDD;">&nbsp;</td>
		<td class="resultRight" style="border-top:3px double #DDD;padding:2px;" onclick="document.location.href='search.php?q={$q}&sid={$sid}';parent.hideSearchPopup(true);">
			<i class="fa fa-search" aria-hidden="true"></i>
			{lng p="details"}...
		</td>
	</tr>
{else}
	<tr>
		<td class="resultLeft" height="20" width="90">&nbsp;</td>
		<td class="resultRight">
			{lng p="nothingfound"}
		</td>
	</tr>
{/if}
</table>
