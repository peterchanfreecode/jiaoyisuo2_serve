<template>
  <div :class="skins=='days'?'login':'login balck-login'">
    <!-- <indexHeader></indexHeader> -->
    <div class="logins">
      <div class="login-header">
        <div class="login-header-content flex between">
          <!-- 轮播图 -->
           <div class="swiper-container banner_wrap swiper-container-horizontal">
            <div class="swiper-wrapper">
              <!-- <div class="swiper-slide sliders"> -->
               <div class="swiper-slide sliders" v-for="(item,index) in swiperImgs" :key="index">
                   <a href="javascript:;">
                   <img :src="item.thumbnail" />
                   </a>
               </div>
            </div>
             <div class="swiper-pagination swiper-pagination02"></div>
        </div>
         
        </div>
       
      </div>
     
      <!-- 合约交易 -->
      <div class="login-market">
        <div>
          <h3>{{$t('header.lever')}}</h3>
        </div>
        <ul>
          <li class="flex between">
            <p>{{$t('fat.name')}}</p>
            <p class="tc">{{$t('market.newprice')}}</p>
            <p class="tc">{{$t('market.change')}}</p>
            <p class="tr">{{$t('market.vol')}}</p>
          </li>
          <li
            class="flex between"
            v-for="item in quotationList"
            :key="item.id"
            v-if="item.is_display == 1&&item.open_lever == 1"
          >
            <p>
              <!-- <img :src="item.logo" class="currencyLogo" alt width="20" height="20"> -->
              {{item.currency_name}}/{{item.legal_name}}
            </p>
            <p class="tc">{{item.now_price || '0.00' |numFilters(4)}}</p>
            <p
              class="tc"
              :class="item.change < 0?'redColor':'greenColor'"
            >{{item.change || '0.00'}}%</p>
            <p class="tr">{{item.volume || '0.00' | numFilters(2)}}</p>
          </li>
        </ul>
      </div>
      <!-- 秒合约交易 -->
      <div class="login-market">
        <div>
          <h3>{{$t('header.seconds')}}</h3>
        </div>
        <ul>
          <li class="flex between">
            <p>{{$t('fat.name')}}</p>
            <p class="tc">{{$t('market.newprice')}}</p>
            <p class="tc">{{$t('market.change')}}</p>
            <p class="tr">{{$t('market.vol')}}</p>
          </li>
          <li
            class="flex between"
            v-for="item in quotationList"
            :key="item.id"
            v-if="item.is_display == 1&& item.open_microtrade == 1"
          >
            <p>
              <!-- <img :src="item.logo" class="currencyLogo" alt width="20" height="20"> -->
              {{item.currency_name}}/{{item.legal_name}}
            </p>
            <p class="tc">{{item.now_price || '0.00' |numFilters(4)}}</p>
            <p
              class="tc"
              :class="item.change < 0?'redColor':'greenColor'"
            >{{item.change || '0.00'}}%</p>
            <p class="tr">{{item.volume || '0.00' | numFilters(2)}}</p>
          </li>
        </ul>
      </div>
      <!-- 优势 -->
       <div class="login-advantage">
        <h3>{{$t('lg.login7')}}</h3>
        <ul class="flex between">
          <li>
            <h4>{{$t('lg.login8')}}</h4>
            <div class="img">
              <img src="../../static/imgs/a19.png" alt>
            </div>
            <div class="text">
              <p>{{$t('lg.login9')}}</p>
              <p>{{$t('lg.login10')}}</p>
              <p>{{$t('lg.login11')}}</p>
            </div>
          </li>
          <li>
            <h4>{{$t('lg.login12')}}</h4>
            <div class="img">
              <img src="../../static/imgs/a20.png" alt>
            </div>
            <div class="text">
              <p>{{$t('lg.login13')}}</p>
              <p>{{$t('lg.login14')}}</p>
              <p>{{$t('lg.login15')}}</p>
            </div>
          </li>
          <li>
            <h4>{{$t('lg.login16')}}</h4>
            <div class="img">
              <img src="../../static/imgs/a21.png" alt>
            </div>
            <div class="text">
              <p>{{$t('lg.login17')}}</p>
              <p>{{$t('lg.login18')}}</p>
              <p>{{$t('lg.login19')}}</p>
            </div>
          </li>
        </ul>
      </div> 
      <!-- 关于我们 -->
       <div class="about-us">
        <div class="about-us-info">
          <h3>{{$t('foo.about')}}</h3>
          <p>{{$t('lg.login20')}}</p>
          <p>{{$t('lg.login21')}}</p>
          <p>{{$t('lg.login22')}}</p>
        </div>
        <div class="bg">
          <div class="bgc"></div>
        </div> 
        <!-- <div class="el-card box-card institutional-investor is-always-shadow">
          <div class="el-card__header">
            <div class="clearfix">
              <span>合作机构</span>
            </div>
          </div>
          <div class="el-card__body flex between pb20 com">
            <div class="item">
              <img src="../../static/imgs/institutional_1.png">
            </div>
            <div class="item">
              <img src="../../static/imgs/institutional_2.png">
            </div>
            <div class="item">
              <img src="../../static/imgs/institutional_3.png">
            </div>
            <div class="item">
              <img src="../../static/imgs/institutional_4.png">
            </div>
            <div class="item">
              <img src="../../static/imgs/institutional_5.png">
            </div>
            <div class="item">
              <img src="../../static/imgs/institutional_6.png">
            </div>
          </div>
        </div> -->
      </div>
      <div class="pt">
        <div class="container">
          <div class="title">{{$t('lg.login23')}}</div>
          <div class="des">{{$t('lg.login24')}}</div>
          <ul>
            <li class="left tc flex center">
              <div class="wrap flex center">
                <img src="../../static/imgs/a14.png" alt class="icon">
                <div class="content flex center">
                  <div class="android item">
                    <img src="../../static/imgs/a12.png" alt>
                    <span
                      class="downloadAndroid"
                      :class="[{'selected':codeShow == 1}]"
                      @mouseover="codeShow = 1"
                      @mouseout="codeShow = 3"
                    >{{$t('lg.login25')}}</span>
                  </div>
                  <div class="ios item">
                    <img src="../../static/imgs/a13.png" alt>
                    <span
                      class="downloadIOS"
                      :class="[{'selected':codeShow == 2}]"
                      @mouseover="codeShow = 2"
                      @mouseout="codeShow = 3"
                    >{{$t('lg.login26')}}</span>
                  </div>
                  <!-- <div class="qrBox" v-if="codeShow == 1|| codeShow == 2">
                    <div class="qr-tip">{{$t('lg.login27')}}</div>
                    <div value level="H" background="#fff" foreground="#000" class="mt10" id="qr">
                      <img height="100" width="100" src="../../static/imgs/qrcode.png" alt>
                    </div>
                  </div> -->
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
   
  </div>
