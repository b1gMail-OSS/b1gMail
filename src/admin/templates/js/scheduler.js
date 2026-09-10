(function() {
	function schedSubmitNextStep(selectEl) {
		if (!selectEl || selectEl.disabled || !selectEl.value) {
			return;
		}
		var form = selectEl.form;
		var nextFlag = document.getElementById('sched_next_submit');
		if (!form || !nextFlag) {
			return;
		}
		nextFlag.disabled = false;
		if (typeof form.requestSubmit === 'function') {
			var nextBtn = document.getElementById('sched_button_next');
			if (nextBtn) {
				form.requestSubmit(nextBtn);
				return;
			}
		}
		form.submit();
	}

	document.addEventListener('change', function(e) {
		var selectEl = e.target.closest('[data-sched-task-select]');
		if (selectEl) {
			schedSubmitNextStep(selectEl);
		}
	});

	document.addEventListener('click', function(e) {
		var btn = e.target.closest('[data-sched-toggle]');
		if (!btn) {
			return;
		}
		e.preventDefault();
		var target = btn.getAttribute('data-sched-target');
		if (!target) {
			return;
		}
		var group = document.querySelector('[data-sched-group="' + target + '"]');
		if (!group) {
			return;
		}
		var selectAll = btn.getAttribute('data-sched-toggle') === 'all';
		group.querySelectorAll('input[type="checkbox"]').forEach(function(cb) {
			cb.checked = selectAll;
		});
	});
})();
