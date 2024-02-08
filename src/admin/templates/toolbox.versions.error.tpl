<fieldset>
	<legend>{lng p="error"}</legend>

	<div class="mb-3 row">
		<label class="col-sm-4 col-form-label">{lng p="toolboxfileerr"}</label>
		<div class="col-sm-8">
			<ul>
				{foreach from=$fileErrors item=item}
					<li>{text value=$item[0]} &raquo; {text value=$item[1]}</li>
				{/foreach}
			</ul>
		</div>
	</div>
</fieldset>

<div class="text-end">
	<input class="btn btn-primary" type="button" onclick="document.location.href='toolbox.php?do=editVersionConfig&versionid={$versionID}&sid={$sid}';" value=" {lng p="back"} " />
</div>
