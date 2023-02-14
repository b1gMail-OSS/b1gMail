<fieldset>
	<legend>{lng p="logs"} ({date nice=true timestamp=$start} - {date nice=true timestamp=$end})</legend>

	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter table-striped">
				<thead>
		<tr>
			<th style="width: 20px;">&nbsp;</th>
			<th>{lng p="entry"}</th>
			<th style="width: 150px;">{lng p="date"}</th>
		</tr>
				</thead>
				<tbody>
		{foreach from=$entries item=entry}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td>
				{if $entry.prioImg == 'debug'}
					<i class="fa-solid fa-bug text-danger"></i>
				{elseif $entry.prioImg == 'info'}
					<i class="fa-solid fa-circle-info text-info"></i>
				{elseif $entry.prioImg == 'warning'}
					<i class="fa-solid fa-triangle-exclamation text-warning"></i>
				{elseif $entry.prioImg == 'error'}
					<i class="fa-regular fa-circle-xmark text-red"></i>
				{else}
					<i class="fa-solid fa-puzzle-piece text-cyan"></i>
				{/if}
			</td>
			<td><code>{text value=$entry.eintrag}</code></td>
			<td>{date nice=true timestamp=$entry.zeitstempel}</td>
		</tr>
		{/foreach}
				</tbody>
	</table>
		</div>
		<div class="card-footer text-end"><input class="btn btn-sm" type="button" value="{lng p="export"}" onclick="parent.frames['top'].location.href='logs.php?sid={$sid}&do=export&start={$start}&end={$end}&q={$ueQ}{$prioQ}';" /></div>
	</div>
</fieldset>

<fieldset>
	<legend>{lng p="filter"}</legend>
	
	<form action="logs.php?sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="row">
			<div class="col-md-6">
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{lng p="from"}</label>
					<div class="col-sm-10">
						{html_select_date prefix="start" time=$start start_year="-5" field_order="DMY" field_separator="."},
						{html_select_time prefix="start" time=$start display_seconds=false}
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{lng p="to"}</label>
					<div class="col-sm-10">
						{html_select_date prefix="end" time=$end start_year="-5" field_order="DMY" field_separator="."},
						{html_select_time prefix="end" time=$end display_seconds=false}
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{lng p="search"}</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="q" value="{if isset($q)}{text value=$q allowEmpty=true}{/if}" placeholder="{lng p="search"}">
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{lng p="priority"}</label>
					<div class="col-sm-10">
						<label class="form-check">
							<input class="form-check-input" type="checkbox"{if $prio[8]} checked="checked"{/if} name="prio[8]" id="prio8">
							<span class="form-check-label"><i class="fa-solid fa-bug text-danger"></i></span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox"{if $prio[2]} checked="checked"{/if} name="prio[2]" id="prio2">
							<span class="form-check-label"><i class="fa-solid fa-circle-info text-info"></i></span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox"{if $prio[1]} checked="checked"{/if} name="prio[1]" id="prio1">
							<span class="form-check-label"><i class="fa-solid fa-triangle-exclamation text-warning"></i></span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox"{if $prio[4]} checked="checked"{/if} name="prio[4]" id="prio4">
							<span class="form-check-label"><i class="fa-regular fa-circle-xmark text-red"></i></span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox"{if $prio[16]} checked="checked"{/if} name="prio[16]" id="prio16">
							<span class="form-check-label"><i class="fa-solid fa-puzzle-piece text-cyan"></i></span>
						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="apply"}" />
		</div>
	</form>
</fieldset>
