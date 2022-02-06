<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.coupons.php?do=edit&save=true&id={$coupon.id}&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/coupon32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="code"}:</td>
				<td class="td2">
					<input type="text" name="code" value="{text value=$coupon.code}" style="width:85%;" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="count"}:</td>
				<td class="td2">
					<input type="checkbox" onchange="EBID('count').value=this.checked?'-1':'0';"{if $coupon.anzahl==-1} checked="checked"{/if} id="count_unlim" />
					<label for="count_unlim"><b>{lng p="unlimited"}</b></label>
					{lng p="or"}
					<input type="text" size="6" name="anzahl" id="count" value="{text value=$coupon.anzahl}" onkeyup="EBID('count_unlim').checked=this.value=='-1';" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="from"}:</td>
				<td class="td2">
					<input type="checkbox"{if $coupon.von==-1} checked="checked"{/if} id="from_unlim" name="von_unlim" />
					<label for="from_unlim"><b>{lng p="unlimited"}</b></label>
					{lng p="or"}
					{if $coupon.von!=-1}{html_select_date prefix="von" start_year="-5" field_order="DMY" field_separator="." time=$coupon.von}, 
					{html_select_time prefix="von" display_seconds=false time=$coupon.von}{else}{html_select_date prefix="von" start_year="-5" field_order="DMY" field_separator="."}, 
					{html_select_time prefix="von" display_seconds=false}{/if}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="to"}:</td>
				<td class="td2">
					<input type="checkbox"{if $coupon.bis==-1} checked="checked"{/if} id="to_unlim" name="bis_unlim" />
					<label for="to_unlim"><b>{lng p="unlimited"}</b></label>
					{lng p="or"}
					{if $coupon.bis!=-1}{html_select_date prefix="bis" end_year="+5" field_order="DMY" field_separator="." time=$coupon.bis}, 
					{html_select_time prefix="bis" display_seconds=false time=$coupon.bis}{else}{html_select_date prefix="bis" end_year="+5" field_order="DMY" field_separator="."}, 
					{html_select_time prefix="bis" display_seconds=false}{/if}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="validity"}:</td>
				<td class="td2">
					<input type="checkbox"{if $coupon.valid_signup=='yes'} checked="checked"{/if} id="valid_signup" name="valid_signup" />
					<label for="valid_signup"><b>{lng p="signup"}</b></label><br />
					<input type="checkbox"{if $coupon.valid_loggedin=='yes'} checked="checked"{/if} id="valid_loggedin" name="valid_loggedin" />
					<label for="valid_loggedin"><b>{lng p="li"}</b></label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="benefit"}:</td>
				<td class="td2">
					<div>
						<input type="checkbox"{if $coupon.ver.gruppe} checked="checked"{/if} name="ver_gruppe" id="ver_gruppe" />
						<label for="ver_gruppe"><b>{lng p="movetogroup"}:</b></label>
						<select name="ver_gruppe_id">
						{foreach from=$groups item=group}
							<option value="{$group.id}"{if $group.id==$coupon.ver.gruppe} selected="selected"{/if}>{text value=$group.title}</option>
						{/foreach}
						</select>
					</div>
					
					<div>
						<input type="checkbox"{if $coupon.ver.sms} checked="checked"{/if} name="ver_credits" id="ver_credits" />
						<label for="ver_credits"><b>{lng p="addcredits"}:</b></label>
						<input type="text" name="ver_credits_count" value="{if $coupon.ver.sms}{text value=$coupon.ver.sms}{else}5{/if}" size="6" />
					</div>
				</td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</p>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="redeemedby"}</legend>
	
	<table class="list">
		<tr>
			<th width="50">{lng p="id"}</td>
			<th width="20%">{lng p="email"}</td>
			<th>{lng p="name"}</td>
		</tr>
		{foreach from=$usedBy item=user}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td>{$user.id}</td>
			<td><a href="users.php?do=edit&id={$user.id}&sid={$sid}">{$user.email}</a><br /><small>{text value=$user.aliases cut=45 allowEmpty=true}</small></td>
			<td>{text value=$user.nachname cut=20}, {text value=$user.vorname cut=20}<br /><small>{text value=$user.strasse cut=20} {text value=$user.hnr cut=5}, {text value=$user.plz cut=8} {text value=$user.ort cut=20}</small></td>
		</tr>
		{/foreach}
	</table>
</fieldset>