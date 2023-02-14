<fieldset>
	<legend>{lng p="db"}</legend>

	<div class="alert alert-warning">{lng p="dbwarn"}</div>

	{if $execute}
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th>{lng p="table"}</th>
						<th>{lng p="query"}</th>
						<th style="width: 100px;">{lng p="status"}</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$result item=table}
						{cycle values="td1,td2" name=class assign=class}
						<tr class="{$class}">
							<td>{$table.table}</td>
							<td><code>{$table.query}</code></td>
							<td class="text-nowrap text-end">
								{if $table.type!='status' && $table.type!='info' && $table.type!='note'}
									<i class="fa-regular fa-circle-xmark text-red"></i>
									{lng p="error"}
								{else}
									<i class="fa-regular fa-circle-check text-green"></i>
									{lng p="success"}
								{/if}
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer text-end">
				<input class="btn btn-sm btn-primary" type="button" value="{lng p="back"}" onclick="document.location.href='optimize.php?sid={$sid}';" />
			</div>
		</div>
	{elseif $executeStruct}
		<form action="optimize.php?do=repairStruct&sid={$sid}" method="post" onsubmit="spin(this)">

			{if $repair}<div class="alert alert-warning">{lng p="dbwarn"}</div>{/if}

			<div class="card">
				<div class="table-responsive">
					<table class="table table-vcenter table-striped">
						<thead>
						<tr>
							<th>{lng p="table"}</th>
							<th style="width: 120px;">{lng p="exists"}</th>
							<th style="width: 120px;">{lng p="structstate"}</th>
							<th style="width: 120px;">{lng p="status"}</th>
						</tr>
						</thead>
						<tbody>
						{foreach from=$result item=table}
							{cycle values="td1,td2" name=class assign=class}
							<tr class="{$class}">
								<td>{$table.table}</td>
								<td>{if $table.exists}{lng p="yes"}{else}{lng p="no"}{/if}</td>
								<td>{$table.missing} / {$table.invalid}</td>
								<td class="text-nowrap text-end">
									{if !$table.exists || $table.missing || $table.invalid}
										<i class="fa-regular fa-circle-xmark text-red"></i>
										{lng p="error"}
									{else}
										<i class="fa-regular fa-circle-check text-green"></i>
										{lng p="ok"}
									{/if}
								</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				</div>
				<div class="card-footer">
					<div class="row">
						<div class="col-md-6">{if $repair}<input class="btn btn-sm btn-warning" type="submit" value="{lng p="repairstruct"}" />{/if}</div>
						<div class="col-md-6 text-end"><input class="btn btn-sm btn-primary" type="button" value=" {lng p="back"} " onclick="document.location.href='optimize.php?sid={$sid}';" /></div>
					</div>
				</div>
			</div>
		</form>
	{else}
		<form action="optimize.php?sid={$sid}&do=execute" method="post" onsubmit="spin(this)">
			<div class="row">
				<div class="col-md-4">
					<div class="mb-3">
						<label class="col-sm-4 col-form-label">{lng p="tables"}</label>
						<select size="10" name="tables[]" multiple="multiple" class="form-select">
							{foreach from=$tables item=table}
								<option value="{$table}" selected="selected">{$table}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="col-md-8">
					<div class="mb-3">
						<label class="col-sm-4 col-form-label">{lng p="action"}</label>
						<div class="mb-3">
							<div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
								<label class="form-selectgroup-item flex-fill">
									<input class="form-selectgroup-input" type="radio" id="op_optimize" name="operation" value="optimize" checked="checked">
									<div class="form-selectgroup-label d-flex align-items-center p-3">
										<div class="me-3"><span class="form-selectgroup-check"></span></div>
										<div>{lng p="op_optimize"}<br /><small>{lng p="op_optimize_desc"}</small></div>
									</div>
								</label>
								<label class="form-selectgroup-item flex-fill">
									<input class="form-selectgroup-input" type="radio" id="op_repair" name="operation" value="repair">
									<div class="form-selectgroup-label d-flex align-items-center p-3">
										<div class="me-3"><span class="form-selectgroup-check"></span></div>
										<div>{lng p="op_repair"}<br /><small>{lng p="op_repair_desc"}</small></div>
									</div>
								</label>
								<label class="form-selectgroup-item flex-fill">
									<input class="form-selectgroup-input" type="radio" id="op_struct" name="operation" value="struct">
									<div class="form-selectgroup-label d-flex align-items-center p-3">
										<div class="me-3"><span class="form-selectgroup-check"></span></div>
										<div>{lng p="op_struct"}<br /><small>{lng p="op_struct_desc"}</small></div>
									</div>
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="text-end">
				<input class="btn btn-primary" type="submit" value=" {lng p="execute"} " />
			</div>
		</form>
	{/if}
</fieldset>