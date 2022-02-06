<div id="contentHeader">
	<div class="left">
		<i class="fa fa-quote-right" aria-hidden="true"></i>
		{lng p="signatures"}
	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=signatures&do=action&sid={$sid}">

<div class="scrollContainer withBottomBar">
<table class="bigTable">
	<tr>
		<th width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'signature');" /></th>
		<th>
			{lng p="title"}
			<i class="fa fa-arrow-up" aria-hidden="true"></i>
		</th>
		<th width="55">&nbsp;</th>
	</tr>
	
	{if $signatureList}
	<tbody class="listTBody">
	{foreach from=$signatureList key=signatureID item=signature}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}" nowrap="nowrap"><input type="checkbox" id="signature_{$signatureID}" name="signature_{$signatureID}" /></td>
		<td class="listTableTDActive" nowrap="nowrap">&nbsp;<a href="prefs.php?action=signatures&do=edit&id={$signatureID}&sid={$sid}"><i class="fa fa-quote-right" aria-hidden="true"></i> {text value=$signature.titel}</a></td>
		<td class="{$class}" nowrap="nowrap">
			<a href="prefs.php?action=signatures&do=edit&id={$signatureID}&sid={$sid}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
			<a onclick="return confirm('{lng p="realdel"}');" href="prefs.php?action=signatures&do=delete&id={$signatureID}&sid={$sid}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		</td>
	</tr>
	{/foreach}
	</tbody>
	{/if}
</table>
</div>

<div id="contentFooter">
	<div class="left">
		<select class="smallInput" name="do2">
			<option value="-">------ {lng p="selaction"} ------</option>
			<option value="delete">{lng p="delete"}</option>
		</select>
		<input class="smallInput" type="submit" value="{lng p="ok"}" />
	</div>
	<div class="right">
		<button type="button" onclick="document.location.href='prefs.php?action=signatures&do=add&sid={$sid}';">
			<i class="fa fa-plus" aria-hidden="true"></i>
			{lng p="addsignature"}
		</button>
	</div>
</div>

</form>
