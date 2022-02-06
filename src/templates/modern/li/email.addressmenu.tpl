<!-- address menu -->
<div id="addressMenu" class="mailMenu" style="display:none;position:absolute;left:0px;top:0px;" oncontextmenu="return(false);" onmousedown="if(event.button==2) return(false);">
	<a id="addressMenuReadItem" class="mailMenuItem" style="display:none;" href="javascript:{if $preview}parent.{/if}document.location.href='email.read.php?id='+encodeURIComponent(currentEMailID)+'&sid='+currentSID;"><i class="fa fa-envelope-o" aria-hidden="true"></i> {lng p="mail_read"}</a>
	<div class="mailMenuSep" id="addressMenuReadItemSep"></div>
	<a class="mailMenuItem" href="javascript:{if $preview}parent.{/if}document.location.href='email.compose.php?to='+encodeURIComponent(currentEMail)+'&sid='+currentSID;"><i class="fa fa-reply" aria-hidden="true"></i> {lng p="sendmail"}</a>
	<a class="mailMenuItem" href="javascript:{if $preview}parent.{/if}document.location.href='organizer.addressbook.php?action=addContact&email='+encodeURIComponent(currentEMail)+'&sid='+currentSID;"><i class="fa fa-address-book-o" aria-hidden="true"></i> {lng p="toaddr"}</a>
</div>