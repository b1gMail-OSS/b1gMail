<div data-role="header" data-position="fixed">
	<a href="email.php?sid={$sid}" data-icon="delete">{lng p="cancel"}</a>
	<h1>{$pageTitle}</h1>
	<a data-icon="check" data-theme="b" onclick="$('#composeForm').trigger('submit');">{lng p="submit"}</a>
</div>

<div data-role="content">
	<form action="email.php?action=sendMail&sid={$sid}" method="post" id="composeForm">
		<input type="hidden" name="actionToken" value="{$actionToken}" />
		{if $smarty.get.reply}<input type="hidden" name="reference" value="reply:{text value=$smarty.get.reply}" />{/if}
		{if $smarty.get.forward}<input type="hidden" name="reference" value="forward:{text value=$smarty.get.forward}" />{/if}

		<div data-role="fieldcontain">
			<label for="from">{lng p="from"}:</label>
			<select name="from" id="from">
			{foreach from=$possibleSenders key=senderID item=sender}
				<option value="{$senderID}"{if $senderID==$mail.from} selected="selected"{/if}>{text value=$sender}</option>
			{/foreach}
			</select>
		</div>
		
		<div data-role="fieldcontain">
			<label for="to">{lng p="to"}:</label>
			<input type="text" name="to" id="to" value="{text value=$mail.to allowEmpty=true}"  />
		</div>
		
		<div data-role="fieldcontain">
			<label for="cc">{lng p="cc"}:</label>
			<input type="text" name="cc" id="cc" value="{text value=$mail.cc allowEmpty=true}"  />
		</div>
		
		<div data-role="fieldcontain">
			<label for="subject">{lng p="subject"}:</label>
			<input type="text" name="subject" id="subject" value="{text value=$mail.subject allowEmpty=true}"  />
		</div>
		
		<textarea name="text" style="min-height:200px;">{$mail.text}</textarea>
	</form>
</div>
