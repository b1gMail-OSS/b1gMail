<fieldset>
	<legend>{lng p="releaseversion"}: {$versionNo}</legend>
	
	<table>
		<tr>
			<td width="36" valign="top"><img src="{$tpldir}images/toolbox32.png" border="0" alt="" width="32" height="32" /></td>
			<td valign="top">
				<p>
					{lng p="toolboxrelease"}
				</p>
				<p>
					<img src="{$tpldir}images/warning.png" border="0" alt="" align="absmiddle" />
					{lng p="toolboxonlinenote"}
				</p>
				<br />
			</td>
		</tr>
		<tr>
			<td width="36" valign="top"><img src="{$tpldir}images/test32.png" border="0" alt="" width="32" height="32" /></td>
			<td valign="top">
				<b>{lng p="test"}</b>
				<p>
					<button class="button" id="testButton_win" onclick="tbxTest({$versionID},'win');">{lng p="test"} (Windows)</button>
					<span id="testLoad_win" style="display:none;">
						<img src="{$tpldir}images/load_16.gif" border="0" alt="" align="absmiddle" />
						{lng p="preparing"}...
					</span>
					<span id="testLink_win" style="display:none;">
						<img src="{$tpldir}images/software32.png" width="16" height="16" border="0" alt="" align="absmiddle" />
						<a href="toolbox.php?do=downloadVersion&versionid={$versionID}&os=win&sid={$sid}" target="_blank">{lng p="download"} (Windows)</a>
					</span>
				</p>
				<p>
					<button class="button" id="testButton_mac" onclick="tbxTest({$versionID},'mac');">{lng p="test"} (Mac)</button>
					<span id="testLoad_mac" style="display:none;">
						<img src="{$tpldir}images/load_16.gif" border="0" alt="" align="absmiddle" />
						{lng p="preparing"}...
					</span>
					<span id="testLink_mac" style="display:none;">
						<img src="{$tpldir}images/software32.png" width="16" height="16" border="0" alt="" align="absmiddle" />
						<a href="toolbox.php?do=downloadVersion&versionid={$versionID}&os=mac&sid={$sid}" target="_blank">{lng p="download"} (Mac)</a>
					</span>
				</p>
				<br />
			</td>
		</tr>
		<tr>
			<td width="36" valign="top"><img src="{$tpldir}images/release32.png" border="0" alt="" width="32" height="32" /></td>
			<td valign="top">
				<b>{lng p="release"}</b>
				<p>
					<button class="button" id="releaseButton" onclick="if(confirm('{lng p="reallyrelease"}')) tbxRelease({$versionID});">{lng p="release"}</button>
					<span id="releaseLoad" style="display:none;">
						<img src="{$tpldir}images/load_16.gif" border="0" alt="" align="absmiddle" />
						{lng p="preparing"}...
					</span>
				</p>
			</td>
		</tr>
	</table>
</fieldset>
