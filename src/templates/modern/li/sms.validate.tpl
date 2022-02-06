<div id="contentHeader">
	<div class="left">
		<i class="fa fa-commenting-o" aria-hidden="true"></i>
		{lng p="sendsms"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

{if $enterCode}
{if $error}
<div class="note">{lng p="invalidsmscode"}</div><br />
{/if}

<form name="f1" method="post" action="sms.php?do=validate&sid={$sid}">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="smsvalidation2"}</th>
		</tr>
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				{lng p="smsvalidation2_text"}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="sms_validation_code">{lng p="validationcode"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="sms_validation_code" id="sms_validation_code" value="" style="width:250px;" />
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>
{else}

<table>
	<tr>
		<td valign="top" width="64" align="center"><i class="fa fa-info-circle fa-5x" aria-hidden="true"></i></td>
		<td valign="top">
			<b>{lng p="smsvalidation2"}</b>
			<br />{lng p="pleasevalidate"}
			<br /><br />
			<input type="button" value="{lng p="ok"} &raquo;" class="primary" onclick="document.location.href='prefs.php?action=contact&sid={$sid}';" />
		</td>
	</tr>
</table>

{/if}

</div></div>
