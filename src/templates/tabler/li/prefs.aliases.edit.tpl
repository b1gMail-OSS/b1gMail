<div class="bm-prefs-page bm-prefs-page-aliases">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-at icon icon-sm" aria-hidden="true"></i>
		{lng p="editalias"}
	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=aliases&do=update&sid={$sid}">
<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">
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
				<label class="form-check mb-0"><input type="checkbox" class="form-check-input" name="email_login" id="email_login"{if $alias.login=='yes'} checked="checked"{/if} /><span class="form-check-label">{lng p="login_with_alias"}</span></label>
			</td>
		</tr>
		</tbody>
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="btn btn-primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</div></div>
</form>
</div>
