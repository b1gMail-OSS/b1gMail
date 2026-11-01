<!DOCTYPE html>
<html>
<head>
	<title>{if $calendarItem}{lng p="editcalendar"}{else}{lng p="addcalendar"}{/if}</title>
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	<link rel="shortcut icon" type="image/png" href="{$selfurl}res/favicon.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	<script src="{sessionurl file='clientlang.php'}"></script>
	<script src="{$selfurl}clientlib/overlay.js"></script>
	<script src="{$tpldir}js/common.js"></script>
	<script src="{$tpldir}js/loggedin.js"></script>
	<script src="{$tpldir}js/dialog.js"></script>
	<script>
	function organizerCollectionCheck(f) {
		var t = f.elements['title'];
		if(!t || String(t.value).replace(/^\s+|\s+$/g, '').length === 0) { alert(lang['fillin']); return false; }
		return true;
	}
	</script>
</head>
<body>
	<form method="post" target="_top" action="{sessionurl file='organizer.calendar.php' params="action=calendars&do={if $calendarItem}save&id={$calendarItem.id}{else}add{/if}"}" onsubmit="return organizerCollectionCheck(this);">
		{csrffield}
		<fieldset>
			<legend>{if $calendarItem}{lng p="editcalendar"}{else}{lng p="addcalendar"}{/if}</legend>
			<table>
				<tr>
					<td><label for="title">{lng p="title"}:</label></td>
					<td><input type="text" name="title" id="title" value="{if isset($calendarItem.title)}{text value=$calendarItem.title allowEmpty=true}{/if}" size="34" style="width:100%;" /></td>
				</tr>
				<tr>
					<td valign="top">{lng p="color"}:</td>
					<td>
						{section name=c loop=6}
						<label style="display:inline-block;margin:0 8px 4px 0;">
							<input type="radio" name="color" value="{$smarty.section.c.index}"{if (!$calendarItem && $smarty.section.c.index==0) || (isset($calendarItem.color) && $calendarItem.color==$smarty.section.c.index)} checked="checked"{/if} />
							<span class="calendarDate_{$smarty.section.c.index}" style="display:inline-block;width:12px;height:12px;vertical-align:middle;{if $smarty.section.c.index==0}background:#3D81EB;{elseif $smarty.section.c.index==1}background:#5DB747;{elseif $smarty.section.c.index==2}background:#FA514A;{elseif $smarty.section.c.index==3}background:#FD9530;{elseif $smarty.section.c.index==4}background:#C358BF;{else}background:#8A74D3;{/if}"></span>
						</label>
						{/section}
					</td>
				</tr>
			</table>
		</fieldset>
		<p align="right">
			<input type="button" onclick="parent.hideOverlay()" value="{lng p="cancel"}" />
			<input type="submit" value="{lng p="ok"}" />
		</p>
	</form>
</body>
</html>
