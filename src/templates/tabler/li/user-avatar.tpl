{if $avatarSize|default:'sm' == 'xl'}
	{assign var=bmAvatarGravatarUrl value=$_userAvatarGravatarUrlXl|default:''}
	{assign var=bmAvatarLibravatarUrl value=$_userAvatarLibravatarUrlXl|default:''}
	{assign var=bmAvatarUploadUrl value=$_userAvatarUploadUrlXl|default:''}
{else}
	{assign var=bmAvatarGravatarUrl value=$_userAvatarGravatarUrlSm|default:''}
	{assign var=bmAvatarLibravatarUrl value=$_userAvatarLibravatarUrlSm|default:''}
	{assign var=bmAvatarUploadUrl value=$_userAvatarUploadUrlSm|default:''}
{/if}
{assign var=bmAvatarMode value=$avatarMode|default:$_userAvatarMode|default:'initials'}
{assign var=bmAvatarInitials value=$avatarInitials|default:$_userInitials|default:''}
{assign var=bmAvatarSize value=$avatarSize|default:'sm'}
{if $bmAvatarMode == 'upload' && $bmAvatarUploadUrl != ''}
<span class="avatar avatar-{$bmAvatarSize|escape}{if $avatarClass|default:'' != ''} {$avatarClass|escape}{/if}" style="padding:0;overflow:hidden;">
	<img src="{$bmAvatarUploadUrl|escape}" alt="" loading="lazy" width="100%" height="100%" style="display:block;object-fit:cover;border-radius:inherit;" data-initials="{$bmAvatarInitials|escape}" data-bg-primary="{if $avatarBgPrimary|default:false}1{else}0{/if}" onerror="bmAvatarInitialsFallback(this)" />
</span>
{elseif $bmAvatarMode == 'initials'}
<span class="avatar avatar-{$bmAvatarSize|escape}{if $avatarClass|default:'' != ''} {$avatarClass|escape}{/if}{if $avatarBgPrimary|default:false} bg-primary-lt{/if}">{$bmAvatarInitials|escape}</span>
{elseif $bmAvatarMode == 'gravatar'}
<span class="avatar avatar-{$bmAvatarSize|escape}{if $avatarClass|default:'' != ''} {$avatarClass|escape}{/if}" style="padding:0;overflow:hidden;">
	<img src="{$bmAvatarGravatarUrl|escape}" alt="" loading="lazy" width="100%" height="100%" style="display:block;object-fit:cover;border-radius:inherit;" data-initials="{$bmAvatarInitials|escape}" data-bg-primary="{if $avatarBgPrimary|default:false}1{else}0{/if}" onerror="bmAvatarInitialsFallback(this)" />
</span>
{elseif $bmAvatarMode == 'libravatar'}
<span class="avatar avatar-{$bmAvatarSize|escape}{if $avatarClass|default:'' != ''} {$avatarClass|escape}{/if}" style="padding:0;overflow:hidden;">
	<img src="{$bmAvatarLibravatarUrl|escape}" alt="" loading="lazy" width="100%" height="100%" style="display:block;object-fit:cover;border-radius:inherit;" data-initials="{$bmAvatarInitials|escape}" data-bg-primary="{if $avatarBgPrimary|default:false}1{else}0{/if}" onerror="bmAvatarInitialsFallback(this)" />
</span>
{elseif $bmAvatarMode == 'libravatar_gravatar_initials'}
<span class="avatar avatar-{$bmAvatarSize|escape}{if $avatarClass|default:'' != ''} {$avatarClass|escape}{/if}" style="padding:0;overflow:hidden;">
	<img src="{$bmAvatarLibravatarUrl|escape}" alt="" loading="lazy" width="100%" height="100%" style="display:block;object-fit:cover;border-radius:inherit;" data-gravatar="{$bmAvatarGravatarUrl|escape}" data-initials="{$bmAvatarInitials|escape}" data-bg-primary="{if $avatarBgPrimary|default:false}1{else}0{/if}" onerror="bmAvatarFallback(this)" />
</span>
{else}
<span class="avatar avatar-{$bmAvatarSize|escape}{if $avatarClass|default:'' != ''} {$avatarClass|escape}{/if}{if $avatarBgPrimary|default:false} bg-primary-lt{/if}">{$bmAvatarInitials|escape}</span>
{/if}
