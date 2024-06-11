<template>
  <div class="wrap">
    <div class="flex between">
      <div class="flex">
        <div class="lever-header fColor1 mb15">{{$t('lever.risk')}}：{{riskRate}}%</div>
        <div class="total-pro fColor1 ml10">
          <p>
           {{$t('lever.allloss')}}：
            <span :class="['red','flex1',{'green':totalPro > 0}]">{{totalPro | numFilters(4)}}</span>
          </p>
        </div>
      </div>
      <p class="curPer fColor1" @click="links">{{$t('lever.all')}}</p>
    </div>

    <ul class="list_head ft14">
      <li class="flex">
        <span class="width3 tls">{{$t('lever.type')}}</span>
        <span class="width1">{{$t('lever.openPrice')}}</span>
        <span class="width1">{{$t('lever.nowPrice')}}</span>
        <span class="width1">{{$t('lever.bail')}}</span>
        <span class="width1">{{$t('lever.styPrice')}}</span>
        <span class="width1">{{$t('lever.stsPrice')}}</span>
        <span class="width2">{{$t('lever.openTime')}}</span>
        <span class="width1">{{$t('lever.rate')}}</span>
        <span class="width1">{{$t('lever.nightFee')}}</span>
        <span class="width1">{{$t('lever.loss')}}</span>
        <span class="width2 trs">{{$t('td.do')}}</span>
      </li>
    </ul>
    <ul class="list_content fColor1 ft12">
      <li v-for="(item,index) in list_content" :key="index" class="flex alcenter">
        <span
          class="width3 tls"
        >{{item.type == 1? $t('td.buyin'):$t('td.sellout')}} {{item.symbol}} x{{item.share}}(No.{{item.id}})</span>
        <span class="width1">{{item.price || '0.00' | numFilters(4)}}</span>
        <span class="width1">{{item.update_price || '0.00' | numFilters(4)}}</span>
        <span class="width1">{{item.caution_money || '0.00' | numFilters(4)}}</span>
        <span class="width1">{{item.target_profit_price || '0.00' | numFilters(4)}}</span>
        <span class="width1">{{item.stop_loss_price || '0.00' | numFilters(4)}}</span>
        <span class="width2">{{item.transaction_time}}</span>
        <span class="width1">{{item.trade_fee || '0.00' | numFilters(4)}}</span>
        <span class="width1">{{item.overnight_money || '0.00' | numFilters(4)}}</span>
        <span
          :class="['red','width1',{'green':item.profits > 0}]"
        >{{item.profits || '0.00' | numFilters(4)}}</span>
        <div class="width2 trs">
          <span class="stop-btn" @click="stopLoss(item.id)">{{$t('lever.setloss1')}}</span>
          <span @click="pingcang(item.id)">{{$t('lever.ping')}}</span>
        </div>
      </li>
    </ul>
    <!-- 分页 -->
    <!-- <el-pagination
      class="mores"
      v-if="total>10"
      layout="prev, pager, next"
      :total="total"
      @current-change="handleCurrentChange"
    ></el-pagination>-->
    <div class="mores" @click="load_more">
      <img v-if="list_content.length == 0" src="../../assets/images/nodata.png" alt>
      <span>{{more}}</span>
    </div>
    <!-- 止盈止损弹窗 -->
    <el-dialog title :visible.sync="modalShow" width="480px" center>
      <div class="transfer-content">
        <h3>{{$t('lever.setloss')}}</h3>
        <div class="transfer-list">
          <div class="loss-madal-content">
            <div class="flex">
              <span>{{$t('lever.styPrice')}}：</span>
              <p>
                <span @click="reduce(1)">-</span>
                <input type="text" v-model="targetProfit" @input="inputValue(1)">
                <span class="adds" @click="add(1)">+</span>
              </p>
            </div>
            <p class="modal-text">{{$t('lever.expectProfit')}}：{{modalProfit}}</p>
            <div class="flex">
              <span>{{$t('lever.stsPrice')}}：</span>
              <p>
                <span @click="reduce(2)">-</span>
                <input type="text" v-model="stopLose" @input="inputValue(2)">
                <span class="adds" @click="add(2)">+</span>
              </p>
            </div>
            <p class="modal-text">{{$t('lever.expectLoss')}}：{{modalStop}}</p>
          </div>
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="comfirm()">{{$t('cuy.confirm')}}</el-button>
      </span>
    </el-dialog>
    <!-- 一键平仓弹窗 -->
    <div class="loss-modal flex" v-show="stopModal">
      <div class="content">
        <div class="loss-modal-header">
          <h5>{{$t('lever.sureClose')}}</h5>
          <p @click="closeStopModal()">X</p>
        </div>
        <div class="stopModal">
          <span :class="['stopall',{'alls':selectType == 'all'}]" @click="selectStop('all')">{{$t('lever.allClose')}}</span>
          <span :class="['stopbuy',{'buys':selectType == 'buy'}]" @click="selectStop('buy')">{{$t('lever.moreClose')}}</span>
          <span
            :class="['stopsell',{'sells':selectType == 'sell'}]"
            @click="selectStop('sell')"
          >{{$t('lever.nullClose')}}</span>
        </div>
        <div class="stop-modal-btns">
          <button type="button" @click="closeStopModal()">{{$t('td.canceil')}}</button>
          <button type="button" @click="comfirmModal()">{{$t('td.confirm')}}</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { MessageBox } from "element-ui";
