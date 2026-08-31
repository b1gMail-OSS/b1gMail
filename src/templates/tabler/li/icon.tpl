{* Map legacy Font Awesome tab/menu icons to Tabler Icons *}
{assign var="_ic" value=$iconClass|default:'icon icon-1'}
{if $faIcon == 'fa-home'}<i class="ti ti-home {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-envelope-o' || $faIcon == 'fa-envelope' || $faIcon == 'fa-envelope-square'}<i class="ti ti-mail {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-calendar'}<i class="ti ti-calendar {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-tasks'}<i class="ti ti-list-check {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-address-book-o' || $faIcon == 'fa-address-book'}<i class="ti ti-address-book {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-sticky-note-o' || $faIcon == 'fa-sticky-note'}<i class="ti ti-notes {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-comments' || $faIcon == 'fa-comment'}<i class="ti ti-message-circle {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-cloud'}<i class="ti ti-cloud {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-download'}<i class="ti ti-download {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-file-o' || $faIcon == 'fa-file'}<i class="ti ti-file {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-cog' || $faIcon == 'fa-gear'}<i class="ti ti-settings {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-search'}<i class="ti ti-search {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-bell'}<i class="ti ti-bell {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-plus-square' || $faIcon == 'fa-plus'}<i class="ti ti-square-plus {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-puzzle-piece'}<i class="ti ti-layout-grid-add {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-folder-open-o' || $faIcon == 'fa-folder-open' || $faIcon == 'fa-folder-o' || $faIcon == 'fa-folder'}<i class="ti ti-folders {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-inbox'}<i class="ti ti-inbox {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-send' || $faIcon == 'fa-paper-plane'}<i class="ti ti-send {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-ban'}<i class="ti ti-ban {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-trash-o' || $faIcon == 'fa-trash'}<i class="ti ti-trash {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-tachometer'}<i class="ti ti-dashboard {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-question' || $faIcon == 'fa-question-circle'}<i class="ti ti-help {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-sign-out' || $faIcon == 'fa-sign-out-alt'}<i class="ti ti-logout {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-pencil' || $faIcon == 'fa-edit'}<i class="ti ti-pencil {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-fax'}<i class="ti ti-printer {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-paper-plane-o' || $faIcon == 'fa-paper-plane'}<i class="ti ti-send {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-archive'}<i class="ti ti-archive {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-id-card-o' || $faIcon == 'fa-id-card'}<i class="ti ti-id {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-user-plus'}<i class="ti ti-user-plus {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-rss'}<i class="ti ti-rss {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-ticket'}<i class="ti ti-ticket {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-birthday-cake'}<i class="ti ti-cake {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-user-shield'}<i class="ti ti-shield-lock {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-exchange'}<i class="ti ti-arrows-exchange {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-cube' || $faIcon == 'fa-box'}<i class="ti ti-package {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-user'}<i class="ti ti-user {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-users'}<i class="ti ti-users {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-newspaper-o' || $faIcon == 'fa-newspaper'}<i class="ti ti-news {$_ic}" aria-hidden="true"></i>
{elseif $faIcon == 'fa-building' || $faIcon == 'fa-building-o' || $faIcon == 'fa-university' || $faIcon == 'fa-bank' || $faIcon == 'fa-building-bank'}<i class="ti ti-building-bank {$_ic}" aria-hidden="true"></i>
{elseif $faIcon}<i class="fa {$faIcon|escape} {$_ic}" aria-hidden="true"></i>
{else}<i class="ti ti-point {$_ic}" aria-hidden="true"></i>
{/if}
