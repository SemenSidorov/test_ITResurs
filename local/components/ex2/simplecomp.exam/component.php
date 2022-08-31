<?
use Bitrix\Main\Loader;
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->SetPageProperty('title', 'В каталоге товаров представлено товаров: ' . $this->arResult['PRODUCTS_COUNT']);