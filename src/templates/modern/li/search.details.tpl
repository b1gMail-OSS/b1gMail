<div id="contentHeader">
	<div class="left">
		<i class="fa fa-search" aria-hidden="true"></i>
		{lng p="search"}: {text value=$q}
	</div>
</div>

<form name="f1" method="post" action="search.php?q={text value=$encodedQ}&sid={$sid}">
<input type="hidden" name="do" value="massAction" />

<div class="scrollContainer withBottomBar">
<table class="bigTable">
	<colgroup>
		<col style="width:24px;" />
		<col style="width:24px;" />
		<col style="" />
		<col style="width:130px;" />
		<col style="width:75px;" />
		<col style="width:75px;" />
	</colgroup>

	<tr>
		<th colspan="2">&nbsp;</th>
		<th><a href="#" onclick="changeSearchSort('title','{$sortOrderInv}');">{lng p="title"}</a>
			{if $sortColumn=='title'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}</th>
		<th><a href="#" onclick="changeSearchSort('date','{$sortOrderInv}');">{lng p="date"}</a>
			{if $sortColumn=='date'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}</th>
		<th><a href="#" onclick="changeSearchSort('size','{$sortOrderInv}');">{lng p="size"}</a>
			{if $sortColumn=='size'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}</th>
		<th><a href="#" onclick="changeSearchSort('score','{$sortOrderInv}');">{lng p="relevance"}</a>
			{if $sortColumn=='score'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}</th>
	</tr>
	
	{if $results}
	{foreach from=$results item=resultCat key=resultCatID}
	<tr>
		<td width="24" align="center" class="folderGroup">
			<input type="checkbox"{if !$resultCat.massActions} disabled="disabled"{else} onclick="checkAll(this.checked, document.forms.f1, 'checkbox_{$resultCatID}_');toggleResultMassActions(document.forms.f1, {$resultCatID});"{/if} />
		</td>
		<td colspan="5" class="folderGroup">
			<a style="display:block;" href="javascript:toggleGroup({$resultCatID});">&nbsp;<img id="groupImage_{$resultCatID}" src="{$tpldir}images/contract.png" width="11" height="11" border="0" align="absmiddle" alt="" />
			&nbsp;{text value=$resultCat.title}</a>
		</td>
	</tr>
	<tbody id="group_{$resultCatID}" style="display:;">
	{foreach from=$resultCat.results item=result key=resultID}
	{cycle values="listTableTR,listTableTR2" assign="class"}
	<tr class="{$class}">
		<td width="24" align="center">
			<input type="checkbox" name="items[{$resultCat.name}][]" id="checkbox_{$resultCatID}_{$resultID}" value="{$result.id}"{if !$resultCat.massActions} disabled="disabled"{/if} onchange="toggleResultMassActions(document.forms.f1, {$resultCatID});" />
		</td>
		<td width="24">
			<i class="fa {if $result.icon}{$result.icon}{else}{$resultCat.icon}{/if}" aria-hidden="true"></i>
		</td>
		<td nowrap="nowrap" style="padding:4px;">
			<a title="{text value=$result.title}" href="{if $result.extLink}{$result.extLink}{else}{$result.link}sid={$sid}{/if}"{if $result.extLink} target="_blank"{/if}>
				<div style="text-overflow:ellipsis;overflow:hidden;{if $result.bold}font-weight:bold;{/if}{if $result.strike}text-decoration:line-through;{/if}">
					{text value=$result.title}
				</div>
				{if $result.excerpt}<div style="color:#777;text-overflow:ellipsis;overflow:hidden;">{$result.excerpt}</div>{/if}
			</a>
		</td>
		<td width="130"><span style="{if $result.bold}font-weight:bold;{/if}{if $result.strike}text-decoration:line-through;{/if}">{if $result.date}{date timestamp=$result.date nice=true}{/if}</span></td>
		<td width="75"><span style="{if $result.bold}font-weight:bold;{/if}{if $result.strike}text-decoration:line-through;{/if}">{if $result.size||$result.size===0}{size bytes=$result.size}{/if}</span></td>
		<td style="text-align:center;">{if $result.score}{$result.score} %{else}-{/if}</td>
	</tr>
	{/foreach}
	{if $resultCat.massActions}
	
	<tr style="display:none;" id="massActions_{$resultCatID}">
		<td colspan="6" class="listTableFoot" style="border-bottom:3px double #CCC;">
			{if $resultCat.icon}<img src="{$tpldir}images/li/{$resultCat.icon}.png" border="0" alt="" width="16" height="16" align="absmiddle" />{/if}
			
			<select class="smallInput" name="massAction_{$resultCat.name}">
				<option value="-">------ {lng p="selaction"} ------</option>
				
				{foreach from=$resultCat.massActions item=actionDescription key=actionName}
				{if is_array($actionDescription)}
				<optgroup label="{$actionName}">
					{foreach from=$actionDescription item=realActionDescription key=realActionName}
					<option value="{$realActionName}">{$realActionDescription}</option>
					{/foreach}
				</optgroup>
				{else}
				<option value="{$actionName}">{$actionDescription}</option>
				{/if}
				{/foreach}
			</select>
			
			<input class="smallInput" type="submit" name="submitMassAction_{$resultCat.name}" value="{lng p="ok"}" />
		</td>
	</tr>
	{/if}
	</tbody>
	{/foreach}
	{else}
	<tr class="listTableTR">
		<td colspan="5" align="center">
			<i>({lng p="nothingfound"})</i>
		</td>
	</tr>
	{/if}
</table>
</div>

<div id="contentFooter">
	<div class="right">
		{lng p="pages"}:
		&nbsp;
		<select class="smallInput" onchange="changeSearchPage(this.value)">
			{section name=page start=0 loop=$pageCount step=1}
				<option value="{$smarty.section.page.index+1}"{if $pageNo==$smarty.section.page.index+1} selected="selected"{/if}>{$smarty.section.page.index+1}</option>
			{/section}
		</select>
	</div>
</div>

</form>
