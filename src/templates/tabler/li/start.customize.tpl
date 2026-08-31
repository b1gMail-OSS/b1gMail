<div class="bm-start-dashboard bm-dashboard-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header bm-dashboard-header">
		<div class="left">
			<i class="ti ti-layout-grid-add icon icon-sm" aria-hidden="true"></i>
			{lng p="customize"}
		</div>
	</div>

	<div class="bm-dashboard-body">
		<div class="card bm-dashboard-card">
			<form name="f1" method="post" action="{sessionurl file='start.php' params='action=saveCustomize'}">
				{csrffield}
				<div class="list-group list-group-flush">
					{foreach from=$possibleWidgets key=widget item=info}
					<label class="list-group-item list-group-item-action form-check d-flex align-items-center gap-2 mb-0">
						<input class="form-check-input m-0" type="checkbox" id="widget_{$widget}" name="widget_{$widget}"{if !empty($info.active)} checked="checked"{/if} />
						<span class="form-check-label d-flex align-items-center gap-2">
							{if !empty($info.icon)}<img src="{$info.icon}" alt="" width="16" height="16" />{/if}
							{$info.title}
						</span>
					</label>
					{/foreach}
				</div>

				<div class="card-footer d-flex justify-content-end">
					<div class="btn-list">
						<button type="submit" class="btn btn-primary">{lng p="ok"}</button>
						<button type="reset" class="btn">{lng p="reset"}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
