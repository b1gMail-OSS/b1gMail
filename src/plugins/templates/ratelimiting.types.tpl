{if $rlMsg}
<div class="alert alert-{$rlMsg.type} mb-3" role="alert">{$rlMsg.text}</div>
{/if}

<fieldset class="mb-4">
	<legend class="h4 mb-3">Rate Limiting</legend>

	<form action="{sessionurl file='plugin.page.php' params="plugin={$rlPlugin}&do=types"}" method="post">
		{csrffield}

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped card-table">
					<thead>
					<tr>
						<th>{lng p="ratelimiting_event"}</th>
						<th style="width:14rem;">{lng p="ratelimiting_max_events"}</th>
						<th style="width:14rem;">{lng p="ratelimiting_in_seconds"}</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$types item=type}
					<tr>
						<td><code>{text value=$type.type}</code></td>
						<td>
							<input type="number" class="form-control" name="types[{$type.type}][max_events]" value="{$type.max_events}" min="1" step="1" required="required" />
						</td>
						<td>
							<input type="number" class="form-control" name="types[{$type.type}][in_seconds]" value="{$type.in_seconds}" min="1" step="1" required="required" />
						</td>
					</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>

		<div class="text-end mt-3">
			<button type="submit" name="save" value="1" class="btn btn-primary">
				<i class="ti ti-device-floppy me-1"></i>
				{lng p="save"}
			</button>
		</div>
	</form>
</fieldset>
