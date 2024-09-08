<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();
use Bitrix\Main\Localization\Loc;

\Bitrix\Main\UI\Extension::load('ui.vue');
define('VUEJS_DEBUG', true);

$this->setFrameMode(true);
?>

<div id="VueBasket">
    <template v-if="arResult.ITEMS.length > 0">
        <div class="items-container">
            <table class="items-container-table">
                <tbody>
                    <tr :class="{'basket-item':!item.DELETED_ITEM,'basket-item deleted':item.DELETED_ITEM}" v-for="(item, index) in arResult.ITEMS">
                        <template v-if="!item.DELETED_ITEM"><?// Обычный товар?>
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
                                    <input :disabled="Number(item.MAX_QUANTITY) <= 0" :min="1" :max="item.MAX_QUANTITY"  v-model.number="item.QUANTITY" @change="QuantityChange(item)"  type="text" class="basket-item-quantity-filed">
                                    <button :disabled="Number(item.MAX_QUANTITY) <= 0 || Number(item.QUANTITY) >= Number(item.MAX_QUANTITY)"  @click="QuantityPlus(item)" class="basket-item-quantity-btn plus"></button>
                                </div>
                                <span><?=Loc::getMessage('VUE_BASKET_TOTAL_COUNT_TITLE')?>: {{ item.MAX_QUANTITY }}</span>
                            </td>
                            
                            <td class="basket-item-price">
                                <template v-if="item.PRICE.BASE_PRICE != item.PRICE.PRICE">
                                    <div class="price-container sale">
                                        <span>{{ FormatPrice(GetFullPrice(item)) }}</span>
                                        <span>{{ FormatPrice(GetFullPrice(item, true)) }}</span>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="price-container">{{ FormatPrice(GetFullPrice(item)) }}</div>
                                </template>
                            </td>

                            <td class="basket-item-action">
                                <div class="basket-item-action-container">
                                    <div class="basket-item-action-delete icon icon-delete" @click="DeleteItem(item)"></div>
                                </div>
                            </td>
                        </template>

                        <template v-else><?// Товар помеченный на удаление?>
                            <td class="basket-item-deleted-title" colspan="2">
                                Товар <a class="title" target="_blank" :href="item.DETAIL_PAGE_URL">{{item.NAME}}</a> был удален из корзины
                            </td>
                            <td class="basket-item-action" colspan="2">
                                <div class="basket-item-action-container">
                                    <div class="basket-item-action-restore" @click="RestoreItem(item)">Восстановить</div>
                                    <div class="basket-item-action-delete icon icon-delete" @click="DeleteItemNode(item)"></div>
                                </div>
                            </td>
                        </template>
                    </tr>
                </tbody>
            </table>
            <?$APPLICATION->ShowViewContent('block_under_basket_items');?>
        </div>

        <div class="total-block-sticky-container">
            <div class="total-block">
                <div class="items-price">Сумма: {{ FormatPrice(ItemsPrice) }}</div>
                <div class="sale-price">Скидка: {{ FormatPrice(TotalSalePrice) }}</div>
                <div class="total-price">Итого: {{ FormatPrice(TotalPrice) }}</div>
            </div>
        </div>
    </template>

    <template v-else>
        <span class="empty-basket">Корзина пуста</span>
    </template>

</div>
<script>
    BX.message({
        arParams: <?=CUtil::PhpToJSObject($arParams)?>,
        arResult: <?=CUtil::PhpToJSObject($arResult)?>,
    });
</script>