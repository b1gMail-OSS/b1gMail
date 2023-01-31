<div id="contentHeader">
	<div class="left">
		<i class="fa fa-user-o" aria-hidden="true"></i>
		{lng p="editalias"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form name="f1" method="post" action="prefs.php?action=aliases&do=update&sid={$sid}">
	<input type="hidden" name="id" value="{if isset($alias.id)}{text value=$alias.id}{/if}" />
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="editalias"}</th>
		</tr>
		
		<tbody id="tbody">
		<tr>
			<td class="listTableLeft"><label for="email_name">{lng p="sendername"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="email_name" id="email_name" value="{if isset($alias.sendername)}{text value=$alias.sendername allowEmpty=true}{/if}" size="34"  />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="email">{lng p="email"}:</label></td>
			<td class="listTableRight">
				<input type="email" name="email" id="email" value="{if isset($alias.email)}{text value=$alias.email}{/if}" size="34" disabled /><br />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="checkbox" name="email_login" id="email_login"{if $alias.login=='yes'} checked="checked"{/if}  />
				<label for="email_login">{lng p="login_with_alias"}</label>
			</td>
		</tr>
		</tbody>
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>

</div></div>
