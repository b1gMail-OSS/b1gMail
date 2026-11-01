{if !isset($sortOrderInv)}{assign var=sortOrderInv value='asc'}{/if}
{if !isset($sortColumn)}{assign var=sortColumn value=''}{/if}
{if !isset($sortOrder)}{assign var=sortOrder value=''}{/if}

<div class="bm-search-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-search icon icon-sm" aria-hidden="true"></i>
			{lng p="search"}{if $q}: {text value=$q}{/if}
		</div>
	</div>

	<form class="bm-search-form" name="f1" method="post" action="search.php?q={text value=$encodedQ|default:''}{$sessionUrlSuffix}">
		{csrffield}
		<input type="hidden" name="do" value="massAction" />

		<div class="scrollContainer withBottomBar bm-search-body">
			<div class="table-responsive">
				<table class="table table-vcenter table-hover card-table bm-search-table">
					<thead>
					<tr>
						<th class="bm-search-col-check">&nbsp;</th>
						<th class="bm-search-col-icon">&nbsp;</th>
						<th>
							<a class="bm-search-sort" href="#" onclick="changeSearchSort('title','{$sortOrderInv}');return false;">{lng p="title"}</a>
							{if $sortColumn=='title'}<i class="ti ti-arrow-{if $sortOrder=='fa-arrow-down'}down{else}up{/if} icon icon-sm ms-1 text-primary" aria-hidden="true"></i>{/if}
						</th>
						<th class="bm-search-col-date">
							<a class="bm-search-sort" href="#" onclick="changeSearchSort('date','{$sortOrderInv}');return false;">{lng p="date"}</a>
							{if $sortColumn=='date'}<i class="ti ti-arrow-{if $sortOrder=='fa-arrow-down'}down{else}up{/if} icon icon-sm ms-1 text-primary" aria-hidden="true"></i>{/if}
						</th>
						<th class="bm-search-col-size">
							<a class="bm-search-sort" href="#" onclick="changeSearchSort('size','{$sortOrderInv}');return false;">{lng p="size"}</a>
							{if $sortColumn=='size'}<i class="ti ti-arrow-{if $sortOrder=='fa-arrow-down'}down{else}up{/if} icon icon-sm ms-1 text-primary" aria-hidden="true"></i>{/if}
						</th>
						<th class="bm-search-col-score">
							<a class="bm-search-sort" href="#" onclick="changeSearchSort('score','{$sortOrderInv}');return false;">{lng p="relevance"}</a>
							{if $sortColumn=='score'}<i class="ti ti-arrow-{if $sortOrder=='fa-arrow-down'}down{else}up{/if} icon icon-sm ms-1 text-primary" aria-hidden="true"></i>{/if}
						</th>
					</tr>
					</thead>

					{if isset($results)}
					{foreach from=$results item=resultCat key=resultCatID}
					<tbody>
					<tr class="bm-search-section-row">
						<td class="bm-search-col-check">
							<label class="form-check mb-0">
								<input type="checkbox" class="form-check-input m-0"{if !isset($resultCat.massActions) || !$resultCat.massActions} disabled="disabled"{else} onclick="checkAll(this.checked, document.forms.f1, 'checkbox_{$resultCatID}_');toggleResultMassActions(document.forms.f1, {$resultCatID});"{/if} aria-label="{text value=$resultCat.title}" />
							</label>
						</td>
						<td colspan="5">
							<button type="button" class="bm-search-section-toggle" onclick="toggleGroup({$resultCatID});">
								<i class="ti ti-chevron-down icon icon-sm" id="groupImage_{$resultCatID}" aria-hidden="true"></i>
								{text value=$resultCat.title}
							</button>
						</td>
					</tr>
					</tbody>
					<tbody id="group_{$resultCatID}">
					{foreach from=$resultCat.results item=result key=resultID}
					<tr>
						<td class="bm-search-col-check">
							<label class="form-check mb-0">
								<input type="checkbox" class="form-check-input m-0" name="items[{$resultCat.name}][]" id="checkbox_{$resultCatID}_{$resultID}" value="{$result.id|default:''}"{if !isset($resultCat.massActions) || !$resultCat.massActions} disabled="disabled"{/if} onchange="toggleResultMassActions(document.forms.f1, {$resultCatID});" aria-label="{text value=$result.title}" />
							</label>
						</td>
						<td class="bm-search-col-icon text-secondary">
							{if !empty($result.icon)}
							{include file="li/icon.tpl" faIcon=$result.icon iconClass="icon icon-sm"}
							{else}
							{include file="li/icon.tpl" faIcon=$resultCat.icon iconClass="icon icon-sm"}
							{/if}
						</td>
						<td class="bm-search-title">
							<a title="{text value=$result.title}" href="{if !empty($result.extLink)}{$result.extLink}{else}{$result.link}{/if}"{if !empty($result.extLink)} target="_blank" rel="noopener noreferrer"{/if}>
								<span class="bm-search-title-text{if !empty($result.bold)} fw-bold{/if}{if !empty($result.strike)} text-decoration-line-through{/if}">{text value=$result.title}</span>
								{if !empty($result.excerpt)}<span class="bm-search-excerpt">{$result.excerpt}</span>{/if}
							</a>
						</td>
						<td class="bm-search-col-date{if !empty($result.bold)} fw-bold{/if}{if !empty($result.strike)} text-decoration-line-through{/if}">{if isset($result.date)}{date timestamp=$result.date nice=true}{/if}</td>
						<td class="bm-search-col-size{if !empty($result.bold)} fw-bold{/if}{if !empty($result.strike)} text-decoration-line-through{/if}">{if isset($result.size) && ($result.size||$result.size===0)}{size bytes=$result.size}{/if}</td>
						<td class="bm-search-col-score text-secondary">{if !empty($result.score)}{$result.score} %{else}&ndash;{/if}</td>
					</tr>
					{/foreach}
					{if isset($resultCat.massActions) && $resultCat.massActions}
					<tr id="massActions_{$resultCatID}" style="display:none;">
						<td colspan="6" class="bm-search-mass">
							<div class="input-group input-group-sm bm-search-action-group">
								<select class="form-select form-select-sm" name="massAction_{$resultCat.name}" aria-label="{lng p="selaction"}">
									<option value="-">{lng p="selaction"}</option>
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
								<button type="submit" class="btn btn-sm btn-primary" name="submitMassAction_{$resultCat.name}">{lng p="ok"}</button>
							</div>
						</td>
					</tr>
					{/if}
					</tbody>
					{/foreach}
					{else}
					<tbody>
					<tr>
						<td colspan="6" class="bm-search-empty">{lng p="nothingfound"}</td>
					</tr>
					</tbody>
					{/if}
				</table>
			</div>
		</div>

		{if isset($pageCount)}
		<div id="contentFooter" class="contentFooter bm-search-footer">
			<div class="right">
				<label class="bm-search-pages" for="searchPageNo">{lng p="pages"}</label>
				<select id="searchPageNo" class="form-select form-select-sm" onchange="changeSearchPage(this.value)" aria-label="{lng p="pages"}">
					{section name=page start=0 loop=$pageCount step=1}
					<option value="{$smarty.section.page.index+1}"{if $pageNo==$smarty.section.page.index+1} selected="selected"{/if}>{$smarty.section.page.index+1}</option>
					{/section}
				</select>
			</div>
		</div>
		{/if}
	</form>
</div>
