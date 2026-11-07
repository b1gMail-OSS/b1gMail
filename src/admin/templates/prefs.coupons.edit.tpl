<fieldset>
	<legend>{lng p="edit"}</legend>

	<form action="{sessionurl file='prefs.coupons.php' params="do=edit&save=true&id={$coupon.id}"}" method="post" onsubmit="spin(this)">
		{csrffield}
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="code"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="code" value="{if isset($coupon.code)}{text value=$coupon.code}{/if}" placeholder="{lng p="code"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="count"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
                        <span class="input-group-text">
							<input class="form-check-input m-0" type="checkbox" onchange="EBID('count').value=this.checked?'-1':'0';"{if $coupon.anzahl==-1} checked="checked"{/if} id="count_unlim" />
                        </span>
					<span class="input-group-text">{lng p="unlimited"} {lng p="or"}</span>
					<input type="text" class="form-control" name="anzahl" id="count" value="{if isset($coupon.anzahl)}{text value=$coupon.anzahl}{/if}" onkeyup="EBID('count_unlim').checked=this.value=='-1';" />
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="from"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
                        <span class="input-group-text">
							<input class="form-check-input m-0" type="checkbox" id="from_unlim" name="von_unlim"{if $coupon.von==-1} checked="checked"{/if} />
                        </span>
					<span class="input-group-text">{lng p="now"} {lng p="or"}</span>
					<input type="text" class="form-control" id="coupon-von" name="von" data-bs-toggle="datepicker" data-bs-position-element="#coupon-von" data-bm-datetime="1" data-bs-date-format="DD.MM.YYYY" data-bs-locale="de" autocomplete="off" value="{if $coupon.von!=-1}{date timestamp=$coupon.von format="d.m.Y H:i"}{else}{date timestamp=$smarty.now format="d.m.Y H:i"}{/if}" placeholder="TT.MM.JJJJ HH:MM">
					<label class="input-group-text mb-0" for="coupon-von"><i class="icon ti ti-calendar-time"></i></label>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="to"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
                        <span class="input-group-text">
							<input class="form-check-input m-0" type="checkbox" id="to_unlim" name="bis_unlim"{if $coupon.bis==-1} checked="checked"{/if} />
                        </span>
					<span class="input-group-text">{lng p="now"} {lng p="or"}</span>
					<input type="text" class="form-control" id="coupon-bis" name="bis" data-bs-toggle="datepicker" data-bs-position-element="#coupon-bis" data-bm-datetime="1" data-bs-date-format="DD.MM.YYYY" data-bs-locale="de" autocomplete="off" value="{if $coupon.bis!=-1}{date timestamp=$coupon.bis format="d.m.Y H:i"}{else}{date timestamp=$smarty.now format="d.m.Y H:i"}{/if}" placeholder="TT.MM.JJJJ HH:MM">
					<label class="input-group-text mb-0" for="coupon-bis"><i class="icon ti ti-calendar-time"></i></label>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="validity"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="valid_signup"{if $coupon.valid_signup=='yes'} checked="checked"{/if}>
					<span class="form-check-label">{lng p="signup"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="valid_loggedin"{if $coupon.valid_loggedin=='yes'} checked="checked"{/if}>
					<span class="form-check-label">{lng p="li"}</span>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="benefit"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
                        <span class="input-group-text">
							<input class="form-check-input m-0" type="checkbox" name="ver_gruppe"{if $coupon.ver.gruppe} checked="checked"{/if} />
                        </span>
					<span class="input-group-text">{lng p="movetogroup"}</span>
					<select name="ver_gruppe_id" class="form-select">
						{foreach from=$groups item=group}
							<option value="{$group.id}"{if $group.id==$coupon.ver.gruppe} selected="selected"{/if}>{text value=$group.title}</option>
						{/foreach}
					</select>
				</div>
				<div class="input-group mb-2">
					<span class="input-group-text">
							<input class="form-check-input m-0" type="checkbox" name="ver_credits"{if $coupon.ver.sms} checked="checked"{/if} />
                        </span>
					<span class="input-group-text">{lng p="addcredits"}</span>
					<input class="form-control" type="text" name="ver_credits_count" value="{if $coupon.ver.sms}{text value=$coupon.ver.sms}{else}5{/if}" />
				</div>
				<div class="input-group mb-2">
					<span class="input-group-text">
							<input class="form-check-input m-0" type="checkbox" name="ver_pacc"{if $coupon.ver.pacc_type} checked="checked"{/if} />
                        </span>
					<span class="input-group-text">{lng p="pacc_discount"}</span>
					<select name="ver_pacc_type" class="form-select">
						<option value="fixed"{if !$coupon.ver.pacc_type||$coupon.ver.pacc_type=='fixed'} selected="selected"{/if}>{lng p="pacc_discount_fixed"}</option>
						<option value="percent"{if $coupon.ver.pacc_type=='percent'} selected="selected"{/if}>{lng p="pacc_discount_percent"}</option>
					</select>
					<input class="form-control" type="text" name="ver_pacc_value" value="{if $coupon.pacc_value_form}{text value=$coupon.pacc_value_form}{else}10{/if}" />
				</div>
				<small class="text-secondary">{lng p="pacc_discount_hint"}</small>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="redeemedby"}</legend>

	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter table-striped">
				<thead>
				<tr>
					<th style="width: 50px;">{lng p="id"}</td>
					<th style="width: 20%;">{lng p="email"}</td>
					<th>{lng p="name"}</td>
				</tr>
				</thead>
				<tbody>
				{foreach from=$usedBy item=user}
					{cycle name=class values="td1,td2" assign=class}
					<tr class="{$class}">
						<td>{$user.id}</td>
						<td><a href="{sessionurl file='users.php' params="do=edit&id={$user.id}"}">{$user.email}</a><br /><small>{text value=$user.aliases cut=45 allowEmpty=true}</small></td>
						<td>{text value=$user.nachname cut=20}, {text value=$user.vorname cut=20}<br /><small>{text value=$user.strasse cut=20} {text value=$user.hnr cut=5}, {text value=$user.plz cut=8} {text value=$user.ort cut=20}</small></td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</fieldset>