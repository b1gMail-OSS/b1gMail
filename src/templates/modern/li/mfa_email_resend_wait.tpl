<div class="text-center" id="bmMfaResendWrap" style="margin-top:8px;"{if $mfaEmailCodeExpiresAt|default:0 > 0} data-expires-at="{$mfaEmailCodeExpiresAt|escape}" data-wait-prefix="{$mfaResendWaitPrefix|escape}"{/if}>
	<button type="submit" form="bmMfaResendForm" formnovalidate="formnovalidate" class="btn btn-link btn-sm" id="bmMfaResendBtn"{if $mfaEmailCodeExpiresAt|default:0 > 0} disabled="disabled"{/if}>{lng p="mfa_resend_code"}</button>
	<div class="text-muted small" id="bmMfaResendWait"{if $mfaEmailCodeExpiresAt|default:0 <= 0} style="display:none;"{/if}></div>
</div>
{if $mfaEmailCodeExpiresAt|default:0 > 0}
<script>
(function() {
	var wrap = document.getElementById('bmMfaResendWrap');
	var btn = document.getElementById('bmMfaResendBtn');
	var waitEl = document.getElementById('bmMfaResendWait');
	var alertEl = document.getElementById('bmMfaCodeValidityAlert');
	if(!wrap || !btn || !waitEl)
		return;
	var expiresAt = parseInt(wrap.getAttribute('data-expires-at') || '0', 10);
	if(expiresAt <= 0)
		return;
	var prefix = wrap.getAttribute('data-wait-prefix') || 'Resend in %s';
	function formatMmSs(totalSec) {
		totalSec = Math.max(0, parseInt(totalSec, 10));
		var m = Math.floor(totalSec / 60);
		var s = totalSec % 60;
		return m + ':' + (s < 10 ? '0' : '') + s;
	}
	function tick() {
		var sec = Math.max(0, expiresAt - Math.floor(Date.now() / 1000));
		if(sec <= 0) {
			btn.disabled = false;
			waitEl.style.display = 'none';
			if(alertEl)
				alertEl.style.display = 'none';
			return;
		}
		btn.disabled = true;
		waitEl.style.display = '';
		waitEl.textContent = prefix.replace('%s', formatMmSs(sec));
		setTimeout(tick, 1000);
	}
	tick();
})();
</script>
{/if}
