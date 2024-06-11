<template>
  <div :class="skins=='days'?'wrap':'wrap black'">
    <!-- <indexHeader></indexHeader> -->
    <div class="lever-content">
      <div class="tab-header">
        <span :class="[{'active':status == 1}]" @click="tabClick(1)">{{$t('lever.tdin')}}</span>
        <span :class="[{'active':status == 0}]" @click="tabClick(0)">{{$t('lever.listin')}}</span>
        <!-- <span :class="[{'active':status == 2}]" @click="tabClick(2)">平仓中</span> -->
        <span :class="[{'active':status == 3}]" @click="tabClick(3)">{{$t('lever.closed')}}</span>
        <span :class="[{'active':status == 4}]" @click="tabClick(4)">{{$t('lever.revoked')}}</span>
      </div>

      <ul class="list_head ft14">
        <li class="flex">
          <span class="width2">{{$t('fat.type')}}</span>
          <span class="width1" v-if="status == 0 || status == 4">{{$t('lever.entrustPrice')}}</span>
          <span class="width1" v-else>{{$t('lever.openPrice')}}</span>
          <span class="width1">{{$t('lever.nowPrice')}}</span>
          <span class="width1">{{$t('lever.styPrice')}}</span>
          <span class="width1">{{$t('lever.stsPrice')}}</span>
          <span class="width1">{{$t('lever.bail')}}</span>
          <span class="width1">{{$t('lever.canBail')}}</span>
          <span class="width2">{{$t('lever.openTime')}}</span>
          <span class="width2" v-if="status == 3">{{$t('lever.closeTime')}}</span>
          <span class="width1">{{$t('lever.rate')}}</span>
          <span class="width1">{{$t('lever.nightFee')}}</span>
          <span class="width1">{{$t('lever.loss')}}</span>
          <!-- <span class="width1">{{$t('fat.status')}}</span> -->
          <span class="width1" v-if="status == 0 || status == 1">{{$t('td.do')}}</span>
        </li>
      </ul>
      <ul class="list_content ft12">
        <li v-for="(item,index) in list_content" :key="index" class="flex alcenter center">
          <span
            class="width2"
          >{{item.type == 1? $t('td.buyin'):$t('td.sellout')}} {{item.symbol}} x{{item.share}}(No.{{item.id}})</span>
          <span
            class="width1"
            v-if="status == 0 || status == 4"
          >{{item.origin_price || '0.00' | numFilters(4)}}</span>
          <span class="width1" v-else>{{item.price || '0.00' | numFilters(4)}}</span>
          <span class="width1">{{item.update_price || '0.00' | numFilters(4)}}</span>
          <span class="width1">{{item.target_profit_price || '0.00' | numFilters(4)}}</span>
          <span class="width1">{{item.stop_loss_price || '0.00' | numFilters(4)}}</span>
          <span class="width1">{{item.origin_caution_money || '0.00' | numFilters(4)}}</span>
          <span class="width1">{{item.caution_money || '0.00' | numFilters(4)}}</span>
          <span class="width2">{{item.transaction_time}}</span>
          <span class="width2" v-if="status == 3">{{item.handle_time}}</span>
          <span class="width1">{{item.trade_fee || '0.00' | numFilters(4)}}</span>
          <span class="width1">{{item.overnight_money || '0.00' | numFilters(4)}}</span>
          <span
            class="width1"
            :class="item.profits > 0?'greenColor':'redColor'"
          >{{item.profits || '0.00' | numFilters(4)}}</span>
          <!-- <span class="width1">{{item.status_name}}</span> -->
          <div class="width1 btns tr" v-if="status == 0 || status == 1">
            <button type="button" v-if="status == 0" @click="cannelOrder(item.id)">{{$t('lever.revoke')}}</button>
            <button type="button" v-if="status == 1" @click="closePosition(item.id)">{{$t('lever.ping')}}</button>
          </div>
        </li>
      </ul>
      <!-- <div class="mores" @click="load_more">
        <img v-if="list_content.length == 0" src="../../assets/images/nodata.png" alt>
        <span>{{more}}</span>
      </div>-->
      <!-- 分页 -->
      <el-pagination
        class="mores"
        v-if="total>10"
        layout="prev, pager, next"
        :total="total"
        @current-change="handleCurrentChange"
      ></el-pagination>
    </div>

    <!-- <indexFooter></indexFooter> -->
  </div>
