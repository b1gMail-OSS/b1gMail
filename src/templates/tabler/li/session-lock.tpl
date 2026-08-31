<div id="bmSessionLockOverlay" class="bm-session-lock-overlay d-none" aria-hidden="true">
	<div class="bm-session-lock-backdrop"></div>
	<div class="bm-session-lock-panel page page-center">
		<div class="container-tight py-4">
			<form class="card card-md" id="bmSessionLockOverlayForm" method="post" autocomplete="off">
				{csrffield}
				<div class="card-body text-center">
					<div class="mb-4">
						<h2 class="card-title">{lng p="session_locked"}</h2>
						<p class="text-secondary">{lng p="session_locked_desc"}</p>
					</div>
					<div class="mb-4">
						{include file="li/user-avatar.tpl" avatarSize="xl" avatarClass="mb-3" avatarBgPrimary=true}
						<h3 class="mb-0">
							{if $_userDisplayName|default:'' != ''}
							{text value=$_userDisplayName allowEmpty=true}
							{else}
							{email value=$_userEmail}
							{/if}
						</h3>
					</div>
					<div id="bmSessionLockOverlayError" class="alert alert-danger d-none" role="alert"></div>
					<div class="mb-4">
						<input type="password" class="form-control" id="bmSessionLockOverlayPassword" name="password" placeholder="{lng p="password"}" autocomplete="current-password" aria-required="true">
					</div>
					<div class="d-grid gap-2">
						<button type="submit" class="btn btn-primary">
							<i class="ti ti-lock-open icon icon-2" aria-hidden="true"></i>
							{lng p="session_unlock"}
						</button>
						<a href="{sessionurl file='start.php' params='action=logout'}" class="btn btn-ghost-secondary" onclick="return confirm('{lng p="logoutquestion"}');">{lng p="logout"}</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal modal-blur fade" id="bmSessionWarnModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-body text-center py-4">
				<i class="ti ti-clock-hour-4 icon mb-2 text-warning icon-lg" aria-hidden="true"></i>
				<h3>{lng p="session_warn_title"}</h3>
				<div class="text-secondary">{lng p="session_warn_text"}</div>
			</div>
			<div class="modal-footer">
				<div class="w-100">
					<div class="row">
						<div class="col">
							<a href="{sessionurl file='start.php' params='action=logout'}" class="btn w-100" onclick="return confirm('{lng p="logoutquestion"}');">{lng p="logout"}</a>
						</div>
						<div class="col">
							<button type="button" class="btn btn-primary w-100" id="bmSessionKeepAlive">{lng p="session_stay_active"}</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
