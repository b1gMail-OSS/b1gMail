{* Push availability prompt (modern template) *}
<div id="bmPushPrompt" class="bm-push-prompt" role="alert" style="display:none;">
	<div class="bm-push-prompt-inner">
		<i class="fa fa-bell" aria-hidden="true"></i>
		<span class="bm-push-prompt-text">{lng p="push_prompt_text"}</span>
		<button type="button" class="primary bm-push-prompt-enable">{lng p="push_enable"}</button>
		<button type="button" class="bm-push-prompt-decline">{lng p="push_prompt_decline"}</button>
		<a href="prefs.php?action=common&amp;sid={$sid}">{lng p="prefs"}</a>
		<button type="button" class="bm-push-prompt-dismiss" aria-label="{lng p="close"}">&times;</button>
	</div>
</div>
