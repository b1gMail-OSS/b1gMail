<div class="container" style="max-width:480px;margin-top:40px;">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">{text value=$pageTitle}</h3>
		</div>
		<div class="panel-body">
			{if isset($mfaError)}
				<div class="alert alert-danger">{text value=$mfaError}</div>
			{/if}
			{if isset($mfaInfo)}
				<div class="alert alert-info">{text value=$mfaInfo}</div>
			{/if}

			{if $recoveryMode}
				<p class="text-muted">{lng p="mfa_verify_recovery_hint"}</p>
			{else}
				<p class="text-muted">{lng p="mfa_verify_hint"}</p>
			{/if}

			<form method="post" action="{$nliUrlMfaVerify}{$sessionUrlSuffix}" autocomplete="off">
				{csrffield}
				<input type="hidden" name="do" value="mfaVerify" />
				<div class="form-group">
					<label for="mfa_code">{lng p="mfa_code"}</label>
					<input type="text" class="form-control" name="mfa_code" id="mfa_code" inputmode="numeric" pattern="[0-9A-Za-z]*" maxlength="16" required="required" autofocus="autofocus" />
				</div>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="mfa_use_backup" value="1" id="mfa_use_backup" />
						{lng p="mfa_use_backup"}
					</label>
				</div>
				<button type="submit" class="btn btn-primary btn-block">{lng p="mfa_verify_submit"}</button>
			</form>

			<form method="post" action="{$nliUrlMfaVerify}{$sessionUrlSuffix}" class="text-center" style="margin-top:12px;">
				{csrffield}
				<input type="hidden" name="do" value="mfaResend" />
				<button type="submit" class="btn btn-link">{lng p="mfa_resend_code"}</button>
			</form>

			<p class="text-center" style="margin-top:12px;">
				<a href="{$nliUrlHome}{$sessionUrlSuffix}">{lng p="back"}</a>
			</p>
		</div>
	</div>
</div>
