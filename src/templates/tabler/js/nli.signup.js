/*
 * b1gMail – NLI signup wizard (Tabler)
 */

var emailTimeout = false, emailValueOld = '', emailLastChecked = '';
var emailCheckPending = false, emailCheckState = 'unknown';
var suggestionsTimeout = false, suggestionsRequest = null;

function bmEmailAlertShow($alert)
{
	$alert.removeClass('d-none');
}

function bmEmailAlertHide($alert)
{
	$alert.addClass('d-none');
}

/**
 * Tabler alert with icon (d-flex + alert-icon)
 *
 * @param {jQuery} $alert
 * @param {string} type success|danger|warning|info|loading
 * @param {string} messageHtml
 */
function bmEmailAlertSet($alert, type, messageHtml)
{
	var iconClass = 'ti-info-circle';

	if(type === 'success')
		iconClass = 'ti-check';
	else if(type === 'danger')
		iconClass = 'ti-alert-circle';
	else if(type === 'warning')
		iconClass = 'ti-alert-triangle';
	else if(type === 'loading')
		iconClass = '';

	$alert.removeClass('alert-success alert-danger alert-warning alert-info d-none');

	if(type === 'loading')
	{
		$alert.addClass('alert-info');
		$alert.html(
			'<div class="d-flex">'
			+ '<div><span class="spinner-border spinner-border-sm text-info" role="status" aria-hidden="true"></span></div>'
			+ '<div>' + messageHtml + '</div>'
			+ '</div>'
		);
		return;
	}

	$alert.addClass('alert-' + type);
	$alert.html(
		'<div class="d-flex">'
		+ '<div><i class="ti ' + iconClass + ' alert-icon icon" aria-hidden="true"></i></div>'
		+ '<div>' + messageHtml + '</div>'
		+ '</div>'
	);
}

function bmSuggestionsEnabled()
{
	return $('#email_suggestions_wrap').length > 0 || $('#suggestionsModal').length > 0;
}

function bmSuggestionsHide()
{
	$('#email_suggestions_wrap').addClass('d-none');
	$('#email_suggestions_content').empty();
}

function bmSuggestionsShow(html)
{
	var $wrap = $('#email_suggestions_wrap'), $content = $('#email_suggestions_content');
	if(!$wrap.length || !html || $.trim(html) === '')
	{
		bmSuggestionsHide();
		return;
	}
	$content.html(html);
	$wrap.removeClass('d-none');
}

function loadAddressSuggestions(target)
{
	if(!bmSuggestionsEnabled())
		return;

	var firstName = $('#firstname').val(), lastName = $('#surname').val(),
		choice = $('#email_local').val(), domain = $('#email_domain').val();

	if($.trim(firstName) === '' && $.trim(lastName) === '' && $.trim(choice) === '')
	{
		bmSuggestionsHide();
		return;
	}

	var $body = target === 'modal' ? $('#suggestionsBody') : $('#email_suggestions_content');
	if(!$body.length)
		return;

	if(suggestionsRequest && suggestionsRequest.readyState !== 4)
		suggestionsRequest.abort();

	$body.html('<div class="text-center py-3 text-secondary">' + lang['pleasewait'] + '</div>');
	if(target === 'modal')
		bmModal('suggestionsModal', 'show');
	else
		$('#email_suggestions_wrap').removeClass('d-none');

	suggestionsRequest = $.post('index.php',
		{ 'action': 'showAddressSugestions', 'firstName': firstName, 'lastName': lastName,
			'choice': choice, 'domain': domain },
		function(data)
		{
			suggestionsRequest = null;
			var html = $.trim(data);
			if(target === 'modal')
				$('#suggestionsBody').html(html);
			else
				bmSuggestionsShow(html);
		});
}

function scheduleAddressSuggestions(delay)
{
	if(!bmSuggestionsEnabled())
		return;
	if(suggestionsTimeout)
		window.clearTimeout(suggestionsTimeout);
	suggestionsTimeout = window.setTimeout(function() {
		loadAddressSuggestions('inline');
	}, delay || 700);
}

function scheduleEmailCheck(delay)
{
	if(emailTimeout)
		window.clearTimeout(emailTimeout);
	emailTimeout = window.setTimeout(function() {
		checkEMailAvailability();
	}, delay || 500);
}

