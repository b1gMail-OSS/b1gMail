<fieldset>
	<legend>{lng p="installplugin"}</legend>

	<form action="{sessionurl file='plugins.php' params="action=install&do=uploadPlugin"}" method="post" enctype="multipart/form-data" onsubmit="spin(this)">
		{csrffield}
		<div class="alert alert-warning">{lng p="sourcewarning"}</div>
		<p>{lng p="install_desc"}</p>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="plugpackage"}</label>
			<div class="col-sm-10">
				<input type="file" class="form-control" name="package" accept=".bmplugin" placeholder="{lng p="plugpackage"}">
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="install"}" />
		</div>
	</form>
</fieldset>