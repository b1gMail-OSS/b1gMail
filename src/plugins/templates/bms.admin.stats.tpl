<fieldset class="mb-4">
	<legend class="h4 mb-3">{lng p="stats"}</legend>
	
	<form action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=stats"}" method="post">
	{csrffield}
		<table width="100%">
			<tr>
				<td class="td2" align="left">
					<div class="input-group input-group-wide">
						<select class="form-select" name="statType" style="max-width:12rem;">
						{foreach from=$statTypes item=type}
							<option value="{$type}"{if $statType==$type} selected="selected"{/if}>{lng p=$type}</option>
						{/foreach}
						</select>
						{html_select_date prefix="time" start_year="-5" time=$time display_days=false field_separator="" all_extra='class="form-select"'}
						<button type="submit" class="btn btn-primary">{lng p="show"} &raquo;</button>
					</div>
				</td>
				<td align="right">
					<img src="../plugins/templates/images/bms_stats_reset.png" border="0" alt="" width="16" height="16" align="absmiddle" />
					<button type="submit" class="btn btn-link p-0 align-baseline" form="bmsResetStatsForm" onclick="return confirm('{lng p="bms_real_reset"}');">{lng p="bms_reset_stats"}</button>
				</td>
			</tr>
		</table>
	</form>

	<form id="bmsResetStatsForm" method="post" action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=stats"}" style="display:none;" aria-hidden="true">
		{csrffield}
		<input type="hidden" name="do" value="reset" />
	</form>
</fieldset>
	
{foreach from=$stats item=stat}
<fieldset class="mb-4">
	<legend class="h4 mb-3">{$stat.title}</legend>
	
	<center>
		<table class="statsTable">
			<tr>
				<th colspan="{$stat.count+1}">{text value=$stat.title}</th>
			</tr>
			<tr style="height:250px;">
				<td width="30" class="yScale">{foreach from=$stat.yScale item=val}<div>{$val}&nbsp;</div>{/foreach}</td>
				{foreach from=$stat.data item=values key=day}
				<td class="bar">{if $values[$stat.key]!==false}<div title="{$values[$stat.key]}" style="height:{if $stat.heights[$day]==0}1{else}{$stat.heights[$day]}{/if}px;"></div>{/if}</td>
				{/foreach}
			</tr>
			<tr>
				<td rowspan="2"></td>
				{foreach from=$stat.data item=values key=day}<td class="xLines"></td>{/foreach}
			</tr>
			<tr>
				{foreach from=$stat.data item=values key=day}<td class="xScale">{$day}</td>{/foreach}
			</tr>
		</table>

		<table class="list" style="width:692px;">
			<tr>
				<th width="60">{lng p="day"}</th>
				<th>{lng p="value"}</th>
				<th width="60">{lng p="day"}</th>
				<th>{lng p="value"}</th>
				<th width="60">{lng p="day"}</th>
				<th>{lng p="value"}</th>
				<th width="60">{lng p="day"}</th>
				<th>{lng p="value"}</th>
			</tr>
			<tr>
		{assign var="i" value=0}
		{foreach from=$stat.data item=values key=day}
		{assign var="i" value=$i+1}
				<td class="td2">{$day}</td>
				<td class="td1"{if $i!=4} style="border-right: 1px solid #BBBBBB;"{/if}>{implode pieces=$values glue=" / "}</td>
		{if $i==4}
		{assign var="i" value=0}
			</tr>
			<tr>
		{/if}
		{/foreach}
		{if $i<4}
		{math assign="i" equation="(x - y)" x=4 y=$i}
		{section loop=$i name=rest}
				<td class="td2">&nbsp;</td>
				<td class="td1"{if $smarty.section.rest.index!=$i-1} style="border-right: 1px solid #BBBBBB;"{/if}>&nbsp;</td>
		{/section}
			</tr>
		{/if}
		
			<tr>
				<td colspan="8" class="footer" style="text-align:center;">
					{lng p="sum"}:
					{$stat.sum}
				</td>
			</tr>
		</table>
	</center>
</fieldset>
{/foreach}