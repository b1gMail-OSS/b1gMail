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
						<label class="form-label d-block" id="mfaCodeLabel">{lng p="mfa_code"}</label>
						<div class="d-flex justify-content-center" id="mfaOtpCodeWrap">
							<div class="otp otp-lg bm-mfa-otp" id="mfaOtpCode" data-bs-toggle="otp" data-bs-length="6" data-bs-groups="[3,3]" data-bs-type="numeric">
								<input type="text" name="mfa_code" id="mfa_code" maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" required="required" autofocus="autofocus" aria-labelledby="mfaCodeLabel" />
							</div>
						</div>
						<div class="d-flex justify-content-center d-none" id="mfaOtpBackupWrap">
							<div class="otp otp-lg bm-mfa-otp" id="mfaOtpBackup" data-bs-toggle="otp" data-bs-length="8" data-bs-groups="[4,4]" data-bs-type="numeric">
								<input type="text" id="mfa_code_backup" maxlength="8" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" aria-labelledby="mfaCodeLabel" />
							</div>
						</div>
					</div>
					<div class="form-check mb-3">
						<input type="checkbox" class="form-check-input" name="mfa_use_backup" value="1" id="mfa_use_backup" onchange="bmMfaBackupToggle(this.checked)" />
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
				<script>
				function bmInitMfaOtp(root) {
					if(!window.tabler || !tabler.OtpInput)
						return;
					(root || document).querySelectorAll('.bm-mfa-otp[data-bs-toggle="otp"]').forEach(function(el) {
						tabler.OtpInput.getOrCreateInstance(el);
					});
				}
				bmInitMfaOtp();
				function bmMfaBackupToggle(on) {
					var codeWrap = document.getElementById('mfaOtpCodeWrap');
					var backupWrap = document.getElementById('mfaOtpBackupWrap');
					var code = document.getElementById('mfa_code');
					var backup = document.getElementById('mfa_code_backup');
					if(!code || !backup)
						return;
					if(on) {
						codeWrap.classList.add('d-none');
						backupWrap.classList.remove('d-none');
						code.disabled = true;
						code.required = false;
						code.removeAttribute('name');
						backup.disabled = false;
						backup.required = true;
						backup.setAttribute('name', 'mfa_code');
						backup.focus();
					} else {
						backupWrap.classList.add('d-none');
						codeWrap.classList.remove('d-none');
						backup.disabled = true;
						backup.required = false;
						backup.removeAttribute('name');
						code.disabled = false;
						code.required = true;
						code.setAttribute('name', 'mfa_code');
						code.focus();
					}
				}
				</script>
			</div>
		</div>
	</div>
</div>
