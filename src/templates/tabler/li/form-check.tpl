{* Tabler checkbox: label.form-check > input.form-check-input + span.form-check-label
   Params: id, name, value, checked, disabled, readonly, compact (m-0), inline, wrapClass,
   labelKey, label, labelBold, ariaLabel, ariaHidden, onclick, onchange, onkeypress *}
<label class="form-check{if !empty($inline)} form-check-inline{/if}{if isset($wrapClass)} {$wrapClass}{else} mb-0{/if}">
	<input type="checkbox" class="form-check-input{if !empty($compact)} m-0{/if}"{if isset($id)} id="{$id}"{/if}{if isset($name)} name="{$name}"{/if}{if isset($value)} value="{$value}"{/if}{if !empty($checked)} checked="checked"{/if}{if !empty($disabled)} disabled="disabled"{/if}{if !empty($readonly)} readonly="readonly"{/if}{if isset($onclick)} onclick="{$onclick}"{/if}{if isset($onchange)} onchange="{$onchange}"{/if}{if isset($onkeypress)} onkeypress="{$onkeypress}"{/if}{if isset($ariaLabel)} aria-label="{$ariaLabel}"{/if}{if !empty($ariaHidden)} aria-hidden="true"{/if} />
	{if isset($labelKey)}<span class="form-check-label">{if !empty($labelBold)}<b>{/if}{lng p=$labelKey}{if !empty($labelBold)}</b>{/if}</span>{elseif isset($label)}<span class="form-check-label">{$label}</span>{/if}
</label>
