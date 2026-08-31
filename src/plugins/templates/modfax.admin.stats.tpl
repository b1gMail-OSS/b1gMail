<fieldset>
	<legend>{lng p="stats"}</legend>
	
	<form action="{$pageURL}{$sessionUrlSuffixHtml}" method="get">
		<div class="row g-2 align-items-end">
			<div class="col-md-4">
				<label class="form-label" for="statType">{lng p="stats"}</label>
				<select class="form-select" name="statType" id="statType">
				{foreach from=$statTypes item=type}
					<option value="{$type}"{if $statType==$type} selected="selected"{/if}>{lng p="modfax_$type"}</option>
				{/foreach}
				</select>
			</div>
			<div class="col-md-4">
				<label class="form-label">{lng p="time"}</label>
				<div>{html_select_date prefix="time" start_year="-5" time=$time display_days=false}</div>
			</div>
			<div class="col-md-4">
				<button class="btn btn-primary" type="submit">{lng p="show"}</button>
			</div>
		</div>
	</form>
	<form action="{$pageURL}{$sessionUrlSuffixHtml}" method="post" class="mt-2">
		{csrffield}
		<input type="hidden" name="fax_stats_reset" value="1" />
		<button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('{lng p="realdel"}');">{lng p="reset"}</button>
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
		{math assign="i" equation="(x - y)*2" x=4 y=$i}
		{section loop=$i name=rest}
				<td style="border-top:1px solid #A0A0A0;border-right:1px solid #EEE;border-bottom:1px solid #EEE;">&nbsp;</td>
		{/section}
			</tr>
		{/if}
		</table>
	</center>
</fieldset>
{/foreach}
