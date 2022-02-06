<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.ads.php?do=edit&save=true&id={$ad.id}&sid={$sid}" method="post" onsubmit="spin(this);">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/ad32.png" border="0" alt="" width="32" height="32" /></td>
				<td colspan="2" style="border: 1px solid #DDDDDD;background-color:#FFFFFF;">
					<textarea name="code" id="code" class="plainTextArea" style="width:100%;height:120px;font-family:courier;">{$ad.code}</textarea>
				</td>
			</tr>
			<tr>
				<td class="td1" width="160">{lng p="category"}:</td>
				<td class="td2"><input type="text" name="category" size="36" value="{text value=$ad.category allowEmpty=true}" /></td>
			</tr>
			<tr>
				<td class="td1" width="160">{lng p="weight"}:</td>
				<td class="td2"><input type="text" name="weight" size="4" value="{$ad.weight}" />%</td>
			</tr>
			<tr>
				<td class="td1">{lng p="paused"}?</td>
				<td class="td2"><input type="checkbox" name="paused"{if $ad.paused} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="comment"}:</td>
				<td class="td2"><textarea style="width:100%;height:65px;" name="comments">{text allowEmpty=true value=$ad.comments}</textarea></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</p>
	</form>
</fieldset>
