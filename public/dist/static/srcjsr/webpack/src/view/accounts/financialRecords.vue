<template>
  <div :class="skins=='days'?'record':'record balck'">
    <div class="content">
      <div class="header">
        <h1>{{$t('miscro.recordWithdrawal')}}</h1>
      </div>
      <ul class="list">
        <li class="flex between">
          <p>{{$t('td.num')}}</p>
          <p class="tc">{{$t('asset.record')}}</p>
          <p class="tr">{{$t('td.time')}}</p>
        </li>
        <li class="flex between" v-for="(item,index) in list" :key="item.id" v-show="index < 5">
          <p>{{item.value}}</p>
          <p class="tc">{{item.info}}</p>
          <p class="tr">{{item.created_time}}</p>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      list: [],
      token: localStorage.getItem("token") || "",
      skins: localStorage.getItem("skin") || "days"
    };
  },

  created() {},
  mounted() {
    var that = this;
    that.init();
  },

  methods: {
    init() {
      var that = this;
      this.$http({
        url: "/api/" + "charge_mention/log",
        method: "post",
        data: {
          limit: 5
        },
        headers: {
          Authorization: that.token
        }
      })
        .then(res => {
          if (res.data.type == "ok") {
            that.list = res.data.message.data;
          }
        })
        .catch(error => {});
    }
  }
};
</script>

<style scoped>
.record .header{
  background-color: #fff;
  padding: 20px;
  font-size: 18px;
  font-weight: bold;
}
.record .list{
 background-color: #fff;
  padding:0 20px;
  margin-top: 20px;
  min-height: 400px;
}
.record .list li{
  line-height: 50px;
  border-bottom: 1px solid #e6ecf2;
}
.record .list li p{
  width: 25%;
}
.record .list li p:nth-child(2){
  width: 50%;
}
.balck .header{
  background-color: #1e2b34;
  color: #fff;
}
.balck .list{
  background-color: #1e2b34;
}
.balck .list li{
    border-bottom: 1px solid #000;
    color: #fff;
}
</style>



// WEBPACK FOOTER //
// src/view/accounts/financialRecords.vue