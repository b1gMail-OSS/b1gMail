<form action="{$pageURL}&sid={$sid}&action=prefs&do=featureFields&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="pacc_fields"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th>{lng p="category"}</th>
						<th style="width: 70px;">{lng p="show"}</th>
						<th style="width: 70px;">{lng p="pacc_fieldpos"}</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$fieldPositions item=value key=key}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td>{$fieldTitles[$key]}</td>
							<td><input type="checkbox" class="form-check-input" name="fields[]" value="{$key}"{if $fields[$key]} checked="checked"{/if} /></td>
							<td><input type="text" class="form-control" name="positions[{$key}]" value="{$fieldPositions[$key]}" /></td>
						</tr>
					{/foreach}
				</table>
			</div>
		</div>
	</fieldset>

	<div class="row">
		<div class="col-md-6"><input class="btn btn-primary" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='{$pageURL}&action=prefs&sid={$sid}';" /></div>
		<div class="col-md-6 text-end"><input class="btn btn-primary" type="submit" value=" {lng p="save"} " /></div>
	</div>
</form>