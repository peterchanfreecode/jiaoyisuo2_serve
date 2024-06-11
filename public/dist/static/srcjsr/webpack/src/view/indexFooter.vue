<template>
  <footer :class="skins == 'nights'?'footer':''">
    <!-- <div class="kefu">
      <router-link to="/complaint"><img src="../../static/imgs/kefu.png" alt=""></router-link>
    </div> -->
    <div class="footer-content">
      <div class="flex footer-top between">
        <div class="footer-header-left flex between">
          <!-- <div @click="aboutLink()">关于我们</div> -->
          <div v-for="item in footList" :key="item.id" @click="goDetail(item.id)">{{item.title}}</div>
        </div>
        <div class="footer-header-right flex between">
          <span class="iconfont iconinformatiom"></span>
          <span class="iconfont iconfabu"></span>
          <span class="iconfont iconfacebook"></span>
          <span class="iconfont icontwitter"></span>
          <span class="iconfont iconweibo"></span>
          <span class="iconfont icontubiaozhizuo"></span>
          <span class="iconfont iconqq"></span>
        </div>
      </div>
      <div class="flex footer-bottom between">
        <div>© 2018-2020 KiBiEx All Rights Reserved</div>
        <div class="flex footer-bottom-right">
          <p class="iconfont iconshijian"></p>
          <p>{{years}}</p>
          <p>{{times}}</p>
        </div>
      </div>
    </div>
  </footer>
</template>
<script>
export default {
  name: "indexFooter",
  data() {
    return {
      footList: [],
      years: "",
      times: "",
      skins:localStorage.getItem('skin') || 'days',
    };
  },
  created() {},
  mounted() {
    var that = this;
    that.aboutUs();
    var myDate = new Date();
    var yearText = myDate.getFullYear();
    var monthText = myDate.getMonth();
    var dayText = myDate.getDate();
    var hourText = myDate.getHours();
    var minutes = myDate.getMinutes();
    var seconds = myDate.getSeconds();
    that.years = yearText + '-' + monthText + '-' + dayText;
    that.times = hourText + ':' + minutes + ':' + seconds;
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
      that.years = yearText + '-' + monthText + '-' + dayText;
      that.times = hourText + ':' + minutes + ':' + seconds;
    }, 1000);
  },
  methods: {
    // 公告详情
    goDetail(id) {
      var id = id;
      this.$router.push({
        name: "noticeDetail",
        query: { id: id }
      });
    },
    aboutUs() {
      var that = this;
      this.$http.post("/api/news/list", { c_id: 19 }).then(res => {
        if (res.data.type == "ok") {
          var list = res.data.message.list;
          that.footList = list;
        } else {
          // layer.msg(res.message);
        }
      });
    },
    aboutLink(){
      var that = this;
      this.$router.push({
        name: "aboutUs",
        // query: { id: id }
      });
    }
  }
};
</script>


<style scoped lang='scss'>
footer {
  height: 170px;
  background-color: #fff;
  width: 100%;
  border-top: 1px solid #e3dcca;
}
.footer{
  background-color: #1e2b34;
  border-top: 1px solid #373839;
}

.kefu{
  position: fixed;
  right: 75px;
  bottom: 50px;
  z-index: 100;
}
.kefu img{
  width: 50px;
  height: 50px;
}
.footer-content {
  width: 1160px;
  margin: 0 auto;
}
.footer-top {
  line-height: 94px;
  height: 94px;
  border-bottom: 1px solid #3c3c3c;
}
.footer-header-left {
  width: 600px;
}
.footer-header-left div {
  font-size: 16px;
  color: #393e55;
  cursor: pointer;
}
.footer-header-right {
  width: 260px;
  margin-right: 10px;
}
.iconfont {
  font-size: 18px;
  color: #393e55;
}
.iconfabu {
  font-size: 20px;
}
.footer-bottom{
  line-height: 75px;
  height: 75px;
  color: #393e55;
  font-size: 12px;
}
.footer-bottom-right p{
  margin-left: 10px;
}
.footer div{
  color: #becbc6;
}
.footer .iconfont{
  color: #becbc6;
}
.footer .footer-top{
  border-bottom:1px solid #373839;
}
</style>





// WEBPACK FOOTER //
// src/view/indexFooter.vue