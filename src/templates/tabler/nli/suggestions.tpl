<p class="text-secondary small mb-2">
	{lng p="suggestions_desc"}
</p>

<div class="list-group list-group-flush bm-email-suggestion-list">
	{foreach from=$suggestions item=email}
	<div class="list-group-item d-flex flex-wrap align-items-center justify-content-between gap-2 px-0">
		<span class="text-break">
			<i class="ti ti-circle-check text-success me-1" aria-hidden="true"></i>
			{email value=$email}
		</span>
		<button type="button" class="btn btn-sm btn-primary" onclick="chooseAddress('{email value=$email}');return false;">{lng p="choose"}</button>
	</div>
	{/foreach}
</div>
