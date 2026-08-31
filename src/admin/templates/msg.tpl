<fieldset>
	<legend>{$msgTitle}</legend>

	{if !empty($msgIcon)}
		{if $msgIcon == 'error32'}
			<div class="alert alert-danger alert-dismissible" role="alert">
				<div class="alert-icon">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2" aria-hidden="true">
						<path d="M12 9v4"></path>
						<path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
						<path d="M12 16h.01"></path>
					</svg>
				</div>
				<div>
					<div class="alert-description">{$msgText}</div>
				</div>
			</div>
		{elseif $msgIcon == 'add32'}
			<div class="alert alert-success alert-dismissible" role="alert">
				<div class="alert-icon">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2" aria-hidden="true">
						<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
						<path d="M12 9h.01"></path>
						<path d="M11 12h1v4h1"></path>
					</svg>
				</div>
				<div>
					<div class="alert-description">{$msgText}</div>
				</div>
			</div>
		{else}
			<div class="alert alert-info alert-dismissible" role="alert">
				<div class="alert-icon">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2" aria-hidden="true">
						<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
						<path d="M12 9h.01"></path>
						<path d="M11 12h1v4h1"></path>
					</svg>
				</div>
				<div>
					<div class="alert-description">{$msgText}</div>
				</div>
			</div>
		{/if}
	{else}
		<div class="alert alert-info alert-dismissible" role="alert">
			<div class="alert-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2" aria-hidden="true">
					<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
					<path d="M12 9h.01"></path>
					<path d="M11 12h1v4h1"></path>
				</svg>
			</div>
			<div>
				<div class="alert-description">{$msgText}</div>
			</div>
		</div>
	{/if}

	{if !empty($backLink)}
	<div class="text-end">
		<input class="btn btn-primary" type="button" data-href="{$backLink|escape:'html'}" onclick="bmNavigateBack(this);" value="{lng p="back"}" />
	</div>
	{else}
		<div class="text-end">
		<input class="btn btn-primary" type="button" onclick="history.back(1);" value="{lng p="back"}" />
	</div>
	{/if}
</fieldset>

{if isset($reloadMenu) && $reloadMenu}
<script>
	parent.frames['menu'].location.href = 'main.php?action=menu&item=4{$sessionUrlSuffix}';
</script>
{/if}
