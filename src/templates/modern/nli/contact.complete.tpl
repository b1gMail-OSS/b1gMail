<div class="container">
	<div class="page-header"><h1>{lng p="addrselfcomplete"}</h1></div>

	<p>
		{lng p="completeintro"}
	</p>

	<div class="row"><div class="col-md-8"><form action="index.php?action=completeAddressBookEntry&contact={$contact.id}&key={$contact.invitationCode}" method="post">
		<input type="hidden" name="do" value="save" />

		<div class="panel panel-default">
			<div class="panel-heading panel-title">
				<span class="glyphicon glyphicon-user"></span>
				{lng p="common"}
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label" for="anrede">
								{lng p="salutation"}
							</label>
							<select class="form-control" name="anrede" id="anrede">
								<option value=""{if $contact.anrede==''} selected="selected"{/if}>&nbsp;</option>
								<option value="frau"{if $contact.anrede=='frau'} selected="selected"{/if}>{lng p="mrs"}</option>
								<option value="herr"{if $contact.anrede=='herr'} selected="selected"{/if}>{lng p="mr"}</option>
							</select>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label" for="vorname">
								{lng p="firstname"}
							</label>
							<input type="text" class="form-control" id="vorname" name="vorname" value="{if isset($contact.vorname)}{text value=$contact.vorname allowEmpty=true}{/if}" disabled="disabled" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label" for="nachname">
								{lng p="surname"}
							</label>
							<input type="text" class="form-control" id="nachname" name="nachname" value="{if isset($contact.nachname)}{text value=$contact.nachname allowEmpty=true}{/if}" disabled="disabled" />
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading panel-title">
				<span class="glyphicon glyphicon-home"></span>
				{lng p="priv"}
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label" for="strassenr">
								{lng p="streetnr"}
							</label>
							<input type="text" class="form-control" id="strassenr" name="strassenr" value="{if isset($contact.strassenr)}{text value=$contact.strassenr allowEmpty=true}{/if}" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label" for="plz">
								{lng p="zip"}
							</label>
							<input type="text" class="form-control" id="plz" name="plz" value="{if isset($contact.plz)}{text value=$contact.plz allowEmpty=true}{/if}" />
						</div>
					</div>
					<div class="col-md-8">
						<div class="form-group">
							<label class="control-label" for="ort">
								{lng p="city"}
							</label>
							<input type="text" class="form-control" id="ort" name="ort" value="{if isset($contact.ort)}{text value=$contact.ort allowEmpty=true}{/if}" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label" for="land">
								{lng p="country"}
							</label>
							<input type="text" class="form-control" id="land" name="land" value="{if isset($contact.land)}{text value=$contact.land allowEmpty=true}{/if}" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label" for="email">
								{lng p="email"}
							</label>
							<input type="text" class="form-control" id="email" name="email" value="{if isset($contact.email)}{text value=$contact.email allowEmpty=true}{/if}" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label" for="tel">
								{lng p="phone"}
							</label>
							<input type="text" class="form-control" id="tel" name="tel" value="{if isset($contact.tel)}{text value=$contact.tel allowEmpty=true}{/if}" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label" for="fax">
								{lng p="fax"}
							</label>
							<input type="text" class="form-control" id="fax" name="fax" value="{if isset($contact.fax)}{text value=$contact.fax allowEmpty=true}{/if}" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label" for="handy">
								{lng p="mobile"}
							</label>
							<input type="text" class="form-control" id="handy" name="handy" value="{if isset($contact.handy)}{text value=$contact.handy allowEmpty=true}{/if}" />
						</div>
					</div>
					<div class="col-md-6">
						&nbsp;
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading panel-title">
				<span class="glyphicon glyphicon-briefcase"></span>
				{lng p="work"}
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label" for="work_strassenr">
								{lng p="streetnr"}
							</label>
							<input type="text" class="form-control" id="work_strassenr" name="work_strassenr" value="{if isset($contact.work_strassenr)}{text value=$contact.work_strassenr allowEmpty=true}{/if}" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label" for="work_plz">
								{lng p="zip"}
							</label>
							<input type="text" class="form-control" id="work_plz" name="work_plz" value="{if isset($contact.work_plz)}{text value=$contact.work_plz allowEmpty=true}{/if}" />
						</div>
					</div>
					<div class="col-md-8">
						<div class="form-group">
							<label class="control-label" for="work_ort">
								{lng p="city"}
							</label>
							<input type="text" class="form-control" id="work_ort" name="work_ort" value="{if isset($contact.work_ort)}{text value=$contact.work_ort allowEmpty=true}{/if}" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label" for="work_land">
								{lng p="country"}
							</label>
							<input type="text" class="form-control" id="work_land" name="work_land" value="{if isset($contact.work_land)}{text value=$contact.work_land allowEmpty=true}{/if}" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label" for="work_email">
								{lng p="email"}
							</label>
							<input type="text" class="form-control" id="work_email" name="work_email" value="{if isset($contact.work_email)}{text value=$contact.work_email allowEmpty=true}{/if}" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label" for="work_tel">
								{lng p="phone"}
							</label>
							<input type="text" class="form-control" id="work_tel" name="work_tel" value="{if isset($contact.work_tel)}{text value=$contact.work_tel allowEmpty=true}{/if}" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label" for="work_fax">
								{lng p="fax"}
							</label>
							<input type="text" class="form-control" id="work_fax" name="work_fax" value="{if isset($contact.work_fax)}{text value=$contact.work_fax allowEmpty=true}{/if}" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label" for="work_handy">
								{lng p="mobile"}
							</label>
							<input type="text" class="form-control" id="work_handy" name="work_handy" value="{if isset($contact.work_handy)}{text value=$contact.work_handy allowEmpty=true}{/if}" />
						</div>
					</div>
					<div class="col-md-6">
						&nbsp;
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading panel-title">
				<span class="glyphicon glyphicon-list-alt"></span>
				{lng p="misc"}
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label" for="firma">
								{lng p="company"}
							</label>
							<input type="text" class="form-control" id="firma" name="firma" value="{if isset($contact.firma)}{text value=$contact.firma allowEmpty=true}{/if}" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label" for="position">
								{lng p="position"}
							</label>
							<input type="text" class="form-control" id="position" name="position" value="{if isset($contact.position)}{text value=$contact.position allowEmpty=true}{/if}" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label" for="web">
								{lng p="web"}
							</label>
							<input type="text" class="form-control" id="web" name="web" value="{if isset($contact.web)}{text value=$contact.web allowEmpty=true}{/if}" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label" for="birthday">
								{lng p="birthday"}
							</label>
							<div class="form-inline">{html_select_date time=$contact.geburtsdatum start_year="-120" end_year="+0" prefix="geburtsdatum_" field_order="DMY" all_extra="class='form-control'"}</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="alert alert-info">
			<span class="glyphicon glyphicon-info-sign"></span>
			{lng p="iprecord"}
		</div>

		<div class="form-group">
			<button type="submit" id="signupSubmit" class="btn btn-success pull-right" data-loading-text="{lng p="pleasewait"}">
				<span class="glyphicon glyphicon-ok"></span> {lng p="save"}
			</button>
		</div>

	</form></div></div>
</div>
