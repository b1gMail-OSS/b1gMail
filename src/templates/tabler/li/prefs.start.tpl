<div class="bm-prefs-page bm-prefs-overview">
	<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
		<div class="left">
			<i class="ti ti-settings icon icon-sm" aria-hidden="true"></i>
			{lng p="prefs"}
		</div>
	</div>

	<div class="scrollContainer bm-prefs-body">
		{if $templatePrefs.prefsLayout=='onecolumn'}
		<div class="card bm-prefs-overview-card">
			<div class="list-group list-group-flush bm-prefs-overview-list">
				{foreach from=$prefsItems item=null key=item}
				<a href="{sessionurl file='prefs.php' params="action={$item}"}" class="list-group-item list-group-item-action bm-prefs-overview-item">
					<span class="avatar avatar-sm bg-primary-lt text-primary bm-prefs-overview-icon">
						{include file="li/prefs.item-icon.tpl"}
					</span>
					<span class="bm-prefs-overview-text">
						<span class="bm-prefs-overview-title">{lng p="$item"}</span>
						<span class="bm-prefs-overview-desc">{lng p="prefs_d_$item"}</span>
					</span>
					<i class="ti ti-chevron-right icon text-secondary bm-prefs-overview-chevron" aria-hidden="true"></i>
				</a>
				{/foreach}
			</div>
		</div>
		{else}
		<div class="bm-prefs-overview-grid">
			{foreach from=$prefsItems item=null key=item}
			<a href="{sessionurl file='prefs.php' params="action={$item}"}" class="card bm-prefs-overview-tile">
				<div class="card-body bm-prefs-overview-tile-body">
					<span class="avatar avatar-sm bg-primary-lt text-primary bm-prefs-overview-icon">
						{include file="li/prefs.item-icon.tpl"}
					</span>
					<span class="bm-prefs-overview-text">
						<span class="bm-prefs-overview-title">{lng p="$item"}</span>
						<span class="bm-prefs-overview-desc">{lng p="prefs_d_$item"}</span>
					</span>
					<i class="ti ti-chevron-right icon text-secondary bm-prefs-overview-chevron" aria-hidden="true"></i>
				</div>
			</a>
			{/foreach}
		</div>
		{/if}
	</div>
</div>
