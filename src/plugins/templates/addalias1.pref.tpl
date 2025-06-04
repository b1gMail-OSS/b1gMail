<form action="plugin.page.php?plugin=addalias&action=page1&sid={$sid}" method="post" onsubmit="spin(this)">

<fieldset>
	<legend>{lng p="addalias_name"}</legend>
	
	<table>
		<tr>
			<td width="48"><i class="fa-solid fa-plus fa-2xl"></i></td>
			<td width="10">&nbsp;</td>
			<td><b>{lng p="addalias_name"}</b><br />{lng p="addalias_text"}</td>
		</tr>
	</table>
</fieldset>

{if $tpl_use==3}
<fieldset>
	<legend>{lng p="add"}</legend>
	{if $tpl_email_locked==false}
		<center><img src="./templates/images/ok.png" border="0" alt="{lng p="ok"}" width="16" height="16" /> {lng p="success"}!</center>
	{else}
		<center><img src="./templates/images/error.png" border="0" alt="{lng p="error"}" width="16" height="16" /> {lng p="error"}! {lng p="addresstaken"}</center>
	{/if}
</fieldset>
{/if}

<fieldset>
	<legend>{lng p="groups"}</legend>
		<table width="100%">
			<tr>
				<td class="td1" width="150">{lng p="groups"}:</td>
				<td class="td2"><select style="width: 180px;" name="gruppe" {if $tpl_use>=1 AND $tpl_use!=3} disabled="disable"{/if}>
					<option value="-1" {if $selected_gruppe==-1} selected="selected"{/if}>{lng p="all"}</option>
				{foreach from=$gruppen item=gruppe}
					<option value="{$gruppe.id}"{if $selected_gruppe==$gruppe.id} selected="selected"{/if}>{text value=$gruppe.titel}</option>
				{/foreach}
				</select>
				<input type="hidden" value="{$selected_gruppe}" name="gruppe_hidden"> <a href="plugin.page.php?plugin=addalias&action=page2&sid={$sid}"><img src="./templates/images/help.png" border="0" alt="Bearbeiten" width="16" height="16" /></a></td>
			</tr>
		</table>

		{if $tpl_use==0 or $tpl_use==3}
		<p align="right">
			<input class="button" type="submit" value=" {lng p="next"} " />
		</p>
		{/if}
</fieldset>

{if $tpl_use>=1 and $tpl_use!=3}
<fieldset>
	<legend>{lng p="users"}</legend>

		<table>
			<tr>
				<td class="td1" width="150">{lng p="users"}:</td>
				<td class="td2"><select style="width: 180px;" name="user" {if $tpl_use>=2} disabled="disable"{/if}>
				{foreach from=$users item=user}
					<option value="{$user.id}"{if $selected_user==$user.id} selected="selected"{/if}>{text value=$user.email}</option>
				{/foreach}
				</select>
				<input type="hidden" value="{$selected_user}" name="user_hidden"> <a href="plugin.page.php?plugin=addalias&action=page2&sid={$sid}"><img src="./templates/images/help.png" border="0" alt="Bearbeiten" width="16" height="16" /></a></td>
			</tr>
		</table>

		{if $tpl_use==1}
		<p align="right">
			<input class="button" type="submit" value=" {lng p="next"} " />
		</p>
		{/if}
</fieldset>
{/if}

{if $tpl_use>=2 and $tpl_use!=3}
<fieldset>
	<legend>{lng p="alias"}</legend>
		<table>
		<tr>
			<td class="td1">{lng p="type"}:</td>
			<td class="td2">{lng p="aliastype_1"}</td>
		</tr>
		<tr>
			<td class="td1"><label for="typ_1_email">{lng p="email"}:</label></td>
			<td class="td2">
				<input type="text" name="typ_1_email" id="typ_1_email" value="" size="34" /><br />
			</td>
		</tr>
		<tr>
			<td class="td2"></td>
			<td class="td2"></td>
		</tr>
		<tr>
			<td class="td2"></td>
			<td class="td2">{lng p="or"}</td>
		</tr>
		<tr>
			<td class="td2"></td>
			<td class="td2"></td>
		</tr>
		<tr>
			<td class="td1">{lng p="type"}:</td>
			<td class="td2">{lng p="aliastype_1"} + {lng p="aliastype_2"}</td>
		</tr>
		<tr>
			<td class="td1"><label for="email_local">{lng p="email"}:</label></td>
			<td class="td2">
				<input type="text" name="email_local" id="email_local" value="" size="20"/>
				<select name="email_domain" id="email_domain">
				{foreach from=$domainList item=domain}
					<option value="{$domain}">@{$domain}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		</table>

		<p align="right">
			<input class="button" type="submit" value=" {lng p="execute"} " />
		</p>
</fieldset>
{/if}