function bmGetBootstrap()
{
	if(typeof bootstrap !== 'undefined')
		return bootstrap;
	if(typeof tabler !== 'undefined' && tabler.bootstrap)
		return tabler.bootstrap;
	return null;
}

function bmCollapseShow(el)
{
	if(!el)
		return;
	var bs = bmGetBootstrap();
	bmSignupAllowShow(el);
	if(bs && bs.Collapse)
		bs.Collapse.getOrCreateInstance(el, { toggle: false }).show();
	else
		$(el).addClass('show');
}

function bmCollapseHide(el)
{
	if(!el)
		return;
	var bs = bmGetBootstrap();
	if(bs && bs.Collapse)
	{
		var inst = bs.Collapse.getInstance(el);
		if(inst)
			inst.hide();
		else
			bs.Collapse.getOrCreateInstance(el, { toggle: false }).hide();
	}
	else
		$(el).removeClass('show');
}

function bmModal(el, action)
{
	var node = typeof(el) == 'string' ? document.getElementById(el) : el[0];
	if(!node)
		return;
	var bs = bmGetBootstrap();
	if(!bs || !bs.Modal)
		return;
	var modal = bs.Modal.getOrCreateInstance(node);
	if(action == 'show')
		modal.show();
	else
		modal.hide();
}

function bmSignupSteps()
{
	return $('#signup .bm-signup-step');
}

function bmSignupCollapseEl(stepEl)
{
	var id = stepEl.data('signup-target');
	return id ? document.getElementById(id) : null;
}

function bmSignupAllowShow(collapseEl)
{
	if(collapseEl)
		$(collapseEl).data('bm-allow-show', true);
}

function bmSignupShowStep(stepEl, focusFirst)
{
	var $all = bmSignupSteps(), idx = $all.index(stepEl);
	var collapseEl = bmSignupCollapseEl(stepEl);

	$all.each(function(i, el) {
		var $step = $(el), collapseEl = bmSignupCollapseEl($step);
		if(i === idx)
		{
			bmCollapseShow(collapseEl);
			$step.addClass('bm-signup-step-active').removeClass('bm-signup-step-done');
		}
		else
		{
			bmCollapseHide(collapseEl);
			if(i < idx)
				$step.removeClass('bm-signup-step-active').addClass('bm-signup-step-done');
			else
				$step.removeClass('bm-signup-step-active bm-signup-step-done');
		}
	});

	bmSignupUpdateNav();
	if(focusFirst !== false)
	{
		var $focus = stepEl.find('.accordion-collapse.show').find('input, select, textarea').filter(':visible').first();
		if($focus.length)
			$focus.focus();
	}
}

function bmSignupUpdateNav()
{
	var $steps = bmSignupSteps(), $active = $steps.filter('.bm-signup-step-active'), idx = $steps.index($active);
	$steps.each(function(i, el) {
		var $footer = $(el).find('.bm-signup-step-footer');
		var $nav = $footer.find('.bm-signup-nav');
		$nav.find('[data-role="prev-block"]').toggle(i > 0);
		$nav.find('[data-role="next-block"]').toggle(i < $steps.length - 1);
	});
	if(idx === $steps.length - 1)
		$('#signupFinishHint').removeClass('d-none');
	else
		$('#signupFinishHint').addClass('d-none');
}

function bmSignupMarkDone(stepEl)
{
	stepEl.removeClass('bm-signup-step-active').addClass('bm-signup-step-done');
}

function chooseAddress(addr)
{
	var parts = addr.split('@');
	$('#email_local').val(parts[0]);
	if(parts.length > 1 && parts[1])
	{
		$('#email_domain').val(parts[1]);
		var $group = $('#email_local').closest('.nli-domain-group');
		if($group.length)
		{
			$group.find('[data-bind="label"]').text('@' + parts[1]);
			$group.find('.domainMenu li').removeClass('active');
			$group.find('.domainMenu li').each(function() {
				var d = $(this).find('a').text().replace(/^@/, '');
				if(d === parts[1])
					$(this).addClass('active');
			});
		}
	}
	emailLastChecked = '';
	emailCheckState = 'unknown';
	checkEMailAvailability();
	bmModal('suggestionsModal', 'hide');
}

