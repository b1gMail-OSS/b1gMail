<div id="contentHeader">
	<div class="left">
		<i class="fa fa-ban" aria-hidden="true"></i>
		{lng p="antispam"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form name="f1" method="post" action="prefs.php?action=antispam&do=save&sid={$sid}">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="antispam"}</th>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="spamfilter">{lng p="spamfilter"}:</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="spamfilter" id="spamfilter"{if $spamFilter} checked="checked"{/if} />
				<label for="spamfilter"><b>{lng p="enable"}</b></label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="unspamme">{lng p="unspamme"}:</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="unspamme" id="unspamme"{if $unspamMe} checked="checked"{/if} />
				<label for="unspamme"><b>{lng p="marknonspam"}</b></label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="addressbook_nospam">{lng p="mailsfromab"}:</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="addressbook_nospam" id="addressbook_nospam"{if $addressbookNoSpam} checked="checked"{/if} />
				<label for="addressbook_nospam"><b>{lng p="marknonspam"}</b></label>
			</td>
		</tr>
		{if $localMode}
		<tr>
			<td class="listTableLeft"><label for="bayes_border">{lng p="bayesborder"}:</label></td>
			<td class="listTableRight">
				<table class="bayesBorderTable">
					<tr>
						<td colspan="2">
							<div class="bayesBorderSlider">
								<table class="bayesBorderTable2">
									<td><input type="radio" name="bayes_border" value="98"{if $bayes_border==98} checked="checked"{/if} /></td>
									<td><input type="radio" name="bayes_border" value="96"{if $bayes_border==96} checked="checked"{/if} /></td>
									<td><input type="radio" name="bayes_border" value="94"{if $bayes_border==94} checked="checked"{/if} /></td>
									<td><input type="radio" name="bayes_border" value="92"{if $bayes_border==92} checked="checked"{/if} /></td>
									<td><input type="radio" name="bayes_border" value="90"{if $bayes_border==90} checked="checked"{/if} /></td>
									<td><input type="radio" name="bayes_border" value="88"{if $bayes_border==88} checked="checked"{/if} /></td>
									<td><input type="radio" name="bayes_border" value="84"{if $bayes_border==84} checked="checked"{/if} /></td>
									<td><input type="radio" name="bayes_border" value="80"{if $bayes_border==80} checked="checked"{/if} /></td>
									<td><input type="radio" name="bayes_border" value="75"{if $bayes_border==75} checked="checked"{/if} /></td>
									<td><input type="radio" name="bayes_border" value="70"{if $bayes_border==70} checked="checked"{/if} /></td>					
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<td class="bayesBorderLeftTD">{lng p="defensive"}</td>
						<td class="bayesBorderRightTD">{lng p="aggressive"}</td>
					</tr>
				</table>
			</td>
		</tr>
		{/if}
		<tr>
			<td class="listTableLeft"><label for="spamaction">{lng p="spamaction"}:</label></td>
			<td class="listTableRight">
				<select name="spamaction" id="spamaction">
					<option value="-1"{if $spamAction==-1} selected="selected"{/if}>{lng p="block"}</option>
					
					<optgroup label="{lng p="move"} {lng p="moveto"}">
					{foreach from=$dropdownFolderList key=dFolderID item=dFolderTitle}
					<option value="{$dFolderID}" style="font-family:courier;"{if $spamAction==$dFolderID} selected="selected"{/if}>{$dFolderTitle}</option>
					{/foreach}
					</optgroup>
				</select>
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

{if $localMode}
<br />
<form name="f1" method="post" action="prefs.php?action=antispam&do=resetDB&sid={$sid}">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="spamindex"}</th>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="entries"}:</td>
			<td class="listTableRight">
				{$dbEntries}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" value="{lng p="resetindex"}"{if $dbEntries==0} disabled="disabled"{/if} /><br />
				<small>{lng p="resetindextext"}</small>
			</td>
		</tr>
	</table>
</form>
{/if}

</div></div>
