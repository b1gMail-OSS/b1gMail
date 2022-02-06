<div id="contentHeader">
	<div class="left">
		<i class="fa fa-user-o" aria-hidden="true"></i>
		{lng p="contact"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

{if $errorStep}
<div class="note">
	{$errorInfo}
</div>
<br />
{/if}

<form name="f1" method="post" action="prefs.php?action=contact&do=save&sid={$sid}">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="contact"}</th>
		</tr>
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-address-card-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="common"}</td>
		</tr>
		{if $f_anrede!="n"}<tr>
			<td class="listTableLeft">{if $f_anrede=="p"}*{/if} <label for="salutation">{lng p="salutation"}</label>:</td>
			<td class="listTableRight">
				<select name="salutation" id="salutation">
					<option value="">&nbsp;</option>
					<option value="herr"{if $anrede=='herr'} selected="selected"{/if}>{lng p="mr"}</option>
					<option value="frau"{if $anrede=='frau'} selected="selected"{/if}>{lng p="mrs"}</option>
				</select>
			</td>
		</tr>{/if}
		<tr>
			<td class="listTableLeft">* <label for="vorname">{lng p="firstname"}</label>:</td>
			<td class="listTableRight">
				<input type="text" name="vorname" id="vorname" value="{text value=$vorname allowEmpty=true}" size="35" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="nachname">{lng p="surname"}</label>:</td>
			<td class="listTableRight">
				<input type="text" name="nachname" id="nachname" value="{text value=$nachname allowEmpty=true}" size="35" />
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-user-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="contact"}</td>
		</tr>
		{if $f_strasse!="n"}<tr>
			<td class="listTableLeft">{if $f_strasse=="p"}*{/if} <label for="strasse">{lng p="streetnr"}</label>:</td>
			<td class="listTableRight">
				<input type="text" name="strasse" id="strasse" value="{text value=$strasse allowEmpty=true}" size="35" />
				<input type="text" name="hnr" id="hnr" value="{text value=$hnr allowEmpty=true}" size="6" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">{if $f_strasse=="p"}*{/if} <label for="plz">{lng p="zipcity"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="plz" id="plz" value="{text value=$plz allowEmpty=true}" size="6" />
				<input type="text" name="ort" id="ort" value="{text value=$ort allowEmpty=true}" size="35" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">{if $f_strasse=="p"}*{/if} <label for="land">{lng p="country"}:</label></td>
			<td class="listTableRight">
				<select name="land" id="land">
					{foreach from=$countryList item=country key=id}
					<option value="{$id}"{if $land==$id} selected="selected"{/if}>{$country}</option>
					{/foreach}
				</select>
			</td>
		</tr>{/if}
		{if $f_telefon!="n"}<tr>
			<td class="listTableLeft">{if $f_telefon=="p"}*{/if} <label for="tel">{lng p="phone"}:</label></td>
			<td class="listTableRight">	
				<input type="text" name="tel" id="tel" value="{text value=$tel allowEmpty=true}" size="35" />
			</td>
		</tr>{/if}
		{if $f_fax!="n"}<tr>
			<td class="listTableLeft">{if $f_fax=="p"}*{/if} <label for="fax">{lng p="fax"}:</label></td>
			<td class="listTableRight">	
				<input type="text" name="fax" id="fax" value="{text value=$fax allowEmpty=true}" size="35" />
			</td>
		</tr>{/if}
		{if $f_mail2sms_nummer!="n"}<tr>
			<td class="listTableLeft">{if $f_mail2sms_nummer=="p"}*{/if} {lng p="mobile"}:</td>
			<td class="listTableRight">	
				{mobileNr name="mail2sms_nummer" value=$mail2sms_nummer size="280px"}
			</td>
		</tr>{/if}
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-microchip" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="misc"}</td>
		</tr>
		{if $f_alternativ!="n"}<tr>
			<td class="listTableLeft">{if $f_alternativ=="p"}*{/if} <label for="altmail">{lng p="altmail2"}:</label></td>
			<td class="listTableRight">	
				<input type="text" name="altmail" id="altmail" value="{text value=$altmail allowEmpty=true}" size="35" />
			</td>
		</tr>{/if}
		{foreach from=$profileFields item=profileField}
		{assign var=fieldID value=$profileField.id}
		<tr>
			<td class="listTableLeft">{if $profileField.needed}*{/if} <label for="field_{$profileField.id}">{$profileField.title}</label>:</td>
			<td class="listTableRight">
				{if $profileField.type==1}
					<input type="text" id="field_{$profileField.id}" name="field_{$profileField.id}" value="{text value=$profileField.value allowEmpty=true}" size="35" />
				{elseif $profileField.type==2}
					<input type="checkbox" id="field_{$profileField.id}" name="field_{$profileField.id}"{if $profileField.value} checked="checked"{/if} />
				{elseif $profileField.type==4}
					<select name="field_{$profileField.id}" id="field_{$profileField.id}">
					{foreach from=$profileField.extra item=item}
						<option value="{text value=$item allowEmpty=true}"{if $profileField.value==$item} selected="selected"{/if}>{text value=$item allowEmpty=true}</option>
					{/foreach}
					</select>
				{elseif $profileField.type==8}
					{foreach from=$profileField.extra item=item}
						<input type="radio" id="field_{$profileField.id}_{$item}" name="field_{$profileField.id}" value="{text value=$item allowEmpty=true}"{if $profileField.value==$item} checked="checked"{/if} />
						<label for="field_{$profileField.id}_{$item}"><b>{$item}</b></label> &nbsp;
					{/foreach}
				{elseif $profileField.type==32}
					{if $profileField.value}
					{html_select_date time=$profileField.value year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix="field_$fieldID" field_order="DMY"}
					{else}
					{html_select_date time="---" year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix="field_$fieldID" field_order="DMY"}
					{/if}
				{/if}
			</td>
		</tr>
		{/foreach}

		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>

{if $errorStep}
<script>
<!--
{foreach from=$invalidFields item=field}
	markFieldAsInvalid('{$field}');
{/foreach}
//-->
</script>
{/if}
