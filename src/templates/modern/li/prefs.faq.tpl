<div id="contentHeader">
	<div class="left">
		<i class="fa fa-question-circle-o" aria-hidden="true"></i>
		{lng p="faq"}
	</div>
</div>

<div class="scrollContainer">
<table class="bigTable">
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
