<form method="post" action="users.php?action=create&create=true&sid={$sid}" onsubmit="spin(this)">
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="profile"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="email"}</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" name="email" value="{if isset($user.email)}{text value=$user.email allowEmpty=true}{/if}" placeholder="{lng p="email"}">
					</div>
					<div class="col-sm-3">
						<select name="emailDomain" class="form-select">
							{foreach from=$domainList item=domain}
								<option value="{$domain}">@{domain value=$domain}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="salutation"}</label>
					<div class="col-sm-8">
						<select name="anrede" class="form-select">
							<option value="">&nbsp;</option>
							<option value="herr">{lng p="mr"}</option>
							<option value="frau">{lng p="mrs"}</option>
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="firstname"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="vorname" value="{if isset($user.vorname)}{text value=$user.vorname allowEmpty=true}{/if}" placeholder="{lng p="firstname"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="lastname"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="nachname" value="{if isset($user.nachname)}{text value=$user.nachname allowEmpty=true}{/if}" placeholder="{lng p="lastname"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="company"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="company" value="{if isset($user.company)}{text value=$user.company allowEmpty=true}{/if}" placeholder="{lng p="company"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="streetno"}</label>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="strasse" value="{if isset($user.strasse)}{text value=$user.strasse allowEmpty=true}{/if}" placeholder="{lng p="street"}">
					</div>
					<div class="col-sm-2">
						<input type="text" class="form-control" name="hnr" value="{if isset($user.hnr)}{text value=$user.hnr allowEmpty=true}{/if}" placeholder="{lng p="no"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="zipcity"}</label>
					<div class="col-sm-2">
						<input type="text" class="form-control" name="plz" value="{if isset($user.plz)}{text value=$user.plz allowEmpty=true}{/if}" placeholder="{lng p="zip"}">
					</div>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="ort" value="{if isset($user.ort)}{text value=$user.ort allowEmpty=true}{/if}" placeholder="{lng p="city"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="country"}</label>
					<div class="col-sm-8">
						<select name="land" class="form-select">
							{foreach from=$countries item=countryName key=countryID}
								<option value="{$countryID}"{if isset($user.land) && $countryID==$user.land} selected="selected"{/if}>{text value=$countryName}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="tel"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="tel" value="{if isset($user.tel)}{text value=$user.tel allowEmpty=true}{/if}" placeholder="{lng p="tel"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="fax"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="fax" value="{if isset($user.fax)}{text value=$user.fax allowEmpty=true}{/if}" placeholder="{lng p="fax"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="cellphone"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="mail2sms_nummer" value="{if isset($user.mail2sms_nummer)}{text value=$user.mail2sms_nummer allowEmpty=true}{/if}" placeholder="{lng p="cellphone"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="altmail"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="altmail" value="{if isset($user.altmail)}{email value=$user.altmail}{/if}" placeholder="{lng p="altmail"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="taxid"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="taxid" value="{if isset($user.taxid)}{text value=$user.taxid}{/if}" placeholder="{lng p="taxid"}">
					</div>
				</div>
				{foreach from=$profileFields item=profileField}
					{assign var=fieldID value=$profileField.id}
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{$profileField.title}</label>
						<div class="col-sm-8">
							{if $profileField.type==1}
								<input type="text" class="form-control" name="field_{$profileField.id}" value="{if isset($profileField.value)}{text value=$profileField.value allowEmpty=true}{/if}" style="width:85%;" />
							{elseif $profileField.type==2}
								<div class="form-check">
									<input type="checkbox" class="form-check-input" name="field_{$profileField.id}"{if $profileField.value} checked="checked"{/if} />
								</div>
							{elseif $profileField.type==4}
								<select name="field_{$profileField.id}" class="form-select">
									{foreach from=$profileField.extra item=item}
										<option value="{text value=$item allowEmpty=true}"{if $profileField.value==$item} selected="selected"{/if}>{text value=$item allowEmpty=true}</option>
									{/foreach}
								</select>
							{elseif $profileField.type==8}
								{foreach from=$profileField.extra item=item}
									<div class="form-check">
										<input type="radio" class="form-check-input" id="field_{$profileField.id}_{$item}" name="field_{$profileField.id}" value="{if isset($item)}{text value=$item allowEmpty=true}{/if}"{if $profileField.value==$item} checked="checked"{/if} />
										<span class="form-check-label">{$item}</span> &nbsp;
									</div>
								{/foreach}
							{elseif $profileField.type==32}
								{if $profileField.value}
									{html_select_date time=$profileField.value year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix="field_$fieldID" field_order="DMY"}
								{else}
									{html_select_date time="---" year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix="field_$fieldID" field_order="DMY"}
								{/if}
							{/if}
						</div>
					</div>
				{/foreach}
				{if $historyCount}
					<div class="alert alert-warning">
						{$historyCount} {lng p="oldcontacts"}
						<a href="users.php?do=contactHistory&id={$user.id}&sid={$sid}">{lng p="show"} &raquo;</a>
					</div>
				{/if}
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="common"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="group"}</label>
					<div class="col-sm-8">
						<select name="gruppe" class="form-select">
							{foreach from=$groups item=groupItem}
								<option value="{$groupItem.id}"{if isset($group.id) && $groupItem.id==$group.id} selected="selected"{/if}>{text value=$groupItem.title}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="status"}</label>
					<div class="col-sm-8">
						<select name="gesperrt" class="form-select">
							<option value="no"{if isset($user.gesperrt) && $user.gesperrt=='no'} selected="selected"{/if}>{lng p="active"}</option>
							<option value="yes"{if isset($user.gesperrt) && $user.gesperrt=='yes'} selected="selected"{/if}>{lng p="locked"}</option>
							<option value="locked"{if isset($user.gesperrt) && $user.gesperrt=='locked'} selected="selected"{/if}>{lng p="notactivated"}</option>
							<option value="delete"{if isset($user.gesperrt) && $user.gesperrt=='delete'} selected="selected"{/if}>{lng p="deleted"}</option>
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="newpassword"}</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" name="passwort" value="" placeholder="{lng p="newpassword"}">
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend>{lng p="notes"}</legend>

				<textarea class="form-control" style="min-height: 150px;" name="notes">{if isset($user.notes)}{text value=$user.notes allowEmpty=true}{/if}</textarea>
			</fieldset>
		</div>
	</div>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="create"}" />
	</div>
</form>