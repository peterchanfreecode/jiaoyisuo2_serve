var vm = new Vue({
    el: '#app',
    data() {
        return {
            isBuy: false,
            totalCount: 0.0,
            card: [],
            current: null,
            detail: null,
            census: {},
        };
    },
    mounted() {
        this.listAjax();
        this.getCensus();
    },
    updated() {
        window.changeLg();
    },
    methods: {
        hanlderDetailClick(item){
            window.location.href = 'detail.html?id='+item.id;
        },
        buyClick(item) {
            var _ = this;
            if (!item.money || Number(item.money) < Number(item.save_min)) {
                layer_msg((window.getLocal('language') === 'zh' ? '最小存币数量 ' : 'Amount of Deposited Coins ') + item.save_min);
                return;
            }
            _.current = item;
            _.isBuy = true;
            _.getDetail(item);
        },
        orderClick() {
            window.location.href = 'lockming.html';
        },
        getPlace(item) {
            return (window.getLocal('language') === 'zh' ? '最小存币数量 ' : 'Amount of Deposited Coins ') + item.save_min;
        },
        getLevel(item) {
            return item.level;
        },
        getCensus() {
            var _ = this;
            initDataTokens(
                {
                    url: 'deposit/census',
                    type: 'post',
                },
                function (res) {
                    if (res.type != 'ok') return;
                    _.census = res.message;
                }
            );
        },
        listAjax() {
            var _ = this;
            layer_loading();
            initDataTokens(
                {
                    url: 'deposit/config',
                    type: 'post',
                },
                function (res) {
                    if (res.type != 'ok') return;
                    const handle = property => {
                        return function (a, b) {
                            const val1 = a[property];
                            const val2 = b[property];
                            return val1 - val2;
                        };
                    };
                    _.card = res.message.sort(handle('day'));
                }
            );
        },
        getDetail(item) {
            var _ = this;
            initDataTokens(
                {
                    url: 'deposit/detail',
                    type: 'post',
                    data: {
                        id: item.id,
                        num: item.money,
                    },
                },
                function (res) {
                    if (res.type != 'ok') return;
                    _.detail = res.message;
                }
            );
        },
        change(e, item) {
            var _ = this;
            item.money = e.target.value;
            item.total = (parseFloat(item.rate) * item.day * Number(e.target.value)) / 100;
            _.$forceUpdate();
        },
        submit() {
            var _ = this;
            if (!_.detail) return;
            layer_loading();
            initDataTokens(
                {
                    url: 'deposit/buy',
                    type: 'post',
                    data: {
                        id: _.detail.id,
                        num: _.detail.num,
                    },
                },
                function (res) {
                    layer_msg(res.message);
                    if (res.type != 'ok') {
                        return;
                    }
                    _.isBuy = false;
                    _.getCensus();
                }
            );
        },
    },
});
