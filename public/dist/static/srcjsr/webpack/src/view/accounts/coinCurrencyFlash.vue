<template>
  <div :class="skins=='days'?'coinCurrencyFlash':'coinCurrencyFlash balck'">
    <div class="top">
      <div class="real_time_price">
        <p class="tc">{{$t('miscro.realPrice')}}</p>
        <p class="tc">{{prices}}</p>
      </div>
      <!-- <div>
        {{$t('miscro.text10')}}:{{priceFub}}
      </div> -->
      <div class="convertibility-header flex between mt20">
        <div class="tl">
          <p>{{$t('miscro.currencyExchange')}}</p>
          <el-dropdown trigger="click" @command="selectedOne">
            <!-- <img class="logos" :src="leftLogo" alt=""> -->
            <span class="el-dropdown-link">
              <!-- <img class="logos" :src="leftLogo" alt=""> -->
              {{value1}}<i class="el-icon-arrow-down el-icon--right"></i>
            </span>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item
                :command="{id:item.id,name:item.name,logos:item.logo,minNUm:item.min_number,maxNUm:item.max_number,prices:item.price,balances:item.balance,pbPrice:item.to_pb_price}"
                v-for="item in leftList" :key="item.id">
                {{item.name}}</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </div>
        <!-- <img class="tab-logo" src="../../../static/imgs/transer.png" alt="" @click="coinTab"> -->
        <img class="tab-logo" src="../../../static/imgs/transer.png" alt="">
        <div class="tl">
          <p>{{$t('miscro.currencyExchangeIn')}}</p>
          <el-dropdown trigger="click" @command="selectedTwo">
            <!-- <img class="logos" :src="rightLogo" alt=""> -->
            <span class="el-dropdown-link">
              <!-- <img class="logos" :src="rightLogo" alt=""> -->
              {{value2}}<i class="el-icon-arrow-down el-icon--right"></i>
            </span>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item
                :command="{id:item.id,name:item.name,logos:item.logo,minNUm:item.min_number,maxNUm:item.max_number,prices:item.price,balances:item.balance,pbPrice:item.to_pb_price}"
                v-for="item in rightList" :key="item.id">{{item.name}}
              </el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </div>
      </div>
      <div class="content flex between">
      <!-- <span>{{$t('cuy.price')}}</span> -->
        <input type="number" v-model="inputPrice" :placeholder="$t('lever.pprice')">
        <!-- <span>{{$t('td.num')}}</span> -->
        <input type="number" v-model="inputNUm" :placeholder="$t('fat.pnums')">
      </div>
			<div class="mr20">
				<p class="mt10">{{$t('miscro.cashableBalance')}}{{balance}}</p>
				<!-- <p class="mt10">{{$t('miscro.minimumCashable')}}{{minNumber}}</p>
				<p class="mt10">{{$t('miscro.maximumCashable')}}{{maxNumber}}</p> -->
			</div>
			<div class="btns">
				<button type="button" class="white" @click="sumbit()">{{$t('jc.exnow')}}</button>
			</div>
			<div class="tc mt10" style="color:#f0b90b;">{{$t('miscro.automaticallys')}}</div>
    </div>
    <div class="btm">
      <div class="order-list">
        <p class="order-list-text">{{$t('jc.record')}}</p>
        <ul>
          <li class="flex between ft10 tc alcenter">
            <p class="width1">{{$t('fat.type')}}</p>
            <p class="width2">{{$t('td.num')}}</p>
            <p class="width3">{{$t('cuy.price')}}</p>
            <p class="width3">{{$t('td.time')}}</p>
            <p class="width2">{{$t('fat.status')}}</p>
          </li>
          <li class="flex between ft10 tc alcenter" v-for="item in orderList" :key="item.id">
            <p class="width1">{{item.l_currency}}{{$t('miscro.match')}}{{item.r_currency}}</p>
            <p class="width2">{{item.num}}</p>
            <p class="width3">{{item.price}}</p>
            <p class="width3">{{item.review_time}}</p>
            <p class="width2">{{item.status_name}}</p>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>
