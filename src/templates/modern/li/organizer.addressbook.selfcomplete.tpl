<div id="contentHeader">
	<div class="left">
		<i class="fa fa-address-book-o" aria-hidden="true"></i>
		{lng p="complete"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<fieldset>
	<legend>{lng p="address"}</legend>

	{lng p="completetext"}
	
	<form action="organizer.addressbook.php?action=sendSelfComplete&id={$id}&sid={$sid}" method="post">
		<p>
			{if $privateMail}
				<i class="fa fa-check-square-o" aria-hidden="true"></i> 
				<input type="radio" name="destMail" value="private" checked="checked" id="destMail_private" />
				<label for="destMail_private">{$privateMail}</label><br />
			{/if}
			{if $workMail}
				<i class="fa fa-check-square-o" aria-hidden="true"></i> 
				<input type="radio" name="destMail" value="work"{if !$privateMail} checked="checked"{/if} id="destMail_work" />
				<label for="destMail_work">{$workMail}</label><br />
			{/if}
		</p>
		
		<p align="right">
			<input type="button" value="{lng p="cancel"}" onclick="history.back()" />
			<input type="submit" value="{lng p="ok"}" />
		</p>
	</form>
	
</fieldset>

</div></div>
