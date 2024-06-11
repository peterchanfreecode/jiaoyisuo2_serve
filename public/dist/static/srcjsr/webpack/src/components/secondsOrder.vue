<template>
  <div :class="skins=='days'?'home whiteBg':'home'">
    <indexHeader></indexHeader>
    <div class="content flex between">
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
        <div class="flex">
          <div class="tran-box">
            <!-- <leverTransaction :leverData="leverData"></leverTransaction> -->
            <leverTransaction :leverTradeId="leverTradeId"></leverTransaction>
          </div>
         
        </div>
      </div>
      <div class="main-bottom">
         <div class="trade-box">
            <trade :leverTradeId="leverTradeId" :currencyName="currencyName"></trade>
            <!-- <trade></trade> -->
          </div>
      </div>
    </div>
  </div>
</template>

<script>
import indexHeader from "@/view/indexHeader";
import tv from "@/view/lever/secondTv";
import trade from "@/view/lever/second-trade";
import Kline from "@/view/lever/leverKline";
import leverTransaction from "@/view/lever/second_transactions";
export default {
  name: "leverDealCenter",
  components: {
    indexHeader: indexHeader,
    tv: tv,
    trade: trade,
    leverTransaction: leverTransaction,
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
      leverTradeId: 0,
      years: "",
      times: "",
      types: "seconds",
      skins: localStorage.getItem("skin") || "days",
      insurancType: [],
      userInsurancId: "",
      bmbBalance:'',
      currencyName:""
    };
  },
  watch: {
    $route(to, from) {
      if (this.leverData.currencyId != to.query.currencyId) {
        location.reload();
      }
      this.leverData.currencyId = to.query.currencyId;
      this.init();
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
    $("#app").css("background", "#000;");
    // document.getElementById("app").style("background", "background:#000;");
    document.querySelector("html").setAttribute("style", "background:#000;");
    var that = this;
    that.token = localStorage.getItem("token");
    that.leverData.legalId = this.$route.query.legalId || "";
    that.leverData.currencyId = this.$route.query.currencyId || "";
    
  },
  mounted() {
    var that = this;
    that.init();
    that.getDeal();
    setInterval(function() {
      if (localStorage.getItem("orderStatus") == 1) {
        that.getDeal();
      }
    }, 1);
  },

  beforeDestroy() {
    document.querySelector("html").removeAttribute("style");
    document.getElementById("app").removeAttribute("style");
  },
  methods: {
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
                    that.symbol =
                      res.data.message[i].quotation[j].currency_name +
                      "/" +
                      res.data.message[i].quotation[j].legal_name;
                    that.leverTradeId = res.data.message[i].quotation[j].id;
                    that.currencyName = res.data.message[i].quotation[j].currency_name;
                  }
                }
              }
            }
            that.quotationList = arr;
            if (that.symbol == "") {
              that.symbol = arr[0].currency_name + "/" + arr[0].legal_name;
              that.leverData.legalId = arr[0].legal_id;
              that.leverData.currencyId = arr[0].currency_id;
              that.leverTradeId = arr[0].id;
              that.currencyName = arr[0].currency_name;
            }
            that.marketSocket();
          }
        })
        .catch(error => {});
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
    getDeal() {
      var that = this;
      this.$http({
        url: "/api/microtrade/payable_currencies",
        method: "get",
        data: {},
        headers: { Authorization: that.token }
      }).then(res => {
        if (res.data.type == "ok") {
          var datas = res.data.message;
          if (datas.length > 0) {
            for (var i = 0; i < datas.length; i++) {
              if (datas[i].name == "AITB") {
                that.bmbBalance = datas[i].user_wallet.insurance_balance;
                that.insurancType = datas[i].insurance_types;
                if(datas[i].user_insurance){
                  that.userInsurancId =datas[i].user_insurance.insurance_type_id;
                }
                
              }
            }
          }
        }
      });
    }
  }
};
</script>

<style scoped>
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
/* .whiteBg >>> .trade-box {
  background-color: #fff;
} */
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
}
.whiteBg >>> .content .el-input__inner {
  background-color: #fff;
  border: 1px solid #e9e9e9;
}
.whiteBg >>> .content .el-select {
  background-color: #fff;
}
.whiteBg >>> .el-select-dropdown {
  background-color: #fff;
}
/* .whiteBg >>> .share-rights {
  background-color: #fff;
} */
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
.whiteBg >>> .curency-list span {
  color: #333;
  border: 1px solid #e9e9e9;
}
.whiteBg >>> .share-rights p {
  color: #333;
  border: 1px solid #e9e9e9;
}
.whiteBg >>> .mult-content p {
  color: #333;
  border: 1px solid #e9e9e9;
}
.content {
  width: calc(100% - 40px);
  min-width: 1200px;
  margin: 0 auto 10px;
}
.main-top {
  width: 73%;
  margin-right: 1px;
}
/* .tv-box {
  height: 550px;
} */
.exchange-box {
  background-color: #1e2b34;
  height: 550px;
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
  width: 26%;
}
.tran-box {
  background-color: #1e2b34;
  margin-top: 1px;
  height: 467px;
  overflow-y: scroll;
  width: 100%;
  margin-top: 10px;
}
.tran-box::-webkit-scrollbar {
  width: 0;
}

.trade-box {
  /* background-color: #1e2b34; */
  margin-top: 1px;
  height: 467px;
  /* overflow-y: scroll; */
}
/* .footer {
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
} */
.ins-header{
  line-height: 50px;
  padding-left: 15px;
}
.ins-header div{
  margin-left: 10px;
}
.colorGrey{
  display: inline-block;
  height: 30px;
  line-height: 30px;
  margin-top: 7px;
  padding: 0 8px;
  border: 1px solid #e6ecf2;
  border-radius: 4px;
  margin-right: 10px;
}
.ins-header .active{
  border: 1px solid #f0b90b;
  color: #f0b90b;
}
</style>



// WEBPACK FOOTER //
// src/components/secondsOrder.vue