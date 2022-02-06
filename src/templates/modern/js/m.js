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

function loadMoreMails(folder, page)
{
	$.ajax({
		type:		'POST',
		url:		'email.php',
		data:		({ sid: currentSID, listOnly: 'true', folder: folder, page: page }),
		cache:		false,
		dataType:	'text'
	}).done(function (msg)
	{
		$('#mailList').append(msg);
		$('#mailList').listview('refresh');
		if(msg.indexOf('<!-- hideMoreMailsLink -->') >= 0)
			$('#moreMailsLink').remove();
	});
}

function setTaskDone(id, done)
{
	$.ajax({
		type:		'POST',
		url: 		'tasks.php',
		data:		({ sid: currentSID, action: 'setTaskDone', id: id, done: done }),
		cache: 		false,
		dataType:	'text'
	});
}

function initTaskList()
{
	$('#page').live('pagebeforeshow', function(e, data)
	{
		$('input[type="checkbox"]').each(function()
		{
			($(this).is(':checked')) ? $(this).parent().parent().addClass('checked') : $(this).parent().parent().addClass('not-checked');
		});
	});

	$('.listCheckbox').bind('click', function(e)
	{
		if($(this).find('input[type="checkbox"]').is(':checked'))
		{
			setTaskDone($(this).find('input[type="checkbox"]').attr('name').substr(1), false);
			$(this).removeClass('checked').addClass('not-checked');
			$(this).find('input[type="checkbox"]').attr('checked', false);
		}
		else
		{
			setTaskDone($(this).find('input[type="checkbox"]').attr('name').substr(1), true);
			$(this).removeClass('not-checked').addClass('checked');
			$(this).find('input[type="checkbox"]').attr('checked', true);
		}
	});
}
