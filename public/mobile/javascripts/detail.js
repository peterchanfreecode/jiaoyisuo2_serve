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
            inputMoney: '',
        };
    },
    mounted() {
        this.listAjax();
        this.getCensus();
    },
    updated() {
        window.changeLg();
    },
    computed: {
        importedProducts() {
            if (!this.inputMoney) return 0;
            return (this.card.rate / 100) * this.card.day * this.inputMoney;
        },
    },
    methods: {
        handerAllClick() {
            this.inputMoney = this.card.change_balance;
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
                    url: 'deposit/depositDetail',
                    type: 'post',
                    data: {
                        id: window.location.search.replace('?id=', ''),
                    },
                },
                function (res) {
                    if (res.type != 'ok') return;
                    _.card = res.message;
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
        taderSubmit() {
            var that = this;
            if (!that.inputMoney || Number(that.inputMoney) < Number(that.card.save_min)) {
                layer_msg((window.getLocal('language') === 'zh' ? '最小存币数量 ' : 'Amount of Deposited Coins ') + that.card.save_min);
                return;
            }
            layer_loading();
            initDataTokens(
                {
                    url: 'deposit/buy',
                    type: 'post',
                    data: {
                        id: that.card.id,
                        num: that.inputMoney,
                    },
                },
                function (res) {
                    layer_msg(res.message);
                    if (res.type != 'ok') {
                        return;
                    }
                    setTimeout(() => {
                        window.location.href = 'hosted.html';
                    }, 1000);
                }
            );
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
