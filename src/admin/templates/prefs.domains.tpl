<fieldset>
	<legend>{lng p="domains"}</legend>
	
	<form action="prefs.common.php?action=domains&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection2(document.forms.f1,'domains[','[del]');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="domain"}</th>
			<th style="text-align:center;" width="100">{lng p="login"}</th>
			<th style="text-align:center;" width="100">{lng p="signup"}</th>
			<th style="text-align:center;" width="100">{lng p="aliases"}</th>
			<th width="80">{lng p="pos"}</th>
			<th width="35">&nbsp;</th>
		</tr>
		
		{foreach from=$domains item=domain}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/domain.png" border="0" alt="" width="16" height="16" /></td>
			<td><input type="checkbox" name="domains[{$domain.domain}][del]" /></td>
			<td>{domain value=$domain.domain}</td>
			<td style="text-align:center;"><input type="checkbox" name="domains[{$domain.domain}][in_login]"{if $domain.in_login} checked="checked"{/if} /></td>
			<td style="text-align:center;"><input type="checkbox" name="domains[{$domain.domain}][in_signup]"{if $domain.in_signup} checked="checked"{/if} /></td>
			<td style="text-align:center;"><input type="checkbox" name="domains[{$domain.domain}][in_aliases]"{if $domain.in_aliases} checked="checked"{/if} /></td>
			<td><input type="text" name="domains[{$domain.domain}][pos]" value="{text value=$domain.pos allowEmpty=true}" size="6" /></td>
			<td>
				<a href="prefs.common.php?action=domains&delete={$domain.urlDomain}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="8">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" class="smallInput">
						<option value="-">------------</option>
						
						<optgroup label="{lng p="actions"}">
							<option value="delete">{lng p="delete"}</option>
						</optgroup>
					</select>&nbsp;
				</div>
				<div style="float:left;">
					<input type="submit" name="executeMassAction" value=" {lng p="execute"} " class="smallInput" />
				</div>
				<div style="float:right;">
					<input type="submit" name="save" class="button" value=" {lng p="save"} " />&nbsp; 
				</div>
			</td>
		</tr>
	</table>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="adddomain"}</legend>
	
	<form action="prefs.common.php?action=domains&add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="3"><img src="{$tpldir}images/domain32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="domain"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="domain" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="show_at"}:</td>
				<td class="td2"><input type="checkbox" name="in_login" id="in_login" checked="checked" />
								<label for="in_login">{lng p="login"}</label>
								<br />
								<input type="checkbox" name="in_signup" id="in_signup" checked="checked" />
								<label for="in_signup">{lng p="signup"}</label>
								<br />
								<input type="checkbox" name="in_aliases" id="in_aliases" checked="checked" />
								<label for="in_aliases">{lng p="aliases"}</label>
								</td>
			</tr>
			<tr>
				<td class="td1">{lng p="pos"}:</td>
				<td class="td2"><input type="text" name="pos" value="0" size="6" /></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>
