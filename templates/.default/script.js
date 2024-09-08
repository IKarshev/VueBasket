BX.ready(function(){
    BX.Vue.create({
        el: '#VueBasket',
        data: {
            arParams: BX.message('arParams'),
            arResult: BX.message('arResult'),
            SalePrice: 200,
        },
        computed: {
            ItemsPrice() { // Возвращаем полную стоимость товаров без скидок
                let price = 0;
                this.arResult.ITEMS.forEach(item => {
                    if (!item.DELETED_ITEM){
                        price += item.QUANTITY * item.PRICE.BASE_PRICE;
                    }
                });

                return price;
            },
            TotalSalePrice() { // Возвращает сумму скидок товаров
                let price = 0;
                this.arResult.ITEMS.forEach(item => {
                    if (!item.DELETED_ITEM){
                        price += item.QUANTITY * item.PRICE.DISCOUNT;
                    }
                });
                return price;
            },
            TotalPrice() { // Возвращаем проную стоимость товаров со скидками
                return Number(this.ItemsPrice) - Number(this.TotalSalePrice);
            },
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
            FormatPrice: function(price){ // Форматируем цену
                var decimal=2;
                var separator=' ';
                var decpoint = '.';
                var r=parseFloat(price)
                var exp10=Math.pow(10,decimal);
                r=Math.round(r*exp10)/exp10;
                rr=Number(r).toFixed(decimal).toString().split('.');
                b=rr[0].replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g,"\$1"+separator);
                r=(rr[1]?b+ decpoint +rr[1]:b);
                return this.arResult.PRICE_FORMAT.replace('#', r);
            },
            GetFullPrice: function(item, UseSalePrice = false){ // Возвращаем полную стоимость
                // TODO - форматирование цены, подстановка валюты из настроек
                price = (UseSalePrice) ? item.PRICE.PRICE : item.PRICE.BASE_PRICE;
                return item.QUANTITY * price;
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
        },
    });
});