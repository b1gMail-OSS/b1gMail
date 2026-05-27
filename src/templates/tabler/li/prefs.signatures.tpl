<div class="bm-prefs-page bm-prefs-page-signatures">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-quote icon icon-sm" aria-hidden="true"></i>
		{lng p="signatures"}
	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=signatures&do=action&sid={$sid}">

<div class="scrollContainer withBottomBar bm-prefs-body bm-prefs-list-body">
<div class="card bm-prefs-table-card">
<div class="table-responsive bm-prefs-table-wrap">
<table class="bigTable table table-vcenter table-hover bm-prefs-table">
	<tr>
		<th class="bm-prefs-col-check"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'signature');" aria-label="{lng p="selaction"}" /></label></th>
		<th>
			{lng p="title"}
			<i class="fa fa-arrow-up" aria-hidden="true"></i>
		</th>
		<th class="bm-prefs-col-actions">&nbsp;</th>
	</tr>
	
	{if $signatureList}
	<tbody class="listTBody">
	{foreach from=$signatureList key=signatureID item=signature}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class} bm-prefs-col-check" nowrap="nowrap"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="signature_{$signatureID}" name="signature_{$signatureID}" aria-label="{text value=$signature.titel}" /></label></td>
		<td class="{$class}" nowrap="nowrap"><a href="prefs.php?action=signatures&do=edit&id={$signatureID}&sid={$sid}"><i class="ti ti-quote icon icon-sm text-secondary me-1" aria-hidden="true"></i>{text value=$signature.titel}</a></td>
		<td class="{$class} bm-prefs-col-actions text-end" nowrap="nowrap">
			<div class="btn-group btn-group-sm bm-prefs-row-actions" role="group" aria-label="{lng p="actions"}">
				<a href="prefs.php?action=signatures&do=edit&id={$signatureID}&sid={$sid}" class="btn btn-outline-secondary btn-icon" title="{lng p="edit"}" aria-label="{lng p="edit"}"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
				<a onclick="return confirm('{lng p="realdel"}');" href="prefs.php?action=signatures&do=delete&id={$signatureID}&sid={$sid}" class="btn btn-outline-secondary btn-icon text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
			</div>
		</td>
	</tr>
	{/foreach}
	</tbody>
	{/if}
</table>
</div>
</div>
</div>

<div id="contentFooter" class="contentFooter bm-organizer-footer bm-prefs-footer">
	<div class="left">
		<div class="input-group input-group-sm bm-prefs-action-group">
			<select class="form-select bm-prefs-action-select" name="do2">
				<option value="-">------ {lng p="selaction"} ------</option>
				<option value="delete">{lng p="delete"}</option>
			</select>
			<input class="btn btn-primary" type="submit" value="{lng p="ok"}" />
		</div>
	</div>
	<div class="right">
		<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='prefs.php?action=signatures&do=add&sid={$sid}';">
			<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
			{lng p="addsignature"}
		</button>
	</div>
</div>

</form>
</div>
