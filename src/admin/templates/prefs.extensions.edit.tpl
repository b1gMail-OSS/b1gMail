<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.extensions.php?do=edit&id={$extension.id}&save=true&sid={$sid}" method="post" onsubmit="spin(this)" enctype="multipart/form-data">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="filetypes"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="ext" value="{text value=$extension.ext}"{if $extension.ext[0]=='.'} disabled="disabled"{/if} placeholder="{lng p="filetypes"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="icon"}</label>
			<div class="col-sm-10">
				<input type="file" class="form-control" name="icon" accept="image/*" placeholder="{lng p="icon"}">
			</div>
		</div>
		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>