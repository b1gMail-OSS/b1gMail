<form action="search.php?sid={$sid}" method="post" name="searchSideBarForm">

<input type="hidden" name="page" value="1" id="searchSideBarPage" />
<input type="hidden" name="sort" value="{$sortColumn}" id="searchSideBarSort" />
<input type="hidden" name="order" value="{$sortOrder}" id="searchSideBarOrder" />

<div class="sidebarHeading">{lng p="searchfor"}</div>
<center>
	<table>
		<tr>
			<td width="16"><i class="fa fa-search" aria-hidden="true"></i></td>
			<td><input type="text" name="q" id="q" style="width: 150px;" value="{text value=$q allowEmpty=true}" /></td>
		</tr>
	</table>
</center>

<div class="sidebarHeading">{lng p="searchin"}</div>
<div class="contentMenuIcons">
	{foreach from=$categories item=cat key=catName}
		<i class="fa {$cat.icon}" aria-hidden="true"></i>
		<input id="searchIn_{$catName}" type="checkbox" name="searchIn[]" value="{$catName}"{if $searchIn[$catName]} checked="checked"{/if} />
		<label for="searchIn_{$catName}">{$cat.title}</label><br />
	{/foreach}
</div>

<div class="sidebarHeading">{lng p="date"}</div>

<div class="contentMenuIcons">
	<div>
		{lng p="datefrom"}<br />
		{html_select_date prefix="dateFrom" reverse_years=true time=$dateFrom field_order="DMY" month_format="%m" start_year="-5" field_separator="." year_empty="----" month_empty="--" day_empty="--"}
	</div>
	
	<div style="margin-top:0.5em;">
		{lng p="dateto"}<br />
		{html_select_date prefix="dateTo" reverse_years=true time=$dateTo field_order="DMY" month_format="%m" start_year="-5" field_separator="." year_empty="----" month_empty="--" day_empty="--"}
	</div>
</div>

<div class="sidebarHeading"> &nbsp; {lng p="search2"}</div>
<center>
	<input type="submit" class="primary" onclick="EBID('searchSideBarPage').value=0;" value=" {lng p="search2"} " />
	<br /><br />
</center>

</form>
