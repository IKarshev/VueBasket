BX.ready(function(){
    BX.Vue.create({
        el: '#VueBasket',
        data: {
            arParams: BX.message('arParams'),
            arResult: BX.message('arResult'),
        },
        methods: {
            QuantityPlus: function(item){
                if( Number(item.QUANTITY) < Number(item.MAX_QUANTITY) ){
                    this.oldQuantity = item.QUANTITY;
                    item.QUANTITY++;
                    this.QuantityChange(item);
                }
            },
            QuantityMinus: function(item){
                if( Number(item.QUANTITY) > 1 ){
                    this.oldQuantity = item.QUANTITY;
                    item.QUANTITY--;
                    this.QuantityChange(item);
                }
            },
            GetFullPrice(item) {
                // TODO - форматирование цены, подстановка валюты из настроек
                return item.QUANTITY * item.PRICE;
            },
            QuantityChange: function(item){

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
            }
        }
    });
});