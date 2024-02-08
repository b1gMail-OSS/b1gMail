<fieldset>
	<legend>{lng p="coupons"}</legend>

	<form action="prefs.coupons.php?sid={$sid}" method="post" name="f1" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 20px; text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'coupon_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="code"}</th>
						<th style="width: 200px;">{lng p="validitytime"}</th>
						<th style="width: 200px;">{lng p="validity"}</th>
						<th style="width: 160px;">{lng p="used3"} / {lng p="count"}</th>
						<th style="width: 60px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$coupons item=coupon}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center"><input type="checkbox" name="coupon_{$coupon.id}" /></td>
							<td>{text value=$coupon.code}<br /><small>{if $coupon.ver.sms}{lng p="addcredits"}: {$coupon.ver.sms}{/if}{if $coupon.ver.gruppe}{if $coupon.ver.sms}, {/if}{lng p="movetogroup"}: {$groups[$coupon.ver.gruppe].title}{/if}</small></td>
							<td>{lng p="to"} {if $coupon.bis==-1}({lng p="unlimited"}){else}{date timestamp=$coupon.bis}{/if}<br /><small>{lng p="from"} {if $coupon.von==-1}({lng p="unlimited"}){else}{date timestamp=$coupon.von}{/if}</small></td>
							<td>
								{if $coupon.valid_signup}{lng p="signup"}{if $coupon.valid_loggedin},{/if}{/if}
								{if $coupon.valid_loggedin}{lng p="li"}{/if}
							</td>
							<td>{$coupon.used} / {if $coupon.anzahl==-1}({lng p="unlimited"}){else}{$coupon.anzahl}{/if}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="prefs.coupons.php?do=edit&id={$coupon.id}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="prefs.coupons.php?delete={$coupon.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer">
				<div style="float: left;">{lng p="action"}:&nbsp;</div>
				<div style="float: left;">
					<div class="btn-group btn-group-sm">
						<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="delete">{lng p="delete"}</option>
							</optgroup>
						</select>
						<input type="submit" name="executeMassAction" value="{lng p="execute"}" class="btn btn-sm btn-dark-lt" />
					</div>
				</div>
			</div>
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="addcoupon"}</legend>

	<form action="prefs.coupons.php?add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="codes"}</label>
			<div class="col-sm-10">
				<textarea class="form-control" name="code" id="codes" placeholder="{lng p="codes"}"></textarea>
				<div class="text-end">[ <a href="javascript:void(0);" onclick="EBID('generator').style.display=EBID('generator').style.display==''?'none':'';">{lng p="generate"}</a> ]</div>
			</div>
		</div>
		<div id="generator" style="display:none;">
			<fieldset>
				<legend>{lng p="generate"}</legend>

				<div class="row">
					<div class="col-md-6">
						<div class="mb-3 row">
							<label class="col-sm-4 col-form-label">{lng p="count"}</label>
							<div class="col-sm-8">
								<input type="number" class="form-control" id="generator_count" value="10" placeholder="{lng p="count"}">
							</div>
						</div>
						<div class="mb-3 row">
							<label class="col-sm-4 col-form-label">{lng p="length"}</label>
							<div class="col-sm-8">
								<input type="number" class="form-control" id="generator_length" value="10" placeholder="{lng p="length"}">
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3 row">
							<label class="col-sm-4 col-form-check-label">{lng p="chars"}</label>
							<div class="col-sm-8">
								<label class="form-check">
									<input class="form-check-input" type="checkbox" id="generator_az" checked="checked">
									<span class="form-check-label">a-z</span>
								</label>
								<label class="form-check">
									<input class="form-check-input" type="checkbox" id="generator_az2" checked="checked">
									<span class="form-check-label">A-Z</span>
								</label>
								<label class="form-check">
									<input class="form-check-input" type="checkbox" id="generator_09" checked="checked">
									<span class="form-check-label">0-9</span>
								</label>
								<label class="form-check">
									<input class="form-check-input" type="checkbox" id="generator_special">
									<span class="form-check-label">.,_-&amp;$</span>
								</label>
							</div>
						</div>
						<div class="text-end">
							<input class="btn btn-primary" type="button" value="{lng p="generate"}" onclick="generateCodes(EBID('codes')); EBID('generator').style.display='none';" />
						</div>
					</div>
				</div>
			</fieldset>
		</div>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="count"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
                        <span class="input-group-text">
							<input class="form-check-input m-0" type="checkbox" onchange="EBID('count').value=this.checked?'-1':'0';" checked="checked" id="count_unlim" />
                        </span>
					<span class="input-group-text">{lng p="unlimited"} {lng p="or"}</span>
					<input type="text" class="form-control" name="anzahl" id="count" value="-1" onkeyup="EBID('count_unlim').checked=this.value=='-1';" />
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="from"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
                        <span class="input-group-text">
							<input class="form-check-input m-0" type="checkbox" id="from_unlim" name="von_unlim" checked="checked" />
                        </span>
					<span class="input-group-text">{lng p="now"} {lng p="or"}</span>
					{html_select_date prefix="von" start_year="-5" field_order="DMY" field_separator="."},
					{html_select_time prefix="von" display_seconds=false}
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="to"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
                        <span class="input-group-text">
							<input class="form-check-input m-0" type="checkbox" id="to_unlim" name="bis_unlim" checked="checked" />
                        </span>
					<span class="input-group-text">{lng p="now"} {lng p="or"}</span>
					{html_select_date prefix="bis" end_year="+5" field_order="DMY" field_separator="."},
					{html_select_time prefix="bis" display_seconds=false}
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="validity"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="valid_signup" checked="checked">
					<span class="form-check-label">{lng p="signup"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="valid_loggedin" checked="checked">
					<span class="form-check-label">{lng p="li"}</span>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="benefit"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
                        <span class="input-group-text">
							<input class="form-check-input m-0" type="checkbox" name="ver_gruppe" />
                        </span>
					<span class="input-group-text">{lng p="movetogroup"}</span>
					<select name="ver_gruppe_id" class="form-select">
						{foreach from=$groups item=group}
							<option value="{$group.id}">{text value=$group.title}</option>
						{/foreach}
					</select>
				</div>
				<div class="input-group mb-2">
					<span class="input-group-text">
							<input class="form-check-input m-0" type="checkbox" name="ver_credits" />
                        </span>
					<span class="input-group-text">{lng p="addcredits"}</span>
					<input class="form-control" type="text" name="ver_credits_count" value="5" />
				</div>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>