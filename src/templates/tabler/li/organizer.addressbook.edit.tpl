<div class="bm-organizer-page bm-organizer-form-page bm-organizer-contact-form">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-address-book icon icon-sm" aria-hidden="true"></i>
			{if isset($contact) && $contact}{lng p="editcontact"}{else}{lng p="addcontact"}{/if}
		</div>
		<div class="right bm-organizer-header-actions">
			<a href="{sessionurl file='organizer.addressbook.php'}" class="btn btn-sm btn-ghost-secondary">
				<i class="ti ti-arrow-left icon icon-sm me-1" aria-hidden="true"></i>{lng p="back"}
			</a>
		</div>
	</div>

	<form name="f1" method="post" class="bm-organizer-form" action="{if isset($contact) && $contact}{sessionurl file='organizer.addressbook.php' params="action=saveContact&id={$contact.id}"}{else}{sessionurl file='organizer.addressbook.php' params='action=createContact'}{/if}" onsubmit="return(checkContactForm(this));">
		{csrffield}
		<input type="hidden" id="submitAction" name="submitAction" value="" />

		<div class="bm-organizer-form-body">
			<div class="row g-4">
				<div class="col-lg-8">
					<div class="bm-organizer-form-section mb-4">
						<h4 class="bm-organizer-form-section-title">
							<i class="ti ti-id icon icon-sm me-1" aria-hidden="true"></i>{lng p="common"}
						</h4>
						<div class="row g-3">
							<div class="col-md-4">
								<label class="form-label" for="anrede">{lng p="salutation"}</label>
								<select class="form-select" name="anrede" id="anrede">
									<option value=""{if empty($contact.anrede)} selected="selected"{/if}>&nbsp;</option>
									<option value="frau"{if isset($contact) && $contact.anrede=='frau'} selected="selected"{/if}>{lng p="mrs"}</option>
									<option value="herr"{if isset($contact) && $contact.anrede=='herr'} selected="selected"{/if}>{lng p="mr"}</option>
								</select>
							</div>
							<div class="col-md-4">
								<label class="form-label required" for="vorname">{lng p="firstname"}</label>
								<input type="text" class="form-control" name="vorname" id="vorname" value="{if isset($contact.vorname)}{text value=$contact.vorname allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-4">
								<label class="form-label required" for="nachname">{lng p="surname"}</label>
								<input type="text" class="form-control" name="nachname" id="nachname" value="{if isset($contact.nachname)}{text value=$contact.nachname allowEmpty=true}{/if}" />
							</div>
						</div>
					</div>

					<div class="bm-organizer-form-section mb-4">
						<div class="bm-organizer-form-section-header">
							<h4 class="bm-organizer-form-section-title mb-0">
								<i class="ti ti-user icon icon-sm me-1" aria-hidden="true"></i>{lng p="priv"}
							</h4>
							<label class="form-check mb-0" for="default_priv">
								<input type="radio" class="form-check-input" name="default" id="default_priv" value="priv"{if !isset($contact) || $contact.default_address!=2} checked="checked"{/if} />
								<span class="form-check-label">{lng p="default"}</span>
							</label>
						</div>
						<div class="row g-3">
							<div class="col-12">
								<label class="form-label" for="strassenr">{lng p="streetnr"}</label>
								<input type="text" class="form-control" name="strassenr" id="strassenr" value="{if isset($contact.strassenr)}{text value=$contact.strassenr allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="plz">{lng p="zip"}</label>
								<input type="text" class="form-control" name="plz" id="plz" value="{if isset($contact.plz)}{text value=$contact.plz allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-8">
								<label class="form-label" for="ort">{lng p="city"}</label>
								<input type="text" class="form-control" name="ort" id="ort" value="{if isset($contact.ort)}{text value=$contact.ort allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-6">
								<label class="form-label" for="land">{lng p="country"}</label>
								<input type="text" class="form-control" name="land" id="land" value="{if isset($contact.land)}{text value=$contact.land allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-6">
								<label class="form-label" for="email">{lng p="email"}</label>
								<input type="email" class="form-control" name="email" id="email" value="{if !empty($smarty.request.email)}{text value=$smarty.request.email}{elseif isset($contact.email)}{text value=$contact.email allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="tel">{lng p="phone"}</label>
								<input type="tel" class="form-control" name="tel" id="tel" value="{if isset($contact.tel)}{text value=$contact.tel allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="fax">{lng p="fax"}</label>
								<input type="tel" class="form-control" name="fax" id="fax" value="{if isset($contact.fax)}{text value=$contact.fax allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="handy">{lng p="mobile"}</label>
								<input type="tel" class="form-control" name="handy" id="handy" value="{if isset($contact.handy)}{text value=$contact.handy allowEmpty=true}{/if}" />
							</div>
						</div>
					</div>

					<div class="bm-organizer-form-section mb-4">
						<div class="bm-organizer-form-section-header">
							<h4 class="bm-organizer-form-section-title mb-0">
								<i class="ti ti-building icon icon-sm me-1" aria-hidden="true"></i>{lng p="work"}
							</h4>
							<label class="form-check mb-0" for="default_work">
								<input type="radio" class="form-check-input" name="default" id="default_work" value="work"{if isset($contact.default_address) && $contact.default_address==2} checked="checked"{/if} />
								<span class="form-check-label">{lng p="default"}</span>
							</label>
						</div>
						<div class="row g-3">
							<div class="col-12">
								<label class="form-label" for="work_strassenr">{lng p="streetnr"}</label>
								<input type="text" class="form-control" name="work_strassenr" id="work_strassenr" value="{if isset($contact.work_strassenr)}{text value=$contact.work_strassenr allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="work_plz">{lng p="zip"}</label>
								<input type="text" class="form-control" name="work_plz" id="work_plz" value="{if isset($contact.work_plz)}{text value=$contact.work_plz allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-8">
								<label class="form-label" for="work_ort">{lng p="city"}</label>
								<input type="text" class="form-control" name="work_ort" id="work_ort" value="{if isset($contact.work_ort)}{text value=$contact.work_ort allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-6">
								<label class="form-label" for="work_land">{lng p="country"}</label>
								<input type="text" class="form-control" name="work_land" id="work_land" value="{if isset($contact.work_land)}{text value=$contact.work_land allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-6">
								<label class="form-label" for="work_email">{lng p="email"}</label>
								<input type="email" class="form-control" name="work_email" id="work_email" value="{if isset($contact.work_email)}{text value=$contact.work_email allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="work_tel">{lng p="phone"}</label>
								<input type="tel" class="form-control" name="work_tel" id="work_tel" value="{if isset($contact.work_tel)}{text value=$contact.work_tel allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="work_fax">{lng p="fax"}</label>
								<input type="tel" class="form-control" name="work_fax" id="work_fax" value="{if isset($contact.work_fax)}{text value=$contact.work_fax allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="work_handy">{lng p="mobile"}</label>
								<input type="tel" class="form-control" name="work_handy" id="work_handy" value="{if isset($contact.work_handy)}{text value=$contact.work_handy allowEmpty=true}{/if}" />
							</div>
						</div>
					</div>

					<div class="bm-organizer-form-section mb-4">
						<h4 class="bm-organizer-form-section-title">
							<i class="ti ti-dots icon icon-sm me-1" aria-hidden="true"></i>{lng p="misc"}
						</h4>
						<div class="row g-3">
							<div class="col-md-6">
								<label class="form-label" for="firma">{lng p="company"}</label>
								<input type="text" class="form-control" name="firma" id="firma" value="{if isset($contact.firma)}{text value=$contact.firma allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-6">
								<label class="form-label" for="position">{lng p="position"}</label>
								<input type="text" class="form-control" name="position" id="position" value="{if isset($contact.position)}{text value=$contact.position allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-6">
								<label class="form-label" for="web">{lng p="web"}</label>
								<input type="url" class="form-control" name="web" id="web" value="{if isset($contact.web)}{text value=$contact.web allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-6">
								<label class="form-label" for="geburtsdatum_date">{lng p="birthday"}</label>
								<input type="date" class="form-control" id="geburtsdatum_date"
									value="{if !empty($contact.geburtsdatum)}{$contact.geburtsdatum|date_format:"%Y-%m-%d"}{/if}"
									max="{$smarty.now|date_format:"%Y-%m-%d"}"
									onchange="syncSmartyDate('geburtsdatum', 'geburtsdatum_');" />
								<input type="hidden" id="geburtsdatum_Day"   name="geburtsdatum_Day"   value="{if !empty($contact.geburtsdatum)}{$contact.geburtsdatum|date_format:"%d"}{else}0{/if}" />
								<input type="hidden" id="geburtsdatum_Month" name="geburtsdatum_Month" value="{if !empty($contact.geburtsdatum)}{$contact.geburtsdatum|date_format:"%m"}{else}0{/if}" />
								<input type="hidden" id="geburtsdatum_Year"  name="geburtsdatum_Year"  value="{if !empty($contact.geburtsdatum)}{$contact.geburtsdatum|date_format:"%Y"}{else}0{/if}" />
							</div>
							<div class="col-12">
								<label class="form-label" for="kommentar">{lng p="comment"}</label>
								<textarea class="form-control" name="kommentar" id="kommentar" rows="4">{if isset($contact.kommentar)}{text value=$contact.kommentar allowEmpty=true}{/if}</textarea>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="card bm-organizer-form-card mb-4">
						<div class="card-header">
							<h3 class="card-title">{lng p="userpicture"}</h3>
						</div>
						<div class="card-body text-center">
							<input type="hidden" name="pictureFile" id="pictureFile" value="" />
							<input type="hidden" name="pictureMime" id="pictureMime" value="" />
							<a href="javascript:addrUserPicture({if isset($contact) && $contact}{$contact.id}{else}-1{/if});" class="d-inline-block mb-2">
								{if !isset($contact) || !$contact || $contact.picture==''}
								<span class="avatar avatar-xl bm-organizer-contact-avatar bm-organizer-contact-avatar--empty" id="pictureDiv"><i class="ti ti-user" aria-hidden="true"></i></span>
								{else}
								<span class="avatar avatar-xl bm-organizer-contact-avatar" id="pictureDiv" style="background-image: url('{sessionurl file='organizer.addressbook.php' params="action=addressbookPicture&id={$contact.id}"}');"></span>
								{/if}
							</a>
							<div class="text-secondary small">{lng p="changepicbyclick"}</div>
						</div>
					</div>

					<div class="card bm-organizer-form-card mb-4">
						<div class="card-header">
							<h3 class="card-title">{lng p="addressbook"}</h3>
						</div>
						<div class="card-body">
							<select class="form-select" name="addressbook" id="contactAddressbook" onchange="filterAddressbookGroups();">
							{if $writableSharedAddressbooksByOwner}
								<optgroup label="{lng p="myaddressbooks"}">
								{foreach from=$addressbooks key=bookID item=abook}
									<option value="{$bookID}"{if (isset($contact.addressbook_id) && $contact.addressbook_id==$bookID) || (!isset($contact) && $bookID==$currentAddressbookID)} selected="selected"{/if}>{text value=$abook.title}</option>
								{/foreach}
								</optgroup>
								{foreach from=$writableSharedAddressbooksByOwner key=ownerLabel item=ownerBooks}
								<optgroup label="{lng p="sharedaddressbooks"} — {text value=$ownerLabel}">
									{foreach from=$ownerBooks key=bookID item=abook}
									<option value="{$bookID}"{if (isset($contact.addressbook_id) && $contact.addressbook_id==$bookID) || (!isset($contact) && $bookID==$currentAddressbookID)} selected="selected"{/if}>{text value=$abook.title}</option>
									{/foreach}
								</optgroup>
								{/foreach}
							{else}
								{foreach from=$writableAddressbooks key=bookID item=abook}
								<option value="{$bookID}"{if (isset($contact.addressbook_id) && $contact.addressbook_id==$bookID) || (!isset($contact) && $bookID==$currentAddressbookID)} selected="selected"{/if}>{text value=$abook.title}</option>
								{/foreach}
							{/if}
							</select>
						</div>
					</div>
					<div class="card bm-organizer-form-card mb-4">
						<div class="card-header">
							<h3 class="card-title">{lng p="groupmember"}</h3>
						</div>
						<div class="card-body">
							{if !$groups}
							<div class="text-secondary small">{lng p="nogroups"}</div>
							{else}
							<div class="d-flex flex-wrap gap-2 mb-3 bm-group-chips">
								{foreach from=$groups item=group key=groupID}
								<div class="abGroupRow bm-group-chip-wrap" data-addressbook="{$group.addressbook_id}">
									<input type="checkbox" class="btn-check" id="group_{$groupID}" name="group_{$groupID}"{if !empty($group.member)} checked="checked"{/if} autocomplete="off" />
									<label class="btn btn-sm bm-group-chip" for="group_{$groupID}">
										<i class="ti ti-users icon icon-sm" aria-hidden="true"></i>
										<span class="bm-group-chip-label">{text value=$group.title cut=32}</span>
										<i class="ti ti-check bm-group-chip-check icon icon-sm" aria-hidden="true"></i>
									</label>
								</div>
								{/foreach}
							</div>
							{/if}
							<div class="input-group bm-organizer-new-group">
								<span class="input-group-text">
									<i class="ti ti-plus" aria-hidden="true"></i>
								</span>
								<input type="text" class="form-control" id="group_new_name" name="group_new_name" placeholder="{lng p="newgroup"}" value="" oninput="EBID('group_new').checked = this.value.length > 0;" />
								<input type="checkbox" id="group_new" name="group_new" class="d-none" aria-hidden="true" tabindex="-1" />
							</div>
						</div>
					</div>

					<div class="card bm-organizer-form-card mb-4">
						<div class="card-header">
							<h3 class="card-title">{lng p="features"}</h3>
						</div>
						<div class="list-group list-group-flush bm-organizer-contact-features">
							{if isset($contact) && $contact}
							<a href="javascript:addrFunction('exportVCF');" class="list-group-item list-group-item-action">
								<i class="ti ti-address-book icon icon-sm me-2" aria-hidden="true"></i>{lng p="exportvcf"}
							</a>
							<a href="javascript:addrFunction('selfComplete');" class="list-group-item list-group-item-action">
								<i class="ti ti-checkbox icon icon-sm me-2" aria-hidden="true"></i>{lng p="complete"}
							</a>
							<a href="javascript:addrFunction('intelliFolder');" class="list-group-item list-group-item-action">
								<i class="ti ti-folder icon icon-sm me-2" aria-hidden="true"></i>{lng p="convfolder"}
							</a>
							<a href="javascript:addrFunction('sendMail');" class="list-group-item list-group-item-action">
								<i class="ti ti-mail icon icon-sm me-2" aria-hidden="true"></i>{lng p="sendmail"}
							</a>
							{else}
							<a href="javascript:addrImportVCF();" class="list-group-item list-group-item-action">
								<i class="ti ti-upload icon icon-sm me-2" aria-hidden="true"></i>{lng p="importvcf"}
							</a>
							<a href="javascript:addrFunction('selfComplete');" class="list-group-item list-group-item-action">
								<i class="ti ti-checkbox icon icon-sm me-2" aria-hidden="true"></i>{lng p="complete"}
							</a>
							{/if}
						</div>
					</div>
				</div>
			</div>

			<div class="bm-organizer-form-footer">
				<div class="btn-list">
					<button type="submit" class="btn btn-primary">{lng p="ok"}</button>
					<button type="reset" class="btn">{lng p="reset"}</button>
					<a href="{sessionurl file='organizer.addressbook.php'}" class="btn btn-ghost-secondary">{lng p="cancel"}</a>
				</div>
			</div>
		</div>
	</form>
</div>

{if !empty($jsCode)}{$jsCode}{/if}
<script>
<!--
	filterAddressbookGroups();
//-->
</script>
