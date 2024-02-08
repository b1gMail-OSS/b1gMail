<form action="{$pageURL}&sid={$sid}&save=true" method="post" onsubmit="spin(this)" name="f1">
	<fieldset>
		<legend>{lng p="am_mirrorings"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 25px;" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'mirroring_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="am_source"}</th>
						<th>{lng p="am_dest"}</th>
						<th>{lng p="am_timeframe"}</th>
						<th style="width: 65px;">{lng p="emails"}</th>
						<th style="width: 65px;">{lng p="am_errors"}</th>
						<th style="width: 35px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$mirrorings item=item}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center"><input type="checkbox" name="mirroring_{$item.mirrorid}" /></td>
							<td><a href="users.php?do=edit&id={$item.userid}&sid={$sid}">{$item.source}</a> (#{$item.userid})</td>
							<td><a href="users.php?do=edit&id={$item.mirror_to}&sid={$sid}">{$item.dest}</a> (#{$item.mirror_to})</td>
							<td>
								{if $item.begin==0&&$item.end==0}
									({lng p="unlimited"})
								{elseif $item.begin==0}
									{lng p="am_to"} {date timestamp=$item.end}
								{elseif $item.end==0}
									{lng p="am_from"} {date timestamp=$item.begin}
								{else}
									{date timestamp=$item.begin} - {date timestamp=$item.end}
								{/if}
							</td>
							<td>{$item.mail_count}</td>
							<td>{$item.error_count}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="{$pageURL}&delete={$item.mirrorid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer">
				<div style="float: left;">{lng p="action"}:&nbsp;</div>
				<div style="float: left;">
					<div class="btn-group btn-group-sm">
						<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="delete">{lng p="delete"}</option>
							</optgroup>
						</select>
						<input type="submit" name="executeMassAction" value="{lng p="execute"}" class="btn btn-sm btn-dark-lt" />
					</div>
				</div>
			</div>
		</div>
	</fieldset>
</form>

<fieldset>
	<legend>{lng p="am_add"}</legend>

	<form action="{$pageURL}&sid={$sid}&add=true" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="am_source"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="email_source" value="" placeholder="{lng p="am_source"}">
					<span class="input-group-text">{lng p="am_accemail"}</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="am_dest"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="email_dest" value="" placeholder="{lng p="am_dest"}">
					<span class="input-group-text">{lng p="am_accemail"}</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="from"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<span class="input-group-text">
						<input class="form-check-input m-0" type="checkbox" id="from_unlim" name="von_unlim" checked="checked">
					</span>
					<span class="input-group-text">{lng p="now"} {lng p="or"}</span>
					{html_select_date prefix="von" start_year="-5" field_order="DMY" field_separator="."},
					{html_select_time prefix="von" display_seconds=false}
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="to"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<span class="input-group-text">
						<input class="form-check-input m-0" type="checkbox" id="to_unlim" name="bis_unlim" checked="checked">
					</span>
					<span class="input-group-text">{lng p="now"} {lng p="or"}</span>
					{html_select_date prefix="bis" end_year="+5" field_order="DMY" field_separator="."},
					{html_select_time prefix="bis" display_seconds=false}
				</div>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="notices"}</legend>

	<div class="alert alert-warning">
		<ul style="margin:0;">
			<li>{lng p="am_notice1"}</li>
			<li>{lng p="am_notice2"}</li>
			<li>{lng p="am_notice3"}</li>
			<li>{lng p="am_notice4"}</li>
			<li>{lng p="am_notice5"}</li>
		</ul>
	</div>
</fieldset>
