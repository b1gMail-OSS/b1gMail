<div class="nav-item me-3">
	<a href="#" class="nav-link d-flex lh-1 p-0 px-2" onclick="showUserMenu(this); return false;" aria-label="{lng p="prefs"}">
		<span class="avatar avatar-sm">{$_userInitials|escape}</span>
		<div class="d-none d-xl-block ps-2">
			<div>
				{if $_userDisplayName|default:'' != ''}
				{text value=$_userDisplayName allowEmpty=true}
				{else}
				{$_userEmail|escape}
				{/if}
			</div>
			<div class="mt-1 small text-secondary">{$_userEmail|escape}</div>
		</div>
	</a>
</div>
