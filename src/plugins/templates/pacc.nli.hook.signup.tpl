<input type="hidden" name="paccPackage" value="{$paccPackage.id}" />

<div class="panel panel-default">
    <div class="panel-heading panel-title">
        <span class="glyphicon glyphicon-shopping-cart"></span>
        {lng p="pacc_package"}
    </div>
    <div class="panel-collapse">
        <div class="panel-body">
            <strong>{text value=$paccPackage.title}</strong>
            {if $paccPackage.isFree}
                ({lng p="pacc_free"})
            {else}
                ({text value=$paccPackage.priceInterval}
                {text value=$paccPackage.price}
                {text value=$paccPackage.priceTax})
            {/if}
        </div>
    </div>
</div>
