{include file="li/dialog.head.tpl" title=$title dialogBodyClass="bm-dialog-keyring-import"}

<div class="bm-dialog-page">
	<p class="text-secondary bm-dialog-intro mb-3">
		{if $pkcs12Support}
		{lng p="addprivcert12text"}
		{else}
		{lng p="addprivcerttext"}
		{/if}
	</p>

	<form action="{sessionurl file='prefs.php' params='action=keyring&do=uploadPrivateCertificate'}" enctype="multipart/form-data" method="post" autocomplete="off" class="bm-dialog-form">
		{csrffield}
		<div class="card">
			<div class="card-body">
				<div class="row g-3 align-items-center">
					{if $pkcs12Support}
					<div class="col-sm-3">
						<label class="form-label mb-0">* {lng p="pkcs12file"}:</label>
					</div>
					<div class="col-sm-9">
						{fileSelector name="pkcs12File" size="18"}
					</div>
					{else}
					<div class="col-sm-3">
						<label class="form-label mb-0">* {lng p="certificate"}:</label>
					</div>
					<div class="col-sm-9">
						{fileSelector name="certFile" size="18"}
					</div>

					<div class="col-sm-3">
						<label class="form-label mb-0">{lng p="chaincerts"}:</label>
					</div>
					<div class="col-sm-9">
						{fileSelector name="chainFile" size="18"}
					</div>

					<div class="col-sm-3">
						<label class="form-label mb-0">* {lng p="key"}:</label>
					</div>
					<div class="col-sm-9">
						{fileSelector name="pkeyFile" size="18"}
					</div>
					{/if}

					<div class="col-sm-3">
						<label for="pkeyPass" class="form-label mb-0">{lng p="password"}:</label>
					</div>
					<div class="col-sm-9">
						<input type="password" class="form-control" name="pkeyPass" id="pkeyPass" value="" />
					</div>
				</div>
			</div>
		</div>

		<div class="bm-dialog-actions">
			<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()">{lng p="cancel"}</button>
			<button type="submit" class="btn btn-primary">
				<i class="ti ti-check icon" aria-hidden="true"></i>
				{lng p="ok"}
			</button>
		</div>
	</form>
</div>

{include file="li/dialog.foot.tpl"}
