<div class="bm-msg-page">
	<div class="card bm-msg-card">
		<div class="card-body">
			<div class="empty bm-msg-empty">
				<div class="bm-msg-icon">
					{if isset($msgTone) && $msgTone == 'success'}
					<span class="avatar avatar-lg bg-green-lt text-green"><i class="ti ti-circle-check" aria-hidden="true"></i></span>
					{elseif isset($msgTone) && $msgTone == 'error'}
					<span class="avatar avatar-lg bg-red-lt text-red"><i class="ti ti-alert-circle" aria-hidden="true"></i></span>
					{else}
					<span class="avatar avatar-lg bg-azure-lt text-azure"><i class="ti ti-info-circle" aria-hidden="true"></i></span>
					{/if}
				</div>
				<p class="empty-title bm-msg-title">{$title}</p>
				<div class="empty-subtitle text-secondary bm-msg-text">{$msg}</div>
				<div class="empty-action">
					{if $backLink}
					<a class="btn btn-primary" href="{$backLink}">{lng p="back"}</a>
					{else}
					<button type="button" class="btn btn-primary" onclick="history.back();">{lng p="back"}</button>
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>
