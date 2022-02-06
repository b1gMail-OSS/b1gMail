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

function htmlEditor(textareaID)
{
	this.focus = function(toStart)
	{
		if(this.mode == 'text')
		{
			this.textarea.focus();

			if(toStart)
			{
				if(this.textarea.setSelectionRange)
				{
					this.textarea.setSelectionRange(0, 0);
				}
				else if(this.textarea.createTextRange)
				{
					var range = this.textarea.createTextRange();
					range.collapse(true);
					range.moveStart('character', 0);
					range.moveEnd('character', 0);
					range.select();
				}
			}
		}
		else
			this.ckEditor.focus();
	}

	this.textToHTML = function(text)
	{
        var htmlVersion = text;
        htmlVersion = htmlVersion.replace(/\</gi, "&lt;");
        htmlVersion = htmlVersion.replace(/\>/gi, "&gt;");
        htmlVersion = htmlVersion.replace(/\n/gi, "<br />");
        return(htmlVersion);
	}

	this.htmlToText = function(html)
	{
		var textVersion = html;
     	textVersion = textVersion.replace(/\r/g, "");
        textVersion = textVersion.replace(/\n/g, "");
        textVersion = textVersion.replace(/\<p(.*?)\>/gi, "\n");
        textVersion = textVersion.replace(/\<br\>/gi, "\n");
        textVersion = textVersion.replace(/\<br\/\>/gi, "\n");
        textVersion = textVersion.replace(/\<br \/\>/gi, "\n");
        textVersion = textVersion.replace(/\<\/div\>/gi, "\n");
        textVersion = textVersion.replace(/\&amp\;/gi, "&");
        textVersion = textVersion.replace(/\&quot\;/gi, "\"");
        textVersion = textVersion.replace(/\&uuml\;/gi, "ue");
        textVersion = textVersion.replace(/\&ouml\;/gi, "oe");
        textVersion = textVersion.replace(/\&auml\;/gi, "ae");
        textVersion = textVersion.replace(/\&Uuml\;/gi, "Ue");
        textVersion = textVersion.replace(/\&Ouml\;/gi, "Oe");
        textVersion = textVersion.replace(/\&Auml\;/gi, "Ae");
        textVersion = textVersion.replace(/\&szlig\;/gi, "ss");
        textVersion = textVersion.replace(/\&nbsp\;/gi, " ");

        textVersion = textVersion.replace(/\<blockquote(.*?)\>/gi, "<blockquote>");
        textVersion = textVersion.replace(/\<\/blockquote(.*?)\>/gi, "</blockquote>");

		textLines = textVersion.split(/^/mg);

		textVersion = '';
		var quoteLevel = 0, quoteString = '';
		for(var i=0; i<textLines.length; i++)
		{
			if(textLines[i].indexOf('<blockquote>') != -1)
			{
				quoteLevel++;
				quoteString += '> ';
			}

			textVersion += quoteString + textLines[i];

			if(textLines[i].indexOf('</blockquote>') != -1)
			{
				quoteLevel--;

				if(quoteLevel < 1)
					quoteString = '';
				else
					quoteString = quoteString.substring(2);
			}
		}

        textVersion = textVersion.replace(/<(.*?)>/gi, "");

        textVersion = textVersion.replace(/\&lt\;/gi, "<");
        textVersion = textVersion.replace(/\&gt\;/gi, ">");

        return(textVersion);
	}

	this.setText = function(text, isHTML)
	{
		if(this.mode == 'text')
		{
			this.textarea.value = isHTML ? this.htmlToText(text) : text;
		}
		else if(this.mode == 'html')
		{
			this.ckEditor.setData(isHTML ? text : this.textToHTML(text));
		}
	}

	this.clear = function()
	{
		this.setText('');
	}

	this.reset = function()
	{
		this.switchMode(this.initialMode);
		this.setText(this.initialText, this.initialMode=='html');
	}

	this.insertText = function(text)
	{
		if(this.mode == 'text')
		{
			this.textarea.focus();

			if(document.selection)
			{
				var range = document.selection.createRange();
				range.text = text;
			}
			else if(this.textarea.selectionStart || this.textarea.selectionStart == '0')
			{
				var rangeStart = this.textarea.selectionStart,
					rangeEnd = this.textarea.selectionEnd;
				this.textarea.value = this.textarea.value.substring(0, rangeStart) + text + this.textarea.value.substring(rangeEnd, this.textarea.value.length);
				this.textarea.selectionStart = rangeStart + text.length;
				this.textarea.selectionEnd = this.textarea.selectionStart;
			}
			else
				this.textarea.value += text;
		}
		else if(this.mode == 'html')
		{
			this.ckEditor.focus();
			this.ckEditor.insertHtml(text);
		}
	}

	this.getPlainText = function()
	{
		if(this.mode == 'text')
			return(this.textarea.value);
		else if(this.mode == 'html')
			return(this.htmlToText(this.ckEditor.getData()));
		return('');
	}

	this.switchMode = function(newMode, initial)
	{
		if(initial)
			this.initialMode = newMode;
		if(newMode == 'html' && this.mode == 'text')
		{
			if(!initial)
				this.textarea.value = this.textToHTML(this.textarea.value);
			this.createCkEditor();
			this.mode = 'html';
			return(true);
		}
		else if(newMode == 'text' && this.mode == 'html'
				&& (initial || confirm(lang['switchwarning'])))
		{
			data = this.ckEditor.getData();
			this.ckEditor.destroy(true);
			if(!initial)
				this.textarea.value = this.htmlToText(data);
			else
				this.textarea.value = this.initialTaValue;
			this.mode = 'text';
			this.focus(true);
			return(true);
		}
		if(this.modeField)
			document.getElementById(this.modeField).value = this.mode;
		return(false);
	}

	this.ckEditor = null;
	this.initialTaValue = '';

	this.createCkEditor = function()
	{
		var intro = '';

		if(!this.disableIntro)
			intro = '<span style="font-family:arial,helvetica,sans-serif;font-size:12px;">&shy;</span>';

		this.initialTaValue = this.textarea.value;
		this.textarea.value = intro + this.textarea.value;

		this.ckEditor = CKEDITOR.replace(this.textarea, this.ckEditorPrefs);

		var inst = this;

		this.ckEditor.on('instanceReady', function()
		{
			inst.setHeight(inst.height);
			inst.onReady();
			inst.focus();
			inst.onReady = function() { }
		});

		this.ckEditor.on('change', function(evt)
		{
			inst.onChange();
		});
	}

	this.init = function()
	{
		this.createCkEditor();
	}

	this.submit = function()
	{
		if(this.modeField)
			document.getElementById(this.modeField).value = this.mode;
		if(this.mode == 'html')
			this.ckEditor.updateElement();
	}

	this.start = function()
	{
		this.initialText = this.textarea.value;
		this.focus();
	}

	this.setHeight = function(height)
	{
		if(height == null)
			return;

		this.textarea.style.height = height + 'px';

		if(this.mode == 'html' && this.ckEditor.status == 'ready')
			this.ckEditor.resize('100%', height);

		this.height = height;
	}

	this.textareaID = textareaID;
	this.textarea = document.getElementById(textareaID);

	var _this = this;
	addEvent(this.textarea, 'keypress', function() { _this.onChange(); });

	this.mode = 'html';
	this.initialText = '';
	this.initialMode = 'html';
	this.modeField = null;
	this.disableIntro = false;

	this.ckEditorPrefs = {
		uiColor: '#EEEEEE',
		enterMode: CKEDITOR.ENTER_BR,
		shiftEnterMode: CKEDITOR.ENTER_P,
		extraAllowedContent: 'p b i em blockquote font table tr td th hr pre [cellpadding,cellspacing,valign,noshade,face,size,color,align,width,height,colspan,rowspan] {font-family,margin,padding,border-top,border-right,border-left,border-bottom,border,color,text-align,width,height}',
		toolbarGroups: [
			{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
			{ name: 'styles', groups: [ 'styles' ] },
			{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
			{ name: 'forms', groups: [ 'forms' ] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
			{ name: 'paragraph', groups: [ 'align', 'list', 'indent', 'blocks', 'bidi', 'paragraph' ] },
			{ name: 'insert', groups: [ 'insert' ] },
			{ name: 'links', groups: [ 'links' ] },
			{ name: 'colors', groups: [ 'colors' ] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
			{ name: 'tools', groups: [ 'tools' ] },
			{ name: 'others', groups: [ 'others' ] },
			{ name: 'about', groups: [ 'about' ] }
		]
	};
	this.onReady = function() { }
	this.onChange = function() { }
}
