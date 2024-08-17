BX.ready(function(){
    BX.Vue.create({
        el: '#VueBasket',
        data: {
            arParams: BX.message('arParams'),
            arResult: BX.message('arResult'),
            SalePrice: 200,
        },
        computed: {
            ItemsPrice() { // Возвращаем стоимость товаров
                let ItemsPrice = 0;
                this.arResult.ITEMS.forEach(item => {
                    if (!item.DELETED_ITEM){
                        ItemsPrice += item.QUANTITY * item.PRICE;
                    }
                });

                return ItemsPrice;
            },

            TotalPrice() { // Возвращаем итоговую стоимость
                return Number(this.ItemsPrice) - Number(this.SalePrice);
            }
        },
        methods: {
            QuantityPlus: function(item){ // Увеличиваем количество
                if( Number(item.QUANTITY) < Number(item.MAX_QUANTITY) ){
                    this.oldQuantity = item.QUANTITY;
                    item.QUANTITY++;
                    this.QuantityChange(item);
                }
            },
            QuantityMinus: function(item){ // Уменьшаем количество
                if( Number(item.QUANTITY) > 1 ){
                    this.oldQuantity = item.QUANTITY;
                    item.QUANTITY--;
                    this.QuantityChange(item);
                }
            },
            GetFullPrice: function(item){ // Возвращаем полную стоимость
                // TODO - форматирование цены, подстановка валюты из настроек
                return item.QUANTITY * item.PRICE;
            },
            QuantityChange: function(item){ // Изменяем количество

                if ( Number(item.QUANTITY) < 1 || item.QUANTITY === '') item.QUANTITY = 1;
                if ( Number(item.QUANTITY) > Number(item.MAX_QUANTITY)) item.QUANTITY = item.MAX_QUANTITY;

                var request = BX.ajax.runComponentAction('IvanKarshev:VueBasket', 'ChangeItemQuantity', {
                    mode: 'class',
                    data: {
                        'ID': item.ID,
                        'QUANTITY': item.QUANTITY,
                    },
                }).then(
                    response => {
                        item.QUANTITY = response['data']['NEW_QUANTITY'];
                        item.MAX_QUANTITY = response['data']['MAX_QUANTITY'];
                    },
                    error => {
                        item.QUANTITY = this.oldQuantity;
                    }
                );
            },
            DeleteItem: function(item){ // Удаляем элемент из корзины
                // BX.Vue.set(item, 'DELETED_ITEM', true); // Устанавливаем значение реактивно
                var request = BX.ajax.runComponentAction('IvanKarshev:VueBasket', 'DeleteItem', {
                    mode: 'class',
                    data: {
                        'ID': item.ID,
                    },
                }).then(
                    response => {
                        BX.Vue.set(item, 'DELETED_ITEM', true); // Устанавливаем значение реактивно
                    },
                    error => {
                        console.log(error);
                    }
                );
            },
            DeleteItemNode: function(item){ // Удаляем ноду уделанного товара
                let index = this.arResult.ITEMS.indexOf(item);
                if (index !== -1) this.arResult.ITEMS.splice(index, 1);
            },
            RestoreItem: function(item){ // Восстанавливаем товар
                var request = BX.ajax.runComponentAction('IvanKarshev:VueBasket', 'RestoreItem', {
                    mode: 'class',
                    data: {
                        'PRODUCT_ID': item.PRODUCT_ID,
                        'QUANTITY': item.QUANTITY,
                    },
                }).then(
                    response => {
                        item.DELETED_ITEM = false;
                        item.QUANTITY = response.data.QUANTITY;
                    },
                    error => {
                        console.log(error);
                    }
                );
            },
        }
    });
});