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

var emailTimeout = false, emailValueOld = '', emailLastChecked = '';

function chooseAddress(addr)
{
	var parts = addr.split('@');

	$('#email_local').val(parts[0]);
	checkEMailAvailability();

	$('#suggestionsModal').modal('hide');
}

function showAddressSugestions()
{
	var firstName = $("#firstname").val(), lastName = $("#surname").val(),
		choice = $("#email_local").val(), domain = $("#email_domain").val();

	$('#suggestionsBody').html('<center>' + lang['pleasewait'] + '</center>');
	$('#suggestionsModal').modal('show');

	$.post('index.php',
		{ 'action': 'showAddressSugestions', 'firstName': firstName, 'lastName': lastName,
			'choice' : choice, 'domain' : domain },
		function(data)
		{
			$('#suggestionsBody').html(data);
		});
}

function checkEMailAvailability(e)
{
	var suggestionsCode = $('#suggestionsModal').length
		? ' <a href="#" onclick="showAddressSugestions()">' + lang['showsuggestions'] + '</a>'
		: '';

	var $alert = $('#email_alert');

	if($('#email_local').val().length < 1)
	{
		$alert.hide();
		return;
	}

	var emailAddr = $('#email_local').val() + '@' + $('#email_domain').val();

	if(emailLastChecked == emailAddr)
	{
		$alert.show();
		return;
	}

	$alert.removeClass('alert-success').removeClass('alert-danger');
	$alert.addClass('alert-info');
	$alert.html('<span class="glyphicon glyphicon-refresh"></span> ' + lang['checkingaddr']);
	$alert.show();

	$.get('index.php',
		{ action: 'checkAddressAvailability', address: emailAddr },
		function(data)
		{
			var elems = data.getElementsByTagName('available');
			if(elems.length > 0 && elems.item(0).childNodes.length > 0)
			{
				var available = elems.item(0).childNodes.item(0).data;
				$alert.removeClass('alert-info');

				if(available == 1)
				{
					$alert.html('<span class="glyphicon glyphicon-ok"></span> ' + lang['addravailable']);
					$alert.addClass('alert-success');
				}
				else if(available == 2)
				{
					$alert.html('<span class="glyphicon glyphicon-remove"></span> ' + lang['addrinvalid']
						+ suggestionsCode);
					$alert.addClass('alert-danger');
				}
				else
				{
					$alert.html('<span class="glyphicon glyphicon-remove"></span> ' + lang['addrtaken']
						+ suggestionsCode);
					$alert.addClass('alert-danger');
				}
			}
		},
		'xml');

	emailLastChecked = emailAddr;
}

$(document).ready(function() {
	$('#email_local').on('keyup', function(e) {
		var emailValueNew = $(e.currentTarget).val();

		if(emailValueOld != emailValueNew)
		{
			$('#email_alert').hide();

			if(emailTimeout)
			{
				window.clearTimeout(emailTimeout);
				emailTimeout = false;
			}

			emailTimeout = window.setTimeout(function() {
				checkEMailAvailability(null)
			}, 1000);
		}
		else if(e.which == 13)
			checkEMailAvailability();

		emailValueOld = emailValueNew;
	});

	$('#email_local').on('change', checkEMailAvailability);

	$('.panel-collapse').on('hide.bs.collapse', function(e) {
		var $target = $(e.currentTarget), $panel = $target.parents('.panel');
		$panel.removeClass('panel-primary');
		$panel.addClass('panel-default');
	});

	$('.panel-collapse').on('show.bs.collapse', function(e) {
		var $target = $(e.currentTarget), $panel = $target.parents('.panel');
		$panel.removeClass('panel-default');
		$panel.addClass('panel-primary');
	});

	$('.panel-collapse').on('shown.bs.collapse', function(e) {
		if($('#signup').find('.panel-collapse').filter('.in').length > 1)
			return;
		var $target = $(e.currentTarget), $panel = $target.parents('.panel');
		$panel.find('input, select').first().focus();
	});

	$('input, select').on('invalid', function(e) {
		if(!$(e.currentTarget).parents('.panel-collapse').hasClass('in'))
			$(e.currentTarget).parents('.panel-collapse').collapse('show');

		$(e.currentTarget).parents('.form-group').addClass('has-error');
	});

	$('input, select').on('change', function(e) {
		var $formGroup = $(e.currentTarget).parents('.form-group');

		if($formGroup.hasClass('has-error'))
			$formGroup.removeClass('has-error');
	});

	$('[data-role="next-block"]').on('click', function(e) {
		var $panelCollapse = $(e.currentTarget).closest('.panel-collapse'), abort = false;

		$panelCollapse.find('select').each(function(index, obj) {
			if(typeof(obj.willValidate) != 'undefined' && obj.willValidate)
			{
				if(obj.getAttribute('required') != null)
				{
					if(obj.value == '')
					{
						$(obj).trigger('invalid');
						if(!abort)
						{
							$(obj).focus();
							abort = true;
						}
					}
				}
			}
		});

		$panelCollapse.find('input').each(function(index, obj) {
			if(typeof(obj.willValidate) != 'undefined' && obj.willValidate)
			{
				if(!obj.checkValidity()
					|| ($(obj).data('min-length') && obj.value.length < $(obj).data('min-length'))
					|| ($(obj).attr("name") == 'pass2' && obj.value != $('#pass1').val()))
				{
					$(obj).trigger('invalid');
					if(!abort)
					{
						$(obj).focus();
						abort = true;
					}
				}
			}
		});

		if(abort)
			return;

		var $nextBlock = $(e.currentTarget).closest('.panel').next('.panel').find('.panel-collapse');
		$('.panel-collapse').not($nextBlock).collapse('hide');
		$nextBlock.collapse('show');
	});

	$('.panel-collapse').each(function(i, obj) {
		$(obj).collapse({
			toggle: false
		});
	});

	$('#signupForm').on('submit', function(e) {
		$(this).find('[type="submit"]').filter('button').button('loading');
	});
});
