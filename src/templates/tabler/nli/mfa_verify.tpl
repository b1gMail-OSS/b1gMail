<div class="page page-center">
	<div class="container container-tight py-4">
		<div class="card card-md">
			<div class="card-body">
				<h2 class="h2 text-center mb-4">{text value=$pageTitle}</h2>

				{if isset($mfaError)}
					<div class="alert alert-danger">{text value=$mfaError}</div>
				{/if}
				{if isset($mfaInfo)}
					<div class="alert alert-info">{text value=$mfaInfo}</div>
				{/if}

				{if $recoveryMode}
					<p class="text-secondary">{lng p="mfa_verify_recovery_hint"}</p>
				{else}
					<p class="text-secondary">{lng p="mfa_verify_hint"}</p>
				{/if}

				<form method="post" action="{$nliUrlMfaVerify}{$sessionUrlSuffix}" autocomplete="off">
					{csrffield}
					<input type="hidden" name="do" value="mfaVerify" />
					<div class="mb-3">
						<label class="form-label">{lng p="mfa_code"}</label>
						<input type="text" class="form-control" name="mfa_code" inputmode="numeric" pattern="[0-9A-Za-z]*" maxlength="16" required="required" autofocus="autofocus" />
					</div>
					<div class="form-check mb-3">
						<input type="checkbox" class="form-check-input" name="mfa_use_backup" value="1" id="mfa_use_backup" />
						<label class="form-check-label" for="mfa_use_backup">{lng p="mfa_use_backup"}</label>
					</div>
					<div class="form-footer">
						<button type="submit" class="btn btn-primary w-100">{lng p="mfa_verify_submit"}</button>
					</div>
				</form>

				<form method="post" action="{$nliUrlMfaVerify}{$sessionUrlSuffix}" class="mt-3 text-center">
					{csrffield}
					<input type="hidden" name="do" value="mfaResend" />
					<button type="submit" class="btn btn-link">{lng p="mfa_resend_code"}</button>
				</form>

				<div class="text-center mt-3">
					<a href="{$nliUrlHome}{$sessionUrlSuffix}">{lng p="back"}</a>
				</div>
			</div>
		</div>
	</div>
</div>
