<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div>
    <h3>Каталог</h3>
    <ul>
        <?foreach($arResult['NEWS'] as $new){?>
            <li>
                <b><?=$new['NAME']?></b> - <?=$new['DATE_ACTIVE_FROM']?> (<?=implode(', ', $new['SECTIONS']['NAME'])?>)
                <ul>
                    <?foreach($new['SECTIONS']['ID'] as $sectID){
                        foreach($arResult['PRODUCTS'][$sectID] as $product){?>
                            <li>
                                <?=$product['NAME']?> - <?=$arResult['PRICES'][$product['id']]['PRICE']?>
                                <?=$arResult['PRICES'][$product['id']]['CURRENCY']?> - 
                                <?=$product['IBLOCK_ELEMENTS_ELEMENT_CATALOG_MATERIAL_VALUE']?> - <?=$product['IBLOCK_ELEMENTS_ELEMENT_CATALOG_ARTICLE_VALUE']?>
                            </li>
                        <?}?>
                    <?}?>
                </ul>
            </li>
        <?}?>
    </ul>
</div>