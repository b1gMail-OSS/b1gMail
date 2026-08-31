/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */

var clientTZ = (new Date()).getTimezoneOffset() * (-60);

$(document).ready(function()
{
	if(typeof bmCsrfToken !== 'undefined' && bmCsrfToken)
	{
		document.querySelectorAll('form').forEach(function(form)
		{
			if((form.getAttribute('method') || '').toLowerCase() !== 'post')
				return;
			if(form.querySelector('input[name="csrf_token"]'))
				return;
			var inp = document.createElement('input');
			inp.type = 'hidden';
			inp.name = 'csrf_token';
			inp.value = bmCsrfToken;
			form.appendChild(inp);
		});
	}

	$(document.body).on('click', '.domainMenu li a, .domainMenu li', function(e)
	{
		var $item = $(e.currentTarget).is('a') ? $(e.currentTarget).parent() : $(e.currentTarget);
		var $group = $item.closest('.input-group');
		var domain = $item.find('a').length ? $item.find('a').text() : $item.text();
		$group.find('[data-bind="label"]').text(domain);
		$group.find('[data-bind="email-domain"]').val(domain.substr(1));
		$item.closest('ul').find('li').removeClass('active');
		$item.addClass('active');
		if(typeof(checkEMailAvailability) != 'undefined'
			&& $group.find('[data-bind="email-domain"]').attr('id') == 'email_domain')
			checkEMailAvailability(e);
		return(false);
	});

	document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
		new bootstrap.Tooltip(el);
	});

	function loginShowError($form, msg, csrfData)
	{
		var $alert = $form.find('.alert');
		if($alert.length == 0)
		{
			alert(msg);
			return;
		}

		if(csrfData && typeof bmCsrfErrorAlertHtml === 'function')
			$alert.html(bmCsrfErrorAlertHtml(csrfData));
		else
			$alert.html(msg);

		$alert.addClass('alert-danger').css('display', '');
	}

	function loginFunc(event)
	{
		var $form = $(event.currentTarget).closest('form'), formData = $form.serialize() + '&ajax=true',
			$alert = $form.find('.alert');

		$alert.css('display', 'none');

		if($form.data('realSubmit') || $alert.length == 0)
			return(true);

		$.ajax({
			url: $form.attr('action'),
			type: 'POST',
			data: formData,
			dataType: 'json',
			success: function(data)
			{
				if(!data || typeof data.action === 'undefined')
				{
					loginShowError($form, typeof lang_user !== 'undefined' && lang_user.badlogin
						? lang_user.badlogin
						: 'Login failed. Please reload the page and try again.');
					return;
				}
				if(data.action == 'msg')
				{
					$alert.html(data.msg);
					$alert.css('display', '');

					var $pwField = $form.find('input[type=password]');
					if($pwField.length > 0)
					{
						$pwField.val('');
						$pwField.focus();
					}
				}
				else if(data.action == 'redirect')
				{
					document.location.href = data.url;
				}
				else if(data.action == 'resubmit')
				{
					$form.data('realSubmit', true);
					$form.submit();
				}
			},
			error: function(xhr)
			{
				var text = xhr.responseText || '', data = null, msg;

				if(text.indexOf('"action"') !== -1 && text.indexOf('"redirect"') !== -1)
				{
					try {
						data = JSON.parse(text.replace(/^[\s\S]*?(\{[\s\S]*\})[\s\S]*$/m, '$1'));
					} catch(e) { data = null; }
				}

				if(data && data.action === 'redirect' && data.url)
				{
					document.location.href = data.url;
					return;
				}

				msg = (typeof lang_user !== 'undefined' && lang_user.badlogin)
					? lang_user.badlogin
					: 'Login failed. Please reload the page and try again.';
				if(xhr.status === 403 && xhr.responseJSON && xhr.responseJSON.csrfError)
				{
					loginShowError($form, '', xhr.responseJSON);
					return;
				}
				if(xhr.responseJSON && xhr.responseJSON.error)
					msg = xhr.responseJSON.error;
				loginShowError($form, msg);
			}
		});

		event.preventDefault();
	}

	$('#loginFormMain').on('submit', loginFunc);
	$('#loginFormPopover').on('submit', loginFunc);

	$(document).find('input[type="hidden"]').each(function(index, obj) {
		if(obj.name == 'timezone')
			obj.value = clientTZ;
	});

	if($('#loginFormMain').length > 0)
	{
		if($('#email_local').length > 0) $('#email_local').focus();
		else if($('#email_full').length > 0) $('#email_full').focus();
	}

	$(document).on('click', '[data-nli-toggle-password]', function(e) {
		e.preventDefault();
		var fieldId = $(this).attr('data-nli-toggle-password'),
			$field = $('#' + fieldId),
			$icon = $(this).find('.ti');

		if(!$field.length)
			return;

		if($field.attr('type') === 'password') {
			$field.attr('type', 'text');
			$icon.removeClass('ti-eye').addClass('ti-eye-off');
		} else {
			$field.attr('type', 'password');
			$icon.removeClass('ti-eye-off').addClass('ti-eye');
		}
	});
});

function updateFormSSL(elem)
{
	var ssl = elem.checked, $form = $(elem).closest('form');

	if(ssl)
		$form.attr('action', sslURL + 'index.php?action=login');
	else
		$form.attr('action', 'index.php?action=login');
}

function markFieldAsInvalid(name)
{
	var $field = $('#'+name);
	if(!$field.length)
	{
		$field = $('[name="'+name+'"]');
	}

	var $group;
	if(!$field.length && name == 'safecode')
		$group = $('#captchaContainer');
	else
		$group = $field.closest('.form-group');
	$group.addClass('has-error');
	$field.addClass('is-invalid');

		var $collapse = $group.parents('.panel-collapse, .accordion-collapse, .collapse');
	if($collapse.length && !$collapse.hasClass('show'))
	{
		var $step = $collapse.closest('.bm-signup-step');
		if($step.length && typeof bmSignupShowStep === 'function')
			bmSignupShowStep($step);
		else
		{
			var target = $collapse.attr('id'), el = target ? document.getElementById(target) : null;
			if(el)
			{
				$(el).data('bm-allow-show', true);
				var bs = (typeof bootstrap !== 'undefined') ? bootstrap
					: (typeof tabler !== 'undefined' && tabler.bootstrap ? tabler.bootstrap : null);
				if(bs && bs.Collapse)
					bs.Collapse.getOrCreateInstance(el, { toggle: false }).show();
				else
					$(el).addClass('show');
			}
		}
	}
}