</template>

<script>
import indexHeader from "@/view/indexHeader";
import indexFooter from "@/view/indexFooter";
import Swiper from "swiper";
import "swiper/dist/css/swiper.min.css";
export default {
  name: "login",
  components: { indexHeader, indexFooter },
  data() {
    return {
      account_number: "",
      password: "",
      quotationList: [],
      btnSelect: 0,
      userAccount: localStorage.getItem("userAccount") || "",
      codeShow: 3,
      newsList: [],
      skins: localStorage.getItem("skin") || "days",
      swiperImgs:[]
    };
  },
  created() {
    this.account_number = "";
    this.password = "";
    this.init();
    this.$http({
      url: "/api/" + "news/list",
      method: "post",
      data: { c_id: 4 }
    }).then(res => {
      if (res.data.type == "ok") {
        this.newsList = res.data.message.list;
      }
    });
    this.getSwiper();
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
  methods: {
    init() {
      var that = this;
      this.$http({
        url: "/api/" + "currency/quotation_new",
        method: "get",
        data: {}
        // headers: { Authorization: localStorage.getItem("token") }
      })
        .then(res => {
          if (res.data.type == "ok") {
            var arr = [];
            var arr2 = [];
            for (var i = 0; i < res.data.message.length; i++) {
              for (var j = 0; j < res.data.message[i].quotation.length; j++) {
                arr.push(res.data.message[i].quotation[j]);
              }
            }
            that.quotationList = arr;
            that.marketSocket();
          }
        })
        .catch(error => {});
    },
    userInfo() {
      var that = this;
      this.$http({
        url: "/api/" + "user/info",
        method: "get",
        data: {},
        headers: { Authorization: localStorage.getItem("token") }
      })
        .then(res => {
          if (res.data.type == "ok") {
            localStorage.setItem("user_id", res.data.message.id);
            localStorage.setItem("userAccount", res.data.message.account);
            localStorage.setItem(
              "extension_code",
              res.data.message.extension_code
            );
            localStorage.setItem("is_seller", res.data.message.is_seller);
            if (res.data.message.seller) {
              if (res.data.message.seller.length > 0) {
                localStorage.setItem(
                  "status",
                  res.data.message.seller[0].status
                );
                let obj = {
                  sellerValue: res.data.message.is_seller,
                  statusValue: res.data.message.seller[0].status
                };
                eventBus.$emit("seller", obj);
              }
            }
            that.$router.push({ path: "/leverdealCenter" });
          }
        })
        .catch(error => {});
    },
    login() {
      let account_number = this.$utils.trim(this.account_number);
      let password = this.$utils.trim(this.password);
      if (this.account_number.length == "") {
        layer.tips(this.$t('set.enterAccount'), "#account");
        
        return;
      }
      if (this.password.length < 6) {
        layer.tips(this.$t('login.psw6'), "#pwd");
        return;
      }
      var i = layer.load();
      this.$http({
        url: "/api/" + "user/login",
        method: "post",
        data: {
          user_string: account_number,
          password: password,
          type: 1
        }
      })
        .then(res => {
          layer.close(i);
          res = res.data;
          if (res.type === "ok") {
            localStorage.setItem("token", res.message);
            localStorage.setItem("accountNum", account_number);
            this.$store.commit("setAccountNum");
            this.userInfo();
          } else {
            layer.msg(res.message);
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    inputs() {
      var that = this;
      if (that.account_number != "" && that.password.length >= 6) {
        that.btnSelect = 1;
      } else {
        that.btnSelect = 0;
      }
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
              that.quotationList[i].change = msg.change;
              that.quotationList[i].volume = msg.volume;
            }
          }
        }
      });
    },
    links() {
      var that = this;
      that.$router.push({ path: "/leverdealCenter" });
    },
    mine_over() {
      var that = this;
      that.codeShow = true;
    },
    mine_out() {
      var that = this;
      that.codeShow = false;
    },
    getSwiper() {
      this.$http({
        url: "/api/news/list",
        method: "post",
      data: { c_id: 8 }
      })
        .then(res => {
          console.log(res);
          if (res.data.type == "ok") {
            this.swiperImgs = res.data.message.list;
          }
        })
        .then(() => {
          var mySwiper02 = new Swiper(".banner_wrap", {
            // direction: 'vertical',
            loop: true,
            autoplay: 2000,
            // 如果需要分页器
            pagination: ".swiper-pagination02",
            paginationClickable: true,
            observer: true, //修改swiper自己或子元素时，自动初始化swiper
            observeParents: true //修改swiper的父元素时，自动初始化swiper
          });
        });
    },
  }
  // beforeRouteEnter(to, from, next) {
  //   if (from.name == "dealCenter") {
  //     window.location.reload();
  //   }
  //   next();
  // }
};
</script>

