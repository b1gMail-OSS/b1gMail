<fieldset>
	<legend>{lng p="stats"}</legend>
	
	<form action="{$pageURL}&action=stats&sid={$sid}" method="post">
		<div class="row">
			<div class="col-md-6">
				<div class="input-group mb-2">
					<span class="input-group-text"><i class="fa-solid fa-chart-pie"></i></span>
					<select name="statType" class="form-select">
						{foreach from=$statTypes item=type}
							<option value="{$type}"{if $statType==$type} selected="selected"{/if}>{lng p="modfax_$type"}</option>
						{/foreach}
					</select>
					<span class="input-group-text"><i class="fa-regular fa-calendar"></i></span>
					{html_select_date prefix="time" start_year="-5" time=$time display_days=false}

				</div>
			</div>
			<div class="col-md-6 text-end"><input class="btn btn-primary" type="submit" value="{lng p="show"} &raquo;" /></div>
		</div>
	</form>
</fieldset>

{foreach from=$stats item=stat}
	<fieldset>
		<legend>{$stat.title}</legend>

		<div class="row">
			<div class="col-md-8">
				<div class="card">
					<div class="table-responsive">
						<table class="statsTable">
							<thead>
							<tr>
								<th colspan="{$stat.count+1}">{text value=$stat.title}</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td style="width: 30px;" class="yScale">{foreach from=$stat.yScale item=val}<div>{$val}&nbsp;</div>{/foreach}</td>
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
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card">
					<div class="table-responsive">
						<table class="table table-vcenter table-striped">
							<thead>
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
							</thead>
							<tbody>
							<tr>
								{assign var="i" value=0}
								{foreach from=$stat.data item=values key=day}
								{assign var="i" value=$i+1}
								<td style="padding: 3px 10px 3px 10px;">{$day}</td>
								<td style="padding: 3px 10px 3px 10px;{if $i!=4} border-right: 1px solid #BBBBBB;{/if}">{implode pieces=$values glue=" / "}</td>
								{if $i==4}
								{assign var="i" value=0}
							</tr>
							<tr>
								{/if}
								{/foreach}
								{if $i<4}
								{math assign="i" equation="(x - y)" x=4 y=$i}
								{section loop=$i name=rest}
									<td colspan="2" style="padding: 3px 10px 3px 10px;{if $smarty.section.rest.index!=$i-1} border-right: 1px solid #BBBBBB;{/if}">&nbsp;</td>
								{/section}
							</tr>
							{/if}
							</tbody>
						</table>
					</div>
					<div class="card-footer text-end" style="padding: 3px 10px 3px 10px;"> {lng p="sum"}: {$stat.sum}</div>
				</div>
			</div>
		</div>
	</fieldset>
{/foreach}
