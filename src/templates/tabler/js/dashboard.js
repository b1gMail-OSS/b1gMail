/*
 * b1gMail Tabler theme – dashboard widget board styling
 */

var bmWidgetFaToTi = {
	'fa-inbox': 'ti-inbox',
	'fa-envelope-o': 'ti-mail',
	'fa-envelope': 'ti-mail',
	'fa-calendar': 'ti-calendar',
	'fa-tasks': 'ti-list-check',
	'fa-address-book-o': 'ti-address-book',
	'fa-address-book': 'ti-address-book',
	'fa-sticky-note-o': 'ti-notes',
	'fa-sticky-note': 'ti-notes',
	'fa-cloud': 'ti-cloud',
	'fa-cog': 'ti-settings',
	'fa-sign-out': 'ti-logout',
	'fa-folder-open-o': 'ti-folders',
	'fa-folder-open': 'ti-folders',
	'fa-folder-o': 'ti-folder',
	'fa-folder': 'ti-folder',
	'fa-tachometer': 'ti-dashboard',
	'fa-ban': 'ti-ban',
	'fa-trash-o': 'ti-trash',
	'fa-trash': 'ti-trash',
	'fa-share-square-o': 'ti-upload',
	'fa-flag-o': 'ti-flag',
	'fa-flag': 'ti-flag',
	'fa-exclamation': 'ti-alert-triangle'
};

function bmConvertWidgetIcons(container)
{
	if(!container)
		return;

	container.querySelectorAll('i.fa').forEach(function(icon)
	{
		if(icon.closest('#wdDnDArea'))
			return;

		var classes = icon.className.split(/\s+/);

		for(var i = 0; i < classes.length; i++)
		{
			var ti = bmWidgetFaToTi[classes[i]];

			if(ti)
			{
				icon.className = 'ti ' + ti + ' icon icon-sm bm-widget-icon';
				icon.setAttribute('aria-hidden', 'true');
				return;
			}
		}
	});
}

function bmEnhanceWidgetCalendar(table)
{
	if(!table || table.dataset.bmCalendarEnhanced === '1')
		return;

	if(!table.querySelector('colgroup'))
	{
		var colgroup = document.createElement('colgroup');

		for(var i = 0; i < 7; i++)
		{
			var col = document.createElement('col');
			colgroup.appendChild(col);
		}

		table.insertBefore(colgroup, table.firstChild);
	}

	table.classList.add('bm-widget-calendar-table');
	table.dataset.bmCalendarEnhanced = '1';
}

function bmEnhanceWidgetWebdiskDnD(area)
{
	if(!area || area.dataset.bmWebdiskDnDEnhanced === '1')
		return;

	area.classList.add('bm-widget-webdisk-dnd');

	var icon = area.querySelector('i');
	if(icon)
	{
		icon.className = 'ti ti-cloud-upload icon bm-widget-webdisk-dnd-icon';
		icon.setAttribute('aria-hidden', 'true');
	}

	area.dataset.bmWebdiskDnDEnhanced = '1';
}

function bmApplyDashboardCards(boardId)
{
	var root = document.getElementById(boardId);
	if(!root)
		return;

	var dragTable = root.querySelector('.dragTable');
	if(dragTable)
		dragTable.classList.add('bm-widget-grid');

	root.querySelectorAll('.dragTableColumn').forEach(function(col)
	{
		col.classList.add('bm-widget-column');
	});

	root.querySelectorAll('.dragItem').forEach(function(item)
	{
		item.classList.add('card', 'bm-widget-card');

		var bar = item.querySelector('.dragBar');
		if(bar)
		{
			bar.classList.add('card-header', 'bm-widget-drag-header');

			var prefsLink = bar.querySelector('a[onmousedown*="showPrefs"]');
			if(prefsLink)
			{
				prefsLink.className = 'btn btn-ghost-secondary btn-sm btn-icon ms-1';
				prefsLink.innerHTML = '<i class="ti ti-settings icon"></i>';
			}
		}

		item.querySelectorAll('.innerWidget').forEach(function(widget)
		{
			widget.classList.add('card-body');

			if(widget.previousElementSibling && widget.previousElementSibling.classList.contains('notePreview'))
				widget.classList.add('bm-widget-notes-list');
		});

		bmConvertWidgetIcons(item);
		item.querySelectorAll('.miniCalendarTable.inWidget').forEach(bmEnhanceWidgetCalendar);
		item.querySelectorAll('#wdDnDArea').forEach(bmEnhanceWidgetWebdiskDnD);
	});

	var floater = document.getElementById('dc_' + boardId + 'dc');
	if(floater)
	{
		floater.classList.add('card', 'bm-widget-card', 'bm-widget-drag-preview');
		bmConvertWidgetIcons(floater);
	}
}

function bmInitWidgetBoard(elem, cols, instanceName, order, onOrderChangedFn)
{
	var dc = new dragContainer(elem, cols, instanceName);
	dc.order = order;

	var origRun = dc.run.bind(dc);
	dc.run = function()
	{
		origRun();
		bmApplyDashboardCards(elem);
	};

	dc.onOrderChanged = function()
	{
		onOrderChangedFn.call(this);
	};

	var origBarMouseDown = dc.barMouseDown.bind(dc);
	dc.barMouseDown = function(id, evt, obj)
	{
		document.body.classList.add('bm-widget-dragging');
		return origBarMouseDown(id, evt, obj);
	};

	var origParentMouseUp = dc.parentMouseUp.bind(dc);
	dc.parentMouseUp = function(evt)
	{
		document.body.classList.remove('bm-widget-dragging');
		return origParentMouseUp(evt);
	};

	dc.run();
	return dc;
}
