<template>
  <div class="trade">
    <div class="tabs flex">
      <span :class="['curPer',{'active':selectedStatus == 1}]" @click="selectTypes(1)">{{$t('lever.std')}}</span>
      <span :class="['curPer',{'active':selectedStatus == 0}]" @click="selectTypes(0)">{{$t('lever.xtd')}}</span>
    </div>
    <div class="content">
      <div class="types flex mt10">
        <p class="lefts">{{$t('fat.type')}}</p>
        <div class="rights tc" :class="buyType==1?'greenColor':'redColor'">
          {{buyType==1?$t('td.buyin'):$t('td.sellout')}}
          <span class="iconfont iconexchange4jiaohuan" @click="selectBuy"></span>
        </div>
      </div>
      <div class="prices flex mt10" v-if="selectedStatus == 0">
        <p class="lefts">{{$t('cuy.price')}}</p>
        <input class="rights" type="text" v-model="controlInput" @input="calculation">
      </div>
      <div class="mult flex mt10">
        <p class="lefts">{{$t('lever.timed')}}</p>
        <el-select class="rights mults" :placeholder="$t('lever.ptimes')" v-model="multNum" @change="calculation">
          <el-option
            v-for="(item,index) in obj.muilList.muit"
            :key="index"
            :value="item.value"
          >{{item.value}}{{$t('lever.times')}}</el-option>
        </el-select>
      </div>
      <div
        class="share-num mt10"
      >1{{$t('lever.hand')}}{{$t('lever.equal')}} {{obj.currencyList.lever_share_num || '0.00' | numFilters(2)}} {{obj.currencyList.currency_name}}</div>
      <div class="share flex mt10">
        <p class="lefts">{{$t('lever.hands')}}</p>
        <div class="share-rights">
          <input type="text" :placeholder="$t('lever.phand')" v-model="shareNum" @input="calculation">
          <el-select
            class="shareNumber"
            :placeholder="$t('lever.phands')"
            v-model="shareNum"
            @change="calculation"
          >
            <el-option
              v-for="(item,index) in obj.muilList.share"
              :key="index"
              :value="item.value"
            >{{item.value}}{{$t('lever.hand')}}</el-option>
          </el-select>
        </div>
      </div>
      <div class="lever-total mt20">
        <p class="flex between mt10">
          <span>{{$t('lever.contractVal')}}</span>
          <span>≈{{marketTotal || '0.00' |numFilters(4)}}</span>
        </p>
        <p class="flex between mt10">
          <span>{{$t('lever.bail')}}</span>
          <span>≈{{bonds || '0.00' |numFilters(4)}}</span>
        </p>
       
        <p class="flex between mt10">
          <span>{{$t('ctc.balance')}}</span>
          <span>{{obj.balance || '0.00' }}</span>
        </p>
      </div>
    </div>
    <div
      class="sell_btn"
      :class="buyType==1?'greenBg':'redBg'"
      @click="buyBtn()"
    >{{buyType==1?$t('td.buyin'):$t('td.sellout')}}{{obj.currencyList.currency_name}}</div>
    <el-dialog title :visible.sync="dialogVisible" width="400px" center>
      <div class="transfer-content">
        <h3>{{$t('lever.sureOd')}}</h3>
        <div class="transfer-list">
          <p>{{obj.currencyList.currency_name}}/{{obj.currencyList.legal_name}}</p>
          <div class="flex">
            <p>{{$t('fat.type')}}：</p>
            <p>{{buyType==1?$t('td.buyin'):$t('td.sellout')}}</p>
          </div>
          <div class="flex">
            <p>{{$t('lever.hands')}}：</p>
            <p>{{shareNum}}</p>
          </div>
          <div class="flex">
            <p>{{$t('lever.timed')}}：</p>
            <p>{{multNum}}</p>
          </div>
          <div class="flex">
            <p>{{$t('lever.bail')}}：</p>
            <p>{{bonds || '0.00' |numFilters(4)}}</p>
          </div>
          <div class="flex">
            <p>{{$t('lever.rate')}}：</p>
            <p>{{serviceCharge|| '0.00' |numFilters(4)}}</p>
          </div>
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="transferSumbit">{{$t('cuy.confirm')}}</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: "trade",
  props: {
    leverTrade: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      selectedStatus: 1,
      multNum: "",
      shareNum: "",
      buyType: 1,
      controlInput: "",
      obj: {},
      spread: localStorage.getItem("spread") || 0,
      token: localStorage.getItem("token"),
      dialogVisible: false,
      marketTotal: "",
      bonds: "",
      serviceCharge: ""
    };
  },
  created() {},
  mounted() {
    var that = this;
  },
  watch: {
    leverTrade: {
      immediate: true, // 这句重要
      handler(val) {
        this.obj = val;
      },
      deep: true
    }
  },
  computed: {
    switchStatus: function() {
      return this.obj; // 直接监听props里的status状态
    }
  },
  methods: {
    // 选择交易类型
    selectTypes(types) {
      let that = this;
      that.selectedStatus = types;
      that.multNum = "";
      that.shareNum = "";
      that.marketTotal = 0.0;
      that.bonds = 0.0;
      that.serviceCharge = 0.0;
    },
    // 选择是出售还是购买
    selectBuy() {
      var that = this;
      that.buyType = that.buyType == 1 ? "2" : "1";
      that.multNum = "";
      that.shareNum = "";
      that.marketTotal = 0.0;
      that.bonds = 0.0;
      that.serviceCharge = 0.0;
    },
    // 提交订单
    buyBtn() {
      var that = this;
      if (that.multNum == "") {
        layer.msg(that.$t('lever.ptimes'));
        return false;
      }
      if (that.shareNum == "") {
        layer.msg(that.$t('lever.hand3'));
        return false;
      }
      if (that.selectedStatus == 0) {
        if (that.controlInput == "") {
          layer.msg(that.$t('lever.pprice'));
          return false;
        }
      }
      that.dialogVisible = true;
     
    },
    // 计算手续费、保证金
    calculation() {
      var that = this;
      var marketPrice = "";
      that.spread = localStorage.getItem('spread');
      if (that.selectedStatus == 1 && that.buyType == 1) {
        marketPrice =
          (Number(that.obj.newPrice - 0) + Number(that.spread - 0)) *
          (that.shareNum - 0) *
          (that.obj.currencyList.lever_share_num - 0);
      } else if (that.selectedStatus == 1 && that.buyType == 2) {
        marketPrice =
          (Number(that.obj.newPrice - 0) - Number(that.spread - 0)) *
          (that.shareNum - 0) *
          (that.obj.currencyList.lever_share_num - 0);
      } else if (that.selectedStatus == 0 && that.buyType == 1) {
        marketPrice =
          (Number(that.controlInput - 0) + Number(that.spread - 0)) *
          (that.shareNum - 0) *
          (that.obj.currencyList.lever_share_num - 0);
      } else if (that.selectedStatus == 0 && that.buyType == 2) {
        marketPrice =
          (Number(that.controlInput - 0) - Number(that.spread - 0)) *
          (that.shareNum - 0) *
          (that.obj.currencyList.lever_share_num - 0);
      }
      if (that.multNum > 0) {
        that.bonds = marketPrice / (that.multNum - 0);
      }
      that.marketTotal = marketPrice;

      that.serviceCharge =
        marketPrice * (that.obj.currencyList.lever_trade_fee - 0) /(100 -0);
    },
    // 下单
    transferSumbit(){
      var that = this;
       this.$http({
        url: "/api/lever/submit",
        method: "post",
        data: {
          share: that.shareNum,
          multiple: that.multNum,
          legal_id:that.obj.currencyList.legal_id,
      		currency_id: that.obj.currencyList.currency_id,
      		type:that.buyType,
      		status: that.selectedStatus,
      		target_price: that.controlInput,
        },
        headers: { Authorization: that.token }
      }).then(res => {
         layer.msg(res.data.message)
        if (res.data.type == "ok") {
          setTimeout(function(){
            that.$router.push({name:'leverList'});
          },1000)
          
        }
      });
    }
  },
  computed: {
    // buyTotal() {
    //   return this.buyInfo.buy_selected * this.buyInfo.buyNum || 0;
    // },
    // sellTotal() {
    //   return this.sellInfo.sell_selected * this.sellInfo.sellNum || 0;
    // }
  }
};
</script>