function showAddressSugestions()
{
	loadAddressSuggestions('modal');
}

function checkEMailAvailability(e)
{
	var suggestionsCode = $('#suggestionsModal').length
		? ' <a href="#" class="alert-link" onclick="showAddressSugestions();return false;">' + lang['showsuggestions'] + '</a>'
		: '';

	var $alert = $('#email_alert');
	var local = $('#email_local').val().trim();

	if(local.length < 1)
	{
		emailCheckState = 'unknown';
		emailCheckPending = false;
		bmEmailAlertHide($alert);
		if(bmSuggestionsEnabled() && ($('#firstname').val().trim() || $('#surname').val().trim()))
			scheduleAddressSuggestions(400);
		else
			bmSuggestionsHide();
		return;
	}

	var emailAddr = local + '@' + $('#email_domain').val();

	if(emailLastChecked == emailAddr && emailCheckState != 'unknown')
	{
		bmEmailAlertShow($alert);
		return;
	}

	emailCheckPending = true;
	emailCheckState = 'checking';
	bmEmailAlertSet($alert, 'loading', lang['checkingaddr']);
	bmEmailAlertShow($alert);

	$.get('index.php',
		{ action: 'checkAddressAvailability', address: emailAddr },
		function(data)
		{
			emailCheckPending = false;
			var elems = data.getElementsByTagName('available');
			if(elems.length > 0 && elems.item(0).childNodes.length > 0)
			{
				var available = elems.item(0).childNodes.item(0).data;

				if(available == 1)
				{
					emailCheckState = 'available';
					bmEmailAlertSet($alert, 'success', lang['addravailable']);
					bmSuggestionsHide();
				}
				else if(available == 2)
				{
					emailCheckState = 'invalid';
					bmEmailAlertSet($alert, 'danger', lang['addrinvalid'] + suggestionsCode);
					scheduleAddressSuggestions(200);
				}
				else
				{
					emailCheckState = 'taken';
					bmEmailAlertSet($alert, 'danger', lang['addrtaken'] + suggestionsCode);
					scheduleAddressSuggestions(200);
				}
				bmEmailAlertShow($alert);
			}
			else
				emailCheckState = 'unknown';
		},
		'xml');

	emailLastChecked = emailAddr;
}

function bmSignupValidateStep($panelCollapse, requireEmail)
{
	var abort = false;

	$panelCollapse.find('select').each(function(index, obj) {
		if(typeof(obj.willValidate) != 'undefined' && obj.willValidate && obj.getAttribute('required') != null)
		{
			if(obj.value == '')
			{
				$(obj).trigger('invalid');
				if(!abort) { $(obj).focus(); abort = true; }
			}
		}
	});

	$panelCollapse.find('input, textarea').each(function(index, obj) {
		if(typeof(obj.willValidate) == 'undefined' || !obj.willValidate)
			return;
		if(!obj.checkValidity()
			|| ($(obj).data('min-length') && obj.value.length < $(obj).data('min-length'))
			|| (obj.name == 'pass2' && obj.value != $('#pass1').val()))
		{
			$(obj).trigger('invalid');
			if(!abort) { $(obj).focus(); abort = true; }
		}
	});

	if(requireEmail && !abort)
	{
		var local = $('#email_local').val().trim();
		if(local.length < 1)
		{
			$('#email_local').trigger('invalid').focus();
			abort = true;
		}
		else if(emailCheckPending)
		{
			var $alert = $('#email_alert');
			bmEmailAlertSet($alert, 'loading', lang['checkingaddr']);
			bmEmailAlertShow($alert);
			abort = true;
		}
		else if(emailCheckState != 'available')
		{
			checkEMailAvailability();
			var $alert = $('#email_alert');
			if($alert.hasClass('d-none') || !$alert.hasClass('alert-success'))
			{
				if(emailCheckState == 'taken' || emailCheckState == 'invalid')
					bmEmailAlertShow($alert);
				else
				{
					bmEmailAlertSet($alert, 'warning', lang['checkingaddr']);
					bmEmailAlertShow($alert);
				}
			}
			scheduleAddressSuggestions(100);
			$('#email_local').focus();
			abort = true;
		}
	}

	return !abort;
}

