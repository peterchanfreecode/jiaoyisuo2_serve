<template>
  <div :class="skins=='days'?'exchange':'exchange balck'">
    <div class="choice flex">
      <div
        class="title"
        :class="tradeType=='exchange'?'active':''"
        @click="changeClassify('exchange')"
      >{{$t('miscro.title')}}</div>
      <div
        class="title"
        :class="tradeType=='insurance'?'active':''"
        @click="changeClassify('insurance')"
      >{{$t('miscro.contractInsurance')}}</div>
      <div
        class="title"
        :class="tradeType=='rawCurrency'?'active':''"
        @click="changeClassify('rawCurrency')"
      >{{$t('miscro.dangerousCurrency')}}</div>
    </div>
    <!-- 资产兑换 -->
    <div class="top" v-if="tradeType=='exchange'">
      <div class="convertibility-header flex between">
        <div class="tl">
          <p>{{$t('miscro.currencyExchange')}}</p>
          <p>{{leftValue}}</p>
        </div>
        <img class="tab-logo" src="../../../static/imgs/transer.png" alt @click="coinTab">
        <div class="tl">
          <p>{{$t('miscro.currencyExchangeIn')}}</p>
          <p>{{rightValue}}</p>
        </div>
      </div>
      <div class="content flex between">
        <!-- <span>{{$t('cuy.price')}}</span> -->
        <input type="number" v-model="inputPrice" :placeholder="$t('fat.pnums')" @input="inputs()">
        <!-- <span>{{$t('td.num')}}</span> -->
        <input type="number" v-model="inputNUm" :placeholder="$t('miscro.convertibleQuantity')">
      </div>
      <div class="mr20">
        <p class="mt10">{{$t('miscro.cashableBalance')}}{{balance}}</p>
        <p class="mt10">{{$t('miscro.currencyExchanges')}}{{rate}}</p>
        <p class="mt10">{{$t('lever.rate')}}：{{fee}}%</p>
      </div>
      <div class="btns">
        <button type="button" class="white" @click="sumbit1()">{{$t('jc.exnow')}}</button>
      </div>
      <div class="tc mt10" style="color:#f0b90b;">{{$t('miscro.automaticallys')}}</div>
    </div>
    <div class="btm" v-if="tradeType=='exchange'">
      <div class="order-list">
        <p class="order-list-text">{{$t('jc.record')}}</p>
        <ul>
          <li class="flex between ft10 tc alcenter">
            <p class="width1">{{$t('fat.type')}}</p>
            <p class="width2">{{$t('td.num')}}</p>
            <p class="width2">{{$t('lever.rate')}}</p>
            <p class="width3">{{$t('td.time')}}</p>
          </li>
          <li class="flex between ft10 tc alcenter" v-for="item in list" :key="item.id">
            <p class="width1">{{item.form_currency}}兑{{item.to_currency}}</p>
            <p class="width2">{{item.num}}</p>
            <p class="width2">{{item.fee}}</p>
            <p class="width3">{{item.create_time}}</p>
          </li>
        </ul>
        <el-pagination
          layout="prev, pager, next"
          v-if="total1 > 10"
          :total="total1"
          @current-change="handleCurrentChange"
        ></el-pagination>
        <div v-if="list.length==0" class="tc mt10">{{$t('td.nodata')}}</div>
      </div>
    </div>
    <!-- 合约保险 -->
    <div class="top" v-if="tradeType== 'insurance'">
      <div class="convertibility-header flex between">
        <div class="tl">
          <p>{{$t('miscro.insuranceCurrency')}}</p>
          <div>
            <el-dropdown trigger="click" @command="selectedCurrency" class="tc">
              <span class="el-dropdown-link">
                {{insuranceCurrencyName}}
                <i class="el-icon-arrow-down el-icon--right"></i>
              </span>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item
                  v-for="item in currencyList"
                  :key="item.id"
                  :command="item"
                  :disabled="disabledStatu"
                >{{item.name}}</el-dropdown-item>
                <!-- <el-dropdown-item :command="2">反向险</el-dropdown-item> -->
              </el-dropdown-menu>
              <!-- </el-dropdown-menu> -->
            </el-dropdown>
          </div>
        </div>
        <img class="tab-logo" src="../../../static/imgs/transer.png" alt>
        <div class="tl">
          <p>{{$t('miscro.insuranceType')}}</p>
          <el-dropdown trigger="click" @command="selectedOne" class="tc">
            <span class="el-dropdown-link">
              {{insuranceText}}
              <i class="el-icon-arrow-down el-icon--right"></i>
            </span>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item
                v-for="item in insuranceList"
                :key="item.id"
                :command="item"
                :disabled="disabledStatu"
              >{{item.name}}</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </div>
      </div>
      <div class="content ml12 insurance-content">
        <div class="flex alcenter">
          <span>{{$t('miscro.contractAsset')}}</span>
          <input type="number" v-model="insuranceNum" :placeholder="pla" :disabled="disabledStatu">
          <span>{{$t('miscro.tenThousand')}}</span>
        </div>
        <div class="flex alcenter">
          <span>{{$t('miscro.warehouses')}}</span>
          <input
            type="number"
            :value="insuranceNum * insuranceAssets /100"
            :placeholder="$t('miscro.warehouses')"
            readonly
          >
          <span>{{$t('miscro.tenThousand')}}</span>
        </div>
        <div></div>
      </div>
      <div class="ml20 mr20">
        <p class="mt10">{{$t('miscro.availableAssets')}}{{currencyBalnace}}</p>
        <p class="mt10">{{$t('miscro.insuredAssets')}}{{insuranceBalnce}}</p>
        <p class="mt10">{{$t('miscro.insuranceAssets')}}{{InsuranceAssets}}</p>
      </div>
      <div class="insurance-btn flex between">
        <button type="button" class="white" @click="purchaseInsurance">{{$t('miscro.purchaseInsurance')}}</button>
        <button type="button" class="white" @click="insuranceClaims">{{$t('miscro.insuranceClaims')}}</button>
        <button type="button" class="white" @click="insuranceCancellation">{{$t('miscro.insuranceCancellation')}}</button>
      </div>
    </div>
    <!-- 持险生币 -->
    <div class="top" v-if="tradeType== 'rawCurrency'">
      <div class="convertibility-header flex between">
        <div class="tl">
          <p>{{$t('miscro.coinWallet')}}</p>
        </div>
        <img class="tab-logo" src="../../../static/imgs/transer.png" alt>
        <div class="tl">
          <p>{{$t('miscro.bmbWallet')}}</p>
        </div>
      </div>
      <div class="mr20">
        <p class="mt10">{{$t('miscro.cumulativeCoins')}}{{moneyData.sum_balance}}</p>
        <p class="mt10">{{$t('miscro.availableQuantity')}}{{moneyData.usabled_balance}}</p>
      </div>
      <!-- <div class="btns">
        <button type="button" class="white" @click="sumbit3()">转币</button>
      </div> -->
    </div>
    <div class="btm" v-if="tradeType== 'rawCurrency'">
      <div class="order-list">
        <p class="order-list-text">{{$t('miscro.rawCurrency')}}</p>
        <ul>
          <li class="flex between ft10 tc alcenter">
            <p class="width1">{{$t('td.currency')}}</p>
            <p class="width2">{{$t('td.num')}}</p>
            <p class="width3">{{$t('td.time')}}</p>
          </li>
          <li class="flex between ft10 tc alcenter" v-for="item in moneyList" :key="item.id">
            <p class="width1">{{item.currency_name}}</p>
            <p class="width2">{{item.value}}</p>
            <p class="width3">{{item.created_time}}</p>
          </li>
        </ul>
        <el-pagination
          layout="prev, pager, next"
          v-if="moneyTotal > 10"
          :total="moneyTotal"
          @current-change="moneyCurrentChange"
        ></el-pagination>
        <div v-if="moneyList.length==0" class="tc mt10">{{$t('td.nodata')}}</div>
      </div>
    </div>
  </div>
