<fieldset class="mb-4">
	<legend class="h4 mb-3">{lng p="logs"} ({date nice=true timestamp=$start} - {date nice=true timestamp=$end})</legend>
	
	<div class="card mb-3"><div class="table-responsive"><table class="table table-vcenter table-striped card-table">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="90">{lng p="bms_component"}</th>
			<th>{lng p="entry"}</th>
			<th width="150">{lng p="date"}
				<img src="{$tpldir}images/sort_desc.png" border="0" alt="" width="7" height="6" align="absmiddle" /></th>
		</tr>
		
		{foreach from=$entries item=entry}
		<tr>
			<td><img src="{$tpldir}images/{$entry.prioImg}.png" border="0" alt="" width="16" height="16" /></td>
			<td>{text value=$entry.componentName}</td>
			<td><code>{$entry.szEntry}</code></td>
			<td>{date nice=true timestamp=$entry.iDate}</td>
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="4">
				<div style="float:left;">
					<button type="button" class="btn btn-outline-secondary btn-sm"  onclick="parent.frames['top'].location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=logs&action=export&start={$start}&end={$end}&page={$pageNo}&q={$ueQ}{$prioQ}"}';" >{lng p="export"}</button>
				</div>
				<div style="float:right;padding-top:3px;">
					{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"{$pageURL}start={$start}&end={$end}&q={$ueQ}{$prioQ}&page=.s\">.t</a></span> "}&nbsp;
				</div>
			</td>
		</tr>
	</table></div></div>
</fieldset>

<fieldset class="mb-4">
	<legend class="h4 mb-3">{lng p="filter"}</legend>
	
	<form action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=logs"}" method="post" onsubmit="spin(this)">
	{csrffield}
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
					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox"{if isset($component[1]) && $component[1]} checked="checked"{/if} name="component[1]" id="component1" /><span class="form-check-label" for="component1">Core</span></label> &nbsp;

					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox"{if isset($component[2]) && $component[2]} checked="checked"{/if} name="component[2]" id="component2" /><span class="form-check-label" for="component2">POP3</span></label> &nbsp;

					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox"{if isset($component[4]) && $component[4]} checked="checked"{/if} name="component[4]" id="component4" /><span class="form-check-label" for="component4">IMAP</span></label> &nbsp;

					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox"{if isset($component[8]) && $component[8]} checked="checked"{/if} name="component[8]" id="component8" /><span class="form-check-label" for="component8">HTTP</span></label> &nbsp;

					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox"{if isset($component[16]) && $component[16]} checked="checked"{/if} name="component[16]" id="component16" /><span class="form-check-label" for="component16">SMTP</span></label> &nbsp;

					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox"{if isset($component[32]) && $component[32]} checked="checked"{/if} name="component[32]" id="component32" /><span class="form-check-label" for="component32">MSGQueue</span></label> &nbsp;

					<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox"{if isset($component[64]) && $component[64]} checked="checked"{/if} name="component[64]" id="component64" /><span class="form-check-label" for="component64">Plugin</span></label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="priority"}:</td>
				<td class="td2">
					<label class="form-check mb-0"><input class="form-check-input" type="checkbox"{if !empty($prio[8])} checked="checked"{/if} name="prio[8]" id="prio8" /><span class="form-check-label" for="prio8"><img src="{$tpldir}images/debug.png" border="0" alt="" width="16" height="16" /></span></label> &nbsp;

					<label class="form-check mb-0"><input class="form-check-input" type="checkbox"{if !empty($prio[1])} checked="checked"{/if} name="prio[1]" id="prio1" /><span class="form-check-label" for="prio1"><img src="{$tpldir}images/info.png" border="0" alt="" width="16" height="16" /></span></label> &nbsp;

					<label class="form-check mb-0"><input class="form-check-input" type="checkbox"{if !empty($prio[2])} checked="checked"{/if} name="prio[2]" id="prio2" /><span class="form-check-label" for="prio2"><img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" /></span></label> &nbsp;

					<label class="form-check mb-0"><input class="form-check-input" type="checkbox"{if !empty($prio[4])} checked="checked"{/if} name="prio[4]" id="prio4" /><span class="form-check-label" for="prio4"><img src="{$tpldir}images/error.png" border="0" alt="" width="16" height="16" /></span></label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="search"}:</td>
				<td class="td2">
						<input class="form-control" type="text" name="q" value="{if isset($q)}{text value=$q allowEmpty=true}{/if}" size="36" style="width:85%;" />
				</td>
			</tr>
		</table>
		
		<p align="right">
			<button type="submit" class="btn btn-primary"  >{lng p="apply"}</button>
		</p>
	</form>
</fieldset>

<fieldset class="mb-4">
	<legend class="h4 mb-3">{lng p="archiving"}</legend>
		
	<form action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=logs&action=archive"}" method="post" onsubmit="if(EBID('saveCopy').checked || confirm('{lng p="reallynotarc"}')) spin(this); else return(false);">
	{csrffield}
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
			<label class="form-check form-check-inline mb-0"><input class="form-check-input" type="checkbox" name="saveCopy" id="saveCopy" checked="checked" /><span class="form-check-label" for="saveCopy"><b>{lng p="savearc"}</b></span></label>
			<button type="submit" class="btn btn-primary"  >{lng p="execute"}</button>
		</p>
	</form>
</fieldset>
