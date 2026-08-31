<div class="bm-start-dashboard bm-dashboard-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header bm-dashboard-header">
		<div class="left">
			<i class="ti ti-home icon icon-sm" aria-hidden="true"></i>
			{lng p="welcome"}!
		</div>
		<div class="right bm-dashboard-header-actions">
			{if $templatePrefs.showUserEmail}
			<span class="bm-dashboard-header-email text-secondary">{$_userEmail}</span>
			{/if}
			<a href="{sessionurl file='start.php' params='action=customize'}" class="btn btn-sm btn-outline-primary">
				<i class="ti ti-layout-grid-add icon"></i>
				{lng p="customize"}
			</a>
		</div>
	</div>

	{hook id="start.page.tpl:head"}

	<div class="bm-dashboard-body">
		{include file="li/widget-board.tpl" boardSaveCallback="startBoardOrderChanged"}
	</div>

	{hook id="start.page.tpl:foot"}
</div>
