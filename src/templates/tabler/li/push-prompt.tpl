{* Push availability prompt – mobile (full width) or desktop (content area)
   Params: promptId, promptVariant (mobile|desktop) *}
<div id="{$promptId|escape}" class="bm-push-prompt bm-push-prompt--{$promptVariant|escape}" role="alert" style="display:none;">
	{if $promptVariant == 'desktop'}
	<div class="bm-push-prompt-inner">
		<div class="bm-push-prompt-icon" aria-hidden="true">
			<i class="icon ti ti-bell"></i>
		</div>
		<div class="bm-push-prompt-text">{lng p="push_prompt_text_short"}</div>
		<div class="bm-push-prompt-actions">
			<button type="button" class="btn btn-primary btn-sm bm-push-prompt-enable">{lng p="push_enable"}</button>
			<button type="button" class="btn btn-outline-secondary btn-sm bm-push-prompt-decline">{lng p="push_prompt_decline"}</button>
			<a href="{sessionurl file='prefs.php' params='action=common'}" class="btn btn-ghost-primary btn-sm">{lng p="prefs"}</a>
		</div>
	</div>
	{else}
	<div class="alert alert-info alert-dismissible mb-0 rounded-0 border-0 border-bottom">
		<div class="container-xl py-2 d-flex flex-wrap align-items-center gap-2">
			<div class="flex-fill">{lng p="push_prompt_text"}</div>
			<button type="button" class="btn btn-primary btn-sm bm-push-prompt-enable">{lng p="push_enable"}</button>
			<button type="button" class="btn btn-outline-secondary btn-sm bm-push-prompt-decline">{lng p="push_prompt_decline"}</button>
			<a href="{sessionurl file='prefs.php' params='action=common'}" class="btn btn-outline-primary btn-sm">{lng p="prefs"}</a>
			<button type="button" class="btn btn-ghost-secondary btn-sm bm-push-prompt-dismiss" aria-label="{lng p="close"}">&times;</button>
		</div>
	</div>
	{/if}
</div>
