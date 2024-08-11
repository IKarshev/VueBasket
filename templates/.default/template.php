<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();

\Bitrix\Main\UI\Extension::load('ui.vue');
define('VUEJS_DEBUG', true);
?>

<div id="VueBasket">
    <table class="items-container">
        <tbody>
            <tr class="basket-item" v-for="(item, index) in arResult.ITEMS">
                <td class="basket-item-description">
                    <div class="image">
                        <div class="img-container">
                            <img src="<?//=CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src']?>" alt="">
                        </div>
                    </div>
                    <div class="basket-item-description-content">
                        <div class="title-container">
                            <a class="title" target="_blank" :href="item.DETAIL_PAGE_URL">{{item.NAME}}</a>
                        </div>
                        <span class="article">артикул</span>
                    </div>
                </td>

                <td class="basket-item-amount">
                    <div class="basket-item-amount-container">
                        <span @click="AmountMinus(index)" class="basket-item-amount-btn minus"></span>
                        <input type="text" class="basket-item-amount-filed" :value="item.QUANTITY">
                        <span @click="AmountPlus(index)" class="basket-item-amount-btn plus"></span>
                    </div>

                </td>
                
                <td class="price">
                    <div class="price-container">{{ GetFullPrice(item) }}</div>
                </td>

                <td class="basket-item-action">
                    <div class="basket-item-action-container">
                        <?/*
                        <a class="WhishList <?=($arItem['WhishList']) ? 'remove-list' : 'add-list' ?>" href="javascript:void(0);" data-ProductID="<?=$arItem['ID']?>"></a>
                        */?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script>
    BX.message({
        arParams: <?=CUtil::PhpToJSObject($arParams)?>,
        arResult: <?=CUtil::PhpToJSObject($arResult)?>,
    });
</script>