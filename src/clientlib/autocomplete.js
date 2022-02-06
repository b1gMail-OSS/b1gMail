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

var acFields = [];

function __acKeyUp(e, f)
{
	return acFields[f.id].onKeyUp(e);
}

function __acKeyDown(e, f)
{
	return acFields[f.id].onKeyDown(e);
}

function __acHide(f)
{
	acFields[f.id].hideSuggestions();
}

function __acClick(f)
{
	var fields = f.id.split('_'),
		acField = fields[3],
		acID = fields[4];
	acFields[acField].deactivateItem(acFields[acField].activeItem);
	acFields[acField].activateItem(acID);
	acFields[acField].onEnter();
	return(false);
}

function Autocomplete()
{
	this.mode = 'plain';
	this.field = false;
	this.suggestions = [];
	this.suggestionPos = 0;
	this.text = '';
	this.activeItem = 0;
	this.setSearchFunction = function(f)
	{
		this.searchAddresses = f;
	}
	this.setMode = function(mode)
	{
		this.mode = mode;
	}
	this.setField = function(field)
	{
		acFields[field] = this;
		this.field = EBID(field);
		this.field.onkeypress = function(e) { if(!e && event) e = event; return e.keyCode!=13; }
		this.field.onkeyup = function(e) { if(!e && event) e = event; return __acKeyUp(e, this); }
		this.field.onkeydown = function(e) { if(!e && event) e = event; return __acKeyDown(e, this); }
		this.field.onblur = function(e) { __acHide(this); }
	}
	this.setSuggestions = function(suggestions)
	{
		this.suggestions = [];
		for(var i=0; i<suggestions.length; i++)
		{
			var item = [];
			item['text'] = suggestions[i];
			this.suggestions.push(item);
		}
		this.renderSuggestions();
	}
	this.renderSuggestions = function()
	{
		if(this.suggestions.length > 0
			&& !(this.suggestions.length==1 && this.suggestions[0]['text'].length < 2))
		{
			// remove old data
			var child;
			while(child = this.completeDiv.firstChild)
				this.completeDiv.removeChild(child);

			// add new data
			for(var i=this.suggestions.length-1; i>=0; i--)
			{
				var suggestion = this.suggestions[i]['text'];
				suggestion = suggestion.replace(this.text, '#b#' + this.text + '#/b#');
				suggestion = HTMLEntities(suggestion);
				suggestion = suggestion.replace('#b#', '<b>');
				suggestion = suggestion.replace('#/b#', '</b>');

				var item = document.createElement('div');
				item.setAttribute('id', '__suggestionItem_' + this.field.id + '_' + i);
				item.onmousedown = function() { return __acClick(this); }
				item.className = 'suggestionInactive';
				item.innerHTML = suggestion;
				this.completeDiv.insertBefore(item, this.completeDiv.firstChild);

				this.suggestions[i]['div'] = item;
			}

			this.activateItem(0);
			this.showSuggestions();
		}
		else
		{
			this.hideSuggestions();
		}
	}
	this.deactivateItem = function(i)
	{
		this.suggestions[i]['div'].className = 'suggestionInactive';
	}
	this.activateItem = function(i)
	{
		this.suggestions[i]['div'].className = 'suggestionActive';
		this.activeItem = i;
	}
	this.onKeyUp = function(e)
	{
		if(e.isChar || (e.keyCode != 13 && e.keyCode != 38 && e.keyCode != 40))
		{
			var text = this.field.value;

			this.suggestionPos = 0;
			if(this.mode == 'semicolonSeparated')
			{
				var semicolon = text.lastIndexOf(',');
				if(semicolon >= 0)
				{
					this.suggestionPos = semicolon+1;
					text = text.substring(semicolon+1);
				}
			}

			text = trim(text);
			text = text.replace('"', '');
			text = text.replace('<', '');
			text = text.replace('>', '');

			if(text.length >= 1)
			{
				this.text = text;
				this.searchAddresses(this, text);
			}
			else
			{
				this.hideSuggestions();
			}
		}

		if(e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40)
			return(false);
		else
			return(true);
	}
	this.onKeyDown = function(e)
	{
		if(e.keyCode == 13)
		{
			this.onEnter();
			return(false);
		}
		else if(e.keyCode == 38)
		{
			this.deactivateItem(this.activeItem);
			if(this.activeItem > 0)
			{
				this.deactivateItem(this.activeItem);
				this.activeItem--;
			}
			this.activateItem(this.activeItem);
			return(false);
		}
		else if(e.keyCode == 40)
		{
			if(this.activeItem < this.suggestions.length-1)
			{
				this.deactivateItem(this.activeItem);
				this.activeItem++;
			}
			this.activateItem(this.activeItem);
			return(false);
		}

		return(true);
	}
	this.onEnter = function()
	{
		var newValue = this.suggestions[this.activeItem]['text'];
		if(this.suggestionPos > 0)
			newValue = ' ' + newValue;
		this.field.value = this.field.value.substring(0, this.suggestionPos) + newValue;
		this.hideSuggestions();
	}
	this.hideSuggestions = function()
	{
		this.completeDiv.style.display = 'none';
	}
	this.showSuggestions = function()
	{
		this.setPos();
		this.completeDiv.style.display = 'block';
	}
	this.setUp = function()
	{
		var body = document.getElementsByTagName('body').item(0);

		this.completeDiv = document.createElement('div');
		this.completeDiv.setAttribute('id', '__acCompleteDiv_' + this.field.id);
		this.completeDiv.style.display = 'none';
		this.completeDiv.style.position = 'absolute';
		this.completeDiv.style.zIndex = 500;
		this.completeDiv.className = 'completeDiv';
		this.setPos();

		body.insertBefore(this.completeDiv, body.firstChild);
	}
	this.setPos = function()
	{
		this.completeDiv.style.width = getElementMetrics(this.field, 'w') + 'px';
		this.completeDiv.style.left = (getElementMetrics(this.field, 'x')-1) + 'px';
		this.completeDiv.style.top = (getElementMetrics(this.field, 'y')+getElementMetrics(this.field, 'h')+1) + 'px';
	}
}
