<fieldset>
	<legend>{lng p="logs"} ({date nice=true timestamp=$start} - {date nice=true timestamp=$end})</legend>
	
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th>{lng p="entry"}</th>
			<th width="150">{lng p="date"}</th>
		</tr>
		
		{foreach from=$entries item=entry}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td><img src="{$tpldir}images/{$entry.prioImg}.png" border="0" alt="" width="16" height="16" /></td>
			<td><code>{text value=$entry.eintrag}</code></td>
			<td>{date nice=true timestamp=$entry.zeitstempel}</td>
		</tr>
		{/foreach}
	</table>
	
	<p align="right">
		<input class="button" type="button" value=" {lng p="export"} " onclick="parent.frames['top'].location.href='logs.php?sid={$sid}&do=export&start={$start}&end={$end}&q={$ueQ}{$prioQ}';" />
	</p>
</fieldset>

<fieldset>
	<legend>{lng p="filter"}</legend>
	
	<form action="logs.php?sid={$sid}" method="post" onsubmit="spin(this)">
		<table>
			<tr>
				<td width="40" valign="top" rowspan="4"><img src="{$tpldir}images/filter.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="80">{lng p="from"}:</td>
				<td class="td2">
						{html_select_date prefix="start" time=$start start_year="-5" field_order="DMY" field_separator="."}, 
						{html_select_time prefix="start" time=$start display_seconds=false}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="to"}:</td>
				<td class="td2">
						{html_select_date prefix="end" time=$end start_year="-5" field_order="DMY" field_separator="."}, 
						{html_select_time prefix="end" time=$end display_seconds=false}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="priority"}:</td>
				<td class="td2">
					<input type="checkbox"{if $prio[8]} checked="checked"{/if} name="prio[8]" id="prio8" />
					<label for="prio8"><img src="{$tpldir}images/debug.png" border="0" alt="" width="16" height="16" /></label> &nbsp; 
					
					<input type="checkbox"{if $prio[2]} checked="checked"{/if} name="prio[2]" id="prio2" />
					<label for="prio2"><img src="{$tpldir}images/info.png" border="0" alt="" width="16" height="16" /></label> &nbsp; 
					
					<input type="checkbox"{if $prio[1]} checked="checked"{/if} name="prio[1]" id="prio1" />
					<label for="prio1"><img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" /></label> &nbsp; 
					
					<input type="checkbox"{if $prio[4]} checked="checked"{/if} name="prio[4]" id="prio4" />
					<label for="prio4"><img src="{$tpldir}images/error.png" border="0" alt="" width="16" height="16" /></label> &nbsp; 
					
					<input type="checkbox"{if $prio[16]} checked="checked"{/if} name="prio[16]" id="prio16" />
					<label for="prio16"><img src="{$tpldir}images/plugin.png" border="0" alt="" width="16" height="16" /></label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="search"}:</td>
				<td class="td2">
						<input type="text" name="q" value="{text value=$q allowEmpty=true}" size="36" style="width:85%;" />
				</td>
			</tr>
		</table>
		
		<p align="right">
			<input class="button" type="submit" value=" {lng p="apply"} " />
		</p>
	</form>
</fieldset>
