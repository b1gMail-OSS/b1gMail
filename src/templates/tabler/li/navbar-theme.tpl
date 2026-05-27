{if $templatePrefs.enableDarkMode|default:false}
<div class="nav-item d-none d-md-flex me-2">
	<a href="#" class="nav-link px-0 hide-theme-dark" onclick="bmSetTheme('dark'); return false;" title="Dunkelmodus" aria-label="Dunkelmodus aktivieren">
		<i class="icon ti ti-moon icon-1"></i>
	</a>
	<a href="#" class="nav-link px-0 hide-theme-light" onclick="bmSetTheme('light'); return false;" title="Hellmodus" aria-label="Hellmodus aktivieren">
		<i class="icon ti ti-sun icon-1"></i>
	</a>
</div>
{/if}
