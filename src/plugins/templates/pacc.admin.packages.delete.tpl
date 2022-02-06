<form action="{$pageURL}&action=packages&delete={$id}&sid={$sid}" method="post">
<fieldset>
	<legend>{lng p="pacc_deletepackage"}</legend>
	
	{lng p="pacc_deletepackagedesc"}
		
	<p>
		<div>
			<table>
			<tr>
				<td>{text value=$packageTitle}</td>
				<td><b>&nbsp;&raquo;&nbsp;</b></td>
				<td>
					<select name="subscriptionAction">
						<option value="continue">{lng p="pacc_delcontinue"}</option>
						<option value="delete">{lng p="pacc_delfallback"}</option>
					</select>
				</td>
			</tr>
			</table>
		</div>
	</p>
</fieldset>

<p>
	<div style="float:right">
		<input class="button" type="submit" value=" {lng p="delete"} " />
	</div>
</p>
</form>
