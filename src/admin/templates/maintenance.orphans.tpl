<fieldset>
	<legend>{lng p="mailorphans"}</legend>

	<div class="alert alert-warning">{lng p="undowarn"}</div>

	<form action="{adminurl script='maintenance.php' params='action=orphans&do=exec' trailingAmp='false'}" method="post" onsubmit="spin(this)">
		{csrffield}
		<p>{lng p="orphans_desc"}</p>

		<div class="text-end">
			<input class="btn btn-sm btn-warning" type="submit" value="{lng p="execute"}" />
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="diskorphans"}</legend>

	<div class="alert alert-warning">{lng p="undowarn"}</div>
	
	<form action="{adminurl script='maintenance.php' params='action=orphans&do=diskExec' trailingAmp='false'}" method="post" onsubmit="spin(this)">
		{csrffield}
		<p>{lng p="diskorphans_desc"}</p>

		<div class="text-end">
			<input class="btn btn-sm btn-warning" type="submit" value="{lng p="execute"}" />
		</div>
	</form>
</fieldset>
