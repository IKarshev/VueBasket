<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();
use Bitrix\Main\Localization\Loc;

\Bitrix\Main\UI\Extension::load('ui.vue');
define('VUEJS_DEBUG', true);

$this->setFrameMode(true);
?>

<div id="VueBasket">
    <div class="items-container">
        <table class="items-container-table">
            <tbody>
                <tr class="basket-item" v-for="(item, index) in arResult.ITEMS">
                    <td class="basket-item-description">
                        <div class="image">
                            <div class="img-container">
                                <img :src="item.PREVIEW_PICTURE.RESIZE_SRC" alt="">
                            </div>
                        </div>
                        <div class="basket-item-description-content">
                            <div class="title-container">
                                <a class="title" target="_blank" :href="item.DETAIL_PAGE_URL">{{item.NAME}}</a>
                            </div>
                        </div>
                    </td>

                    <td class="basket-item-quantity">
                        <div class="basket-item-quantity-container">
                            <button :disabled="Number(item.MAX_QUANTITY) <= 0 || Number(item.QUANTITY) <= 1 " @click="QuantityMinus(item)" class="basket-item-quantity-btn minus"></button>
                            <input :disabled="Number(item.MAX_QUANTITY) <= 0" v-model.number="item.QUANTITY" @change="QuantityChange(item)" type="text" class="basket-item-quantity-filed">
                            <button :disabled="Number(item.MAX_QUANTITY) <= 0 || Number(item.QUANTITY) >= Number(item.MAX_QUANTITY)"  @click="QuantityPlus(item)" class="basket-item-quantity-btn plus"></button>
                        </div>
                        <span><?=Loc::getMessage('VUE_BASKET_TOTAL_COUNT_TITLE')?>: {{ item.MAX_QUANTITY }}</span>
                    </td>
                    
                    <td class="price">
                        <div class="price-container">{{ GetFullPrice(item) }}</div>
                    </td>

                    <td class="basket-item-action">
                        <div class="basket-item-action-container">
                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <?$APPLICATION->ShowViewContent('block_under_basket_items');?>
    </div>

    <div class="total-block-sticky-container">
        <div class="total-block">
            <div class="items-price">Итого: {{ ItemsPrice }}</div>
            <div class="sale-price">Итого: {{ SalePrice }}</div>
            <div class="total-price">Итого: {{ TotalPrice }}</div>
        </div>
    </div>
</div>
<script>
    BX.message({
        arParams: <?=CUtil::PhpToJSObject($arParams)?>,
        arResult: <?=CUtil::PhpToJSObject($arResult)?>,
    });
</script>