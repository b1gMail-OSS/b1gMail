<div id="contentHeader">
	<div class="left">
		<i class="fa fa-share-square-o" aria-hidden="true"></i>
		{lng p="webdisk"}-{lng p="sharing"} ({text value=$fileName})
	</div>
</div>

<div class="scrollContainer"><div class="pad">
{if isset($shareError) && $shareError != ''}
<div class="mailWarning" style="margin-bottom:10px;">{$shareError}</div>
{/if}
<form action="webdisk.php?action=saveFileShareSettings&id={$id}{$sessionUrlSuffix}" method="post">
	{csrffield}
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="sharing"}</th>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="file"}:</td>
			<td class="listTableRight">{text value=$fileName}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="shareFile">{lng p="share"}:</label></td>
			<td class="listTableRight">
				<label class="form-check mb-0">
					<input class="form-check-input" type="checkbox" name="shareFile" id="shareFile" {if $fileShared}checked="checked" {/if} />
				</label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="password">{lng p="password"}{if $sharePasswordRequired|default:false} *{/if}:</label></td>
			<td class="listTableRight bm-webdisk-share-input-cell">
				<div class="input-group input-group-flat bm-webdisk-share-input-group">
					<input type="password" class="form-control bm-webdisk-share-input" id="password" name="sharePW" value="{if isset($filePW)}{text value=$filePW allowEmpty=true}{/if}" autocomplete="new-password" />
					<span class="input-group-text">
						<a href="#" class="link-secondary" onclick="toggleSharePasswordVisibility('password', this); return false;" aria-label="{lng p="show"}">
							<i class="ti ti-eye icon" aria-hidden="true"></i>
						</a>
					</span>
				</div>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="shareUntil">{lng p="wd_share_expiry"}{if $shareExpiryRequired|default:false} *{/if}:</label></td>
			<td class="listTableRight bm-webdisk-share-date-cell">
				<input type="date" class="form-control bm-webdisk-share-date" id="shareUntil" name="shareUntil" value="{$shareUntilDate|default:''}" min="{$shareExpiryMinDate|default:''}"{if $shareExpiryMaxDate|default:'' != ''} max="{$shareExpiryMaxDate}"{/if} />
				{if $shareExpiryMaxDays|default:0 > 0}
				<div class="bm-webdisk-share-date-help">({lng p="max"}: {$shareExpiryMaxDays} {lng p="days"})</div>
				{/if}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="shareSingleUse">{lng p="wd_share_single_use"}:</label></td>
			<td class="listTableRight">
				<label class="form-check mb-0">
					<input class="form-check-input" type="checkbox" name="shareSingleUse" id="shareSingleUse" {if $shareSingleUse|default:false}checked="checked" {/if} />
				</label>
			</td>
		</tr>
		{if $fileShareURL|default:'' != ''}
		<tr>
			<td class="listTableLeft">{lng p="wd_share_file_link"}:</td>
			<td class="listTableRight"><a href="{$fileShareURL}" target="_blank">{$fileShareURL}</a></td>
		</tr>
		{/if}
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>
</div></div>

<script>
<!--
function toggleSharePasswordVisibility(inputID, trigger)
{
	var input = EBID(inputID),
		icon;

	if(!input || !trigger)
		return;

	icon = trigger.querySelector('i');

	if(input.type === 'password')
	{
		input.type = 'text';
		if(icon)
			icon.className = 'ti ti-eye-off icon';
	}
	else
	{
		input.type = 'password';
		if(icon)
			icon.className = 'ti ti-eye icon';
	}
}
//-->
</script>
