<fieldset>
	<legend>{lng p="mailorphans"}</legend>

	<div class="alert alert-warning">{lng p="undowarn"}</div>

	<form action="maintenance.php?action=orphans&do=exec&sid={$sid}" method="post" onsubmit="spin(this)">
		<p>{lng p="orphans_desc"}</p>

		<div class="text-end">
			<input class="btn btn-sm btn-warning" type="submit" value="{lng p="execute"}" />
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="diskorphans"}</legend>

	<div class="alert alert-warning">{lng p="undowarn"}</div>
	
	<form action="maintenance.php?action=orphans&do=diskExec&sid={$sid}" method="post" onsubmit="spin(this)">
		<p>{lng p="diskorphans_desc"}</p>

		<div class="text-end">
			<input class="btn btn-sm btn-warning" type="submit" value="{lng p="execute"}" />
		</div>
	</form>
</fieldset>
