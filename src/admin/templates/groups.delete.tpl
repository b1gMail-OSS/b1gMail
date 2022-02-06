<form action="groups.php?do=realDelete&sid={$sid}" method="post">
<fieldset>
	<legend>{lng p="deletegroup"}</legend>
	
	{lng p="groupdeletedesc"}
		
	<p>
		<div>
			<table>
			{foreach from=$groupsToDelete item=dGroupTitle key=dGroupID}
			<tr>
				<td>{text value=$dGroupTitle}</td>
				<td><b>&nbsp;&raquo;&nbsp;</b></td>
				<td>
					<select name="groups[{$dGroupID}]">
					{foreach from=$groups key=groupID item=groupTitle}
						<option value="{$groupID}">{text value=$groupTitle}</option>
					{/foreach}
					</select>
				</td>
			</tr>
			{/foreach}
			</table>
		</div>
	</p>
</fieldset>

<p>
	<div style="float:right" class="buttons">
		<input class="button" type="submit" value=" {lng p="move"} &amp; {lng p="delete"} " />
	</div>
</p>
</form>
