/*
 * Mandatory MFA setup modal (non-dismissible)
 */
(function()
{
	function bmGetBootstrap()
	{
		if(typeof bootstrap !== 'undefined' && bootstrap.Modal)
			return bootstrap;
		if(typeof window.tabler !== 'undefined' && window.tabler.Modal)
			return window.tabler;
		return null;
	}

	function initMfaSetupModal()
	{
		var el = document.getElementById('bmMfaSetupModal');
		if(!el)
			return;

		document.body.classList.add('bm-mfa-setup-active');

		el.addEventListener('hide.bs.modal', function(e)
		{
			e.preventDefault();
		});

		var bs = bmGetBootstrap();
		if(!bs || !bs.Modal)
			return;

		var modal = bs.Modal.getOrCreateInstance(el, {
			backdrop: 'static',
			keyboard: false
		});
		modal.show();
	}

	window.addEventListener('load', initMfaSetupModal);
})();
