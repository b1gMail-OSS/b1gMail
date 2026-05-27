{include file="li/navbar-theme.tpl"}
{if $bmNotifyInterval>0}
<div class="nav-item d-none d-md-flex me-2">
	<a href="#" class="nav-link px-0 position-relative" onclick="showNotifications(this); return false;" title="{lng p="notifications"}">
		<i id="notifyIcon" class="icon ti ti-bell icon-1"></i>
		<span class="badge bg-red text-red-fg position-absolute top-0 start-100 translate-middle" id="notifyCount"{if $bmUnreadNotifications==0} style="display:none;"{/if}>{number value=$bmUnreadNotifications min=0 max=99}</span>
	</a>
</div>
{/if}
<div class="nav-item d-none d-md-flex me-2">
	<a href="#" class="nav-link px-0" onclick="showNewMenu(this); return false;" title="{lng p="new"}">
		<i class="icon ti ti-square-plus icon-1"></i>
		<span class="d-none d-lg-inline ms-1">{lng p="new"}</span>
	</a>
</div>
<div class="nav-item d-none d-md-flex me-2">
	<a href="#" class="nav-link px-0" onclick="showSearchPopup(this); return false;" title="{lng p="search"}">
		<i class="icon ti ti-search icon-1"></i>
	</a>
</div>
