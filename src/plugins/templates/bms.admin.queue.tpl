<form action="{$pageURL}&action=msgqueue&do=queue&filter=true&sid={$sid}" method="post" onsubmit="spin(this)" name="f1">
<input type="hidden" name="page" id="page" value="{$pageNo}" />
<input type="hidden" name="sortBy" id="sortBy" value="{$sortBy}" />
<input type="hidden" name="sortOrder" id="sortOrder" value="{$sortOrder}" />
<input type="hidden" name="singleAction" id="singleAction" value="" />
<input type="hidden" name="singleID" id="singleID" value="" />

{if $msg}<div class="note" style="margin:10px;width:auto;">{$msg}</div>{/if}

<fieldset>
	<legend>{lng p="bms_queue"}</legend>

	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'items[]');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th><a href="javascript:updateSort('id');">{lng p="id"}
				{if $sortBy=='id'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th><a href="javascript:updateSort('from');">{lng p="from"}
				{if $sortBy=='from'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th><a href="javascript:updateSort('to');">{lng p="to2"}
				{if $sortBy=='to'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th><a href="javascript:updateSort('size');">{lng p="size"}
				{if $sortBy=='size'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th><a href="javascript:updateSort('last_attempt');">{lng p="bms_last_attempt"}
				{if $sortBy=='last_attempt'}<img src="{$tpldir}images/sort_{$sortOrder}.png" border="0" alt="" width="7" height="6" align="absmiddle" />{/if}</a></th>
			<th width="90">&nbsp;</th>
		</tr>
		
		{foreach from=$queue item=item}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="../plugins/templates/images/bms_{$item.typeIcon}.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center"><input type="checkbox" name="items[]" value="{$item.id}" /></td>
			<td>{$item.id}<br /><small>{$item.hexID}</small></td>
			<td><a title="{text value=$item.from}">{text value=$item.from cut=25}</a><br />
				<small>{date timestamp=$item.date nice=true}</td>
			<td><a title="{text value=$item.to}">{text value=$item.to cut=25}</a></td>
			<td>{size bytes=$item.size}</td>
			<td>{if $item.active}{lng p="active"}{else}{if $item.last_attempt==0}-{else}{date timestamp=$item.last_attempt nice=true}{/if}{/if}{if $item.last_attempt!=0}<br /><small>{$item.attempts} {lng p="bms_attempts"}</small>{/if}</td>
			<td>
				<a href="{$pageURL}&action=msgqueue&do=showQueueItem&id={$item.id}&sid={$sid}" title="{lng p="show"}"><img src="../plugins/templates/images/bms_show.png" border="0" alt="{lng p="show"}" width="16" height="16" /></a>
				{if $queueRunning}<a href="{$pageURL}&action=msgqueue&do=downloadQueueItem&id={$item.id}&sid={$sid}" title="{lng p="bms_download"}" target="_blank"><img src="../plugins/templates/images/bms_download.png" border="0" alt="{lng p="bms_download"}" width="16" height="16" /></a>{/if}
				<a href="javascript:if(confirm('{lng p="realdel"}')) singleAction('delete', '{$item.id}');" title="{lng p="delete"}"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}

		<tr>
			<td class="footer" colspan="8">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" class="smallInput">
						<option value="-">------------</option>

						<optgroup label="{lng p="actions"}">
							<option value="delete">{lng p="delete"}</option>
						</optgroup>
					</select>&nbsp;
				</div>
				<div style="float:left;">
					<input type="submit" name="executeMassAction" value=" {lng p="execute"} " class="smallInput" />
				</div>
				<div style="float:right;padding-top:3px;padding-bottom:3px;">
					{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"javascript:updatePage(.s);\">.t</a></span> "}&nbsp;
				</div>
			</td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>{lng p="filter"}</legend>
	
	<table width="100%">
		<tr>
			<td width="40" valign="top" rowspan="4"><img src="{$tpldir}images/filter.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="80">{lng p="types"}:</td>
			<td class="td2">
				<input type="checkbox" name="types[0]" id="type_0"{if $types[0]} checked="checked"{/if} />
					<label for="type_0"><b>{lng p="bms_inbound"}</b></label><br />
				<input type="checkbox" name="types[1]" id="type_1"{if $types[1]} checked="checked"{/if} />
					<label for="type_1"><b>{lng p="bms_outbound"}</b></label><br />
			</td>
		</tr>
		<tr>
			<td class="td1" width="100">{lng p="from"}:</td>
			<td class="td2">
				<input type="radio" name="use_start" value="no" id="use_start_no"{if !$start} checked="checked"{/if} />
				<label for="use_start_no">{lng p="all"}</label>

				<input type="radio" name="use_start" value="yes" id="use_start_yes"{if $start} checked="checked"{/if} />
				<span onclick="EBID('use_start_yes').checked=true;EBID('use_start_no').checked=false;">
					{html_select_date prefix="start" time=$start start_year="-5" field_order="DMY" field_separator="."}, 
					{html_select_time prefix="start" time=$start display_seconds=false}
				</span>
			</td>
		</tr>
		<tr>
			<td class="td1">{lng p="to"}:</td>
			<td class="td2">
				<input type="radio" name="use_end" value="no" id="use_end_no"{if !$end} checked="checked"{/if} />
				<label for="use_end_no">{lng p="all"}</label>

				<input type="radio" name="use_end" value="yes" id="use_end_yes"{if $end} checked="checked"{/if} />
				<span onclick="EBID('use_end_yes').checked=true;EBID('use_end_no').checked=false;">
					{html_select_date prefix="end" time=$end start_year="-5" field_order="DMY" field_separator="."}, 
					{html_select_time prefix="end" time=$end display_seconds=false}
				</span>
			</td>
		</tr>
		<tr>
			<td class="td1" width="80">{lng p="searchfor"}:</td>
			<td class="td2">
				<input type="text" name="query" size="36" value="{text value=$query allowEmpty=true}" />
			</td>
		</tr>
	</table>

	<p align="right">
		{lng p="perpage"}: 
		<input type="text" name="perPage" value="{$perPage}" size="5" />
		<input class="button" type="submit" value=" {lng p="apply"} " />
	</p>
</fieldset>
	
<p>
	<div style="float:left" class="buttons">
		<input class="button" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='{$pageURL}&action=msgqueue&sid={$sid}';" />
	</div>
</p>

</form>
