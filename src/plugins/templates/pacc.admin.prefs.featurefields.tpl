{if $paccMsg}
<div class="alert alert-{$paccMsg.type} mb-3" role="alert">{$paccMsg.text}</div>
{/if}

<form action="{sessionurl file='plugin.page.php' params="plugin={$paccPlugin}&do=prefs&action=featurefields"}" method="post" onsubmit="spin(this)">
	{csrffield}
	<fieldset>
		<legend>{lng p="pacc_fields"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped card-table">
					<thead>
					<tr>
						<th>{lng p="category"}</th>
						<th style="width: 70px;">{lng p="show"}</th>
						<th style="width: 70px;">{lng p="pacc_fieldpos"}</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$fieldPositions item=value key=key}
					<tr>
						<td>{$fieldTitles[$key]}</td>
						<td><input type="checkbox" class="form-check-input" name="fields[]" value="{$key}"{if $fields[$key]} checked="checked"{/if} /></td>
						<td><input type="text" class="form-control" name="positions[{$key}]" value="{$fieldPositions[$key]}" /></td>
					</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</fieldset>

	<div class="d-flex justify-content-between mt-3">
		<a href="{sessionurl file='plugin.page.php' params="plugin={$paccPlugin}&do=prefs"}" class="btn btn-outline-secondary">
			<i class="ti ti-arrow-left me-1"></i>
			{lng p="back"}
		</a>
		<button type="submit" name="save" value="1" class="btn btn-primary">
			<i class="ti ti-device-floppy me-1"></i>
			{lng p="save"}
		</button>
	</div>
</form>
