{if !isset($wdicons_size_class)}
	{if isset($fa_additionalparamclass) && ($fa_additionalparamclass == 'fa-3x' || $fa_additionalparamclass == 'fa-4x')}
		{assign var=wdicons_size_class value='bm-webdisk-icon-lg' scope='global'}
	{else}
		{assign var=wdicons_size_class value='bm-webdisk-icon-sm' scope='global'}
	{/if}
{/if}
<span class="bm-webdisk-item-icon {$wdicons_size_class}" {$wdicons_additionalparam}>
{if $use_fa_icons==1}
	<i class="ti {if isset($item.icon)}{$item.icon}{else}ti-file{/if} icon" aria-hidden="true"></i>
{else}
	<img src="webdisk.php?action=displayExtension&ext={$item.ext}&sid={$sid}" alt="" {$wdicons_imgattr} />
{/if}
{if isset($item.share) && $item.share}
	<i class="ti ti-arrow-up-right icon bm-webdisk-shared-badge" aria-hidden="true"></i>
{/if}
</span>
