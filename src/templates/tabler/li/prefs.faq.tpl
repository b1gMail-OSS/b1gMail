<div class="bm-prefs-page bm-prefs-page-faq">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-help icon icon-sm" aria-hidden="true"></i>
		{lng p="faq"}
	</div>
</div>

<div class="scrollContainer bm-prefs-body bm-prefs-list-body">
<div class="card bm-prefs-table-card">
<div class="table-responsive bm-prefs-table-wrap">
<table class="bigTable table table-vcenter table-hover bm-prefs-table">
	<tr>
		<th>
			{lng p="question"}
		</th>
	</tr>
	
	{foreach from=$faq item=item}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}">&nbsp;<a href="javascript:toggleGroup({$item.id});"><img id="groupImage_{$item.id}" src="{$tpldir}images/expand.png" width="11" height="11" border="0" alt="" align="absmiddle" /> <i class="fa fa-question-circle-o" aria-hidden="true"></i> {$item.frage}</a></td>
	</tr>
	<tbody id="group_{$item.id}" style="display:none;">
		<tr>
			<td class="listTableTDText">{$item.antwort}</td>
		</tr>
	</tbody>
	{/foreach}
</table>
</div>
</div>
</div>
</div>
