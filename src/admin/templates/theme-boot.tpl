<script>
(function () {
	try {
		var t = localStorage.getItem('bm-acp-theme');
		if (t === 'dark' || t === 'light') {
			document.documentElement.setAttribute('data-bs-theme', t);
			document.documentElement.classList.toggle('theme-dark', t === 'dark');
		}
	} catch (e) {}
})();
</script>