<script>
import "@/lib/clipboard.min.js";
import "@/lib/jquery.qrcode.min.js";
export default {
  name: "coinCurrencyFlash",
  data() {
    return {
      selectValue: '',
			currencyName: '',
			value1: '',
			value2: '',
			leftList: [],
			rightList: [],
			datas: {},
			leftLogo: '',
			rightLogo: '',
			prices: '',
			inputPrice: '',
			inputNUm: '',
			balance: '0.00',
			maxNumber: '',
			minNumber: '',
			leftId: '',
			rightId: '',
			orderList: [],
      typeTab: 1,
      skins: localStorage.getItem("skin") || "days",
      priceFub:''
    };
  },
  beforeCreate() {
    if (
      localStorage.getItem("skin") &&
      localStorage.getItem("skin") == "nights"
    ) {
      document
        .querySelector("html")
        .setAttribute("style", "background-color:#000;");
    } else {
      document
        .querySelector("html")
        .setAttribute("style", "background-color:#f8f6f6;");
    }
  },
  beforeDestroy() {
    document.querySelector("html").removeAttribute("style");
  },
  created() {
    var that = this;
    that.token = localStorage.getItem("token") || "";
  },

  mounted() {
    var that = this;
    that.getinfo()
    that.getList()
  },
  methods: {
    getinfo() {
       var that = this;
       that.$http({
        url: "/api/" + "wallet/flashAgainstList",
        method: "get",
        data: {},
        headers: { Authorization: that.token }
      })
      .then(res => {
        if (res.data.type == 'ok') {
          console.log(res.data.message)
					that.datas = res.data.message;
					that.leftList = res.data.message.left;
					that.rightList = res.data.message.right;
					that.value1 = res.data.message.left[0].name;
					that.leftLogo = res.data.message.left[0].logo;
					that.value2 = res.data.message.right[0].name;
					that.rightLogo = res.data.message.right[0].logo;
					that.prices = res.data.message.left[0].price;
					that.maxNumber = res.data.message.left[0].max_number;
					that.minNumber = res.data.message.left[0].min_number;
					that.leftId = res.data.message.left[0].id;
					that.rightId = res.data.message.right[0].id;
          that.balance = res.data.message.left[0].balance;
          that.priceFub = res.data.message.left[0].to_pb_price;
				}
      })
      .catch(error => {
        console.log(error);
      })
    },
    getList() {
      var that = this;
      that.$http({
        url: "/api/" + "wallet/myFlashAgainstList",
        method: "get",
        data: {},
        headers: { Authorization: that.token }
      })
      .then(res => {
        if (res.data.type == 'ok') {
          that.orderList = res.data.message.data;
        }
      })
      .catch(error => {
        console.log(error);
      })
    },
    selectedOne(command) {
      var that = this;
      that.value1 = command.name;
      that.leftId = command.id;
      that.leftLogo = command.logos;
      // if (that.typeTab == 1) {
        that.prices = command.prices;
        that.maxNumber = command.maxNUm;
        that.minNumber = command.minNUm;
        that.balance = command.balances;
        that.priceFub = command.pbPrice;
      // }
    },
    selectedTwo(command) {
      var that = this;
      that.value2 = command.name;
      that.rightId = command.id;
      that.rightLogo = command.logos;
      // if(that.typeTab == 2){
      //   that.prices = command.prices;
      //   that.maxNumber = command.maxNUm;
      //   that.minNumber = command.minNUm;
      //   that.balance = command.balances;
      // }
    },
    coinTab() {
      var that = this;
      that.typeTab = that.typeTab == 1 ? 2 : 1;
      if (that.typeTab == 1) {
        that.leftList = that.datas.left;
        that.rightList = that.datas.right;
        that.value1 = that.datas.left[0].name;
        that.leftLogo = that.datas.left[0].logo;
        that.value2 = that.datas.right[0].name;
        that.rightLogo = that.datas.right[0].logo;
        that.prices = that.datas.left[0].price;
        that.maxNumber = that.datas.left[0].max_number;
        that.minNumber = that.datas.left[0].min_number;
        that.leftId = that.datas.left[0].id;
        that.rightId = that.datas.right[0].id;
        that.balance = that.datas.left[0].balance;
        that.priceFub = that.datas.left[0].to_pb_price;
      } else {
        that.leftList = that.datas.right;
        that.rightList = that.datas.left;
        that.value1 = that.datas.right[0].name;
        that.leftLogo = that.datas.right[0].logo;
        that.value2 = that.datas.left[0].name;
        that.rightLogo = that.datas.left[0].logo;
        that.prices = that.datas.right[0].price;
        that.maxNumber = that.datas.left[0].max_number;
        that.minNumber = that.datas.left[0].min_number;
        that.leftId = that.datas.right[0].id;
        that.rightId = that.datas.left[0].id;
        that.balance = that.datas.right[0].balance;
        that.priceFub = that.datas.right[0].to_pb_price;
      }
    },
    sumbit() {
				let that = this;
				if (!that.inputPrice) {
					layer.msg(that.$t('lever.pprice'));
					return false;
				}
				if (!that.inputNUm) {
					layer.msg(that.$t('fat.pnums'));
					return false;
        }
        var i = layer.load();
        that.$http({
          url: "/api/" + "wallet/flashAgainst",
          method: "post",
          data: {
            l_currency_id: that.leftId,
						price: that.inputPrice,
						num: that.inputNUm,
						r_currency_id: that.rightId,
          },
          headers: { Authorization: that.token }
        })
        .then(res => {
          layer.close(i);
          layer.msg(res.data.message)
          if (res.data.type == 'ok') {
            setTimeout(function () {
							location.reload();
						}, 1000)
          }
        })
        .catch(error => {
          console.log(error);
        })
			},
  },
};
</script>
<style scoped>
.coinCurrencyFlash {
  width: 100%;
  margin-top: 20px;
}
.coinCurrencyFlash .top,.btm {
  width: 100%;
  background-color: #fff;
  box-shadow: 0 0 29px 0 rgba(32, 36, 48, 0.08);
  border-radius: 2px;
  padding: 20px 0;
  box-sizing: border-box;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  font-size: 15px;
  padding-left: 30px;
  padding-right: 30px;
}
.coinCurrencyFlash .btm{
  padding-top: 10px;
  margin: 10px 0;
}
.real_time_price{
  height: 30px;
  width: 100%;
  line-height: 30px;
  display: flex;
}
.real_time_price p{
  font-size: 16px;
  padding-right: 20px;
}
.convertibility-header{
  width: 70%;
  height: 60px;
}
.convertibility-header .tl{
  width: 20%;
  text-align: center;
}
.tl p{
  font-size: 16px;
}
.el-dropdown-link {
  cursor: pointer;
  color: #f0b90b;
}
.el-icon-arrow-down {
    font-size: 14px;
}
.el-dropdown{
  height: 30px;
  line-height: 30px;
}
.content {
    margin: 0 20px;
    width: 80%;
  }

  .content span {
    display: block;
    width: 60px;
  }

  .content input {
    margin-top: 20px;
    width: 44%;
    border: 1px solid #e9e9e9;
    line-height: 30px;
    padding: 0 10px;
  }

  .password {
    line-height: 40px;
  }

  .password input {
    padding-left: 4px;
  }

  .btns {
    height: 40px;
    line-height: 40px;
    border-radius: 2px;
    background-color: #f0b90b;
    margin-top: 40px;
    cursor: pointer;
  }

  .btns button {
    width: 100%;
    background-color: rgba(0, 0, 0, 0);
    border: none;
    outline: none;
    cursor: pointer;
  }
  .white{
    color: #fff;
  }
  #currency {
    padding: 0 10px;
    border: 1px solid #e9e9e9;
    width: 120px;
    line-height: 30px;
    height: 30px;

  }

  .logos {
    width: 20px;
    height: 20px;
    margin-right: 5px;
  }

  .tab-logo {
    width: 50px;
    height: 50px;
    margin-top: 5px;
    cursor: pointer;
  }

  .coin-currency-flash {
    padding-bottom: 20px;
  }

  .width1 {
    width: 23%;
  }

  .width2 {
    width: 15%;
  }

  .width3 {
    width: 26.5%;
  }

  .order-list {
    width: 100%;
    margin-top: 10px;
  }

  .order-list ul {
    width: 100%;
    margin-top: 10px;
  }

  .order-list ul li {
    border-bottom: 1px solid #e9e9e9;
    padding: 10px 0;
  }

  /* .bg {
    background-color: #fff;
    padding-bottom: 10px;
    padding-top: 10px;
  } */

  .order-list-text {
    width: 100%;
    line-height: 40px;
    /* background-color: #fff; */
    padding-left: 10px;
  }
  .balck .top{
    background-color: #1e2b34;
    color: #fff;
  }
  .balck .btm{
    background-color: #1e2b34;
    color: #fff;
  }
  .balck .btm .order-list ul li {
    border-bottom: 1px solid #000;
  }
  .balck .content input{
    background-color: #1e2b34;
    border: 1px solid #000;
    color: #fff;
  }
</style>





// WEBPACK FOOTER //
// src/view/accounts/coinCurrencyFlash.vue