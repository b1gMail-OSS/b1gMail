<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{text value=$date.title}</title>
    
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	
	<!-- links -->
	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	
	<!-- client scripts -->
	<script src="clientlang.php" type="text/javascript"></script>
	<script src="clientlib/overlay.js" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js" type="text/javascript"></script>
</head>

<body>

	<table cellspacing="0" cellpadding="0" width="100%" style="margin-bottom:12px;">
	<tr>
		<td><h1><i class="fa fa-calendar-o" aria-hidden="true"></i>
		{text value=$date.title cut=45}</h1></td>
		<td align="right" valign="middle">
			<small>{lng p="group"}:</small> {text value=$groups[$date.group].title}
		</td>
	</tr>
	</table>
		
	<fieldset style="margin-bottom:12px;">
		<legend>{lng p="date2"}</legend>
		
		<table>
			<tr>
				<td><b>{lng p="begin"}:</b></td>
				<td>{if ($date.flags&1)}{date timestamp=$date.startdate dayonly=true} ({lng p="wholeday"}){else}{date timestamp=$date.startdate nice=true elapsed=true}{/if}
				{if $date.orig_startdate}<small> ({lng p="thisevent"} {if ($date.flags&1)}{date timestamp=$date.orig_startdate dayonly=true}{else}{date timestamp=$date.orig_startdate nice=true}{/if})</small>{/if}</td>
			</tr>
			<tr>
				<td><b>{lng p="end"}:</b></td>
				<td>{if ($date.flags&1)}{date timestamp=$date.enddate dayonly=true} ({lng p="wholeday"}){else}{date timestamp=$date.enddate nice=true elapsed=true}{/if}
				{if $date.orig_enddate}<small> ({lng p="thisevent"} {if ($date.flags&1)}{date timestamp=$date.orig_enddate dayonly=true}{else}{date timestamp=$date.orig_enddate nice=true}{/if})</small>{/if}</td>
			</tr>
			<tr>
				<td><b>{lng p="location"}:</b> &nbsp;</td>
				<td>{text value=$date.location}</td>
			</tr>
			<tr>
				<td><b>{lng p="reminder"}:</b> &nbsp;</td>
				<td><input type="checkbox"{if ($date.flags&(2|4|8))} checked="checked"{/if} disabled="disabled" /></td>
			</tr>
			<tr>
				<td><b>{lng p="repeating"}:</b> &nbsp;</td>
				<td><input type="checkbox"{if $date.repeat_flags!=0} checked="checked"{/if} disabled="disabled" /></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset style="margin-bottom:12px;">
		<legend>{lng p="attendees"}</legend>
		
		<table width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td><div class="addressDiv" style="height:63px;">
		{if !$attendees}
			<br /><center><i>({lng p="none"})</i></center>
		{else}
			{foreach from=$attendees item=person}
			<div class="addressItem" onclick="parent.document.location.href='organizer.addressbook.php?sid={$sid}&action=editContact&id={$person.id}';">
				<i class="fa fa-user-o" aria-hidden="true"></i>
				{text value=$person.nachname}, {text value=$person.vorname}
			</div>
			{/foreach}
		{/if}
					</div></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset style="margin-bottom:12px;">
		<legend>{lng p="notes"}</legend>
		
		<textarea style="width:100%;height:80px;" readonly="readonly">{text value=$date.text}</textarea>
	</fieldset>
	
	<div>
		<div style="float:left">
		{if $attendees}
			<input type="submit" value=" {lng p="mailattendees"} " onclick="parent.document.location.href='email.compose.php?to={$mailTo}&subject={$mailSubject}&sid={$sid}';" />
		{/if}
		</div>
		<div style="float:right">
			<input type="submit" value=" {lng p="delete"} " onclick="if(confirm('{lng p="realdel"}')) parent.document.location.href='organizer.calendar.php?action=deleteDate&id={$date.id}&sid={$sid}';"/>
			<input type="submit" value=" {lng p="edit"} " onclick="parent.document.location.href='organizer.calendar.php?action=editDate&id={$date.id}{if $date.repeat_flags!=0}&jumpbackDate={$date.startdate}{/if}&sid={$sid}';" />
			<input type="submit" value=" {lng p="close"} " onclick="parent.hideOverlay();" />
		</div>
	</div>
</body>

</html>