</template>
<script>
import "@/lib/clipboard.min.js";
import "@/lib/jquery.qrcode.min.js";
import { MessageBox } from "element-ui";
export default {
  data() {
    return {
      selectValue: "",
      currencyName: "",
      value1: "正向险",
      value2: "",
      leftList: [],
      rightList: [],
      datas: {},
      leftLogo: "",
      rightLogo: "",
      prices: "",
      inputPrice: "",
      inputNUm: "",
      balance: "0.00",
      maxNumber: "",
      minNumber: "",
      leftId: "",
      rightId: "",
      orderList: [],
      typeTab: 1,
      leftValue: "USDT",
      rightValue: "AITB",
      rate: "",
      tradeType: "exchange",
      fee: "",
      rawCurrencyTotal: "0.00",
      rawCurrencyNum: "0.00",
      usdtId: "",
      bmbId: "",
      usdtRate: "",
      bmbRate: "",
      usdtFee: "",
      bmbFee: "",
      usdtBalance: "",
      bmbBalance: "",
      formCurrencyId: "",
      toCurrencyId: "",
      proportion: "",
      bmbProportion: "",
      usdtProportion: "",
      list: [],
      page: 1,
      moreText: "加载更多",
      skins: localStorage.getItem("skin") || "days",
      token: window.localStorage.getItem("token") || "",
      total1: 0,
      // 保险
      pla: "",
      insuranceNum: "",
      currencyBalnace: "0.00",
      insuranceBalnce: "0.00",
      insuranceList: [],
      insuranceText: "",
      insuranceId: "",
      insuranceCurrencyId: "",
      insuranceType: "",
      insuranceAssets: "",
      InsuranceAssets:"",
      insuranceMin: 0,
      insuranceMax: 0,
      currencyList: [],
      insuranceCurrencyName: "",
      microWallet: [],
      multi: "",
      profit: "",
      disabledStatu: false,
      userInsuranceId: "",
      pages: 1,
      moneyList: [],
      moneyData: {},
      moneyTotal: 0
      // moneyText: getlg("more")
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
    that.init();
    that.getList();
    that.getInsuranceType();
    that.insuranceMoney();
  },
  methods: {
    changeClassify(type) {
      let that = this;
      that.tradeType = type;
    },
    init() {
      var that = this;
      if (that.tradeType == "exchange") {
        that
          .$http({
            url: "/api/" + "wallet/conversion_set",
            method: "post",
            data: {},
            headers: {
              Authorization: that.token
            }
          })
          .then(res => {
            if (res.data.type == "ok") {
              var datas = res.data.message;
              var rates = that.$utils.filterDecimals(1 / (datas.bmb_usdt_proportion - 0),4);
              that.usdtRate = "1" + ":" + datas.usdt_bmb_proportion;
              that.bmbRate = rates + ":" + "1";
              that.rate = that.usdtRate;
              that.usdtFee = datas.usdt_bmb_fee;
              that.bmbFee = datas.bmb_usdt_fee;
              that.fee = that.usdtFee;
              that.usdtBalance = datas.user_balance;
              that.bmbBalance = datas.bmb_balance;
              that.balance = that.usdtBalance;
              that.bmbProportion = datas.bmb_usdt_proportion;
              that.usdtProportion = datas.usdt_bmb_proportion;
              that.proportion = that.usdtProportion;
            }
          })
          .catch(error => {});
        that
          .$http({
            url: "/api/" + "wallet/conversion_list",
            method: "get",
            data: {},
            headers: {
              Authorization: that.token
            }
          })
          .then(res => {
            if (res.data.type == "ok") {
              var datas = res.data.message;
              for (var i = 0; i < datas.length; i++) {
                if (datas[i].name == "USDT") {
                  that.usdtId = datas[i].id;
                } else if (datas[i].name == "AITB") {
                  that.bmbId = datas[i].id;
                }
              }
              that.formCurrencyId = that.usdtId;
              that.toCurrencyId = that.bmbId;
            }
          })
          .catch(error => {});
      }
    },
    // 获取兑换列表
    getList() {
      var that = this;
      that
        .$http({
          url: "/api/" + "wallet/my_conversion?page=" + that.page,
          method: "get",
          data: {},
          headers: {
            Authorization: that.token
          }
        })
        .then(res => {
          if (res.data.type == "ok") {
            that.total1 = res.data.message.total;
            if (res.data.message.data.length > 0) {
              that.moreText = that.$t("td.more");
              that.list = res.data.message.data;
            }
          }
        })
        .catch(error => {});
    },
    coinTab() {
      var that = this;
      that.typeTab = that.typeTab == 1 ? 2 : 1;
      that.inputNUm = "";
      that.inputPrice = "";
      if (that.typeTab == 1) {
        that.leftValue = "USDT";
        that.rightValue = "AITB";
        that.rate = that.usdtRate;
        that.fee = that.usdtFee;
        that.balance = that.usdtBalance;
        that.formCurrencyId = that.usdtId;
        that.toCurrencyId = that.bmbId;
        that.proportion = that.usdtProportion;
      } else {
        that.leftValue = "AITB";
        that.rightValue = "USDT";
        that.rate = that.bmbRate;
        that.fee = that.bmbFee;
        that.balance = that.bmbBalance;
        that.formCurrencyId = that.bmbId;
        that.toCurrencyId = that.usdtId;
        that.proportion = that.bmbProportion;
      }
    },
    // 兑换数量实时输入
    inputs() {
      var that = this;
      var arr = that.rate.split(":");
      // that.inputNUm = (that.inputPrice / arr[0]) * arr[1] - that.fee;
      that.inputNUm = that.$utils.filterDecimals(
        (((that.inputPrice - 0) * (100 - that.fee)) / 100) *
          (that.proportion - 0),4);
    },
    // 保险种类
    getInsuranceType() {
      var that = this;
      that
        .$http({
          url: "/api/" + "currency/list",
          method: "get",
          data: {},
          headers: {
            Authorization: that.token
          }
        })
        .then(res => {
          if (res.data.type == "ok") {
            console.log(res);
            var datas = res.data.message.currency;
            var arr = [];
            if (datas.length > 0) {
              for (var i = 0; i < datas.length; i++) {
                if (datas[i].insurancable == 1) {
                  arr.push(datas[i]);
                }
              }
              that.currencyList = arr;
              that.insuranceCurrencyId = arr[0].id;
              that.insuranceCurrencyName = arr[0].name;
              that.getUserInsurance();
            }
          }
        })
        .catch(error => {});
    },
    // 获取用户的保险
    getUserInsurance() {
      var that = this;
      that
        .$http({
          url: "/api/" + "insurance/get_user_currency_insurance",
          method: "post",
          data: {
            currency_id: that.insuranceCurrencyId
          },
          headers: {
            Authorization: that.token
          }
        })
        .then(res => {
          if (res.data.type == "ok") {
            that.currencyBalnace = res.data.message.user_wallet.micro_balance;
            that.insuranceBalnce =res.data.message.user_wallet.insurance_balance;
              that.InsuranceAssets = res.data.message.user_wallet.lock_insurance_balance;
            if (res.data.message.user_insurance &&res.data.message.user_insurance.id) {
              that.insuranceNum =
                res.data.message.user_insurance.amount / 10000;
              that.disabledStatu = true;
              that.insuranceAssets =
                (res.data.message.user_insurance.insurance_amount /
                  res.data.message.user_insurance.amount) *
                100;
              // that.insuranceBalnce = res.message.insurance_amount;
              that.insuranceType =
                res.data.message.user_insurance.insurance_type_id;
              that.userInsuranceId = res.data.message.user_insurance.id;
            } else {
              that.insuranceNum = "";
              that.disabledStatu = false;
            }
            that.$http({
                url: "/api/" + "insurance/get_insurance_type",
                method: "post",
                data: {
                  currency_id: that.insuranceCurrencyId
                },
                headers: {
                  Authorization: that.token
                }
              })
              .then(res => {
                if (res.data.type == "ok") {
                  
                  that.insuranceList = res.data.message;
                  for (var i = 0; i < res.data.message.length; i++) {
                    if (res.data.message[i].name == "反向险") {
                      that.multi = res.data.message[i].claim_rate / 100;
                      that.profit =
                        res.data.message[i].profit_termination_condition;
                    }
                  }
                  if (that.insuranceType) {
                    for (var i = 0; i < res.data.message.length; i++) {
                      if (that.insuranceType == res.data.message[i].id) {
                        that.insuranceText = res.data.message[i].name;
                        that.insuranceMin =
                          res.data.message[i].min_amount / 10000;
                        that.insuranceMax =
                          res.data.message[i].max_amount / 10000;
                        that.pla =
                          that.insuranceMin +
                          "≤"+that.$t('fat.pnums')+"≤" +
                          that.insuranceMax;
                        that.insuranceAssets =
                          res.data.message[i].insurance_assets;
                      }
                    }
                  } else {
                    that.insuranceText = res.data.message[0].name;
                    that.insuranceMin = res.data.message[0].min_amount / 10000;
                    that.insuranceMax = res.data.message[0].max_amount / 10000;
                    that.pla =
                      that.insuranceMin + "≤"+that.$t('fat.pnums')+"≤" + that.insuranceMax;
                    that.insuranceType = res.data.message[0].id;
                    that.insuranceAssets = res.data.message[0].insurance_assets;
                  }
                }
              })
              .catch(error => {});
          }
        })
        .catch(error => {});
    },
    // 选择保险币种
    selectedCurrency(command) {
      var that = this;
      that.insuranceCurrencyId = command.id;
      that.insuranceCurrencyName = command.name;
      that.getUserInsurance();
    },
    // 选择保险类型
    selectedOne(command) {
      var that = this;
      that.insuranceType = command.id;
      console.log(command);
      that.insuranceText = command.name;
      that.insuranceMin = command.min_amount / 10000;
      that.insuranceMax = command.max_amount / 10000;
      that.pla = that.insuranceMin + "≤"+that.$t('fat.pnums')+"≤" + that.insuranceMax;
      that.insuranceAssets = command.insurance_assets;
    },
    // 申购保险
    purchaseInsurance() {
      var that = this;
      var nums =
        (that.insuranceNum -
          0 +
          (that.insuranceNum * that.insuranceAssets) / 100) *
        10000;
      var content = "";
      if (!that.insuranceNum) {
        layer.msg(that.$t('fat.pnums'));
        return false;
      }
      if (nums - 0 > that.currencyBalnace - 0) {
        layer.msg(that.$t('miscro.runningLow'));
        return false;
      }
      if (
        that.insuranceNum < that.insuranceMin ||
        that.insuranceNum > that.insuranceMax
      ) {
        layer.msg(
          that.$t('miscro.purchase') +
            that.insuranceMin +
            that.$t('miscro.reach') +
            that.insuranceMax +
            that.$t('miscro.between')
        );
        return false;
      } else if (that.insuranceNum % 1 != 0) {
        layer.msg(
           that.$t('miscro.onlyEnter') +
            that.insuranceMin +
            "-" +
            that.insuranceMax +
            that.$t('miscro.integersBetween')
        );
        return false;
      }

      if (that.insuranceText == "正向险") {
        content =that.$t('miscro.notReturned');
      } else {
        content =
          that.$t('miscro.settled') +
          that.multi +
          that.$t('miscro.profitable') +
          that.profit +
          "%"+that.$t('miscro.terminated');
      }
      var amont = that.insuranceNum * 10000;
      MessageBox.alert(content, "", {
        confirmButtonText: that.$t('cuy.confirm'),
        customClass: "modalConfirm",
        confirmButtonClass: "confirmBtn",
        closeOnClickModal: true,
        callback: action => {
          if (action == "confirm") {
            that
              .$http({
                url: "/api/" + "insurance/buy_insurance",
                method: "post",
                data: {
                  amount: amont,
                  type_id: that.insuranceType,
                  currency_id: that.insuranceCurrencyId
                },
                headers: {
                  Authorization: that.token
                }
              })
              .then(res => {
                
                  layer.msg(res.data.message);
                  if (res.data.type == "ok") {
                    that.getInsuranceType();
                  }
                
              })
              .catch(error => {});
          }
        }
      });
    },
    // 保险理赔
    insuranceClaims() {
      var that = this;
      MessageBox.alert(
       that.$t('miscro.automatically'),
        "",
        {
          confirmButtonText: that.$t('cuy.confirm'),
          customClass: "modalConfirm",
          confirmButtonClass: "confirmBtn",
          closeOnClickModal: true,
          callback: action => {
            if (action == "confirm") {
              that
                .$http({
                  url: "/api/" + "insurance/claim_apply",
                  method: "post",
                  data: {
                    user_insurance_id: that.userInsuranceId
                  },
                  headers: {
                    Authorization: that.token
                  }
                })
                .then(res => {
                  layer.msg(res.data.message);
                  if (res.data.type == "ok") {
                   
                      that.getInsuranceType();
                    }
                  
                })
                .catch(error => {});
            }
          }
        }
      );
    },
    // 保险解约
    insuranceCancellation() {
      var that = this;
      MessageBox.alert(
        that.$t('miscro.termination'),
        "",
        {
          confirmButtonText: that.$t('cuy.confirm'),
          customClass: "modalConfirm",
          confirmButtonClass: "confirmBtn",
          closeOnClickModal: true,
          callback: action => {
            if (action == "confirm") {
              that
                .$http({
                  url: "/api/" + "insurance/manual_rescission",
                  method: "post",
                  data: {
                    user_insurance_id: that.userInsuranceId
                  },
                  headers: {
                    Authorization: that.token
                  }
                })
                .then(res => {
                  layer.msg(res.data.message);
                  if (res.data.type == "ok") {
                    
                      setTimeout(function() {
                        that.getInsuranceType();
                      }, 500);
                    
                  }
                })
                .catch(error => {});
            }
          }
        }
      );
      // layer.confirm(
      //   "保险解约后，受保资产方可兑换，解约后视为违约，保险仓自动清零。",
      //   function() {
      //     layer.msg("暂未开放");
      //   }
      // );
    },
    selectedTwo(command) {
      var that = this;
      that.value2 = command.name;
      that.rightId = command.id;
      that.rightLogo = command.logos;
      if (that.typeTab == 2) {
        that.prices = command.prices;
        that.maxNumber = command.maxNUm;
        that.minNumber = command.minNUm;
        that.balance = command.balances;
      }
    },

    // 兑换列表加载更多
    mores() {
      var that = this;
      that.page++;
      that.getList();
    },
    sumbit1() {
      let that = this;
      if (!that.inputPrice) {
        layer.msg(that.$t('fat.pnums'));
        return false;
      }
      if (that.typeTab == 2) {
        if (that.inputPrice - 0 < 100) {
          layer.msg(that.$t('miscro.lowestNumber')+"100");
          return false;
        }
      }
      MessageBox.alert(that.$t('miscro.confirmExchange'), "", {
        confirmButtonText: that.$t('cuy.confirm'),
        customClass: "modalConfirm",
        confirmButtonClass: "confirmBtn",
        closeOnClickModal: true,
        callback: action => {
          if (action == "confirm") {
            that
              .$http({
                url: "/api/" + "wallet/conversion",
                method: "post",
                data: {
                  form_currency: that.formCurrencyId,
                  to_currency: that.toCurrencyId,
                  num: that.inputPrice
                },
                headers: {
                  Authorization: that.token
                }
              })
              .then(res => {
                layer.msg(res.data.message);
                if (res.data.type == "ok") {
                  setTimeout(function() {
                    location.reload();
                  }, 500);
                }
              })
              .catch(error => {});
          }
        }
      });
    },
    sumbit3() {
      layer.msg("暂未开放");
    },
    // 分页
    handleCurrentChange(val) {
      var that = this;
      that.page = val;
      that.getList();
    },
    // 持险生币
    insuranceMoney() {
      var that = this;
      that
        .$http({
          url: "/api/" + "insurance_money",
          method: "get",
          data: {},
          headers: {
            Authorization: that.token
          }
        })
        .then(res => {
          if (res.data.type == "ok") {
            that.moneyData = res.data.message;
          }
        })
        .catch(error => {});

      that.insuranceMoneyList();
    },
    // 生币记录
    insuranceMoneyList() {
      var that = this;
      that
        .$http({
          url: "/api/" + "insurance_money_logs?page=" + that.pages,
          method: "get",
          data: {},
          headers: {
            Authorization: that.token
          }
        })
        .then(res => {
          if (res.data.type == "ok") {
            var datas = res.data.message.data;
            that.moneyTotal = res.data.message.total;
            if (datas.length > 0) {
              that.moneyList = that.moneyList.concat(datas);
              // that.moneyText = getlg("more");
            } else if (that.pages > 1) {
              // that.moneyText = getlg("nomore");
            }
          }
        })
        .catch(error => {});
    },
    // 分页
    handleCurrentChange(val) {
      var that = this;
      that.pages = val;
      that.insuranceMoneyList();
    }
  }
};
</script>
<style scoped>
.exchange {
  width: 100%;
  margin-top: 20px;
}
.exchange .top,
.btm,
.choice {
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
.exchange .choice {
  border-bottom: 1px solid #e9e9e9;
}
.choice .title {
  font-size: 16px;
  line-height: 2;
  text-align: center;
  margin-right: 15px;
  cursor: pointer;
}
.choice .active {
  border-bottom: 2px solid #f0b90b;
}
.exchange .btm {
  padding-top: 10px;
  margin: 10px 0;
}
.real_time_price {
  height: 30px;
  width: 100%;
  line-height: 30px;
  display: flex;
}
.real_time_price p {
  font-size: 16px;
  padding-right: 20px;
}
.convertibility-header {
  width: 70%;
  height: 60px;
}
.convertibility-header .tl {
  width: 20%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.tl p {
  font-size: 16px;
}
.el-dropdown-link {
  cursor: pointer;
}
.el-icon-arrow-down {
  font-size: 14px;
}
.el-dropdown {
  height: 20px;
  line-height: 20px;
  width: 80px;
  text-align: center;
}
.content {
  margin: 20px 20px 0;
  width: 80%;
}

.content span {
  display: block;
  width: 125px;
}

.content input {
  width: 44%;
  border: 1px solid #e9e9e9;
  line-height: 30px;
  padding: 0 10px;
}
.insurance-content div {
  margin-top: 20px;
}
.insurance-content input {
  margin-right: 10px;
  width: 30%;
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
.insurance-btn {
  width: 80%;
}
.insurance-btn button {
  margin-top: 40px;
  width: 30%;
  height: 40px;
  line-height: 40px;
  border-radius: 2px;
  background-color: #f0b90b;
}
.white {
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

.order-list-text {
  width: 100%;
  line-height: 40px;
  /* background-color: #fff; */
  padding-left: 10px;
}
.balck .choice {
  background-color: #1e2b34;
  color: #fff;
  border-bottom: 1px solid #252323;
}
.balck .top {
  background-color: #1e2b34;
  color: #fff;
}
.balck .btm {
  background-color: #1e2b34;
  color: #fff;
}
.balck .btm .order-list ul li {
  border-bottom: 1px solid #252323;
}
.balck .el-dropdown-link {
  color: #fff;
}
.balck .content input {
  background-color: #1e2b34;
  color: #fff;
  border: 1px solid #000;
}
.btm >>> .el-pagination {
  text-align: center;
  margin-top: 20px;
  padding-bottom: 40px;
}
.btm >>> .el-pagination .btn-prev,
.btm >>> .el-pagination .btn-next {
  display: none;
}
.btm >>> .el-pager .active {
  color: #f0b90b;
}
</style>





// WEBPACK FOOTER //
// src/view/accounts/exchange.vue