<template>
  <div id="lever-record">
    <div class="title">
      <span class="fl">USDT</span>
      <!-- <span class="fr" @click="$router.go(-1)">返回</span> -->
    </div>
    <div class="list">
      <div class="list-title flex">
        <span>{{$t('fat.type')}}</span>
        <span>{{$t('td.num')}}</span>
        <span>{{$t('td.time')}}</span>
      </div>
      <ul>
        <li v-for="(item,index) in log" :key="index">
          <span>{{item.type}}</span>
          <span>{{item.number || '0.00'}}</span>
          <span>{{item.add_time}}</span>
        </li>
      </ul>
    </div>
    
  </div>
</template>

<script>
export default {
  data() {
    return {
      log: [],
      page: 1
    }
  },
  created() {
    this.getLog();
  },
  methods: {
    getLog:function() {
      let that = this;
      var token = window.localStorage.getItem("token");
      this.$http({
        url: "/api/wallet/hzhistory",
        method: "post",
        headers: { Authorization: token },
        data: {
        }
      }).then(res => {
        if (res.data.type == "ok") {
          let msg = res.data.data;
          that.log = msg;
        }
      });
    }
  }
};
</script>

<style lang='scss' scoped>
#lever-record {
  width: 1200px;
  margin: 0 auto;
  .title {
    height: 60px;
    line-height: 60px;
    padding: 0 30px;
    margin: 20px 0;
    font-size: 20px;
    border-radius: 3px;
    background-color: #fff;
    span {
      cursor: pointer;
    }
  }
  .list {
    min-height: 900px;
    padding: 0 30px;
    background: #fff;
  }
  .list-title,
  li {
    display: flex;
    line-height: 40px;
    justify-content: space-between;
    span {
      flex: 1;
      text-align: center;
    }
  }
  li {
    border-top: 1px solid #2f3d45;
  }
  .loadmore {
    text-align: center;
    padding: 20px;
    cursor: pointer;
  }
}
</style>



// WEBPACK FOOTER //
// src/view/lever/transferRecord.vue