BX.ready(function(){
    BX.Vue.create({
        el: '#VueBasket',
        data: {
            items: BX.message('items'),
        },
        template: '<ul><li v-for="(item, index) in this.items">{{ item }}</li></ul>',
    })
});