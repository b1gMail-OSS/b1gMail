{capture assign="standalonePageTitle"}{$service_title}: {lng p="session_locked"}{/capture}
{include file="nli/standalone.open.tpl"}

<div class="page page-center flex-fill">
	<div class="container-tight py-4">
		{include file="nli/login.brand.tpl"}

		<form class="card card-md" id="bmSessionLockForm" method="post" action="{sessionurl file='start.php' params='action=sessionUnlock'}" autocomplete="off">
			{csrffield}
			<div class="card-body text-center">
				<div class="mb-4">
					<h2 class="card-title">{lng p="session_locked"}</h2>
					<p class="text-secondary">{lng p="session_locked_desc"}</p>
				</div>
				{if $sessionLockName != ''}
				<div class="mb-4">
					{include file="li/user-avatar.tpl" avatarSize="xl" avatarClass="mb-3" avatarBgPrimary=true}
					<h3 class="mb-0">{text value=$sessionLockName}</h3>
					{if $sessionLockEmail != ''}<div class="text-secondary">{email value=$sessionLockEmail}</div>{/if}
				</div>
				{/if}
				{if $sessionUnlockError|default:'' != ''}
				<div class="alert alert-danger" role="alert">{text value=$sessionUnlockError}</div>
				{/if}
				<div class="mb-4">
					<input type="password" class="form-control" name="password" id="bmSessionLockPassword" placeholder="{lng p="password"}" autocomplete="current-password" required>
				</div>
				<div class="d-grid gap-2">
					<button type="submit" class="btn btn-primary">
						<i class="ti ti-lock-open icon icon-2" aria-hidden="true"></i>
						{lng p="session_unlock"}
					</button>
					<a href="{sessionurl file='start.php' params='action=logout'}" class="btn btn-ghost-secondary">{lng p="logout"}</a>
				</div>
			</div>
		</form>
	</div>
</div>

{include file="nli/standalone.close.tpl"}
