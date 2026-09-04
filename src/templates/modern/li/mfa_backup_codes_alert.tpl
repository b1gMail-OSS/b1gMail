<div class="alert alert-warning" role="alert">
	<strong>{lng p="mfa_backup_new"}</strong>
	<p>{lng p="mfa_backup_hint"}</p>
	<ul style="font-family:monospace;margin-bottom:0;">
		{foreach from=$backupCodes item=code}
			<li>{$code}</li>
		{/foreach}
	</ul>
</div>
