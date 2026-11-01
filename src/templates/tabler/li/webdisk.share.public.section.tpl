{*
 * Public link ("share via URL") section inside the combined webdisk
 * folder share dialog. Rendered by organizer.share.dialog.tpl when
 * $shareType == 'wdfolder' && $publicShareAvailable.
 *}
<div class="bm-webdisk-share-public modal-body border-top pt-3">
	<div class="d-flex align-items-center gap-2 mb-3">
		<i class="ti ti-link icon" aria-hidden="true"></i>
		<span class="fw-semibold text-secondary text-uppercase small">{lng p="sharepublicly"|default:"Öffentlicher Link"}</span>
	</div>

	{if !empty($publicShareSuccess)}
	<div class="alert alert-success py-2 mb-3">{$publicShareSuccess}</div>
	{/if}
	{if !empty($publicShareError)}
	<div class="alert alert-danger py-2 mb-3">{$publicShareError}</div>
	{/if}

	<form id="wdSharePublicForm" method="post" action="{sessionurl file=$shareAction params="action=share&id={$shareItem.id}"}" class="bm-webdisk-share-public-form">
		{csrffield}
		<input type="hidden" name="do" value="savePublic" />
		<input type="hidden" name="id" value="{$shareItem.id}" />

		<div class="form-check form-switch mb-3">
			<input class="form-check-input" type="checkbox" role="switch" id="wdSharePublicToggle" name="shareFolder" value="1"{if $publicShareEnabled} checked="checked"{/if} onchange="bmWebdiskPublicShareToggle(this)" />
			<label class="form-check-label" for="wdSharePublicToggle">{lng p="wd_share_enable_public"|default:"Öffentlichen Link aktivieren"}</label>
		</div>

		<div id="wdSharePublicFields"{if !$publicShareEnabled} style="display:none;"{/if}>
			<div class="row g-3 mb-3">
				<div class="col-6">
					<label class="form-label" for="wdSharePublicPW">{lng p="password"}{if $publicSharePasswordRequired} *{/if}</label>
					<div class="input-group input-group-flat">
						<input type="password" class="form-control" id="wdSharePublicPW" name="sharePW" value="{if isset($publicSharePW)}{text value=$publicSharePW allowEmpty=true}{/if}" autocomplete="new-password" />
						<span class="input-group-text">
							<a href="#" class="link-secondary" onclick="bmWebdiskPublicShareToggleVisibility(this); return false;" aria-label="{lng p="show"}">
								<i class="ti ti-eye icon" aria-hidden="true"></i>
							</a>
						</span>
					</div>
				</div>
				<div class="col-6">
					<label class="form-label" for="wdSharePublicUntil">{lng p="wd_share_expiry"}{if $publicShareExpiryRequired} *{/if}</label>
					<input type="date" class="form-control" id="wdSharePublicUntil" name="shareUntil" value="{$publicShareUntilDate|default:''}" min="{$publicShareExpiryMinDate|default:''}"{if $publicShareExpiryMaxDate|default:'' != ''} max="{$publicShareExpiryMaxDate}"{/if} />
					{if $publicShareExpiryMaxDays|default:0 > 0}
					<div class="form-hint small">({lng p="max"}: {$publicShareExpiryMaxDays} {lng p="days"})</div>
					{/if}
				</div>
			</div>

			{if $publicShareEnabled}
			<div class="mb-1">
				<label class="form-label">{lng p="publiclink"|default:"Öffentlicher Link"}</label>
				<div class="input-group input-group-flat">
					<input type="text" class="form-control" id="wdSharePublicUrl" value="{$publicShareUrl|escape}" readonly onclick="this.select();" />
					<span class="input-group-text">
						<a href="#" class="link-secondary" onclick="bmWebdiskPublicShareCopy(); return false;" aria-label="{lng p="copy"}">
							<i class="ti ti-copy icon" aria-hidden="true"></i>
						</a>
					</span>
				</div>
			</div>
			{/if}
		</div>
	</form>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()">{lng p="close"}</button>
	<button type="submit" form="wdSharePublicForm" class="btn btn-primary">
		<i class="ti ti-device-floppy icon icon-sm me-1" aria-hidden="true"></i>
		{lng p="save"}
	</button>
</div>

<script>
function bmWebdiskPublicShareToggle(cb)
{
	var box = document.getElementById('wdSharePublicFields');
	if(!box) return;
	box.style.display = cb.checked ? '' : 'none';
}
function bmWebdiskPublicShareToggleVisibility(trigger)
{
	var input = document.getElementById('wdSharePublicPW');
	if(!input || !trigger) return;
	var icon = trigger.querySelector('i');
	if(input.type === 'password')
	{
		input.type = 'text';
		if(icon) icon.className = 'ti ti-eye-off icon';
	}
	else
	{
		input.type = 'password';
		if(icon) icon.className = 'ti ti-eye icon';
	}
}
function bmWebdiskPublicShareCopy()
{
	var input = document.getElementById('wdSharePublicUrl');
	if(!input) return;
	input.focus();
	input.select();
	try { document.execCommand('copy'); } catch(e) {}
	if(navigator.clipboard && navigator.clipboard.writeText)
		navigator.clipboard.writeText(input.value);
}
</script>
