<p>
	{lng p="suggestions_desc"}
</p>

<table class="table" style="margin-bottom:0;">
	{foreach from=$suggestions item=email}
	<tr>
		<td><span class="glyphicon glyphicon-ok" style="color:darkgreen;"></span>
			{email value=$email}</td>
		<td style="text-align:right;">
			<button class="btn btn-success btn-xs" onclick="chooseAddress('{email value=$email}')">{lng p="choose"}</button>
		</td>
	</tr>
	{/foreach}
</table>
