<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();

\Bitrix\Main\UI\Extension::load('ui.vue');
define('VUEJS_DEBUG', true);
?>

<div id="VueBasket"></div>

<script>
    BX.message({
        items: <?=CUtil::PhpToJSObject($arResult['ITEMS'])?>,
    });
</script>