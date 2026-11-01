{capture name="bmSearchLang"}{lng p="langCode"}{/capture}
<form class="bm-search-sidebar" action="{sessionurl file='search.php'}" method="post" name="searchSideBarForm">
	{csrffield}

<input type="hidden" name="page" value="1" id="searchSideBarPage" />
<input type="hidden" name="sort" value="{$sortColumn|default:''}" id="searchSideBarSort" />
<input type="hidden" name="order" value="{$sortOrderRaw|default:'desc'}" id="searchSideBarOrder" />

<div class="sidebarHeading">{lng p="searchfor"}</div>
<div class="input-group input-group-sm bm-search-query">
	<span class="input-group-text"><i class="ti ti-search" aria-hidden="true"></i></span>
	<input type="search" class="form-control form-control-sm" name="q" id="q" value="{if isset($q)}{text value=$q allowEmpty=true}{/if}" aria-label="{lng p="searchfor"}" />
</div>

<div class="sidebarHeading">{lng p="searchin"}</div>
<div class="bm-search-cats">
	{foreach from=$categories item=cat key=catName}
	<label class="form-check">
		<input id="searchIn_{$catName}" type="checkbox" class="form-check-input" name="searchIn[]" value="{$catName}"{if $searchIn[$catName]} checked="checked"{/if} />
		<span class="form-check-label">{include file="li/icon.tpl" faIcon=$cat.icon iconClass="icon icon-sm"} {$cat.title}</span>
	</label>
	{/foreach}
</div>

<div class="sidebarHeading">{lng p="date"}</div>
<div class="bm-search-dates">
	<div class="input-icon bm-search-date">
		<span class="input-icon-addon" aria-hidden="true"><i class="ti ti-calendar"></i></span>
		<input type="text" class="form-control form-control-sm bm-search-date-input" name="dateFrom" id="searchDateFrom" value="{if $dateFrom && $dateFrom != '--'}{date timestamp=$dateFrom format="d.m.Y"}{/if}" placeholder="{lng p="datefrom"}" aria-label="{lng p="datefrom"}" data-bs-toggle="datepicker" data-bs-date-format="DD.MM.YYYY" data-bs-locale="{$smarty.capture.bmSearchLang|lower}" data-bs-datepicker-theme="light" autocomplete="off" />
	</div>
	<div class="input-icon bm-search-date">
		<span class="input-icon-addon" aria-hidden="true"><i class="ti ti-calendar"></i></span>
		<input type="text" class="form-control form-control-sm bm-search-date-input" name="dateTo" id="searchDateTo" value="{if $dateTo && $dateTo != '--'}{date timestamp=$dateTo format="d.m.Y"}{/if}" placeholder="{lng p="dateto"}" aria-label="{lng p="dateto"}" data-bs-toggle="datepicker" data-bs-date-format="DD.MM.YYYY" data-bs-locale="{$smarty.capture.bmSearchLang|lower}" data-bs-datepicker-theme="light" autocomplete="off" />
	</div>
</div>

<button type="submit" class="btn btn-primary btn-sm w-100 bm-search-submit" onclick="EBID('searchSideBarPage').value=0;">{lng p="search2"}</button>

</form>
