{foreach from=$amMsgs item=msg}
<div class="alert alert-{$msg.type} mb-3" role="alert">{$msg.text}</div>
{/foreach}

<form id="amMassForm" action="{$pageURL}{$sessionUrlSuffixHtml}" method="post" class="d-none" aria-hidden="true">
	{csrffield}
</form>

<div class="card mb-4">
		<div class="card-header">
			<h3 class="card-title">{lng p="am_mirrorings"}</h3>
		</div>
		<div class="table-responsive">
			<table class="table table-vcenter table-striped card-table">
				<thead>
					<tr>
						<th style="width:25px;text-align:center;">
							<a href="javascript:invertSelection(document.getElementById('amMassForm'),'mirroring_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a>
						</th>
						<th>{lng p="am_source"}</th>
						<th>{lng p="am_dest"}</th>
						<th>{lng p="am_timeframe"}</th>
						<th style="width:5rem;" class="text-end">{lng p="emails"}</th>
						<th style="width:5rem;" class="text-end">{lng p="am_errors"}</th>
						<th>{lng p="am_reason"}</th>
						<th style="width:4rem;" class="text-end">{lng p="action"}</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$mirrorings item=item}
					<tr>
						<td class="text-center"><input type="checkbox" class="form-check-input m-0" name="mirroring_{$item.mirrorid}" form="amMassForm" /></td>
						<td>
							<a href="users.php?do=edit&amp;id={$item.userid}{$sessionUrlSuffixHtml}">{text value=$item.source}</a>
							<span class="text-secondary small">(#{$item.userid})</span>
						</td>
						<td>
							<a href="users.php?do=edit&amp;id={$item.mirror_to}{$sessionUrlSuffixHtml}">{text value=$item.dest}</a>
							<span class="text-secondary small">(#{$item.mirror_to})</span>
						</td>
						<td class="text-secondary small">
							{if $item.begin==0&&$item.end==0}
								({lng p="unlimited"})
							{elseif $item.begin==0}
								{lng p="am_to"} {date timestamp=$item.end}
							{elseif $item.end==0}
								{lng p="am_from"} {date timestamp=$item.begin}
							{else}
								{date timestamp=$item.begin} – {date timestamp=$item.end}
							{/if}
						</td>
						<td class="text-end">{text value=$item.mail_count}</td>
						<td class="text-end">{text value=$item.error_count}</td>
						<td>{text value=$item.reason}</td>
						<td class="text-end text-nowrap">
							<form method="post" action="{$pageURL}{$sessionUrlSuffixHtml}" class="d-inline" onsubmit="return confirm('{lng p="realdel"}');">
								{csrffield}
								<input type="hidden" name="am_delete_id" value="{$item.mirrorid}" />
								<button type="submit" class="btn btn-sm" title="{lng p="delete"}"><i class="ti ti-trash text-danger"></i></button>
							</form>
						</td>
					</tr>
				{foreachelse}
					<tr>
						<td colspan="8" class="text-secondary">{lng p="none"}</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
		{if $mirrorings|@count > 0}
		<div class="card-footer d-flex flex-wrap align-items-center gap-2">
			<span class="text-secondary">{lng p="action"}:</span>
			<select name="massAction" class="form-select form-select-sm" style="width:auto;" form="amMassForm">
				<option value="-">------------</option>
				<optgroup label="{lng p="actions"}">
					<option value="delete">{lng p="delete"}</option>
				</optgroup>
			</select>
			<button type="submit" name="executeMassAction" value="1" class="btn btn-sm btn-primary" form="amMassForm" onclick="spin(document.getElementById('amMassForm'));">{lng p="execute"}</button>
		</div>
		{/if}
</div>

<div class="card mb-4">
	<div class="card-header">
		<h3 class="card-title">{lng p="am_add"}</h3>
	</div>
	<div class="card-body">
		<form action="{$pageURL}{$sessionUrlSuffixHtml}" method="post" onsubmit="spin(this)">
			{csrffield}
			<input type="hidden" name="am_add" value="1" />

			<div class="mb-3 row">
				<label class="col-sm-3 col-form-label" for="email_source">{lng p="am_source"}</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="email_source" name="email_source" value="" />
					<div class="form-hint">{lng p="am_accemail"}</div>
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-3 col-form-label" for="email_dest">{lng p="am_dest"}</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="email_dest" name="email_dest" value="" />
					<div class="form-hint">{lng p="am_accemail"}</div>
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-3 col-form-label">{lng p="from"}</label>
				<div class="col-sm-9">
					<label class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" checked="checked" id="from_unlim" name="von_unlim" />
						<span class="form-check-label"><strong>{lng p="now"}</strong></span>
					</label>
					<span class="text-secondary mx-1">{lng p="or"}</span>
					{html_select_date prefix="von" start_year="-5" field_order="DMY" field_separator="."},
					{html_select_time prefix="von" display_seconds=false}
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-3 col-form-label">{lng p="to"}</label>
				<div class="col-sm-9">
					<label class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" checked="checked" id="to_unlim" name="bis_unlim" />
						<span class="form-check-label"><strong>{lng p="unlimited"}</strong></span>
					</label>
					<span class="text-secondary mx-1">{lng p="or"}</span>
					{html_select_date prefix="bis" end_year="+5" field_order="DMY" field_separator="."},
					{html_select_time prefix="bis" display_seconds=false}
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-3 col-form-label" for="am_reason">{lng p="am_reason"}</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="am_reason" name="reason" value="" />
				</div>
			</div>

			<div class="text-end">
				<button type="submit" class="btn btn-primary">
					<i class="ti ti-plus me-1"></i>{lng p="add"}
				</button>
			</div>
		</form>
	</div>
</div>

<div class="card">
	<div class="card-header">
		<h3 class="card-title">{lng p="notices"}</h3>
	</div>
	<div class="card-body">
		<div class="alert alert-warning mb-0" role="alert">
			<ul class="mb-0 ps-3">
				<li>{lng p="am_notice1"}</li>
				<li>{lng p="am_notice2"}</li>
				<li>{lng p="am_notice3"}</li>
				<li>{lng p="am_notice4"}</li>
				<li>{lng p="am_notice5"}</li>
			</ul>
		</div>
	</div>
</div>
