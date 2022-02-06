<fieldset>
	<legend>{lng p="stats"}</legend>
	
	<form action="{$pageURL}&action=stats&sid={$sid}" method="post">
		<table width="100%">
			<tr>
				<td align="left">
					<img src="{$tpldir}images/stats.png" border="0" alt="" width="16" height="16" align="absmiddle" />
					<select name="statType">
					{foreach from=$statTypes item=type}
						<option value="{$type}"{if $statType==$type} selected="selected"{/if}>{lng p=$type}</option>
					{/foreach}
					</select>
					&nbsp;&nbsp;
					<img src="{$tpldir}images/calendar.png" border="0" alt="" width="16" height="16" align="absmiddle" />
					{html_select_date prefix="time" start_year="-5" time=$time display_days=false}
					<input class="button" type="submit" value=" {lng p="show"} &raquo; " />
				</td>
				<td align="right">
					<img src="../plugins/templates/images/bms_stats_reset.png" border="0" alt="" width="16" height="16" align="absmiddle" />
					<a href="#" onclick="if(confirm('{lng p="bms_real_reset"}')) document.location.href='{$pageURL}&action=stats&do=reset&sid={$sid}';">{lng p="bms_reset_stats"}</a>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
	
{foreach from=$stats item=stat}
<fieldset>
	<legend>{$stat.title}</legend>
	
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