<style scoped>
.tabs {
  background-color: #2f3d45;
  line-height: 32px;
  border-bottom: 1px solid #2f3d45;
}
.tabs span {
  display: block;
  width: 50%;
  text-align: center;
  color: #babbbb;
  font-size: 12px;
}
.tabs .active {
  background-color: #1e2b34;
}
.content {
  padding: 7px 20px;
  /* color: hsla(0, 0%, 100%, 0.4); */
}
.trade >>> .el-select .el-input.is-focus .el-input__inner{
  border-color:#304049;
}
.lefts {
  width: 50px;
  padding-right: 10px;
  line-height: 30px;
}
.content >>> .el-select {
  background-color: #1e2b34;
}
.content >>> .el-input__inner {
  background-color: #1e2b34;
  border: 1px solid #304049;
  height: 30px;
  line-height: 30px;
  font-size: 12px;
}
.content >>> .el-input__icon {
  line-height: 30px;
}
.content >>> .popper__arrow {
  left: 50% !important;
}

.rights {
  width: calc(100% - 50px);
  line-height: 30px;
  height: 30px;
  border: 1px solid #304049;
  position: relative;
  background-color: #1e2b34;
}
.rights .iconfont {
  position: absolute;
  right: 10px;
  top: 0;
  cursor: pointer;
  background-color: rgba(0, 0, 0, 0);
}
.rights input {
  background-color: rgba(0, 0, 0, 0);
  padding: 0 15px;
  color: #fff;
}
.shareNumber {
  margin-top: 10px;
  width: 100%;
}
input {
  padding: 0 15px;
  color: #fff;
}
.share-rights {
  width: calc(100% - 50px);
  position: relative;
  background-color: #1e2b34;
}
.share-rights input {
  line-height: 30px;
  height: 30px;
  width: 100%;
  background-color: #1e2b34;
  border: 1px solid #304049;
  padding: 0 15px;
  color: #fff;
  font-size: 12px;
}
.mults {
  border: none;
}
.sell_btn {
  line-height: 40px;
  text-align: center;
  height: 40px;
  color: #fff;
  margin: 10px 15px;
  cursor: pointer;
}
.greenColor {
  border-color: #00c087;
}
.redColor {
  border-color: #e3046f;
}
.transfer-content h3 {
  text-align: center;
  color: #f0b90b;
  font-size: 18px;
}
.transfer-list p {
  line-height: 30px;
  color: #606266;
  line-height: 40px;
}
.trade >>> .el-button--primary {
  background-color: #f0b90b;
  width: 140px;
  border: none;
}
.trade >>> .el-dialog--center .el-dialog__body{
  padding: 25px 25px 10px;
}
</style>




// WEBPACK FOOTER //
// src/view/lever/lever_trade.vue