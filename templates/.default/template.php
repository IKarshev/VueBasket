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
                        <button :disabled="item.MAX_QUANTITY <= 0" @click="QuantityMinus(item)" class="basket-item-quantity-btn minus"></button>
                        <input :disabled="item.MAX_QUANTITY <= 0" v-model.number="item.QUANTITY" @change="QuantityChange(item)" type="text" class="basket-item-quantity-filed">
                        <button :disabled="item.MAX_QUANTITY <= 0"  @click="QuantityPlus(item)" class="basket-item-quantity-btn plus"></button>
                    </div>

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
</div>
<script>
    BX.message({
        arParams: <?=CUtil::PhpToJSObject($arParams)?>,
        arResult: <?=CUtil::PhpToJSObject($arResult)?>,
    });
</script>