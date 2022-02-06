<fieldset>
	<legend>{lng p="logs"} ({date nice=true timestamp=$start} - {date nice=true timestamp=$end})</legend>
	
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="90">{lng p="bms_component"}</th>
			<th>{lng p="entry"}</th>
			<th width="150">{lng p="date"}
				<img src="{$tpldir}images/sort_desc.png" border="0" alt="" width="7" height="6" align="absmiddle" /></th>
		</tr>
		
		{foreach from=$entries item=entry}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td><img src="{$tpldir}images/{$entry.prioImg}.png" border="0" alt="" width="16" height="16" /></td>
			<td>{text value=$entry.componentName}</td>
			<td><code>{$entry.szEntry}</code></td>
			<td>{date nice=true timestamp=$entry.iDate}</td>
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="4">
				<div style="float:left;">
					<input class="button" type="button" value=" {lng p="export"} " onclick="parent.frames['top'].location.href='{$pageURL}&action=logs&sid={$sid}&do=export&start={$start}&end={$end}&page={$pageNo}&q={$ueQ}{$prioQ}';" />
				</div>
				<div style="float:right;padding-top:3px;">
					{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"$pageURL&action=logs&start=$start&end=$end&q=$ueQ$prioQ&page=.s&sid=$sid\">.t</a></span> "}&nbsp;
				</div>
			</td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>{lng p="filter"}</legend>
	
	<form action="{$pageURL}&action=logs&sid={$sid}" method="post" onsubmit="spin(this)">
		<table>
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/filter.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="100">{lng p="from"}:</td>
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
				<td class="td1">{lng p="bms_component"}:</td>
				<td class="td2">
					<input type="checkbox"{if $component[1]} checked="checked"{/if} name="component[1]" id="component1" />
					<label for="component1">Core</label> &nbsp;

					<input type="checkbox"{if $component[2]} checked="checked"{/if} name="component[2]" id="component2" />
					<label for="component2">POP3</label> &nbsp;

					<input type="checkbox"{if $component[4]} checked="checked"{/if} name="component[4]" id="component4" />
					<label for="component4">IMAP</label> &nbsp;

					<input type="checkbox"{if $component[8]} checked="checked"{/if} name="component[8]" id="component8" />
					<label for="component8">HTTP</label> &nbsp;

					<input type="checkbox"{if $component[16]} checked="checked"{/if} name="component[16]" id="component16" />
					<label for="component16">SMTP</label> &nbsp;

					<input type="checkbox"{if $component[32]} checked="checked"{/if} name="component[32]" id="component32" />
					<label for="component32">MSGQueue</label> &nbsp;

					<input type="checkbox"{if $component[64]} checked="checked"{/if} name="component[64]" id="component64" />
					<label for="component64">Plugin</label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="priority"}:</td>
				<td class="td2">
					<input type="checkbox"{if $prio[8]} checked="checked"{/if} name="prio[8]" id="prio8" />
					<label for="prio8"><img src="{$tpldir}images/debug.png" border="0" alt="" width="16" height="16" /></label> &nbsp;

					<input type="checkbox"{if $prio[1]} checked="checked"{/if} name="prio[1]" id="prio1" />
					<label for="prio1"><img src="{$tpldir}images/info.png" border="0" alt="" width="16" height="16" /></label> &nbsp;

					<input type="checkbox"{if $prio[2]} checked="checked"{/if} name="prio[2]" id="prio2" />
					<label for="prio2"><img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" /></label> &nbsp;

					<input type="checkbox"{if $prio[4]} checked="checked"{/if} name="prio[4]" id="prio4" />
					<label for="prio4"><img src="{$tpldir}images/error.png" border="0" alt="" width="16" height="16" /></label>
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

<fieldset>
	<legend>{lng p="archiving"}</legend>
		
	<form action="{$pageURL}&action=logs&do=archive&sid={$sid}" method="post" onsubmit="if(EBID('saveCopy').checked || confirm('{lng p="reallynotarc"}')) spin(this); else return(false);">
		<p>
			{lng p="logarc_desc"}
		</p>
		
		<table>
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/archiving.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="80">{lng p="date"}:</td>
				<td class="td2">
					{html_select_date prefix="date" start_year="-5" field_order="DMY" field_separator="."}, 
					{html_select_time prefix="date" display_seconds=false}
				</td>
			</tr>
		</table>
		
		<p align="right">
			<input type="checkbox" name="saveCopy" id="saveCopy" checked="checked" />
			<label for="saveCopy"><b>{lng p="savearc"}</label>
			<input class="button" type="submit" value=" {lng p="execute"} " />
		</p>
	</form>
</fieldset>
