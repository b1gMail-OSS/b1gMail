<div class="bm-start-dashboard bm-dashboard-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header bm-dashboard-header">
		<div class="left">
			<i class="ti ti-dashboard icon icon-sm" aria-hidden="true"></i>
			{lng p="overview"}
		</div>
		<div class="right bm-dashboard-header-actions">
			<a href="{sessionurl file='organizer.php' params='action=customize'}" class="btn btn-sm btn-outline-primary">
				<i class="ti ti-layout-grid-add icon"></i>
				{lng p="customize"}
			</a>
		</div>
	</div>

	<div class="bm-dashboard-body">
		{include file="li/widget-board.tpl" boardSaveCallback="organizerBoardOrderChanged"}
	</div>
</div>
