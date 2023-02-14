<fieldset>
	<legend>{lng p="installplugin"}: {text value=$meta.name}</legend>
	
	<form action="plugins.php?action=install&do=installPlugin&id={$id}&sid={$sid}" method="post" onsubmit="spin(this)">
		<p>{lng p="install_desc2"}</p>

		<div class="text-center">
			<div id="sigLayer">
				<img src="{$tpldir}images/load_16.gif" align="absmiddle" border="0" alt="" />
				{lng p="checkingsig"}
			</div>

			<script>
				<!--
				registerLoadAction('checkPluginSignature(\'{$signature}\')');
				//-->
			</script>
		</div>

		<div class="row">
			<label class="col-sm-2 col-form-label">{lng p="name"}</label>
			<div class="col-sm-10">
				<div class="form-control-plaintext">{text value=$meta.name}</div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 col-form-label">{lng p="version"}</label>
			<div class="col-sm-10">
				<div class="form-control-plaintext">{text value=$meta.version}</div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 col-form-label">{lng p="vendor"}</label>
			<div class="col-sm-10">
				<div class="form-control-plaintext"><a target="_blank" href="{text value=$meta.vendor_url escape=true}">{text value=$meta.vendor}</a> (<a href="mailto:{text value=$meta.vendor_mail escape=true}">{text value=$meta.vendor_mail}</a>)</div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 col-form-label">{lng p="forb1gmail"}</label>
			<div class="col-sm-10">
				<div class="form-control-plaintext">
					{if !$versionsMatch}<i class="fa-solid fa-triangle-exclamation text-warning"></i>{else}<i class="fa-regular fa-circle-check text-green"></i>{/if}
					{text value=$meta.for_b1gmail}
					{if !$versionsMatch}({lng p="yourversion"}: {$b1gmailVersion}){/if}
				</div>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="install"}" />
		</div>
	</form>
</fieldset>
