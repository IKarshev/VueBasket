BX.ready(function(){
    BX.Vue.create({
        el: '#VueBasket',
        data: {
            arParams: BX.message('arParams'),
            arResult: BX.message('arResult'),
        },
        methods: {
            AmountPlus: function(index){
                // TODO - запрос на бэк
                this.arResult.ITEMS[index].QUANTITY++;
            },
            AmountMinus: function(index){
                // TODO - запрос на бэк
                if( this.arResult.ITEMS[index].QUANTITY > 1 ) this.arResult.ITEMS[index].QUANTITY--;
            },
            GetFullPrice(item) {
                // TODO - форматирование цены, подстановка валюты из настроек
                return item.QUANTITY * item.PRICE;
            },
        }
    });
});