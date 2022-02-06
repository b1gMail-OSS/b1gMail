<fieldset>
	<legend>{lng p="coupons"}</legend>
	
	<form action="prefs.coupons.php?sid={$sid}" method="post" name="f1" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'coupon_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="code"}</th>
			<th>{lng p="validitytime"}</th>
			<th width="160">{lng p="validity"}</th>
			<th width="160">{lng p="used3"} / {lng p="count"}</th>
			<th width="60">&nbsp;</th>
		</tr>
		
		{foreach from=$coupons item=coupon}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/coupon.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center"><input type="checkbox" name="coupon_{$coupon.id}" /></td>
			<td>{text value=$coupon.code}<br />
				<small>{if $coupon.ver.sms}{lng p="addcredits"}: {$coupon.ver.sms}{/if}{if $coupon.ver.gruppe}{if $coupon.ver.sms}, {/if}{lng p="movetogroup"}: {$groups[$coupon.ver.gruppe].title}{/if}</small></td>
			<td>{lng p="to"} {if $coupon.bis==-1}({lng p="unlimited"}){else}{date timestamp=$coupon.bis}{/if}
				<br /><small>{lng p="from"} {if $coupon.von==-1}({lng p="unlimited"}){else}{date timestamp=$coupon.von}{/if}</small></td>
			<td>
				{if $coupon.valid_signup}{lng p="signup"}{if $coupon.valid_loggedin},{/if}{/if}
				{if $coupon.valid_loggedin}{lng p="li"}{/if}
			</td>
			<td>{$coupon.used}
				/ {if $coupon.anzahl==-1}({lng p="unlimited"}){else}{$coupon.anzahl}{/if}</td>
			<td>
				<a href="prefs.coupons.php?do=edit&id={$coupon.id}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="prefs.coupons.php?delete={$coupon.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
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
			</td>
		</tr>
	</table>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="addcoupon"}</legend>
	
	<form action="prefs.coupons.php?add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/coupon32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="codes"}:</td>
				<td class="td2">
					<textarea style="width:100%;height:80px;" name="code" id="codes"></textarea>
					<table cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td align="left"><small>{lng p="sepby"}</small></td>
							<td align="right">[ <a href="javascript:void(0);" onclick="EBID('generator').style.display=EBID('generator').style.display==''?'none':'';">{lng p="generate"}</a> ]</td>
						</tr>
					</table>
					<div id="generator" style="display:none;">
						<fieldset>
							<legend>{lng p="generate"}</legend>
							<table width="100%">
								<tr>
									<td class="td1" width="120">{lng p="count"}:</td>
									<td class="td2"><input type="text" id="generator_count" value="10" size="6" /></td>
								</tr>
								<tr>
									<td class="td1">{lng p="length"}:</td>
									<td class="td2"><input type="text" id="generator_length" value="10" size="6" /></td>
								</tr>
								<tr>
									<td class="td1">{lng p="chars"}:</td>
									<td class="td2">
										<input type="checkbox" checked="checked" id="generator_az" />
											<label for="generator_az"><b>a-z</b></label>
										<input type="checkbox" checked="checked" id="generator_az2" />
											<label for="generator_az2"><b>A-Z</b></label>
										<input type="checkbox" checked="checked" id="generator_09" />
											<label for="generator_09"><b>0-9</b></label>
										<input type="checkbox" id="generator_special" />
											<label for="generator_special"><b>.,_-&amp;$</b></label>
									</td>
								</tr>
								<tr>
									<td class="td1">&nbsp;</td>
									<td class="td2"><input class="button" type="button" value=" {lng p="generate"} " onclick="generateCodes(EBID('codes'));EBID('generator').style.display='none';" /></td>
								</tr>
							</table>
						</fieldset>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="count"}:</td>
				<td class="td2">
					<input type="checkbox" onchange="EBID('count').value=this.checked?'-1':'0';" checked="checked" id="count_unlim" />
					<label for="count_unlim"><b>{lng p="unlimited"}</b></label>
					{lng p="or"}
					<input type="text" size="6" name="anzahl" id="count" value="-1" onkeyup="EBID('count_unlim').checked=this.value=='-1';" />
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="from"}:</td>
				<td class="td2">
					<input type="checkbox" checked="checked" id="from_unlim" name="von_unlim" />
					<label for="from_unlim"><b>{lng p="now"}</b></label>
					{lng p="or"}
					{html_select_date prefix="von" start_year="-5" field_order="DMY" field_separator="."}, 
					{html_select_time prefix="von" display_seconds=false}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="to"}:</td>
				<td class="td2">
					<input type="checkbox" checked="checked" id="to_unlim" name="bis_unlim" />
					<label for="to_unlim"><b>{lng p="unlimited"}</b></label>
					{lng p="or"}
					{html_select_date prefix="bis" end_year="+5" field_order="DMY" field_separator="."}, 
					{html_select_time prefix="bis" display_seconds=false}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="validity"}:</td>
				<td class="td2">
					<input type="checkbox" checked="checked" id="valid_signup" name="valid_signup" />
					<label for="valid_signup"><b>{lng p="signup"}</b></label><br />
					<input type="checkbox" checked="checked" id="valid_loggedin" name="valid_loggedin" />
					<label for="valid_loggedin"><b>{lng p="li"}</b></label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="benefit"}:</td>
				<td class="td2">
					<div>
						<input type="checkbox" name="ver_gruppe" id="ver_gruppe" />
						<label for="ver_gruppe"><b>{lng p="movetogroup"}:</b></label>
						<select name="ver_gruppe_id">
						{foreach from=$groups item=group}
							<option value="{$group.id}">{text value=$group.title}</option>
						{/foreach}
						</select>
					</div>
					
					<div>
						<input type="checkbox" name="ver_credits" id="ver_credits" />
						<label for="ver_credits"><b>{lng p="addcredits"}:</b></label>
						<input type="text" name="ver_credits_count" value="5" size="6" />
					</div>
				</td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>