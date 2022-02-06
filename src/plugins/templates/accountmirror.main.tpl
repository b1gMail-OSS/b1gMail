<form action="{$pageURL}&sid={$sid}&save=true" method="post" onsubmit="spin(this)" name="f1">
	<fieldset>
		<legend>{lng p="am_mirrorings"}</legend>
		
		<table class="list">
			<tr>
				<th width="20">&nbsp;</th>
				<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'mirroring_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
				<th>{lng p="am_source"}</th>
				<th>{lng p="am_dest"}</th>
				<th>{lng p="am_timeframe"}</th>
				<th width="65">{lng p="emails"}</th>
				<th width="65">{lng p="am_errors"}</th>
				<th width="35">&nbsp;</th>
			</tr>
			
			{foreach from=$mirrorings item=item}
			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}">
				<td align="center"><img src="../plugins/templates/images/accountmirror_logo.png" border="0" width="16" height="16" alt="" /></td>
				<td align="center"><input type="checkbox" name="mirroring_{$item.mirrorid}" /></td>
				<td><a href="users.php?do=edit&id={$item.userid}&sid={$sid}">{$item.source}</a> (#{$item.userid})</td>
				<td><a href="users.php?do=edit&id={$item.mirror_to}&sid={$sid}">{$item.dest}</a> (#{$item.mirror_to})</td>
				<td>
					{if $item.begin==0&&$item.end==0}
					({lng p="unlimited"})
					{elseif $item.begin==0}
					{lng p="am_to"} {date timestamp=$item.end}
					{elseif $item.end==0}
					{lng p="am_from"} {date timestamp=$item.begin}
					{else}
					{date timestamp=$item.begin} - {date timestamp=$item.end}
					{/if}
				</td>
				<td>{$item.mail_count}</td>
				<td>{$item.error_count}</td>
				<td>
					<a href="{$pageURL}&delete={$item.mirrorid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
				</td>
			</tr>
			{/foreach}
		
			<tr>
				<td class="footer" colspan="8">
					<div style="float:left;">
						{lng p="action"}: <select name="massAction" class="smallInput">
							<option value="-">------------</option>
							
							<optgroup label="{lng p="actions"}">
								<option value="delete">{lng p="delete"}</option>
							</optgroup>
						</select>&nbsp;
					</div>
					<div style="float:left;">
						<input type="submit" name="executeMassAction" value=" {lng p="execute"} " class="smallInput" />
					</div>
				</td>
			</tr>
		</table>
	</fieldset>
</form>

<fieldset>
	<legend>{lng p="am_add"}</legend>

	<form action="{$pageURL}&sid={$sid}&add=true" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="4"><img src="../plugins/templates/images/accountmirror_add.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="am_source"}:</td>
				<td class="td2">
					<input type="text" size="35" name="email_source" value="" />
					<small>({lng p="am_accemail"})</small>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="am_dest"}:</td>
				<td class="td2">
					<input type="text" size="35" name="email_dest" value="" />
					<small>({lng p="am_accemail"})</small>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="from"}:</td>
				<td class="td2">
					<input type="checkbox" checked="checked" id="from_unlim" name="von_unlim" />
					<label for="from_unlim"><b>{lng p="now"}</b></label>
					{lng p="or"}
					{html_select_date prefix="von" start_year="-5" field_order="DMY" field_separator="."}, 
					{html_select_time prefix="von" display_seconds=false}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="to"}:</td>
				<td class="td2">
					<input type="checkbox" checked="checked" id="to_unlim" name="bis_unlim" />
					<label for="to_unlim"><b>{lng p="unlimited"}</b></label>
					{lng p="or"}
					{html_select_date prefix="bis" end_year="+5" field_order="DMY" field_separator="."}, 
					{html_select_time prefix="bis" display_seconds=false}
				</td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="notices"}</legend>
	
	<table>
		<tr>
			<td width="36" valign="top"><img src="{$tpldir}images/warning32.png" border="0" alt="" width="32" height="32" /></td>
			<td valign="top">
				<ul style="margin:0;">
					<li>{lng p="am_notice1"}</li>
					<li>{lng p="am_notice2"}</li>
					<li>{lng p="am_notice3"}</li>
					<li>{lng p="am_notice4"}</li>
					<li>{lng p="am_notice5"}</li>
				</ul>
			</td>
		</tr>
	</table>
</fieldset>
