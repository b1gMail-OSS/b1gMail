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

function selecTable(table, rowTagName, enableDnD)
{
	this.table = table;
	this.rowTagName = rowTagName;
	this.sel = [];
	this.enableDnD = enableDnD;
	this.dragging = false;
	this.multiContextMenu = false;

	this.rowMouseDown = function(event, element)
	{
		return(true);
	}

	this.unselectAll = function()
	{
		if(this.sel.length == 0)
			return;

		for(var i=0; i<this.sel.length; i++)
		{
			this.unselect(this.sel[i], false);
		}

		this.sel.length = 0;
	}

	this.unselect = function(element, cleanup)
	{
		this.cbStyleRow(element, false);

		var cb = this.getRowCB(element);
		if(cb != null)
			cb.checked = false;

		if(cleanup)
		{
			var newSel = [], i;
			for(i=0; i<this.sel.length; i++)
				if(this.sel[i] != element)
					newSel.push(this.sel[i]);
			this.sel = newSel;
		}
	}

	this.selectAll = function()
	{
		this.unselectAll();

		var rows = this.getRows();
		for(var i=0; i<rows.length; i++)
			this.select(rows[i]);

		this.selChanged();
	}

	this.select = function(element)
	{
		for(var i=0; i<this.sel.length; i++)
			if(this.sel[i] == element)
				return;

		this.cbStyleRow(element, true);

		this.sel.push(element);

		var cb = this.getRowCB(element);
		if(cb != null)
			cb.checked = true;
	}

	this.selectSingleItem = function(element)
	{
		this.cbSelectSingleItem(element);
	}

	this.selChanged = function()
	{
		this.cbSelectionChanged();
	}

	this.rowMouseUp = function(event, element, drag)
	{
		var i, evtSource, isCB;

		if(event.target)
			evtSource = event.target;
		else if(event.srcElement)
			evtSource = event.srcElement;

		if(evtSource)
		{
			if(evtSource.tagName.toUpperCase() == 'INPUT' && evtSource.type == 'checkbox')
			{
				isCB = true;
			}
			else
			{
				var inputs = evtSource.getElementsByTagName('input');
				for(var i=0; i<inputs.length; i++)
				{
					if(inputs[i].type != 'checkbox')
						continue;
					if(inputs[i].offsetParent != evtSource)
						continue;
					isCB = true;
					break;
				}
			}
		}

		var isSelected = false;
		for(i=0; i<this.sel.length; i++)
		{
			if(this.sel[i] == element)
			{
				isSelected = true;
				break;
			}
		}

		if(drag && isSelected)
			return;

		var didUnselect = false;
		if((event.button == 2 && !this.multiContextMenu) || (!accelKeyPressed(event) && !event.shiftKey && !isCB && !(event.button == 2 && this.multiContextMenu && isSelected)))
		{
			this.unselectAll();
			didUnselect = true;
		}

		if(event.button != 2)
		{
			for(i=0; i<this.sel.length; i++)
			{
				if(this.sel[i] == element)
				{
					if(this.sel.length > 1)
						this.unselect(element, true);
					this.selChanged();
					if(this.sel.length == 1)
						this.selectSingleItem(this.sel[0]);
					return;
				}
			}
		}

		if(event.shiftKey && event.button != 2 && this.sel.length >= 1)
		{
			var fromElement = this.sel[this.sel.length-1],
				toElement = element;

			if(fromElement != toElement)
			{
				var rows = this.getRows(),
					foundFrom = false,
					foundTo = false;

				for(i=0; i<rows.length; i++)
				{
					var row = rows[i];

					if(row == fromElement)
						foundFrom = true;

					if(row == toElement)
						foundTo = true;

					if(foundFrom && foundTo)
						break;

					if(row != toElement && row != fromElement && (foundFrom || foundTo))
						this.select(row);
				}
			}
		}

		if(!this.multiContextMenu || (event.button != 2 && !isSelected) || didUnselect)
			this.select(element);

		if((this.sel.length == 1 && (!this.multiContextMenu || event.button != 2 || didUnselect)))
			this.selectSingleItem(element);

		if(event.button == 2 && this.sel.length > 0)
		{
			if(this.sel.length == 1)
				this.rowContextMenu(event, element);
			else
				this.multiRowContextMenu(event, this.sel);
		}

		this.selChanged();
	}

	this.rowDblClick = function(event, element)
	{
		return this.cbItemDoubleClick(element);
	}

	this.rowContextMenu = function(event, element)
	{
		return this.cbItemContextMenu(element, event);
	}

	this.multiRowContextMenu = function(event, elements)
	{
		return this.cbMultiItemsContextMenu(elements, event);
	}

	this.rowDragStart = function(event, element)
	{
		this.rowMouseUp(event, element, true);
		return this.cbItemDragStart(element, event);
	}

	this.getRows = function()
	{
		var temp = this.table.getElementsByTagName(this.rowTagName), res = [];
		for(var i=0; i<temp.length; i++)
		{
			if(!this.cbRowFilter(temp[i]))
				continue;
			res.push(temp[i]);
		}
		return(res);
	}

	this.getItemID = function(element)
	{
		return(this.cbGetItemID(element));
	}

	this.getIDList = function()
	{
		var res = [];
		for(var i=0; i<this.sel.length; i++)
			res.push(this.getItemID(this.sel[i]));
		return(res);
	}

	this.init = function()
	{
		var _this = this;
		var rows = this.getRows();
		for(var i=0; i<rows.length; i++)
		{
			var row = rows[i];
			addEvent(row, 'mousedown', (function(row) { return function(event) { return _this.rowMouseDown(event, row); }; })(row) );
			addEvent(row, 'mouseup', (function(row) { return function(event) { return _this.rowMouseUp(event, row); }; })(row));
			addEvent(row, 'dblclick', (function(row) { return function(event) { return _this.rowDblClick(event, row); }; })(row));
			addEvent(row, 'contextmenu', function(event) { event.preventDefault(); event.stopPropagation(); return(false); } );
			if(this.enableDnD)
			{
				addEvent(row, 'dragstart', (function(row) { return function(event) { return _this.rowDragStart(event, row); }; })(row));
				row.setAttribute('draggable', true);
			}

			var input = this.getRowCB(row);
			if(input != null)
			{
				addEvent(input, 'click', function(event) { event.preventDefault(); return(false); });
			}
		}
	}

	this.getRowCB = function(row)
	{
		var inputs = row.getElementsByTagName('input');
		for(var j=0; j<inputs.length; j++)
		{
			var input = inputs[j];
			if(input.type != 'checkbox')
				continue;
			if(input.id.indexOf('selecTable_') != 0)
				continue;

			return(input);
		}
		return(null);
	}

	//
	// callbacks
	//
	this.cbRowFilter = function(element)
	{
		return(true);
	}
	this.cbStyleRow = function(element, selected)
	{
		if(selected)
			element.className += ' selected';
		else
			element.className = element.className.replace(' selected', '');
	}
	this.cbSelectSingleItem = function(element)
	{
	}
	this.cbSelectionChanged = function()
	{
	}
	this.cbItemDoubleClick = function(element)
	{
		return(false);
	}
	this.cbItemContextMenu = function(element, event)
	{
		return(false);
	}
	this.cbMultiItemsContextMenu = function(elements, event)
	{
		return(false);
	}
	this.cbItemDragStart = function(elememt, event)
	{
		return(false);
	}
	this.cbGetItemID = function(element)
	{
		return(element.id);
	}
}
