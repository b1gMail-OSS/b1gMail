<div class="page page-center">
	<div class="container container-tight py-4">
		<div class="card card-md">
			<div class="card-body">
				<h2 class="h2 text-center mb-4">{text value=$pageTitle}</h2>

				<p class="text-secondary">{lng p="resetpwhint"}</p>

				{if isset($errorInfo)}
					<div class="alert alert-danger">{$errorInfo}</div>
				{/if}

				<form method="post" action="{$resetPasswordUrl}" autocomplete="off">
					{csrffield}
					<input type="hidden" name="do" value="setPassword" />
					<input type="hidden" name="key" value="{$resetPasswordKey}" />
					<div class="mb-3">
						<label class="form-label" for="pass1">{lng p="password"}</label>
						<input type="password" class="form-control" name="pass1" id="pass1" required="required" autocomplete="new-password" autofocus="autofocus" />
					</div>
					<div class="mb-3">
						<label class="form-label" for="pass2">{lng p="repeat"}</label>
						<input type="password" class="form-control" name="pass2" id="pass2" required="required" autocomplete="new-password" />
					</div>
					<div class="form-footer">
						<button type="submit" class="btn btn-primary w-100">{lng p="resetpwsubmit"}</button>
					</div>
				</form>

				<div class="text-center mt-3">
					<a href="{$nliUrlHome}{$sessionUrlSuffix}">{lng p="back"}</a>
				</div>
			</div>
		</div>
	</div>
</div>
