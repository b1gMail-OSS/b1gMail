<?php
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

if(!defined('B1GMAIL_INIT'))
	die('Directly calling this file is not supported');

if(class_exists('DOMDocument'))
{
	/**
	 * HTML email formatter / sanitizer
	 *
	 * The new strategy is to use a whitelist instead of a blacklist to improve
	 * security. This requires us to parse the HTML document and rebuild it from
	 * its DOM representation.
	 *
	 */
	class BMHTMLEMailFormatter
	{
		protected $root;
		protected $cidMap = array();
		public $externalFiltered, $filteredTags, $filteredAttributes;
		protected $allowedTags = array(
			0 => array('a', 'abbr', 'acronym', 'address', 'area', 'b', 'basefont', 'bdo', 'big',
						'blockquote', 'body', 'br', 'caption', 'center', 'cite', 'code', 'col', 'colgroup',
						'dd', 'del', 'dfn', 'dir', 'div', 'dl', 'dt', 'em', 'fieldset', 'font', 'h1',
						'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'html', 'i', 'ins', 'label', 'legend', 'li', 'map',
						'menu', 'ol', 'p', 'pre', 'q', 's', 'samp', 'small', 'span', 'strike', 'strong',
						'sub', 'sup', 'table', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'tt', 'u', 'ul',
						'var', 'img', 'head', 'mail', 'section', 'main', 'article')
		);
		protected $allowedAttributes = array(
			0 => array('name', 'class', 'title', 'alt', 'width', 'height', 'align', 'nowrap', 'col',
						'row', 'id', 'rowspan', 'colspan', 'cellspacing', 'cellpadding', 'valign',
						'bgcolor', 'color', 'border', 'bordercolorlight', 'bordercolordark', 'face',
						'marginwidth', 'marginheight', 'axis', 'border', 'abbr', 'char', 'charoff',
						'clear', 'compact', 'coords', 'vspace', 'hspace', 'cellborder', 'size', 'lang',
						'dir')
		);
		protected $hiddenTags = array('html', 'body', 'head');
		protected $level = 0;
		protected $allowExternal = false;
		protected $attachmentBaseURL = '';
		protected $composeBaseURL = '';
		protected $replyMode = false;

		public function __construct($code, $encoding)
		{
			$code = preg_replace('~<meta.*?([/]{0,1})>~i', '<meta$1>', $code);
			$code = preg_replace('~<[/]{0,1}o:p>~i', '', $code);

			if(function_exists('mb_convert_encoding'))
			{
				$code2 = mb_convert_encoding($code, 'HTML-ENTITIES', $encoding);
				if($code2 !== false)
					$code = $code2;
			}

			$this->externalFiltered = false;
			$this->filteredTags = $this->filteredAttributes = array();
			$this->root = new DOMDocument('1.0', $encoding);
			@$this->root->loadHTML('<?xml encoding="'.$encoding.'">' . $code);
		}

		public function setReplyMode($replyMode)
		{
			$this->replyMode = $replyMode;
		}

		public function setComposeBaseURL($url)
		{
			$this->composeBaseURL = $url;
		}

		public function setAttachmentBaseURL($url)
		{
			$this->attachmentBaseURL = $url;
		}

		public function setLevel($level)
		{
			$this->level = $level;
		}

		public function setAllowExternal($allowExternal)
		{
			$this->allowExternal = $allowExternal;
		}

		public function setAttachments($attachments)
		{
			$this->cidMap = array();

			foreach($attachments as $key=>$att)
			{
				$cid = trim(str_replace(array('<', '>'), '', $att['cid']));
				$this->cidMap[$cid] = $key;
			}
		}

		public function format()
		{
			return(CharsetDecode($this->formatNode($this->root), 'utf8'));
		}

		protected function formatAttributes($node, $lcTag)
		{
			$result = '';
			$addTarget = '_blank';

			foreach($node->attributes as $name=>$attr)
			{
				$lcName = strtolower($name);
				$val = $node->getAttribute($name);
				$allow = false;

				for($i=0; $i<=$this->level; ++$i)
				{
					if(in_array($lcName, $this->allowedAttributes[$i]))
					{
						$allow = true;
						break;
					}
				}

				if($lcName == 'href' && preg_match('~^(http|https|ftp)://~i', $val))
				{
					$val = ($this->replyMode ? '' : 'deref.php?') . str_replace('#','%23',$val);
					$allow = true;
				}
				else if($lcName == 'href' && preg_match('~^mailto:~i', $val))
				{
					$mailAddr = ExtractMailAddress($val);
					if($mailAddr)
					{
						$val = ($this->replyMode ? '' : $this->composeBaseURL) . urlencode(ExtractMailAddress($val));
						$addTarget = '_top';
						$allow = true;
					}
				}
				else if(($lcName == 'src' && $lcTag == 'img') || $lcName == 'background')
				{
					$isExternal = preg_match('~^.+://~', $val);
					$allow = !$isExternal || $this->allowExternal;

					if(!$isExternal && preg_match('~^cid:~i', $val) && !$this->replyMode)
					{
						$cid = substr($val, 4);
						if(isset($this->cidMap[$cid]))
						{
							$val = $this->attachmentBaseURL . $this->cidMap[$cid];
						}
						else
							$allow = false;
					}

					if($isExternal && !$this->allowExternal)
						$this->externalFiltered = true;
				}
				else if($lcName == 'style')
				{
					if(!$this->isExternalCSS($val) || $this->allowExternal)
						$allow = true;

					if($allow && preg_match('~cid:~i', $val) && !$this->replyMode)
					{
						foreach($this->cidMap as $cidKey=>$cidVal)
							$val = str_replace('cid:' . $cidKey, $this->attachmentBaseURL . $cidVal, $val);
					}
				}

				if($allow)
					$result .= ' ' . $name . '="' . htmlspecialchars($val) . '"';
			}

			if($lcTag == 'a' && $addTarget)
				$result .= ' target="' . $addTarget . '"';
			if($lcTag == 'a')
				$result .= ' rel="noopener noreferrer"';

			return($result);
		}

		protected function isExternalCSS($css)
		{
			return(preg_match('~url~i', $css) || preg_match('~import~i', $css));
		}

		protected function formatNode($node)
		{
			$result = '';

			if($node->hasChildNodes())
			{
				for($node = $node->firstChild; $node; $node = $node->nextSibling)
				{
					if($node->nodeType == XML_TEXT_NODE)
					{
						$result .= HTMLFormat($node->nodeValue);
					}
					else if($node->nodeType == XML_CDATA_SECTION_NODE)
					{
						$result .= $node->nodeValue;
					}
					else if($node->nodeType == XML_HTML_DOCUMENT_NODE)
					{
						$result .= $this->formatNode($node);
					}
					else if($node->nodeType == XML_ELEMENT_NODE)
					{
						$lcTag = strtolower($node->tagName);
						$allow = false;

						for($i=0; $i<=$this->level; ++$i)
						{
							if(in_array($lcTag, $this->allowedTags[$i]))
							{
								$allow = true;
								break;
							}
						}

						if($lcTag == 'style')
						{
							if($this->isExternalCSS($this->formatNode($node)))
							{
								if($this->allowExternal)
									$allow = true;
								else
									$this->externalFiltered = true;
							}
							else
								$allow = true;
						}

						if($allow)
						{
							$nodeCode = $this->formatNode($node);

							if(in_array($lcTag, $this->hiddenTags))
							{
								$result .= $nodeCode;
							}
							else
							{
								$result .= '<' . $node->tagName . $this->formatAttributes($node, $lcTag);
								if(strlen($nodeCode) == 0 && ($lcTag == 'br' || $lcTag == 'hr'))
									$result .= ' />';
								else
									$result .= '>' . $nodeCode . '</' . $node->tagName . '>';
							}
						}
						else
							$this->filteredTags[] = $lcTag;
					}
				}
			}

			return($result);
		}
	}
}
else
{
	// PHP DOM extension not installed, fall back to legacy approach
	class BMHTMLEMailFormatter
	{
		public $externalFiltered, $filteredTags, $filteredAttributes;

		protected $level = 0;
		protected $allowExternal = false;
		protected $attachmentBaseURL = '';
		protected $composeBaseURL = '';
		protected $replyMode = false;

		protected $htmlCode = '';

		public function __construct($code, $encoding)
		{
			$code = preg_replace('~<meta.*?([/]{0,1})>~i', '<meta$1>', $code);
			$code = preg_replace('~<[/]{0,1}o:p>~i', '', $code);

			if(function_exists('mb_convert_encoding'))
			{
				$code2 = mb_convert_encoding($code, 'HTML-ENTITIES', $encoding);
				if($code2 !== false)
					$code = $code2;
			}

			$this->externalFiltered = false;
			$this->filteredTags = $this->filteredAttributes = array();
			$this->htmlCode = $code;
		}

		public function setReplyMode($replyMode)
		{
			$this->replyMode = $replyMode;
		}

		public function setComposeBaseURL($url)
		{
			$this->composeBaseURL = $url;
		}

		public function setAttachmentBaseURL($url)
		{
			$this->attachmentBaseURL = $url;
		}

		public function setLevel($level)
		{
			$this->level = $level;
		}

		public function setAllowExternal($allowExternal)
		{
			$this->allowExternal = $allowExternal;
		}

		public function setAttachments($attachments)
		{
			$this->cidMap = array();

			foreach($attachments as $key=>$att)
			{
				$cid = trim(str_replace(array('<', '>'), '', $att['cid']));
				$this->cidMap[$cid] = $key;
			}
		}

		/**
		 * tag processor (preg_replace_callback callback)
		 *
		 * @return string
		 */
		protected function tagProcessor($in)
		{
			$in = $in[0];

			$in = preg_replace('~\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*~', '' , $in);

			$oldIn = false;
			while($oldIn !== $in)
			{
				$oldIn = $in;
				$in = preg_replace_callback('~&#x([0-9a-f]+);~i', function($matches)
					{
						return(chr(hexdec($matches[1])));
					}, $in);
				$in = preg_replace_callback('~&#([0-9]+);~', function($matches)
					{
						return(chr($matches[1]));
					}, $in);
			}
			$in = preg_replace('~(expression|javascript)~i', 'blocked_$1', $in);

			return($in);
		}

		public function format()
		{
			$scriptParams = array(
				'onabort', 'onblur', 'onchange', 'onclick', 'ondblclick', 'onerror', 'onfocus',
				'onkeydown', 'onkeypress', 'onkeyup', 'onload', 'onmousedown', 'onmousemove',
				'onmouseout', 'onmouseover', 'onmouseup', 'onreset', 'onresize', 'onselect', 'onsubmit',
				'onunload'
			);

			$in = $this->htmlCode;
			$in = preg_replace("/(" . implode('|', $scriptParams) . ")=([\"']*)/i", "blocked_\\1=\\2", $in);
			$in = preg_replace("/<(object|embed|script)([^>]*)>/i", "<span blocked=\"\\1\" style=\"display:none;\">", $in);
			$in = preg_replace("/<\/(object|embed|script)([^>]*)>/i", "</span>", $in);
			$in = preg_replace("/href=([\"']*)javascript\:/i", "blocked_href=\\1javascript:", $in);
			$in = preg_replace_callback('/<[^>]*>/', array($this, 'tagProcessor'), $in);

			if(!$this->allowExternal)
			{
				$in = preg_replace("/(src|href|background)=([\"']*)([h])/i", "blocked_\\1=\\2\\3", $in);
			}
			else
			{
				$in = preg_replace("/href=\"([a-zA-Z]{3,6}):\/\//i", 'target="_blank" href="deref.php?\\1://', $in);
			}

			$in = preg_replace("/href=\"mailto\:([a-zA-Z0-9\.\_-]*\@[a-zA-Z0-9\.\_-]*\.[a-zA-Z0-9\.\_-]*)([\?]*)([^&]*)\"/i", 'target="_top" href="' . $composeBaseURL . '\\1&\\3"', $in);

			if(count($this->cidMap) > 0)
			{
				foreach($this->cidMap as $cid=>$key)
				{
					if(!empty($cid))
					{
						$in = str_replace('"cid:' . $cid . '"',
											'"' . $this->attachmentBaseURL . $key . '"',
											$in);
						$in = str_replace('\'cid:' . $cid . '\'',
											'\'' . $this->attachmentBaseURL . $key . '\'',
											$in);
					}
				}
			}

			return($in);
		}
	}
}
