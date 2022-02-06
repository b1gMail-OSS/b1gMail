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
	$('button[data-toggle=popover]').popover({
		html : true,
		content: function() {
		  return $('#loginPopover').html();
		}
	}).click(function(e){
		e.stopPropagation();
		$('#email_local_p').focus();
	});

	$(document.body).on('click', '.domainMenu li', function(e)
	{
		var $target = $(e.currentTarget);
		var $group = $target.closest('.input-group-btn');
		var domain = $target.text();
		$group.find('[data-bind="label"]').text(domain);
		$group.find('[data-bind="email-domain"]').val(domain.substr(1));
		$group.children('.dropdown-toggle').dropdown('toggle');
		$target.closest('ul').find('li').removeClass('active');
		$target.addClass('active');
		if(typeof(checkEMailAvailability) != 'undefined'
			&& $group.find('[data-bind="email-domain"]').attr('id') == 'email_domain')
			checkEMailAvailability(e);
		return(false);
	});

	$('html').click(function(e)
	{
		if($(e.target).parents('.popover').length == 0
			|| $(e.target).prop('tagName') == 'A')
			$('button[data-toggle=popover]').popover('hide');
	});

	$('[data-toggle="tooltip"]').tooltip();

	function loginFunc(event)
	{
		var $form = $(event.currentTarget).closest('form'), formData = $form.serialize() + '&ajax=true',
			$alert = $form.find('.alert');

		$alert.css('display', 'none');

		if($form.data('realSubmit') || $alert.length == 0)
			return(true);

		$.post($form.attr('action'), formData, function(data)
		{
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
		$group = $field.closest('[class="form-group"]');
	$group.addClass('has-error');

	if(!$group.parents('.panel-collapse').hasClass('in'))
		$group.parents('.panel-collapse').collapse('show');
}