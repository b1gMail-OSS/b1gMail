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
		protected $externalBaseHref = '';

		public function __construct($code, $encoding)
		{
			$this->externalBaseHref = $this->extractExternalBaseHref($code);
			$code = preg_replace('~<meta.*?([/]{0,1})>~i', '<meta$1>', $code);
			$code = preg_replace('~<[/]{0,1}o:p>~i', '', $code);

			if(function_exists('mb_encode_numericentity'))
			{
				$code2 = mb_encode_numericentity($code, [0x80, 0x10FFFF, 0, ~0], $encoding);
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

		public function getExternalBaseHref()
		{
			return $this->externalBaseHref;
		}

		protected function extractExternalBaseHref($code)
		{
			if(!is_string($code) || $code === '')
				return '';

			if(preg_match('~<base\b[^>]*\bhref\s*=\s*["\'](https?://[^"\'\s>]+)["\']~i', $code, $m)
				|| preg_match('~<base\b[^>]*\bhref\s*=\s*(https?://[^"\'\s>]+)~i', $code, $m))
			{
				$href = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
				if(preg_match('~^https?://~i', $href))
					return $href;
			}

			return '';
		}

		protected function absolutizeUrl($url)
		{
			$url = trim((string)$url);
			if($url === '' || preg_match('~^(https?:|data:|cid:|mailto:|#)~i', $url))
				return $url;
			if(strpos($url, '//') === 0)
				return $url;

			$base = $this->externalBaseHref;
			if($base === '')
				return $url;

			$parts = parse_url($base);
			if(empty($parts['scheme']) || empty($parts['host']))
				return $url;

			$origin = $parts['scheme'].'://'.$parts['host']
				.(isset($parts['port']) ? ':'.$parts['port'] : '');
			if(isset($url[0]) && $url[0] === '/')
				return $origin.$url;

			$pathDir = isset($parts['path']) ? $parts['path'] : '/';
			$slash = strrpos($pathDir, '/');
			$pathDir = ($slash === false) ? '/' : substr($pathDir, 0, $slash + 1);
			if($pathDir === '' || $pathDir[0] !== '/')
				$pathDir = '/'.ltrim($pathDir, '/');

			return $origin.$pathDir.$url;
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
					$val = ($this->replyMode ? '' : DerefUrl(str_replace('#', '%23', $val)));
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
					$isCid = preg_match('~^cid:~i', $val);
					$isData = preg_match('~^data:~i', $val);

					if($isCid && !$this->replyMode)
					{
						$cid = substr($val, 4);
						if(isset($this->cidMap[$cid]))
							$val = $this->attachmentBaseURL . $this->cidMap[$cid];
						else
						{
							$allow = false;
							continue;
						}
					}

					$isOurAttachment = ($this->attachmentBaseURL !== '' && strpos($val, $this->attachmentBaseURL) === 0);
					if($this->allowExternal && !$isData && !$isCid && !$isOurAttachment)
						$val = $this->absolutizeUrl($val);
					$isOurAttachment = ($this->attachmentBaseURL !== '' && strpos($val, $this->attachmentBaseURL) === 0);
					$isSafe = $isData || $isOurAttachment;
					$isExternal = !$isSafe;
					$allow = !$isExternal || $this->allowExternal;

					if($isExternal && !$this->allowExternal)
						$this->externalFiltered = true;
				}
				else if($lcName == 'style')
				{
					$val = $this->sanitizeCSS($val);
					$allow = $val !== '';

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
			return (bool)preg_match('~@import|url\s*\(~i', (string)$css);
		}

		/**
		 * Decode CSS escapes/entities and drop scriptable or remote constructs.
		 *
		 * @param string $css
		 * @return string
		 */
		protected function sanitizeCSS($css)
		{
			$css = (string)$css;
			if($css === '')
				return '';

			$css = preg_replace('~/\*.*?\*/~s', '', $css);
			$prev = null;
			while($prev !== $css)
			{
				$prev = $css;
				$css = html_entity_decode($css, ENT_QUOTES | ENT_HTML5, 'UTF-8');
				$css = preg_replace_callback('~\\\\([0-9a-fA-F]{1,6})[ \t\r\n\f]?~', function($m)
				{
					$cp = hexdec($m[1]);
					if($cp < 32 || $cp === 0x7F)
						return '';
					if(function_exists('mb_chr'))
					{
						$ch = mb_chr($cp, 'UTF-8');
						return $ch !== false ? $ch : '';
					}
					return $cp < 256 ? chr($cp) : '';
				}, $css);
			}
			$css = preg_replace('~[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]~', '', $css);

			$dangerous = (bool)preg_match('~expression|javascript|vbscript|behavior|-moz-binding|</\s*style|<\s*script~i', $css);
			$remote = $this->isExternalCSS($css);
			if($remote && !$this->allowExternal)
			{
				$this->externalFiltered = true;
				$css = preg_replace('~[^{};@]*?(?:@import|url\s*\()[^{};]*;?~i', '', $css);
				$remote = $this->isExternalCSS($css);
			}
			if($dangerous || ($remote && !$this->allowExternal))
			{
				$css = preg_replace('~[^{};]*?(?:expression|javascript|vbscript|behavior|-moz-binding)[^{};]*;?~i', '', $css);
			}

			$css = preg_replace('~</\s*style[^>]*>~i', '', $css);
			$css = preg_replace('~<\s*script[^>]*>~i', '', $css);
			$css = str_replace(array('<', '>'), '', $css);
			$css = trim($css);

			if($css !== '' && ($this->isExternalCSS($css) && !$this->allowExternal
				|| preg_match('~expression|javascript|vbscript|behavior|-moz-binding~i', $css)))
				return '';

			return $css;
		}

		/**
		 * @param \DOMNode $node
		 * @return string
		 */
		protected function collectText($node)
		{
			$text = '';
			if(!$node->hasChildNodes())
				return $text;

			for($child = $node->firstChild; $child; $child = $child->nextSibling)
			{
				if($child->nodeType == XML_TEXT_NODE || $child->nodeType == XML_CDATA_SECTION_NODE)
					$text .= $child->nodeValue;
				else if($child->nodeType == XML_ELEMENT_NODE)
					$text .= $this->collectText($child);
			}

			return $text;
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
						$result .= HTMLFormat($node->nodeValue);
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
							$css = $this->sanitizeCSS($this->collectText($node));
							if($css !== '')
								$result .= '<style type="text/css">'.$css.'</style>';
							continue;
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

			if(function_exists('mb_encode_numericentity'))
			{
				$code2 = mb_encode_numericentity($code, [0x80, 0x10FFFF, 0, ~0], $encoding);
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
				'onunload', 'onpointerdown', 'onpointerup', 'onpointermove', 'ontouchstart',
				'ontouchend', 'onanimationend', 'ontransitionend', 'onwheel', 'onpaste', 'oninput'
			);

			$in = $this->htmlCode;
			$in = preg_replace_callback('~<!\[CDATA\[(.*?)\]\]>~is', function($m)
			{
				return HTMLFormat($m[1]);
			}, $in);
			$in = preg_replace('~<style\b[^>]*>.*?</style>~is', '', $in);
			$in = preg_replace('~\sstyle\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)~i', '', $in);
			$in = preg_replace("/(" . implode('|', $scriptParams) . ")=([\"']*)/i", "blocked_\\1=\\2", $in);
			$in = preg_replace("/<(object|embed|script|iframe|svg|math|link|meta)([^>]*)>/i", "<span blocked=\"\\1\" style=\"display:none;\">", $in);
			$in = preg_replace("/<\/(object|embed|script|iframe|svg|math|link|meta)([^>]*)>/i", "</span>", $in);
			$in = preg_replace("/href=([\"']*)javascript\:/i", "blocked_href=\\1javascript:", $in);
			$in = preg_replace_callback('/<[^>]*>/', array($this, 'tagProcessor'), $in);

			if(!$this->allowExternal)
			{
				$in = preg_replace("/(src|href|background)=([\"']*)([h])/i", "blocked_\\1=\\2\\3", $in);
				$in = preg_replace("/(src|background)=([\"']*)(\/\/)/i", "blocked_\\1=\\2\\3", $in);
			}
			else
			{
				$in = preg_replace_callback(
					'/href="((?:https?|ftp):\/\/[^"]+)"/i',
					function($matches)
					{
						return 'target="_blank" href="' . htmlspecialchars(DerefUrl($matches[1]), ENT_QUOTES, 'UTF-8') . '"';
					},
					$in);
			}

			$in = preg_replace("/href=\"mailto\:([a-zA-Z0-9\.\_-]*\@[a-zA-Z0-9\.\_-]*\.[a-zA-Z0-9\.\_-]*)([\?]*)([^&]*)\"/i", 'target="_top" href="' . $this->composeBaseURL . '\\1&\\3"', $in);

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
