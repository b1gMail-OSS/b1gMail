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

function EBID(e)
{
	return(document.getElementById(e));
}

function dragContainer(elem, cols, instanceName)
{
	this.instanceName = instanceName;
	this.parentContainer = EBID(elem);
	this.elemContainer = EBID(elem+'_elems');
	this.name = 'dc_' + elem;
	this.cols = cols;
	this.containers = [];
	this.order = '';
	this.startDragging = false;
	this.dragID = '';
	this.margin = 4;
	this.activeTarget = false;
	this.disableMD = false;

	this.onOrderChanged = function() {}

	this.init = function()
	{
		var elems = this.elemContainer.getElementsByTagName("div");
		for(var i=0; i<elems.length; i++)
		{
			var elem = elems[i];
			this.containers.push(elem);
		}
	}

	this.run = function()
	{
		// create table
		var colWidth = Math.floor(100/this.cols);
		var tableCode = '<table class="dragTable" onmouseup="' + this.instanceName + '.parentMouseUp(event)" onmousemove="' + this.instanceName + '.parentMouseMove(event)">';
		tableCode += '<tr>';
		for(var col=0; col<this.cols; col++)
			tableCode += '<td id="' + this.name + 'col_' + col + '" class="dragTableColumn" width="' + colWidth + '%">&nbsp;</td>';
		tableCode += '</tr>';
		tableCode += '</table>';
		tableCode += '<div class="dragItem" onmouseup="' + this.instanceName + '.parentMouseUp(event)" onmousemove="' + this.instanceName + '.parentMouseMove(event)" id="' + this.name + 'dc" style="display:none;position:absolute;top:0px;left:0px;"></div>';

		// render table
		this.parentContainer.innerHTML = tableCode;

		// create dock items
		this.createDockItems();
	}

	this.parseOrder = function(o)
	{
		// create result array
		var result = [];
		for(var col=0; col<this.cols; col++)
			result.push([]);

		// parse order string
		var rows = o.split(';');
		for(var row=0; row<rows.length; row++)
		{
			var cols = rows[row].split(',');
			for(var col=0; col<cols.length; col++)
				result[col].push(cols[col]);
		}

		// return
		return(result);
	}

	this.orderToString = function(o)
	{
		var result = [];

		for(var col=0; col<this.containers.length; col++)
		{
			for(var row=0; row<this.cols; row++)
			{
				if(o[row][col])
					result += o[row][col] + ',';
				else
					result += ',';
			}
			if(result.substr(result.length-1, 1) == ',')
				result = result.substr(0, result.length-1);
			result += ';';
		}

		if(result.substr(result.length-1, 1) == ';')
			result = result.substr(0, result.length-1);

		result = result.replace(/,;$/g, '');
		result = result.replace(/;,,;/g, ';');
		result = result.replace(/^,,;/g, '');
		result = result.replace(/;,,$/g, '');
		return(result);
	}

	this.getOrder = function()
	{
		return(this.parseOrder(this.order));
	}

	this.getContainerById = function(id)
	{
		for(var i=0; i<this.containers.length; i++)
			if(this.containers[i].id == id)
				return(this.containers[i]);
		return(false);
	}

	this.createDockItems = function()
	{
		// get order
		var order = this.getOrder();

		// loop through cols
		for(var col=0; col<order.length; col++)
		{
			var columnData = '';
			var colContainers = order[col];
			for(var container=0; container<colContainers.length; container++)
			{
				if(colContainers[container])
				{
					var colContainer = this.getContainerById(colContainers[container]);
					if(colContainer)
					{
						if(colContainer.getAttribute('rel'))
							var attrs = colContainer.getAttribute('rel').split(',');
						else
							var attrs = [ 0, 0, 0, 0 ];
						var hasPrefs = attrs[0] == 1, prefsW = attrs[1], prefsH = attrs[2], icon = attrs[3];

						columnData += '<div class="dragTargetInactive" onmousemove="' + this.instanceName + '.targetMouseMove(event, this)" style="height:' + this.margin + 'px;" id="' + this.name + 'dragTarget_' + col + '_' + container + '"></div>';
						columnData += '<div class="dragItem" id="' + this.name + 'dragItem_' + colContainers[container] + '">';
						columnData +=	'<div class="dragBar" onmousedown="return ' + this.instanceName + '.barMouseDown(\'' + colContainers[container] + '\', event, this)">' + (icon != 0 ? '<img src="' + icon + '" border="0" alt="" /> ' : '') + colContainer.title + '<div style="float:right;">' + (hasPrefs ? '<a href="javascript:void(0);" onmousedown="' + this.instanceName + '.showPrefs(\'' + colContainers[container] + '\','+prefsW+','+prefsH+');" title="' + lang['prefs'] + '"><img src="' + tplDir + 'images/li/dragbar_prefs.png" border="0" alt="' + lang['prefs'] + '" /></a>' : '') + '&nbsp;</div></div>';
						columnData += '</div>';
					}
				}
			}
			columnData += '<div class="dragTargetInactive" onmousemove="' + this.instanceName + '.targetMouseMove(event, this)" style="height:' + this.margin + 'px;" id="' + this.name + 'dragTarget_' + (col) + '_' + container + '"></div>';
			EBID(this.name + 'col_' + col).innerHTML = columnData;

			for(var container=0; container<colContainers.length; container++)
			{
				if(colContainers[container])
				{
					var colContainer = this.getContainerById(colContainers[container]);
					if(colContainer)
					{
						var c = EBID(this.name + 'dragItem_' + colContainers[container]);
						c.appendChild(colContainer);
					}
				}
			}
		}
	}

	this.showPrefs = function(name, w, h)
	{
		this.disableMD = true;

		var colContainer = this.getContainerById(name);

		openOverlay('start.php?action=showWidgetPrefs&name='+name+'&sid='+currentSID,
			colContainer.title + ': ' + lang['prefs'],
			w,
			h,
			true);
	}

	this.barMouseDown = function(id, evt, obj)
	{
		if(this.disableMD)
		{
			this.disableMD = false;
			return(false);
		}

		this.startDragging = true;
		this.dragID = id;
		return(false);
	}

	this.targetMouseMove = function(evt, obj)
	{
		if(this.startDragging)
		{
			if(this.activeTarget != obj
				&& this.activeTarget != false)
			{
				this.activeTarget.className = 'dragTargetInactive';
				this.activeTarget.style.height = this.margin + 'px';
			}

			var dragContainer = EBID(this.name + 'dc');
			obj.className = 'dragTargetActive';
			obj.style.height = dragContainer.style.height;
			this.activeTarget = obj;
		}
	}

	this.parentMouseMove = function(evt)
	{
		if(this.startDragging)
		{
			var dragItem = EBID(this.name + 'dragItem_' + this.dragID);
			var dragContainer = EBID(this.name + 'dc');

			var offsetX = getElementMetrics(dragContainer.parentNode, 'x');
			var offsetY = getElementMetrics(dragContainer.parentNode, 'y');

			dragContainer.style.left = (-offsetX+evt.clientX+8) + 'px';
			dragContainer.style.top = (-offsetY+evt.clientY+8) + 'px';

			if(dragContainer.style.display != '')
			{
				dragContainer.style.display = '';
				dragContainer.innerHTML = dragItem.innerHTML;
				dragContainer.style.width = (dragItem.offsetWidth ? dragItem.offsetWidth : dragItem.clientWidth) + 'px';
				dragContainer.style.height = (dragItem.offsetHeight ? dragItem.offsetHeight : dragItem.clientHeight) + 'px';
				dragItem.style.display = 'none';
			}
		}
	}

	this.parentMouseUp = function(evt)
	{
		if(this.startDragging)
		{
			if(this.activeTarget)
			{
				var theOrder = this.getOrder();
				var theNewOrder = [];
				var theID = this.activeTarget.id.split('_');
				var theCol = theID[theID.length-2];
				var theRow = theID[theID.length-1];
				var theItem = this.dragID;

				for(var col=0; col<theOrder.length; col++)
				{
					theNewOrder.push([]);
					for(var row=0; row<this.containers.length+1; row++)
					{
						if(theCol == col && theRow == row)
						{
							theNewOrder[col].push(theItem);
						}
						if(theOrder[col][row]
							&& theOrder[col][row] != theItem)
						{
							theNewOrder[col].push(theOrder[col][row]);
						}
					}
				}

				this.order = this.orderToString(theNewOrder);
				this.onOrderChanged();
				this.run();
			}
			else
			{
				this.run();
			}

			this.startDragging = false;
			this.dragID = false;
			this.activeTarget = false;
		}
	}

	this.calculateOrder = function()
	{
		var result  = '';
		for(var col=0; col<this.cols; col++)
		{
			var elems = EBID(this.name + 'col_' + col).getElementsByTagName('div');
			for(var i=0; i<elems.length; i++)
			{
				var elem = elems[i];
				if(elem.className == 'dragItem')
					result += elem.id.substr(this.name.length+9) + ',';
			}
			if(result.substr(-1) == ',')
				result = result.substr(0, result.length-1);
			result += ';';
		}
		if(result.substr(-1) == ';')
			result = result.substr(0, result.length-1);
		return(result);
	}

	this.init();
}
