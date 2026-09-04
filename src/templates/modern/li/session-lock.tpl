<div id="bmSessionLockOverlay" class="bm-session-lock-overlay d-none" aria-hidden="true">
	<div class="bm-session-lock-backdrop"></div>
	<div class="bm-session-lock-panel">
		<form class="bm-session-lock-card" id="bmSessionLockOverlayForm" method="post" autocomplete="off">
			{csrffield}
			<div class="bm-session-lock-body text-center">
				<h2>{lng p="session_locked"}</h2>
				<p class="text-muted">{lng p="session_locked_desc"}</p>
				<div class="bm-session-lock-user">
					<strong>
						{if $_userDisplayName|default:'' != ''}
						{text value=$_userDisplayName allowEmpty=true}
						{else}
						{email value=$_userEmail}
						{/if}
					</strong>
				</div>
				<div id="bmSessionLockOverlayError" class="alert alert-danger d-none" role="alert"></div>
				<div class="form-group">
					<input type="password" class="form-control" id="bmSessionLockOverlayPassword" name="password" placeholder="{lng p="password"}" autocomplete="current-password" aria-required="true" />
				</div>
				<button type="submit" class="btn btn-primary btn-block">{lng p="session_unlock"}</button>
				<a href="{sessionurl file='start.php' params='action=logout'}" class="btn btn-link btn-block" onclick="return confirm('{lng p="logoutquestion"}');">{lng p="logout"}</a>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="bmSessionWarnModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body text-center">
				<h4>{lng p="session_warn_title"}</h4>
				<p class="text-muted">{lng p="session_warn_text"}</p>
			</div>
			<div class="modal-footer">
				<a href="{sessionurl file='start.php' params='action=logout'}" class="btn btn-default" onclick="return confirm('{lng p="logoutquestion"}');">{lng p="logout"}</a>
				<button type="button" class="btn btn-primary" id="bmSessionKeepAlive">{lng p="session_stay_active"}</button>
			</div>
		</div>
	</div>
</div>