<style scoped lang="scss">
.logins {
  margin-top: 66px;
}
// 黑色版本css样式
.balck-login {
  background-color: #000;
}
.balck-login .login-market h3 {
  color: #fff;
}
.balck-login .login-market li {
  background-color: #1e2b34;
  color: #fff;
}
.balck-login .login-advantage ul li {
  color: #fff;
  background-image: -webkit-gradient(
      linear,
      left top,
      left bottom,
      from(#1e2b34),
      to(#1e2b34)
    ),
    -webkit-gradient(linear, left top, left bottom, from(#1e2b34), to(#1e2b34));
  background-image: linear-gradient(#1e2b34, #1e2b34),
    linear-gradient(#1e2b34, #1e2b34);
  background-blend-mode: normal, normal;
  -webkit-box-shadow: 0 3px 14px 1px rgba(0, 0, 0, 0.09);
  box-shadow: 0 3px 14px 1px rgba(0, 0, 0, 0.09);
}
.balck-login .login-advantage .text{
  color: #fff;
}
.balck-login .about-us{
  background-color: #000;
}
.login-body .el-form .active {
  background-color: #f0b90b;
  border-color: #f0b90b;
  color: #fff;
  opacity: 1;
}
.login-header {
  width: 100%;
  // height: 688px;
  width: 100%;
  // background: url(../../static/imgs/banner.jpg);
  position: relative;
}
.login-header-content {
  // height: 688px;
  position: relative;
  margin: 0 auto;
}
.login-header-content .swiper-slide img{
  width: 100%;
  max-height: 698px;
  height: 698px;
}
.login-header-left {
  padding-top: 134px;
  text-align: center;
  padding-left: 70px;
}
.description,
.trading-platform {
  line-height: 32px;
  padding-bottom: 10px;
  font-weight: 700;
}
.description-e {
  margin-bottom: 23px;
}
.description,
.description-e,
.trading-platform,
.trading-platform-e {
  color: #fff;
  text-shadow: 0 0 10px rgba(0, 0, 0, 0.66);
  -webkit-text-shadow: 0 0 10px rgba(0, 0, 0, 0.66);
  -moz-text-shadow: 0 0 10px rgba(0, 0, 0, 0.66);
}
.line {
  width: 135px;
  height: 1px;
  background: #fff;
  margin: 30px auto;
}
.trade-box {
  font-size: 18px;
  line-height: 18px;
  color: #f8f6f6;
  max-width: 650px;
}
.btn {
  background-image: linear-gradient(
      55deg,
      #8f9fca,
      #b0a497 0,
      #d0a863 0,
      #e9cc8a
    ),
    linear-gradient(#fbfafa, #fbfafa);
  display: inline-block;
  border-radius: 5px;
  height: 48px;
  line-height: 48px;
  color: #252b47;
  font-size: 20px;
  padding: 0 20px;
  margin: 14px 0;
  cursor: pointer;
}
.trade-box a {
  display: inline-block;
  color: #d0a863;
  padding: 10px;
  text-decoration: underline;
}
.login-header-right {
  padding-top: 128px;
}
.login-wrap {
  width: 360px;
  height: 365px;
  border-radius: 2px;
  box-sizing: border-box;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  background-color: hsla(0, 0%, 100%, 0.88);
  box-shadow: 0 8px 32px 5px rgba(0, 0, 0, 0.47);
  border-radius: 10px;
  overflow: hidden;
}
.login-title {
  padding-top: 50px;
  text-align: center;
}
.login-title h2 {
  height: 17px;
  font-size: 22px;
  font-weight: 400;
  color: #252b47;
  margin: 0 auto;
  letter-spacing: 2px;
}
.login-body {
  width: 280px;
  margin: 40px auto 0;
}
.login-body .el-form-item__label {
  display: none;
}
.login-wrap input {
  background-color: hsla(0, 0%, 88%, 0);
  height: 38px;
  border: 2px solid #a0a0a0;
  -webkit-appearance: none;
  background-color: #fff;
  background-image: none;
  border-radius: 4px;
  box-sizing: border-box;
  color: #606266;
  display: inline-block;
  font-size: inherit;
  height: 40px;
  line-height: 40px;
  outline: 0;
  padding: 0 15px;
  transition: border-color 0.2s cubic-bezier(0.645, 0.045, 0.355, 1);
  width: 100%;
  margin-bottom: 14px;
}
.login-btn {
  font-size: 16px;
  background: #c2c2c2;
  border-color: #c2c2c2;
  color: #fff;
  margin-top: 26px;
  width: 280px;
  height: 38px;
  border-radius: 4px;
  opacity: 0.7;
}
.toggle {
  font-size: 14px;
  margin-top: 30px;
  background-color: rgba(37, 43, 71, 0.43);
  height: 56px;
}
.toggle p.fl {
  padding: 17px 10px 17px 0;
  width: 130px;
  vertical-align: middle;
}
.toggle p.fr {
  color: #252b47;
  padding: 17px 0;
  vertical-align: middle;
}
.toggle-container {
  width: 280px;
  margin: 0 auto;
  overflow: hidden;
}
.system {
  width: 1160px;
  height: 58px;
  margin: 0 auto;
  background-image: linear-gradient(
      hsla(0, 0%, 100%, 0.89),
      hsla(0, 0%, 100%, 0.89)
    ),
    linear-gradient(#f4f4f4, #f4f4f4);
  background-blend-mode: normal, normal;
  box-shadow: 0 3px 18px 2px rgba(0, 0, 0, 0.1);
  border-radius: 10px;
  z-index: 22;
  padding: 0 40px 0 10px;
  font-size: 14px;
  line-height: 58px;
  text-align: center;
  position: relative;
  top: -30px;
}
.system li {
  float: left;
  padding-left: 40px;
  line-height: 58px;
  font-size: 16px;
  width: 28%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  text-align: center;
}
.login-top {
  width: 1160px;
  margin: 65px auto 40px;
  background: #f8f6f6;
}
.login-top-left,
.login-top-right {
  width: 560px;
  height: 110px;
  line-height: 110px;
  background-color: #fff;
  box-shadow: 0 3px 14px 1px rgba(0, 0, 0, 0.09);
  border-radius: 8px;
  box-sizing: border-box;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  padding: 0 15px;
  cursor: pointer;
  font-size: 16px;
  position: relative;
}
.login-top-left strong,
.login-top-right strong {
  font-size: 36px;
  color: #252b47;
}
.login-top-left .percentage,
.login-top-right .percentage {
  position: absolute;
  top: -10px;
  right: 15px;
  font-size: 15px;
  font-weight: 700;
}
.login-top-left .percentage:before,
.login-top-right .percentage:before {
  content: "\25B2";
}
.login-market {
  width: 1160px;
  margin: 40px auto 0;
  // background-color: #fff;
  // box-shadow: 0 3px 14px 1px rgba(0, 0, 0, 0.09);
  border-radius: 0 0 7px 7px;
  padding: 30px 60px 0;
  box-sizing: border-box;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
}
.login-market h3 {
  margin-bottom: 10px;
  padding: 0 15px;
  line-height: 38px;
  border-left: 2px solid #f0b90b;
  font-size: 16px;
}
.login-market li {
  font-size: 16px;
  height: 52px;
  line-height: 52px;
  color: #252b47;
  background-color: #fff;
  margin-bottom: 10px;
  padding: 0 15px;
}
.login-market li:last-child {
  border: none;
}
.login-market li p {
  width: 25%;
}
.login-advantage {
  height: 324px;
  width: 100%;
  background-image: url(../../static/imgs/ourInfo.jpg);
  background-color: #1f2a48;
  background-repeat: no-repeat;
  background-position: 50%;
  position: relative;
  z-index: 10;
  margin-top: 82px;
}
.login-advantage h3 {
  padding-top: 74px;
  letter-spacing: 2px;
  color: #f0b90b;
  font-size: 24px;
  line-height: 24px;
  text-align: center;
  font-weight: 400;
  padding-bottom: 55px;
}
.login-advantage ul {
  height: 342px;
  width: 869px;
  margin: 0 auto;
}
.login-advantage ul li {
  width: 265px;
  height: 342px;
  background-image: linear-gradient(#fff, #fff),
    linear-gradient(#f0b90b, #f0b90b);
  background-blend-mode: normal, normal;
  box-shadow: 0 3px 14px 1px rgba(0, 0, 0, 0.09);
  border-radius: 10px;
  text-align: center;
}
.login-advantage ul li h4 {
  color: #f0b90b;
  font-size: 22px;
  line-height: 22px;
  padding-top: 43px;
  padding-bottom: 32px;
  font-weight: 400;
}
.login-advantage .text {
  font-size: 16px;
  color: #2d3854;
}
.login-advantage .img {
  height: 132px;
}
.about-us {
  position: relative;
  width: 100%;
  background-color: #f8f6f6;
  padding-top: 260px;
  box-sizing: border-box;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
}
.about-us-info {
  width: 100%;
  height: 586px;
  background-image: url(../../static/imgs/aboutUs.jpg);
  background-repeat: no-repeat;
  background-position: 50%;
}
.about-us h3 {
  font-size: 25px;
  line-height: 25px;
  letter-spacing: 2px;
  color: #f0b90b;
  padding-top: 188px;
  font-weight: 400;
  text-align: center;
  padding-bottom: 24px;
}
.about-us p,
.pt {
  text-align: center;
}
.about-us .about-us-info p {
  font-size: 16px;
  line-height: 32px;
  color: #1e2643;
  width: 915px;
  margin: 0 auto;
}
.about-us .institutional-investor {
  position: absolute;
  width: 1198px;
  left: 50%;
  transform: translate(-50%, -50%);
}
.el-card.is-always-shadow,
.el-card.is-hover-shadow:focus,
.el-card.is-hover-shadow:hover {
  box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
}
.el-card {
  border-radius: 4px;
  border: 1px solid #ebeef5;
  background-color: #fff;
  overflow: hidden;
  color: #303133;
  transition: 0.3s;
}
.about-us .el-card.institutional-investor .el-card__header {
  border-bottom: unset;
  text-align: center;
  font-size: 25px;
  color: #f0b90b;
}
.el-card__header {
  padding: 18px 20px;
  box-sizing: border-box;
}
.pt {
  height: 500px;
  background: url(../../static/imgs/pt.jpg);
  text-align: center;
}
.pt .container {
  padding-top: 100px;
  width: 1160px;
  margin: 0 auto;
}
.pt .title {
  padding-top: 66px;
  font-size: 26px;
  line-height: 26px;
  color: #fff;
}
.pt .des {
  padding-top: 20px;
  padding-bottom: 72px;
  font-size: 16px;
  line-height: 16px;
  color: #f1f1f1;
}
.pt ul li {
  position: relative;
}
.pt ul li .wrap .content {
  display: inline-block;
  margin-left: 32px;
  height: 90px;
  vertical-align: middle;
  position: relative;
}
.pt ul li .wrap .content .item {
  height: 30px;
  line-height: 30px;
  margin-bottom: 16px;
  display: block;
  text-align: left;
  color: #fff;
}
.pt ul li .wrap .content .item span {
  vertical-align: sub;
  margin-left: 15px;
  cursor: pointer;
}
.qrBox {
  width: 120px;
  background: #fff;
  border-radius: 5px;
  overflow: hidden;
  margin-left: 10px;
  padding: 10px 0;
  position: absolute;
  left: 160px;
  top: 0;
}
.icon {
  width: 99px;
  height: 90px;
}
.el-card__body {
  padding: 0 40px 10px;
}
.cny {
  font-size: 15px;
  color: #aaaeba;
  font-weight: 700;
}
.com .item img {
  width: 163px;
}
.currencyLogo {
  margin-right: 10px;
  position: relative;
  top: -3px;
}
</style>



// WEBPACK FOOTER //
// src/components/index.vue