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

var olCounter = 0, olOverlays = new Object;

function __hideOverlay(id)
{
	olOverlays[id].hide();
}

function __getLastOLID()
{
	var lastOLID = -1;
	for(var olID in olOverlays)
		if(olOverlays[olID] != null)
			lastOLID = olID;
	return(lastOLID);
}

function __getPLastOLID()
{
	var lastOLID = -1, pLastOLID = -1;
	for(var olID in olOverlays)
		if(olOverlays[olID] != null)
		{
			pLastOLID = lastOLID;
			lastOLID = olID;
		}
	return(pLastOLID);
}

function __getOLCount()
{
	var i = 0;
	for(var olID in olOverlays)
		if(olOverlays[olID] != null)
			i++;
	return(i);
}

function hideOverlay()
{
	var lastOLID = __getLastOLID();
	if(lastOLID >= 0)
		__hideOverlay(lastOLID);
}

function overlayDocument()
{
	if(olOverlays.length <= 1)
		return(window);
	else
	{
		return(frames['__olFrame_'+__getPLastOLID()]);
	}
}

function openOverlay(url, name, w, h, clean, noClickHide)
{
	if(top != window)
		return top.openOverlay(url, name, w, h, clean);

	var overlay = new Overlay(noClickHide);
	overlay.setSize(w, h);
	overlay.setCaption(name);
	overlay.setPage(url, clean);
	overlay.show();
	return(overlay);
}

function Overlay(noClickHide)
{
	this.id = olCounter++;
	this.visible = false;
	this.hide = function()
	{
		olOverlays[this.id] = null;

		if(__getOLCount() == 0)
		{
			this.olBackground.style.display = 'none';
			this.olContainer.style.display = 'none';
			this.olBody.removeChild(this.olBackground);
			this.olBody.removeChild(this.olContainer);
		}
		else
		{
			this.olContainer.style.display = 'none';
			this.olBackground.removeChild(this.olContainer);
		}
		this.visible = false;
	}
	this.show = function()
	{
		this.olBackground.style.display = 'block';
		this.olContainer.style.display = 'block';
		this.visible = true;
	}
	this.init = function(noClickHide)
	{
		this.olBody = document.getElementsByTagName('body').item(0);

		// container
		this.olContainer = document.createElement('div');
		this.olContainer.style.display = 'none';
		this.olContainer.style.position = 'absolute';
		this.olContainer.setAttribute('id', '__olContainer_'+this.id);
		this.olContainer.className = '__olContainer';
		this.olContainer.style.zIndex = 102+this.id;
		this.olBody.insertBefore(this.olContainer, this.olBody.firstChild);

		// background
		if(__getOLCount() == 1)
		{
			this.olBackground = document.createElement('div');
			this.olBackground.style.position = 'fixed';
			this.olBackground.style.display = 'none';
			this.olBackground.style.top = '0px';
			this.olBackground.style.left = '0px';
			this.olBackground.style.right = '0px';
			this.olBackground.style.bottom = '0px';
			this.olBackground.style.zIndex = 100+this.id;
			this.olBackground.style.width = '100%';
			this.olBackground.style.height = '100%';
			this.olBackground.id = '__olBackground';
			if(!noClickHide)
				this.olBackground.onclick = function() { hideOverlay(); return(false); }
			this.olBody.insertBefore(this.olBackground, this.olBody.firstChild);
		}
		else
		{
			this.olBackground = EBID('__olBackground');
		}

		// content
		this.olContent = document.createElement('div');
		this.olContent.style.width = '100%';
		this.olContent.style.height = '100%';
		this.olContent.setAttribute('id', '__olContent_'+this.id);
		this.olContent.className = '__olContent';
		this.olContainer.insertBefore(this.olContent, this.olContainer.firstChild);

		// caption
		this.olCaption = document.createElement('div');
		this.olCaption.style.width = '100%';
		this.olCaption.setAttribute('id', '__olCaption_'+this.id);
		this.olCaption.className = '__olCaption';
		this.olContainer.insertBefore(this.olCaption, this.olContainer.firstChild);
	}
	this.setSize = function(w, h)
	{
		h += (document.all ? 15 : 0);

		this.w = w;
		this.h = h;

		this.olContainer.style.left = (getDocumentMetrics('windowW')/2 - w/2) + 'px';
		this.olContainer.style.top = (getDocumentMetrics('rScrollY') + getDocumentMetrics('windowH')/2 - h/2) + 'px';
		this.olContainer.style.width = w + 'px';

		this.olContent.style.height = this.h + 'px';
	}
	this.setCaption = function(caption)
	{
		this.olCaption.innerHTML = caption;
	}
	this.setPage = function(url, clean)
	{
		this.olFrame = document.createElement('iframe');
		this.olFrame.setAttribute('name', '__olFrame_' + this.id);
		this.olFrame.setAttribute('id', '__olFrame_' + this.id);
		this.olFrame.setAttribute('scrolling', clean ? 'no' : 'auto');
		this.olFrame.setAttribute('width', '100%');
		this.olFrame.setAttribute('height', this.h);
		this.olFrame.setAttribute('src', url);
		this.olFrame.setAttribute('frameBorder', 0);
		this.olFrame.setAttribute('border', 0);
		this.olContent.insertBefore(this.olFrame, this.olContent.firstChild);
	}

	olOverlays[this.id] = this;
	this.init(noClickHide);
}
