<form action="prefs.abuse.php?save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="common"}</legend>

		<table>
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/abuse32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="220">{lng p="ap_medium_limit"}:</td>
				<td class="td2">
					<img src="templates/images/indicator_yellow.png" border="0" alt="" align="absmiddle" />
					<input type="number" min="1" step="1" name="ap_medium_limit" value="{text allowEmpty=true value=$bm_prefs.ap_medium_limit}" style="width:60px;" />
					{lng p="points"}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="ap_hard_limit"}:</td>
				<td class="td2">
					<img src="templates/images/indicator_red.png" border="0" alt="" align="absmiddle" />
					<input type="number" min="1" step="1" name="ap_hard_limit" value="{text allowEmpty=true value=$bm_prefs.ap_hard_limit}" style="width:60px;" />
					{lng p="points"}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="ap_expire_time"}:</td>
				<td class="td2"><input type="number" min="1" step="1" name="ap_expire_time" value="{text allowEmpty=true value=$bm_prefs.ap_expire_time/3600}" style="width:80px;" /> {lng p="hours"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="ap_expire_mode"}:</td>
				<td class="td2">
					<input type="radio" name="ap_expire_mode" value="dynamic" id="ap_expire_mode_dynamic"{if $bm_prefs.ap_expire_mode=='dynamic'} checked="checked"{/if} />
					<label for="ap_expire_mode_dynamic">{lng p="ap_dynamic"}</label><br />
					<input type="radio" name="ap_expire_mode" value="static" id="ap_expire_mode_static"{if $bm_prefs.ap_expire_mode=='static'} checked="checked"{/if} />
					<label for="ap_expire_mode_static">{lng p="ap_static"}</label>
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="ap_autolock"}?</td>
				<td class="td2">
					<input name="ap_autolock"{if $bm_prefs.ap_autolock=='yes'} checked="checked"{/if} type="checkbox" id="ap_autolock" />
					<label for="ap_autolock">{lng p="ap_athardlimit"}</label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="ap_autolock_notify"}?</td>
				<td class="td2"><input name="ap_autolock_notify" id="ap_autolock_notify"{if $bm_prefs.ap_autolock_notify=='yes'} checked="checked"{/if} type="checkbox" />
					<label for="ap_autolock_notify"> {lng p="to2"}: </label><input type="text" name="ap_autolock_notify_to" value="{email value=$bm_prefs.ap_autolock_notify_to}" size="24" /></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>{lng p="pointtypes"}</legend>

		<table class="list">
			<tr>
				<th width="20">&nbsp;</th>
				<th>{lng p="title"}</th>
				<th width="80">{lng p="points"}</th>
			</tr>

			{foreach from=$apTypes item=apType key=apTypeID}
			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}">
				<td align="center"><img src="{$tpldir}/images/abuse.png" border="0" alt="" width="16" height="16" /></td>
				<td>
					{$apType.title}
					{if $apType.prefs}
					<div>
						<table class="subTable">
							{foreach from=$apType.prefs key=prefKey item=prefDetails}
							<tr>
								<td>{$prefDetails.title}</td>
								<td>
									{if $prefDetails.type==1}
									<input type="text" name="types[{$apTypeID}][prefs][{$prefKey}]" value="{text value=$prefDetails.value}" style="width:100px;" class="smallInput" />
									{/if}
								</td>
							</tr>
							{/foreach}
						</table>
					</div>
					{/if}
				</td>
				<td><input type="text" name="types[{$apTypeID}][points]" value="{$apType.points}" size="6" /></td>
			</tr>
			{/foreach}
		</table>
	</fieldset>

	<p>
		<div style="float:right;" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
