(function () {
	'use strict';

	function byId(id) {
		return document.getElementById(id);
	}

	document.addEventListener('click', function (e) {
		var toggle = e.target.closest('[data-setup-toggle-password]');
		if (!toggle) {
			return;
		}
		e.preventDefault();
		var input = byId(toggle.getAttribute('data-setup-toggle-password'));
		if (!input) {
			return;
		}
		input.type = input.type === 'password' ? 'text' : 'password';
	});

	function setDisabled(selector, disabled) {
		document.querySelectorAll(selector).forEach(function (el) {
			el.querySelectorAll('input, select, textarea').forEach(function (field) {
				if (field.type === 'radio') {
					return;
				}
				field.disabled = disabled;
			});
		});
	}

	function syncConditionalFields() {
		var pop3 = byId('receive_method_pop3');
		if (pop3) {
			setDisabled('[data-setup-pop3]', !pop3.checked);
		}
		var smtp = byId('send_method_smtp');
		if (smtp) {
			setDisabled('[data-setup-smtp]', !smtp.checked);
		}
		var sendmail = byId('send_method_sendmail');
		if (sendmail) {
			setDisabled('[data-setup-sendmail]', !sendmail.checked);
		}
		var smtpAuth = byId('smtp_auth');
		if (smtpAuth) {
			setDisabled('[data-setup-smtp-auth]', !smtp.checked || !smtpAuth.checked);
		}
	}

	document.addEventListener('change', function (e) {
		if (e.target.matches('input[name="receive_method"], input[name="send_method"], #smtp_auth')) {
			syncConditionalFields();
		}
	});

	function logLine(text) {
		var log = byId('log');
		if (!log) {
			return;
		}
		log.classList.remove('d-none');
		log.value = text + '\n' + log.value;
	}

	function request(url, body, callback) {
		fetch(url, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body
		})
			.then(function (res) {
				return res.text();
			})
			.then(function (text) {
				callback({ readyState: 4, responseText: text });
			})
			.catch(function () {
				logLine('HTTP error — retry in 10s');
				window.setTimeout(function () {
					callback({ readyState: -1, responseText: '' });
				}, 10000);
			});
	}

	function runProgress(root) {
		var steps = JSON.parse(root.getAttribute('data-steps') || '[]');
		var script = root.getAttribute('data-script');
		var ajaxStep = root.getAttribute('data-ajax-step') || '4';
		var lng = root.getAttribute('data-lng') || '';
		var step = -1;
		var pos = 0;
		var allQ = -1;

		function failStep(message) {
			logLine(message);
			var status = byId('step_' + steps[step] + '_status');
			if (status) {
				status.innerHTML = '<span class="setup-status setup-status-err"><i class="ti ti-x"></i></span>';
			}
		}

		function markDone(index) {
			var status = byId('step_' + steps[index] + '_status');
			var progress = byId('step_' + steps[index] + '_progress');
			if (status) {
				status.innerHTML = '<span class="setup-status setup-status-ok"><i class="ti ti-check"></i></span>';
			}
			if (progress) {
				progress.innerHTML = '<strong>100%</strong>';
			}
		}

		function stepStep() {
			var csrf = root.getAttribute('data-csrf') || '';
			var params = new URLSearchParams();
			params.set('step', ajaxStep);
			params.set('do', steps[step]);
			params.set('pos', String(pos));
			if (lng) {
				params.set('lng', lng);
			}
			if (csrf) {
				params.set('setup_csrf', csrf);
			}
			request(script, params.toString(), function (e) {
				if (e.readyState !== 4) {
					return;
				}
				var response = (e.responseText || '').replace(/^\uFEFF/, '').trim();
				var firstLine = response.split(/\r?\n/, 1)[0] || '';
				if (firstLine.substr(0, 3) !== 'OK:') {
					failStep(firstLine || ('Unexpected response (pos ' + pos + ')'));
					return;
				}
				response = firstLine.substr(3);
				if (response === 'DONE') {
					init(step + 1);
					return;
				}
				var numbers = response.split('/');
				if (numbers.length !== 2) {
					failStep(firstLine || ('Unexpected response (pos ' + pos + ')'));
					return;
				}
				if (steps[step] === 'struct2' && allQ === -1) {
					allQ = parseInt(numbers[1], 10);
				}
				if (steps[step] === 'struct2') {
					numbers[1] = '' + allQ;
				}
				pos = parseInt(numbers[0], 10);
				var progress = byId('step_' + steps[step] + '_progress');
				if (progress) {
					progress.innerHTML = '<strong>' + Math.round(pos / parseInt(numbers[1], 10) * 100) + '%</strong> <small>('
						+ pos + ' / ' + parseInt(numbers[1], 10) + ')</small>';
				}
				stepStep();
			});
		}

		function init(theStep) {
			if (step !== -1) {
				markDone(step);
			}
			if (theStep < steps.length) {
				step = theStep;
				var text = byId('step_' + steps[step] + '_text');
				var status = byId('step_' + steps[step] + '_status');
				var row = text ? text.closest('tr') : null;
				if (text) {
					text.classList.add('fw-bold');
				}
				if (row) {
					row.classList.add('is-active');
				}
				if (status) {
					status.innerHTML = '<span class="setup-status"><span class="spinner-border spinner-border-sm"></span></span>';
				}
				pos = 0;
				stepStep();
				return;
			}
			var done = byId('done');
			if (done) {
				done.classList.remove('d-none');
			}
			var next = byId('next_button');
			if (next) {
				next.disabled = false;
			}
		}

		init(0);
	}

	document.addEventListener('DOMContentLoaded', function () {
		syncConditionalFields();
		var progress = byId('setup-progress');
		if (progress) {
			runProgress(progress);
		}
	});
})();
