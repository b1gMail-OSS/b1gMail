<div id="contentHeader">
	<div class="left">
		<i class="fa fa-address-book-o" aria-hidden="true"></i>
		{if $contact}{lng p="editcontact"}{else}{lng p="addcontact"}{/if}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form name="f1" method="post" action="organizer.addressbook.php?action={if $contact}saveContact&id={$contact.id}{else}createContact{/if}&sid={$sid}" onsubmit="return(checkContactForm(this));">
	<input type="hidden" id="submitAction" name="submitAction" value="" />
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="3"> {if $contact}{lng p="editcontact"}{else}{lng p="addcontact"}{/if}</th>
		</tr>
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-address-card-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="common"}</td>
			
			<td class="listTableRightest" rowspan="26" width="180">				
				<fieldset>
					<legend>{lng p="userpicture"}</legend>
					<input type="hidden" name="pictureFile" id="pictureFile" value="" />
					<input type="hidden" name="pictureMime" id="pictureMime" value="" />
					<br /><center><div id="pictureDiv" style="background-size: cover; background-position: center center; background-repeat: no-repeat; background-image: url({if !$contact || $contact.picture==''}{$tpldir}images/li/no_picture.png{else}organizer.addressbook.php?action=addressbookPicture&id={$contact.id}&sid={$sid}{/if}); width: 80px; height: 80px;"><a href="javascript:addrUserPicture({if $contact}{$contact.id}{else}-1{/if});"><img src="{$tpldir}images/li/pic_frame.gif" width="80" height="80" border="0" alt="" /></a></div></center>
					<br /><small>{lng p="changepicbyclick"}</small>
				</fieldset>
				<small><br /></small>				
				<fieldset>
					<legend>{lng p="groupmember"}</legend>
					<div align="left">
						{if !$groups}<small>{lng p="nogroups"}</small>{else}
						{foreach from=$groups item=group key=groupID}
							<input type="checkbox" id="group_{$groupID}" name="group_{$groupID}"{if $group.member} checked="checked"{/if} />
							<label for="group_{$groupID}">{text value=$group.title cut=18}</label><br />
						{/foreach}
						{/if}

						<input type="checkbox" id="group_new" name="group_new" />
						<input type="text" name="group_new_name" placeholder="{lng p="newgroup"}" value="" class="smallInput" style="width:120px;" onchange="this.onkeypress();" onkeypress="EBID('group_new').checked = this.value.length > 0;" /><br />
					</div>
				</fieldset>
				<small><br /></small>				
				<fieldset>
					<legend>{lng p="features"}</legend>
					<div align="left">
						{if $contact}
							<a href="javascript:addrFunction('exportVCF');"><i class="fa fa-address-card-o" aria-hidden="true"></i> {lng p="exportvcf"}</a><br />
							<a href="javascript:addrFunction('selfComplete');"><i class="fa fa-check-square-o" aria-hidden="true"></i> {lng p="complete"}</a><br />
							<a href="javascript:addrFunction('intelliFolder');"><i class="fa fa-folder" aria-hidden="true"></i> {lng p="convfolder"}</a><br />
							<a href="javascript:addrFunction('sendMail');"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> {lng p="sendmail"}</a><br />
						{else}
							<a href="javascript:addrImportVCF();"><i class="fa fa-upload" aria-hidden="true"></i> {lng p="importvcf"}</a><br />
							<a href="javascript:addrFunction('selfComplete');"><i class="fa fa-check-square-o" aria-hidden="true"></i> {lng p="complete"}</a><br />
						{/if}
					</div>
				</fieldset>
			</td>	
		</tr>
		<tr>
			<td class="listTableLeft"><label for="anrede">{lng p="salutation"}:</label></td>
			<td class="listTableRight">
				<select name="anrede" id="anrede">
					<option value=""{if $contact.anrede==''} selected="selected"{/if}>&nbsp;</option>
					<option value="frau"{if $contact.anrede=='frau'} selected="selected"{/if}>{lng p="mrs"}</option>
					<option value="herr"{if $contact.anrede=='herr'} selected="selected"{/if}>{lng p="mr"}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"> * <label for="vorname">{lng p="firstname"}</label> / * <label for="nachname">{lng p="surname"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="vorname" id="vorname" value="{text value=$contact.vorname allowEmpty=true}" size="20" />
				<input type="text" name="nachname" id="nachname" value="{text value=$contact.nachname allowEmpty=true}" size="20" />
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-user-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td>{lng p="priv"}</td>
						<td align="right">
							<label for="default_priv">{lng p="default"}</label>
							<input type="radio" name="default" id="default_priv" value="priv"{if $contact.default_address!=2} checked="checked"{/if} />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="strassenr">{lng p="streetnr"}</label>:</td>
			<td class="listTableRight">
				<input type="text" name="strassenr" id="strassenr" value="{text value=$contact.strassenr allowEmpty=true}" size="30" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="plz">{lng p="zipcity"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="plz" id="plz" value="{text value=$contact.plz allowEmpty=true}" size="6" />
				<input type="text" name="ort" id="ort" value="{text value=$contact.ort allowEmpty=true}" size="20" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="land">{lng p="country"}:</label></td>
			<td class="listTableRight">	
				<input type="text" name="land" id="land" value="{text value=$contact.land allowEmpty=true}" size="30" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="email">{lng p="email"}:</label></td>
			<td class="listTableRight">	
				<input type="text" name="email" id="email" value="{if $smarty.request.email}{text value=$smarty.request.email}{else}{text value=$contact.email allowEmpty=true}{/if}" size="30" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="tel">{lng p="phone"}:</label></td>
			<td class="listTableRight">	
				<input type="text" name="tel" id="tel" value="{text value=$contact.tel allowEmpty=true}" size="30" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="fax">{lng p="fax"}:</label></td>
			<td class="listTableRight">	
				<input type="text" name="fax" id="fax" value="{text value=$contact.fax allowEmpty=true}" size="30" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="handy">{lng p="mobile"}:</label></td>
			<td class="listTableRight">	
				<input type="text" name="handy" id="handy" value="{text value=$contact.handy allowEmpty=true}" size="30" />
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-building-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td>{lng p="work"}</td>
						<td align="right">
							<label for="default_work">{lng p="default"}</label>
							<input type="radio" name="default" id="default_work" value="work"{if $contact.default_address==2} checked="checked"{/if} />
						</td>
					</tr>
				</table></td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="work_strassenr">{lng p="streetnr"}</label>:</td>
			<td class="listTableRight">
				<input type="text" name="work_strassenr" id="work_strassenr" value="{text value=$contact.work_strassenr allowEmpty=true}" size="30" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="work_plz">{lng p="zipcity"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="work_plz" id="work_plz" value="{text value=$contact.work_plz allowEmpty=true}" size="6" />
				<input type="text" name="work_ort" id="work_ort" value="{text value=$contact.work_ort allowEmpty=true}" size="20" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="work_land">{lng p="country"}:</label></td>
			<td class="listTableRight">	
				<input type="text" name="work_land" id="work_land" value="{text value=$contact.work_land allowEmpty=true}" size="30" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="work_email">{lng p="email"}:</label></td>
			<td class="listTableRight">	
				<input type="text" name="work_email" id="work_email" value="{text value=$contact.work_email allowEmpty=true}" size="30" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="tel">{lng p="phone"}:</label></td>
			<td class="listTableRight">	
				<input type="text" name="work_tel" id="work_tel" value="{text value=$contact.work_tel allowEmpty=true}" size="30" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="fax">{lng p="fax"}:</label></td>
			<td class="listTableRight">	
				<input type="text" name="work_fax" id="work_fax" value="{text value=$contact.work_fax allowEmpty=true}" size="30" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="work_handy">{lng p="mobile"}:</label></td>
			<td class="listTableRight">	
				<input type="text" name="work_handy" id="work_handy" value="{text value=$contact.work_handy allowEmpty=true}" size="30" />
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-microchip" aria-hidden="true"></i></td>
			<td class="listTableRightDesc">{lng p="misc"}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="firma">{lng p="company"}:</label></td>
			<td class="listTableRight"><input type="text" name="firma" id="firma" value="{text value=$contact.firma allowEmpty=true}" size="30" /></td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="position">{lng p="position"}</label>:</td>
			<td class="listTableRight">
				<input type="text" name="position" id="position" value="{text value=$contact.position allowEmpty=true}" size="30" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="web">{lng p="web"}:</label></td>
			<td class="listTableRight">	
				<input type="text" name="web" id="web" value="{text value=$contact.web allowEmpty=true}" size="30" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="birthday"}:</td>
			<td class="listTableRight">	
				{if $contact.geburtsdatum}
				{html_select_date time=$contact.geburtsdatum year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix="geburtsdatum_" field_order="DMY"}
				{else}
				{html_select_date time="---" year_empty="---" day_empty="---" month_empty="---" start_year="-120" end_year="+0" prefix="geburtsdatum_" field_order="DMY"}
				{/if}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="kommentar">{lng p="comment"}:</label></td>
			<td class="listTableRight">	
				<textarea class="textInput" name="kommentar" id="kommentar">{text value=$contact.kommentar allowEmpty=true}</textarea>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr> 
	</table>
</form>

{$jsCode}

</div></div>
