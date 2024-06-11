<template>
  <div :class="skins=='days'?'home whiteBg':'home'">
    <indexHeader></indexHeader>
    <div class="flex content between">
      <div class="main-top">
        <div class="tv-box">
          <tv
            v-if="this.quotationList.length > 0"
            :quotationList="quotationList"
            :symbol="symbol"
            :types="types"
          ></tv>
        </div>
        <div class="Kline">
          <kline :symbol="symbol"></kline>
        </div>
        <div class="tran-box">
          <leverTransaction :leverData="leverData"></leverTransaction>
        </div>
      </div>
      <div class="main-bottom">
        <div class="balance-box">
          <div class="title flex">
            <img src="../../static/imgs/money.png" alt>
            <p>{{$t('miscro.contractBalance')}}</p>
          </div>
          <ul class="lever-account">
            <li
              v-for="item in walletList"
              :key="item.id"
              v-show="item.is_lever == 1"
            >{{item.lever_balance}} {{item.currency_name}}</li>
          </ul>
        </div>
        <div class="exchange-box">
          <exchange :topBuy="topBuy" :symbol="symbol"></exchange>
        </div>
        <div class="trade-box">
          <trade :leverTrade="leverTrade"></trade>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import indexHeader from "@/view/indexHeader";
import tv from "@/view/lever/leverTv";
import Kline from "@/view/lever/leverKline";
import exchange from "@/view/lever/lever_exchange";
import trade from "@/view/lever/lever_trade";
import leverTransaction from "@/view/lever/lever_transactions";
export default {
  name: "leverDealCenter",
  components: {
    indexHeader,
    tv,
    exchange,
    trade,
    leverTransaction,
    Kline
  },
  data() {
    return {
      isRouterAlive: true,
      token: localStorage.getItem("token"),
      quotationList: [],
      leverData: {
        legalId: "",
        currencyId: ""
      },
      marketData: {},
      currencyData: {},
      symbol: "",
      topBuy: {
        buyList: [],
        sellList: [],
        newsPrice: ""
      },
      leverTrade: {
        muilList: {},
        leverLimit: {},
        balance: "",
        currencyList: {},
        newPrice: ""
      },
      years: "",
      times: "",
      types: "lever",
      skins: localStorage.getItem("skin") || "days",
      walletList: []
    };
  },
  watch: {
    $route(to, from) {
      if (this.leverData.currencyId != to.query.currencyId) {
        location.reload();
      }
      this.leverData.currencyId = to.query.currencyId;
      this.init();
      this.positionSocket();
      // return this.init();
    }
  },
  beforeCreate() {
    if (
      localStorage.getItem("skin") &&
      localStorage.getItem("skin") == "nights"
    ) {
      document.querySelector("html").setAttribute("style", "background:#000;");
      document.querySelector("body").setAttribute("style", "background:#000;");
    } else {
      document
        .querySelector("html")
        .setAttribute("style", "background:#f8f6f6;");
      document
        .querySelector("body")
        .setAttribute("style", "background:#f8f6f6;");
    }
  },
  created() {
    var that = this;
    that.token = localStorage.getItem("token");
    that.leverData.legalId = this.$route.query.legalId || "";
    that.leverData.currencyId = this.$route.query.currencyId || "";
    var myDate = new Date();
    var yearText = myDate.getFullYear();
    var monthText = myDate.getMonth();
    var dayText = myDate.getDate();
    var hourText = myDate.getHours();
    var minutes = myDate.getMinutes();
    var seconds = myDate.getSeconds();
    that.years = yearText + "-" + monthText + "-" + dayText;
    that.times = hourText + ":" + minutes + ":" + seconds;
    setInterval(function() {
      myDate = new Date();
      yearText = myDate.getFullYear();
      monthText = myDate.getMonth() - 0 + 1;
      dayText = myDate.getDate();
      hourText = myDate.getHours();
      minutes = myDate.getMinutes();
      seconds = myDate.getSeconds();
      if (monthText < 10) {
        monthText = "0" + monthText;
      }
      if (dayText < 10) {
        dayText = "0" + dayText;
      }
      if (hourText < 10) {
        hourText = "0" + hourText;
      }
      if (minutes < 10) {
        minutes = "0" + minutes;
      }
      if (seconds < 10) {
        seconds = "0" + seconds;
      }
      that.years = yearText + "-" + monthText + "-" + dayText;
      that.times = hourText + ":" + minutes + ":" + seconds;
    }, 1000);
  },
  mounted() {
    var that = this;
    that.init();
    that.positionSocket();
    that.getWallet();
  },

  beforeDestroy() {
    document.querySelector("html").removeAttribute("style");
    document.querySelector("body").removeAttribute("style");
  },
  methods: {
    getWallet() {
      var that = this;
      this.$http({
        url: "/api/" + "wallet/list",
        method: "post",
        data: {},
        headers: { Authorization: that.token }
      })
        .then(res => {
          if (res.data.type == "ok") {
            console.log(res);
            that.walletList = res.data.message.lever_wallet.balance;
          } else {
            return;
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    init() {
      var that = this;
      this.$http({
        url: "/api/" + "currency/quotation_new",
        method: "get",
        data: {}
      })
        .then(res => {
          if (res.data.type == "ok") {
            var arr = [];
            var arr2 = [];
            for (var i = 0; i < res.data.message.length; i++) {
              for (var j = 0; j < res.data.message[i].quotation.length; j++) {
                if (res.data.message[i].quotation[j].is_display == 1) {
                  arr.push(res.data.message[i].quotation[j]);
                  if (
                    that.leverData.legalId ==
                      res.data.message[i].quotation[j].legal_id &&
                    that.leverData.currencyId ==
                      res.data.message[i].quotation[j].currency_id
                  ) {
                    that.leverTrade.currencyList =res.data.message[i].quotation[j];
                    that.symbol =res.data.message[i].quotation[j].currency_name +"/" +res.data.message[i].quotation[j].legal_name;
                  }
                }
              }
            }
            that.quotationList = arr;
            if (that.symbol == "") {
              that.symbol = arr[0].currency_name + "/" + arr[0].legal_name;
              that.leverData.legalId = arr[0].legal_id;
              that.leverData.currencyId = arr[0].currency_id;
              that.leverTrade.currencyList = arr[0];
            }

            that.getDeal();
            localStorage.setItem("spread", that.leverTrade.currencyList.spread);
            that.marketSocket();
          }
        })
        .catch(error => {});
    },
    getDeal() {
      var that = this;
      this.$http({
        url: "/api/lever/deal",
        method: "post",
        data: {
          legal_id: that.leverData.legalId,
          currency_id: that.leverData.currencyId
        },
        headers: { Authorization: that.token }
      }).then(res => {
        if (res.data.type == "ok") {
          that.leverTrade.muilList = res.data.message.multiple;
          that.leverTrade.leverLimit = res.data.message.lever_share_limit;
          that.leverTrade.balance = res.data.message.user_lever;
          that.leverTrade.newPrice = res.data.message.last_price;
          var arr1 = [];
          var arr2 = [];
          var arr3 = [];
          var arr4 = [];
          if (res.data.message.lever_transaction.in.length > 0) {
            var buyList = res.data.message.lever_transaction.in;
            for (var i in buyList) {
              arr1 = [];
              arr1[0] = buyList[i].price;
              arr1[1] = buyList[i].number;
              arr2.push(arr1);
            }
          }
          if (res.data.message.lever_transaction.out.length > 0) {
            var sellList = res.data.message.lever_transaction.out.reverse();
            for (var i in sellList) {
              arr3 = [];
              arr3[0] = sellList[i].price;
              arr3[1] = sellList[i].number;
              arr4.push(arr3);
            }
          }

          that.topBuy.buyList = arr2;
          that.topBuy.sellList = arr4;
          that.topBuy.newsPrice = res.data.message.last_price;
          that.buySocket();
        }
      });
    },
    // 行情socket
    marketSocket() {
      var that = this;
      that.$socket.emit("login", localStorage.getItem("user_id"));
      that.$socket.on("daymarket", msg => {
        if (msg.type == "daymarket") {
          for (var i = 0; i < that.quotationList.length; i++) {
            if (
              that.quotationList[i].legal_id == msg.legal_id &&
              that.quotationList[i].currency_id == msg.currency_id
            ) {
              that.quotationList[i].now_price = msg.now_price;
              // that.quotationList[i].spread = msg.spread;
              that.quotationList[i].change = msg.change;
              that.quotationList[i].volume = msg.volume;
            }
          }
        }
      });
    },
    // 盘口数据socket
    buySocket() {
      var that = this;
      that.$socket.emit("login", localStorage.getItem("user_id"));
      that.$socket.on("market_depth", msg => {
        if (msg.type == "market_depth") {
          var inData = msg.bids;
          var outData = msg.asks;
          if (
            msg.currency_id == that.leverData.currencyId &&
            msg.legal_id == that.leverData.legalId
          ) {
            that.topBuy.buyList = inData;
            that.topBuy.sellList = outData;
          }
        }
      });
    },
    //更新价格socket
    positionSocket() {
      var that = this;
      that.$socket.emit("login", localStorage.getItem("user_id"));
      that.$socket.on("kline", msg => {
        if (msg.type == "kline") {
          if (that.symbol == msg.symbol) {
            that.topBuy.newsPrice = msg.close;
            that.leverTrade.newPrice = msg.close;
          }
        }
      });
    }
  }
};
</script>

<style scoped>
/* *::-webkit-scrollbar-corner {
  background: #1e2b34;
}
*::-webkit-scrollbar-thumb {
  background: #2a4253;
  border-radius: 4px;
}
*::-webkit-scrollbar-track {
  background: #1e2b34;
} */
.home {
  color: #fff;
  height: 100%;
  width: 100%;
  min-width: 1200px;
  overflow: hidden;
  -moz-user-select: all;
  -ms-user-select: all;
  user-select: all;
  -webkit-user-select: all;
  margin: 66px auto 0;
  background-color: #000;
}
.whiteBg {
  background-color: #f8f6f6;
  color: #333;
}
.whiteBg >>> .lists {
  background-color: #fff;
}
.whiteBg >>> .lists li {
  background-color: #fff;
  border-bottom: 1px solid #e9e9e9;
  color: #333;
}
.whiteBg >>> .lists .active {
  background-color: #f8f6f6;
}
.whiteBg >>> .Kline {
  background-color: #fff;
}
.whiteBg .exchange-box {
  background-color: #fff;
}
.whiteBg >>> .new_price {
  color: #333;
}
.whiteBg >>> .new_price {
  border-bottom: 1px solid #e9e9e9;
}
.whiteBg >>> .line {
  border-bottom: 1px solid #e9e9e9;
}
.whiteBg >>> .list-item li span,
.whiteBg >>> .exchange_title span {
  color: #333;
}
.whiteBg .tran-box {
  background-color: #fff;
  color: #333;
  margin-top: 10px;
}
.whiteBg >>> .list_head {
  border-bottom: 1px solid #e9e9e9;
}
.whiteBg >>> .fColor1 {
  color: #333;
}
.whiteBg >>> .list_head {
  color: #333;
}
.whiteBg >>> .trade-box {
  background-color: #fff;
}
.whiteBg >>> .tabs .active {
  background-color: #f0b90b;
  color: #fff;
}
.whiteBg >>> .tabs {
  background-color: #fff;
  border-bottom: 1px solid #e9e9e9;
}
.whiteBg >>> .rights {
  background-color: #fff;
  border: 1px solid #e9e9e9;
  color: #333;
}
.whiteBg >>> .content .el-input__inner {
  background-color: #fff;
  border: 1px solid #e9e9e9;
}
.whiteBg >>> .content .el-select {
  background-color: rgba(0,0,0,0);
  border: none;
}
.whiteBg >>> .el-select-dropdown {
  background-color: #fff;
}
.whiteBg >>> .share-rights {
  background-color: #fff;
}
.whiteBg >>> .share-rights input {
  background-color: #fff;
  border: 1px solid #e9e9e9;
  color: #333;
}
.whiteBg >>> .trade .el-select .el-input.is-focus .el-input__inner {
  border-color: #f1f1f1;
}
.whiteBg >>> .footer-content {
  background-color: #fff;
}
.whiteBg >>> .footer {
  background-color: #fff;
}
.content {
  width: calc(100% - 40px);
  min-width: 1200px;
  margin: 0 auto 10px;
}
.main-top {
  width: 71%;
  margin-right: 1px;
}
/* .tv-box {
  height: 550px;
} */
.exchange-box {
  background-color: #1e2b34;
  /* height: 550px; */
  margin-top: 15px;
  margin-bottom: 15px;
}
.username {
  line-height: 30px;
  width: 100%;
  padding: 0 10px;
  border-bottom: 1px solid #2f3d45;
}
.username p {
  color: #2b88e5;
  font-size: 12px;
}
.username img {
  width: 17px;
}
.main-bottom {
  width: calc(29% - 20px);
}
.whiteBg .balance-box {
  background-color: #fff;
}
.balance-box{
  width: 100%;
  padding: 15px 30px;
  margin-bottom: 2px;
  background-color: #1e2b34;
  margin-top: 10px;
}
.balance-box .title p{
  color: #fff;
}
.whiteBg .balance-box .title p{
  color: #333;
}
.balance-box .title {
  width: 100%;
  font-size: 16px;
  color: #333;
  height: 25px;
}
.balance-box .num {
  width: 100%;
  height: 85px;
  line-height: 85px;
  text-align: center;
  font-size: 18px;
  color: #000;
  font-weight: bold;
}
.tran-box {
  background-color: #1e2b34;
  margin-top: 1px;
  height: 467px;
  overflow-y: scroll;
}
.tran-box::-webkit-scrollbar {
  width: 0;
}
/* .tran-box::-webkit-scrollbar-corner {
  width: 1px;
  background: #1e2b34;
}
.tran-box::-webkit-scrollbar-thumb {
  background: #2a4253;
  border-radius: 4px;
}
.tran-box::-webkit-scrollbar-track {
  background: #1e2b34;
} */
.trade-box {
  background-color: #1e2b34;
  margin-top: 1px;
  height: 467px;
  /* overflow-y: scroll; */
}
.footer {
  width: calc(100% - 2px);
  height: 30px;
  line-height: 30px;
  background-color: #1e2b34;
  margin-top: 1px;
  margin-left: 1px;
}
.footer-content {
  width: 96%;
  margin: 0 auto;
  color: rgb(128, 137, 142);
  font-size: 12px;
}
.title img {
  margin-right: 5px;
}
.lever-account{
  text-align: center;
  margin-top: 10px;
  font-size: 16px;
}
</style>



// WEBPACK FOOTER //
// src/components/lever_dealCenter.vue