export default {
  props: {
    leverData: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      list_content: [],
      page: 1,
      more: this.$t('td.more'),
      modalShow: false,
      targetProfit: "",
      stopLose: "",
      modalProfit: "",
      modalStop: "",
      modalId: "",
      // 开仓价
      presentPrice: "",
      riskRate: "",
      totalPro: "",
      stopModal: false,
      selectType: "all",
      // 当前价
      openPrice: "",
      // 当前盈亏
      profitsPrice: "",
      orderType: "",
      shareNum: "",
      leverDatas: this.leverData,
      total: ""
    };
  },
  computed: {
    listenState() {
      //监听交易对
      if (this.leverDatas.currencyId) {
        // console.log(this.page)
        //  if(this.page == 1){
        //    this.list_content=[];
        //  }
        this.init();
      }
    }
  },
  watch: {
    listenState: function(a, b) {
      //监听交易对
      if (a != b && b != "") {
        this.widget.setSymbol(
          a,
          localStorage.getItem("tim"),
          function onReadyCallback() {}
        );
      }
    },
    $route(to, from) {
      this.leverData.currencyId = to.query.currencyId;
      this.page = 1;
      if (this.page == 1) {
        this.list_content = [];
      }
    }
  },
  created() {
    let that = this;
  },
  methods: {
    init() {
      var that = this;
      this.more = that.$t('lever.loading');
      this.$http({
        url: "/api/" + "lever/dealall",
        method: "post",
        data: {
          legal_id: that.leverDatas.legalId,
          currency_id: that.leverDatas.currencyId,
          page: that.page
        },
        headers: { Authorization: localStorage.getItem("token") }
      })
        .then(res => {
          if (res.data.type == "ok") {
            this.more = this.$t('td.more');
            this.list_content = this.list_content.concat(
              res.data.message.order.data
            );
            if (that.page == 1) {
              this.loads();
            }
            // this.list_content = res.data.message.order.data;
            this.riskRate = res.data.message.hazard_rate;
            this.totalPro = res.data.message.profits_total;
            this.profitsPrice = res.data.message.profits;
            this.total = res.data.message.order.total;
            if (res.data.message.order.data.length == 0 && that.page > 1) {
              this.more = this.$t('td.nomore');
            }
            if (res.data.message.order.data.length == 0 && that.page == 1) {
              this.more = this.$t('td.nodata');
            }
          } else {
            layer.msg(res.data.message);
          }
        })
        .catch(error => {
          console.log(error);
        });
    },

    // 轮询
    loads() {
      let that = this;
      that.$socket.emit("login", localStorage.getItem("user_id"));
      that.$socket.on("lever_trade", msg => {
        if (msg.type == "lever_trade") {
          var datas = JSON.parse(msg.trades_all);
          that.riskRate = msg.hazard_rate;
          that.totalPro = msg.profits_all;
          that.profitsPrice = msg.profits;
          if (datas.length > 0) {
            let arr = [];
            for (let i in datas) {
              if (
                that.leverData.legalId == datas[i].legal &&
                that.leverData.currencyId == datas[i].currency
              ) {
                arr.push(datas[i]);
              }
            }
            that.list_content = arr;
          }
        }
      });
    },

    pingcang(id) {
      let that = this;
      // this.$t('set.enterAccount')
      MessageBox.alert(that.$t('lever.sureping'), "", {
        confirmButtonText: that.$t('cuy.confirm'),
        customClass: "modalConfirm",
        confirmButtonClass: "confirmBtn",
        closeOnClickModal: true,
        callback: action => {
          if (action == "confirm") {
            that
              .$http({
                url: "/api/" + "lever/close",
                method: "post",
                data: {
                  id: id
                },
                headers: { Authorization: localStorage.getItem("token") }
              })
              .then(res => {
                if (res.data.type == "ok") {
                  layer.msg(res.data.message);
                  setTimeout(function() {
                    location.reload();
                  }, 1000);
                } else {
                  layer.msg(res.data.message);
                }
              })
              .catch(error => {
                console.log(error);
              });
          }
        }
      });
      // layer.confirm("确定平仓？", function() {

      // });
    },
    // 分页
    handleCurrentChange(val) {
      var that = this;
      that.page = val;
      that.init();
    },
    load_more() {
      this.page++;
      this.init();
    },
    // 设置止盈止损
    stopLoss(ids) {
      let that = this;
      that.modalShow = true;
      for (let i in that.list_content) {
        if (that.list_content[i].id == ids) {
          that.shareNum = that.list_content[i].share;
          that.modalId = that.list_content[i].id;
          that.presentPrice = that.$utils.filterDecimals(that.list_content[i].price,2);
          that.openPrice = that.$utils.filterDecimals(
            that.list_content[i].update_price
          ,2);
          if (that.list_content[i].target_profit_price > 0) {
            that.targetProfit = that.$utils.filterDecimals(
              that.list_content[i].target_profit_price
            ,2);
          } else {
            that.targetProfit = that.openPrice;
          }
          if (that.list_content[i].stop_loss_price > 0) {
            that.stopLose = that.$utils.filterDecimals(
              that.list_content[i].stop_loss_price
            ,2);
          } else {
            that.stopLose = that.openPrice;
          }

          if (that.list_content[i].type == 1) {
            that.orderType = "buy";
            if (that.list_content[i].target_profit_price > 0) {
              that.modalProfit = that.$utils.filterDecimals(
                (that.targetProfit -
                  parseFloat(that.list_content[i].price) -
                  0) *
                (that.list_content[i].share - 0)
              ,2);
            } else {
              that.modalProfit = "0.00";
            }
            if (that.list_content[i].stop_loss_price > 0) {
              that.modalStop = that.$utils.filterDecimals(
                (that.$utils.filterDecimals(that.list_content[i].price,2) -
                  that.stopLose -
                  0) *
                (that.list_content[i].share - 0)
              ,2);
            } else {
              that.modalStop = "0.00";
            }
          } else {
            that.orderType = "sell";
            if (that.list_content[i].target_profit_price > 0) {
              that.modalProfit = that.$utils.filterDecimals(
                (that.$utils.filterDecimals(that.list_content[i].price,2) -
                  that.targetProfit -
                  0) *
                (that.list_content[i].share - 0)
              ,2);
            } else {
              that.modalProfit = "0.00";
            }
            if (that.list_content[i].stop_loss_price > 0) {
              that.modalStop = that.$utils.filterDecimals(
                (that.stopLose - parseFloat(that.list_content[i].price) - 0) *
                (that.list_content[i].share - 0)
              ,2);
            } else {
              that.modalStop = "0.00";
            }
          }
        }
      }
    },
    // 加
    add(type) {
      let that = this;
      if (that.orderType == "buy") {
        if (type == 1) {
          that.targetProfit = that.$utils.filterDecimals(parseFloat(that.targetProfit) + 0.01,2);
          that.modalProfit = that.$utils.filterDecimals(
            (parseFloat(that.targetProfit) - that.presentPrice - 0) *
            (that.shareNum - 0)
          ,2);
        } else {
          that.stopLose = that.$utils.filterDecimals(parseFloat(that.stopLose) + 0.01,2);
          that.modalStop = that.$utils.filterDecimals(
            (that.presentPrice - that.stopLose - 0) *
            (that.shareNum - 0)
          ,2);
        }
      } else {
        if (type == 1) {
          that.targetProfit = that.$utils.filterDecimals(parseFloat(that.targetProfit) + 0.01,2);
          that.modalProfit = that.$utils.filterDecimals(
            (parseFloat(that.presentPrice - that.targetProfit) - 0) *
            (that.shareNum - 0)
          ,2);
        } else {
          that.stopLose = that.$utils.filterDecimals(parseFloat(that.stopLose) + 0.01,2);
          that.modalStop = that.$utils.filterDecimals(
            (parseFloat(that.stopLose - that.presentPrice) - 0) *
            (that.shareNum - 0)
          ,2);
        }
      }
    },
    // 减
    reduce(type) {
      let that = this;
      if (that.orderType == "buy") {
        if (type == 1) {
          if (that.targetProfit > 0) {
            that.targetProfit = that.$utils.filterDecimals(parseFloat(that.targetProfit) - 0.01,2);
            that.modalProfit = that.$utils.filterDecimals(
              (parseFloat(that.targetProfit) - that.presentPrice - 0) *
              (that.shareNum - 0)
            ,2);
          } else {
            layer.msg(that.$t('lever.thanzone'));
          }
        } else {
          if (that.stopLose > 0) {
            that.stopLose = that.$utils.filterDecimals(parseFloat(that.stopLose) - 0.01,2);
            that.modalStop = that.$utils.filterDecimals(
              (that.presentPrice - that.stopLose - 0) *
              (that.shareNum - 0)
            ,2);
          } else {
            layer.msg(that.$t('lever.thanzone'));
          }
        }
      } else {
        if (type == 1) {
          if (that.targetProfit > 0) {
            that.targetProfit = that.$utils.filterDecimals(parseFloat(that.targetProfit) - 0.01,2);
            that.modalProfit = that.$utils.filterDecimals(
              (that.presentPrice - that.targetProfit - 0) *
              (that.shareNum - 0)
            ,2);
          } else {
            layer.msg(that.$t('lever.thanzone'));
          }
        } else {
          if (that.stopLose > 0) {
            that.stopLose = that.$utils.filterDecimals(parseFloat(that.stopLose) - 0.01,2);
            that.modalStop = that.$utils.filterDecimals(
              (parseFloat(that.stopLose - that.presentPrice) - 0) *
              (that.shareNum - 0)
            ,2);
          } else {
            layer.msg(that.$t('lever.thanzone'));
          }
        }
      }
    },
    // 输入框输入
    inputValue(type) {
      let that = this;
      if (that.orderType == "buy") {
        if (type == 1) {
          let inputModal = that.$utils.filterDecimals(
            (parseFloat(that.targetProfit) -
              parseFloat(that.presentPrice) -
              0) *
            (that.shareNum - 0)
          ,2);
          if (inputModal > 0) {
            that.modalProfit = inputModal;
          } else {
            that.modalProfit = 0.0;
          }
        } else {
          let inputModal = that.$utils.filterDecimals(
            (parseFloat(that.presentPrice) - parseFloat(that.stopLose) - 0) *
            (that.shareNum - 0)
          ,2);
          if (inputModal > 0) {
            that.modalStop = inputModal;
          } else {
            that.modalStop = 0.0;
          }
        }
      } else {
        if (type == 1) {
          let inputModal = that.$utils.filterDecimals(
            (parseFloat(that.presentPrice) -
              parseFloat(that.targetProfit) -
              0) *
            (that.shareNum - 0)
          ,2);
          if (inputModal > 0) {
            that.modalProfit = inputModal;
          } else {
            that.modalProfit = 0.0;
          }
        } else {
          let inputModal = that.$utils.filterDecimals(
            (parseFloat(that.stopLose) - parseFloat(that.presentPrice) - 0) *
            (that.shareNum - 0)
          ,2);
          if (inputModal > 0) {
            that.modalStop = inputModal;
          } else {
            that.modalStop = 0.0;
          }
        }
      }
    },
    // 关闭弹窗
    closeMosal() {
      let that = this;
      that.modalShow = false;
    },
    // 确认设置
    comfirm() {
      let that = this;
      this.$http({
        url: "/api/" + "lever/setstop",
        method: "post",
        data: {
          id: that.modalId,
          target_profit_price: that.targetProfit,
          stop_loss_price: that.stopLose
        },
        headers: { Authorization: localStorage.getItem("token") }
      })
        .then(res => {
          if (res.data.type == "ok") {
            that.modalShow = false;
            layer.msg(res.data.message);
            for (let i in that.list_content) {
              if (that.modalId == that.list_content[i].id) {
                that.list_content[i].target_profit_price = that.targetProfit;
                that.list_content[i].stop_loss_price = that.stopLose;
              }
            }
          } else {
            layer.msg(res.data.message);
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    // 一键平仓
    stopTotal() {
      let that = this;
      that.stopModal = true;
    },
    // 关闭一键平仓弹窗
    closeStopModal() {
      let that = this;
      that.stopModal = false;
    },
    // 选择平仓类型
    selectStop(types) {
      let that = this;
      that.selectType = types;
    },
    comfirmModal() {
      let that = this;
      let num = 0;
      if (that.selectType == "all") {
        num = 0;
      } else if (that.selectType == "buy") {
        num = 1;
      } else {
        num = 2;
      }
      this.$http({
        url: "/api/" + "lever/batch_close",
        method: "post",
        data: {
          type: num
        },
        headers: { Authorization: localStorage.getItem("token") }
      })
        .then(res => {
          if (res.data.type == "ok") {
            that.stopModal = false;
            layer.msg(res.data.message);
            that.list_content = [];
            that.init();
          } else {
            that.stopModal = false;
            layer.msg(res.data.message);
          }
        })
        .catch(error => {
          that.stopModal = false;
          console.log(error);
        });
    },
    links() {
      var that = this;
      that.$router.push({
        name: "leverList"
      });
    }
  }
};
</script>
<style scoped>
.total-pro {
  margin-bottom: 20px;
  margin-left: 30px;
}
.wrap {
  width: 100%;
  padding: 10px 15px;
}
ul li {
  line-height: 33px;
}
ul li span {
  display: inline-block;
}
ul li div {
  display: inline-block;
}
ul li div span {
  border-radius: 3px;
  cursor: pointer;
  min-height: 33px;
  font-size: 14px;
  border: none;
  line-height: 33px;
  text-align: center;
}
.list_head {
  color: #becbc6;
  border-bottom: 1px solid #2f3d45;
}
.red {
  color: #cc4951;
  cursor: pointer;
}
.green {
  color: #0d8551;
}
.stop-btn {
  margin-right: 10px;
}
.mores {
  color: #999;
  font-size: 14px;
  margin-top: 10px;
  cursor: pointer;
  text-align: center;
}
.mores img {
  width: 120px;
  height: 120px;
  margin: 30px auto 0;
}
.mores span {
  display: block;
  text-align: center;
}
.width1 {
  width: 8%;
  text-align: center;
}
.width2 {
  width: 13%;
  text-align: center;
}
.width3 {
  width: 20%;
  text-align: center;
}
.loss-modal {
  width: 100%;
  height: 100%;
  position: fixed;
  left: 0;
  top: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.7);
  justify-content: center;
  align-items: center;
}
.content {
  width: 500px;
  background-color: #fff;
  margin: 0 auto;
  border-radius: 5px;
}
.loss-modal-header {
  line-height: 40px;
  text-align: center;
  position: relative;
}
.loss-modal-header p {
  position: absolute;
  right: 10px;
  top: 0;
}
.loss-madal-content {
  margin: 15px 15px 0;
}
.loss-madal-content div {
  line-height: 40px;
  margin-bottom: 10px;
}
.loss-madal-content div p {
  display: inline-block;
  border: 1px solid #d1d3df;
  border-radius: 3px;
  height: 40px;
  position: relative;
}
.loss-madal-content p span {
  display: inline-block;
  width: 60px;
  text-align: center;
  font-size: 30px;
  position: relative;
  top: -3px;
  cursor: pointer;
}
.loss-madal-content p .adds {
  top: -1px;
}
.wrap >>> .el-dialog--center .el-dialog__body {
  padding: 25px 25px 20px;
}
.wrap >>> .el-button--primary {
  background-color: #f0b90b;
  width: 140px;
  border: none;
}
.loss-madal-content input {
  background-color: rgba(0, 0, 0, 0);
  border-left: 1px solid #d1d3df;
  border-right: 1px solid #d1d3df;
  line-height: 40px;
  position: relative;
  top: -6px;
  text-align: center;
}
.modal-text {
  width: 100%;
  text-align: center;
  line-height: 40px;
}
.modal-btn {
  border-bottom-left-radius: 5px;
  border-bottom-right-radius: 5px;
  font-size: 0;
}
.modal-btn button {
  width: 50%;
  line-height: 50px;
  border: none;
  float: left;
  font-size: 14px;
  color: #fff;
  background: #9db5c7;
}
.modal-btn button:last-child {
  border-left: 1px solid #2f3d45;
  background: #689cf1;
}
.total-pro button {
  border-radius: 3px;
  color: white;
  background-color: #f0b90b;
  cursor: pointer;
  min-height: 33px;
  min-width: 80px;
  font-size: 14px;
  border: none;
  padding: 0 5px;
  line-height: 33px;
  text-align: center;
}
.stopModal {
  margin: 20px 15px;
  text-align: center;
  padding-bottom: 20px;
}
.stopModal span {
  padding: 6px 15px;
  border-radius: 4px;
}
.stopall {
  border: 1px solid #f0b90b;
  color: #f0b90b;
  margin-right: 10px;
}
.alls {
  color: #fff;
  background-color: #f0b90b;
}
.stopbuy {
  border: 1px solid #0d8551;
  color: #0d8551;
  margin-right: 10px;
}
.buys {
  color: #fff;
  background-color: #0d8551;
}
.stopsell {
  border: 1px solid #cc4951;
  color: #cc4951;
}
.sells {
  color: #fff;
  background-color: #cc4951;
}
.stop-modal-btns {
  width: 100%;
  font-size: 0;
}
.stop-modal-btns button {
  width: 50%;
  float: left;
  font-size: 14px;
  line-height: 40px;
  background-color: #9db5c7;
  border: none;
  outline: none;
  color: #fff;
}
.stop-modal-btns button:last-child {
  background-color: #689cf1;
}
.mores >>> .el-pager li {
  background: rgba(0, 0, 0, 0);
}
.mores >>> .btn-prev,
.mores >>> .btn-next {
  display: none;
}
.transfer-content h3 {
  text-align: center;
  color: #f0b90b;
  font-size: 18px;
}
.tls {
  text-align: left !important;
}
.trs {
  text-align: right !important;
}
</style>



// WEBPACK FOOTER //
// src/view/lever/lever_transactions.vue