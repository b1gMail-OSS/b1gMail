<fieldset>
	<legend>{lng p="contacthistory"} ({email value=$user.email}, #{$user.id})</legend>

	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th>{lng p="salutation"}</th>
			<th>{lng p="name"}</th>
			<th>{lng p="streetno"}</th>
			<th>{lng p="zipcity"}, {lng p="country"}</th>
			<th>{lng p="tel"}, {lng p="fax"}</th>
			<th>{lng p="cellphone"}</th>
			<th>{lng p="discarded"}</th>
		</tr>

		{foreach from=$history item=item}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center">{if !$item.changeDate}<img src="{$tpldir}images/user_active.png" border="0" width="16" height="16" alt="" />{/if}</td>
			<td>{if $item.anrede=='herr'}{lng p="mr"}{elseif $item.anrede=='frau'}{lng p="mrs"}{else}-{/if}</td>
			<td>{text value=$item.nachname cut=20}, {text value=$item.vorname cut=20}</td>
			<td>{text value=$item.strasse cut=20} {text value=$item.hnr}</td>
			<td>{text value=$item.plz} {text value=$item.ort cut=20}<br /><small>{text value=$countries[$item.land]}</small></td>
			<td>{text value=$item.tel cut=20}<br /><small>{text value=$item.fax cut=20}</small></td>
			<td>{text value=$item.mail2sms_nummer cut=20}</td>
			<td>{if $item.changeDate}{date timestamp=$item.changeDate}{else}-{/if}</td>
		</tr>
		{/foreach}
	</table>
</fieldset>

<p>
	<div style="float:left" class="buttons">
		<input class="button" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='users.php?do=edit&id={$user.id}&sid={$sid}';" />
	</div>
	<div style="float:right" class="buttons">
		<input class="button" type="button" value=" {lng p="clearhistory"} " onclick="document.location.href='users.php?do=clearHistory&id={$user.id}&sid={$sid}';" />
	</div>
</p>