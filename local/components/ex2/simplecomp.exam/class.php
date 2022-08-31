<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
            
use Bitrix\Main\Entity\ExpressionField;
\Bitrix\Main\Loader::IncludeModule("iblock");
\Bitrix\Main\Loader::IncludeModule("catalog");

class Catalog extends CBitrixComponent
{
    public function executeComponent()
    {
        if($this->startResultCache()){
            $classProducts = \Bitrix\Iblock\Iblock::wakeUp($this->arParams['PRODUCTS_IBLOCK_ID'])->getEntityDataClass();
            $classProductsSection = \Bitrix\Iblock\Model\Section::compileEntityByIblock($this->arParams['PRODUCTS_IBLOCK_ID']);
            $classNews = \Bitrix\Iblock\Iblock::wakeUp($this->arParams['NEWS_IBLOCK_ID'])->getEntityDataClass();

            $arSect = [];
            $arSectID = [];
            $obSections = $classProductsSection::getList([
                'select' => ['ID', 'NAME', 'UF_NEWS_LINK'],
                'filter' => ['=IBLOCK_ID' => $this->arParams['PRODUCTS_IBLOCK_ID'], '=ACTIVE' => 'Y'],
            ]);
            while($sect = $obSections->Fetch()){
                $arSect[$sect['ID']] = $sect['NAME'];
                $arSectID[] = $sect['ID'];
                if($sect['UF_NEWS_LINK']){
                    foreach($sect['UF_NEWS_LINK'] as $newID){
                        $arNews[$newID]['SECTIONS']['NAME'][] = $sect['NAME'];
                        $arNews[$newID]['SECTIONS']['ID'][] = $sect['ID'];
                    }
                }
            }

            $arNewsIDs = [];
            $news = $classNews::getList([
                'select' => ['ID', 'NAME', 'DATE_ACTIVE_FROM'],
                'filter' => ['=IBLOCK_ID' => $this->arParams['NEWS_IBLOCK_ID'], '=ACTIVE' => 'Y', 'ID' => array_keys($arNews)]
            ]);
            while($new = $news->Fetch()){
                $arNews[$new['ID']]['NAME'] = $new['NAME'];
                $arNews[$new['ID']]['DATE_ACTIVE_FROM'] = $new['DATE_ACTIVE_FROM'];
                $arNewsIDs[] = $new['ID'];
            }
            $diff = array_diff(array_keys($arNews), $arNewsIDs);
            foreach($diff as $ID){
                unset($arNews[$ID]);
            }

            $arProducts = [];
            $arProductsIDs = [];
            $products = $classProducts::getList([
                'select' => ['ID', 'IBLOCK_SECTION_ID', 'NAME', 'MATERIAL', 'ARTICLE'],
                'filter' => ['=ACTIVE' => 'Y', 'IBLOCK_SECTION_ID' => $arSectID],
                'count_total' => true,
            ]);
            while($product = $products->Fetch()){
                $arProducts[$product['IBLOCK_SECTION_ID']][] = $product;
                $arProductsIDs[] = $product['ID'];
            }
            $countProducts = $products->getCount();

            $arPrices = [];
            $prices = \Bitrix\Catalog\PriceTable::getList([
                'filter' => [
                    'PRODUCT_ID' => $arProductsIDs,
                    'CATALOG_GROUP_ID' => 1 // базовый тип цены (ну или любой, который нужен в данном компоненте)
                ],
            ]);
            while($price = $prices->Fetch()){
                $arPrices[$price['PRODUCT_ID']] = $price;
            }
        
            $this->arResult = [
                'NEWS' => $arNews,
                'SECTIONS' => $arSect,
                'PRODUCTS' => $arProducts,
                'PRICES' => $arPrices,
                'PRODUCTS_COUNT' => $countProducts
            ];
            $this->includeComponentTemplate();
        }
    }
}
?>