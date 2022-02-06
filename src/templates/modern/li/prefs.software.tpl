<div id="contentHeader">
	<div class="left">
		<i class="fa fa-download" aria-hidden="true"></i>
		{lng p="software"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
	<table class="listTable">
		<tr>
			<th class="listTableHead"> {lng p="software"}</th>
		</tr>
		<tr>
			<td class="pad">
				<p>
					{$introText}
				</p>
				
				<table>
					{if $releaseFiles.win}
					<tr>
						<td valign="top" width="64">
							<i class="fa fa-windows fa-4x" aria-hidden="true"></i>
						</td>
						<td>
							<strong>Windows</strong> <small>(Version {$verNo})</small>
							<p>
								{lng p="software_win"}
							</p>
							<button onclick="document.location.href='prefs.php?action=software&do=download&os=win&sid={$sid}';">
								<i class="fa fa-download" aria-hidden="true"></i>
								{lng p="download"}
								<small>&nbsp;({size bytes=$fileSizes.win})</small>
							</button>
							<br /><br /><br />
						</td>
					</tr>
					{/if}
					
					{if $releaseFiles.mac}
					<tr>
						<td valign="top" width="64">
							<i class="fa fa-apple fa-4x" aria-hidden="true"></i>
						</td>
						<td>
							<strong>Mac</strong> <small>(Version {$verNo})</small>
							<p>
								{lng p="software_mac"}
							</p>
							<button onclick="document.location.href='prefs.php?action=software&do=download&os=mac&sid={$sid}';">
								<i class="fa fa-download" aria-hidden="true"></i>
								{lng p="download"}
								<small>&nbsp;({size bytes=$fileSizes.mac})</small>
							</button>
							<br /><br />
						</td>
					</tr>
					{/if}
				</table>
			</td>
		</tr>
	</table>
</div></div>