function bmSignupInitWizard()
{
	var $wizard = $('#signup');
	if(!$wizard.length)
		return;

	bmSignupSteps().each(function(i, el) {
		$(el).find('.bm-signup-step-badge').text(i + 1);
	});

	$wizard.on('show.bs.collapse', function(e) {
		if(!$(e.target).data('bm-allow-show'))
			e.preventDefault();
	});

	$wizard.find('.bm-signup-step-head').on('click', function(e) {
		var $step = $(e.currentTarget).closest('.bm-signup-step');
		if($step.hasClass('bm-signup-step-done'))
			bmSignupShowStep($step);
	});

	$('[data-role="next-block"]').on('click', function(e) {
		var $step = $(e.currentTarget).closest('.bm-signup-step, .accordion-item');
		var $collapse = $step.find('.accordion-collapse');
		var requireEmail = $step.data('signup-email') == 1;

		if(!bmSignupValidateStep($collapse, requireEmail))
			return;

		bmSignupMarkDone($step);
		var $next = $step.nextAll('.bm-signup-step').first();
		if($next.length)
			bmSignupShowStep($next);
	});

	$('[data-role="prev-block"]').on('click', function(e) {
		var $step = $(e.currentTarget).closest('.bm-signup-step, .accordion-item');
		var $prev = $step.prevAll('.bm-signup-step').first();
		if($prev.length)
			bmSignupShowStep($prev, false);
	});

	bmSignupShowStep(bmSignupSteps().first());
}

$(document).ready(function() {
	if(typeof bootstrap === 'undefined' && typeof tabler !== 'undefined' && tabler.bootstrap)
		window.bootstrap = tabler.bootstrap;

	$('#email_local').on('input keyup', function(e) {
		var emailValueNew = $(e.currentTarget).val();
		if(emailValueOld != emailValueNew)
		{
			emailCheckState = 'unknown';
			emailLastChecked = '';
			bmEmailAlertHide($('#email_alert'));
			scheduleEmailCheck(500);
		}
		else if(e.which == 13)
			checkEMailAvailability();
		emailValueOld = emailValueNew;
	});

	$('#email_local').on('change blur', function() {
		emailLastChecked = '';
		checkEMailAvailability();
	});

	$('#firstname, #surname').on('input change blur', function() {
		if(bmSuggestionsEnabled())
			scheduleAddressSuggestions(600);
	});

	if(bmSuggestionsEnabled() && ($('#firstname').val().trim() || $('#surname').val().trim()))
		scheduleAddressSuggestions(300);

	$('input, select').on('invalid', function(e) {
		var $collapse = $(e.currentTarget).closest('.accordion-collapse');
		if($collapse.length && !$collapse.hasClass('show'))
		{
			var $step = $collapse.closest('.bm-signup-step');
			bmSignupShowStep($step);
		}
		$(e.currentTarget).closest('.mb-3, .form-group').addClass('has-error');
	});

	$('input, select').on('change input', function(e) {
		$(e.currentTarget).closest('.mb-3, .form-group').removeClass('has-error');
		$(e.currentTarget).removeClass('is-invalid');
	});

	$('#signupForm').on('submit', function(e) {
		var $steps = bmSignupSteps();
		var ok = true;
		$steps.each(function() {
			var $c = $(this).find('.accordion-collapse');
			var reqEmail = $(this).data('signup-email') == 1;
			if(!bmSignupValidateStep($c, reqEmail))
				ok = false;
		});
		if(!ok)
		{
			e.preventDefault();
			var $firstBad = $steps.filter(function() {
				var $c = $(this).find('.accordion-collapse');
				return !bmSignupValidateStep($c, $(this).data('signup-email') == 1);
			}).first();
			if($firstBad.length)
				bmSignupShowStep($firstBad);
			return false;
		}
		if(!emailCheckPending && emailCheckState != 'available' && $('#email_local').val().trim())
		{
			e.preventDefault();
			bmSignupShowStep($steps.filter('[data-signup-email="1"]').first());
			checkEMailAvailability();
			return false;
		}
		$(this).find('[type="submit"]').filter('button').prop('disabled', true);
	});

	bmSignupInitWizard();
});
