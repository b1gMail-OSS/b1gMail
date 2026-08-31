<div class="nav-item bm-li-navbar-user">
	<a href="#" class="nav-link d-flex lh-1 text-reset p-0 px-2" onclick="showUserMenu(this); return false;" aria-label="{lng p="prefs"}">
		{include file="li/user-avatar.tpl" avatarSize="sm" avatarBgPrimary=true}
		<div class="d-none d-lg-block ps-2 bm-li-user-label">
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