</template>
<script>
import indexHeader from "@/view/indexHeader";
import indexFooter from "@/view/indexFooter";
import { MessageBox } from "element-ui";
export default {
  components: { indexHeader, indexFooter },
  data() {
    return {
      legal_id: "",
      currency_id: "",
      list_content: [],
      page: 1,
      more: "加载更多",
      status: 1,
      flag: true,
      total: "",
      skins: localStorage.getItem("skin") || "days"
    };
  },
  created() {
    let that = this;
    // that.legal_id = localStorage.getItem("legal_id");
    // that.currency_id = localStorage.getItem("currency_id");
    that.init();
    if (that.status == 0 || that.status == 1) {
      that.polling();
    }
  },
  beforeCreate() {
    document
      .querySelector("html")
      .setAttribute("style", "background-color:#f7f7f7;");
  },
  beforeDestroy() {
    document.querySelector("html").removeAttribute("style");
  },
  methods: {
    init() {
      let that = this;
      that.more = that.$t('lever.loading');
      if (that.flag) {
        var i = layer.load();
        this.$http({
          url: "/api/" + "lever/my_trade",
          method: "post",
          data: {
            status: that.status,
            legal_id: that.legal_id,
            currency_id: that.currency_id,
            page: that.page
          },
          headers: { Authorization: localStorage.getItem("token") }
        })
          .then(res => {
            layer.close(i);
            that.flag = true;
            if (res.data.type == "ok") {
              that.more = that.$t('td.more');
              // if (that.page == 1) {
              that.list_content = res.data.message.message.data;
              that.total = res.data.message.message.total;
            } else {
              layer.msg(res.data.message);
            }
          })
          .catch(error => {
            console.log(error);
          });
      }
    },

    // 轮询数据
    polling() {
      let that = this;
      that.$socket.emit("login", localStorage.getItem("user_id"));
      that.$socket.on("lever_trade", msg => {
        if (msg.type == "lever_trade") {
          if (that.status == 0) {
            var datas = JSON.parse(msg.trades_entrust);
            that.list_content = datas;
          } else if (that.status == 1) {
            let datas = JSON.parse(msg.trades_all);
            that.list_content = datas;
          }
        }
      });
    },
    // // 加载更多
    // load_more() {
    //   this.page++;
    //   this.init();
    //   that.flag = false;
    // },
    // 分页
    handleCurrentChange(val) {
      var that = this;
      that.page = val;
      that.init();
    },
    // 状态切换
    tabClick(type) {
      let that = this;
      that.status = type;
      that.list_content = [];
      that.page = 1;
      that.init();
      that.flag = false;
      if (type == 0 || type == 1) {
        that.polling();
      }
    },
    // 平仓
    closePosition(ids) {
      let that = this;
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
                  id: ids
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
      //   var i = layer.load();
      //   that
      //     .$http({
      //       url: "/api/" + "lever/close",
      //       method: "post",
      //       data: {
      //         id: ids
      //       },
      //       headers: { Authorization: localStorage.getItem("token") }
      //     })
      //     .then(res => {
      //       layer.close(i);
      //       if (res.data.type == "ok") {
      //         layer.msg(res.data.message);
      //         setTimeout(function() {
      //           that.tabClick(that.status);
      //         }, 200);
      //       } else {
      //         layer.msg(res.data.message);
      //       }
      //     })
      //     .catch(error => {
      //       layer.close(i);
      //       console.log(error);
      //     });
      // });
    },
    // 撤单
    cannelOrder(ids) {
      let that = this;
      MessageBox.alert(that.$t('lever.revokeOrder'), "", {
        confirmButtonText: that.$t('cuy.confirm'),
        customClass: "modalConfirm",
        confirmButtonClass: "confirmBtn",
        closeOnClickModal: true,
        callback: action => {
          if (action == "confirm") {
            that
              .$http({
                url: "/api/" + "lever/cancel",
                method: "post",
                data: {
                  id: ids
                },
                headers: { Authorization: localStorage.getItem("token") }
              })
              .then(res => {
                layer.close(i);
                if (res.data.type == "ok") {
                  layer.msg(res.data.message);
                  setTimeout(function() {
                    that.tabClick(that.status);
                  }, 200);
                } else {
                  layer.msg(res.data.message);
                }
              })
              .catch(error => {
                layer.close(i);
                console.log(error);
              });
          }
        }
      });
      // layer.confirm("确定撤消订单吗？", function() {
      //   var i = layer.load();
      //   that
      //     .$http({
      //       url: "/api/" + "lever/cancel",
      //       method: "post",
      //       data: {
      //         id: ids
      //       },
      //       headers: { Authorization: localStorage.getItem("token") }
      //     })
      //     .then(res => {
      //       layer.close(i);
      //       if (res.data.type == "ok") {
      //         layer.msg(res.data.message);
      //         setTimeout(function() {
      //           that.tabClick(that.status);
      //         }, 200);
      //       } else {
      //         layer.msg(res.data.message);
      //       }
      //     })
      //     .catch(error => {
      //       layer.close(i);
      //       console.log(error);
      //     });
      // });
    }
  }
};
</script>
<style scoped>
.wrap {
  width: 100%;
}
.lever-content {
  width: 1200px;
  margin: 66px auto 30px;
  min-height: 660px;
  background-color: #fff;
  padding-top: 10px;
  position: relative;
  top:10px;

}
.black .lever-content{
  background-color: #1e2b34;
  color: #fff;
}
.black  .list_head{
  border-bottom: 1px solid #000;
}
.tab-header {
  line-height: 40px;
  padding: 0 20px;
}
.tab-header span {
  padding-bottom: 10px;
  margin-right: 20px;
  cursor: pointer;
}
.list_head li span:first-child {
  text-align: left;
}
.list_head li span:last-child {
  text-align: right;
}
.tab-header .active {
  border-bottom: 2px solid #f0b90b;
}
ul li {
  padding: 8px 10px;
}
ul li span {
  display: inline-block;
}
ul li div {
  display: inline-block;
}
ul li div span {
  border-radius: 3px;
  background-color: #f0b90b;
  cursor: pointer;
  min-height: 33px;
  min-width: 80px;
  font-size: 14px;
  border: none;
  padding: 0 5px;
  text-align: center;
}
.list_head {
  border-bottom: 1px solid #e4e7ed;
}
.list_content {
  min-height: 400px;
}
.list_content li span:first-child {
  text-align: left;
}
.list_content li span:last-child {
  text-align: right;
}
.stop-btn {
  margin-right: 10px;
}
.mores {
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
  width: 9%;
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
  background-color: #262a42;
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
  margin: 0 15px;
  padding-bottom: 10px;
}
.loss-madal-content div {
  line-height: 40px;
  margin-bottom: 10px;
}
.loss-madal-content div p {
  display: inline-block;
  border: 1px solid #2e333c;
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
}
.loss-madal-content input {
  background-color: rgba(0, 0, 0, 0);
  border-left: 1px solid #2e333c;
  border-right: 1px solid #2e333c;
  line-height: 40px;
  position: relative;
  top: -8px;
  text-align: center;
}
.modal-text {
  width: 100%;
  text-align: center;
  line-height: 60px;
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
  border-left: 1px solid #2e333c;
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
  border: 1px solid #e3046f;
  color: #e3046f;
}
.sells {
  color: #fff;
  background-color: #e3046f;
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
.btns button {
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
.lever-content >>> .btn-prev,
.lever-content >>> .btn-next {
  display: none;
}
</style>



// WEBPACK FOOTER //
// src/view/lever/leverList.vue