<template>
  <div :class="skins=='days'?'login':'login balck-login'">
    <!-- <indexHeader></indexHeader> -->
    <div class="logins">
      <div class="login-header">
        <div class="login-header-content">
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
          <div class="login-header-right">
            <div class="login-wrap">
              <div class="login-title">
                <h2>{{$t('lg.login')}}</h2>
              </div>
              <div class="flex center mt20">
                <p class="tc" @click="setIsMb('mobile')">
                  <span class="pb5 plr10" :class="[{'actives':isMb=='mobile'}]">{{$t('fat.phone')}}</span>
                </p>
                <p class="tc ml30" @click="setIsMb('email')">
                  <span class="pb5 plr10" :class="[{'actives':isMb=='email'}]">{{$t('fat.email')}}</span>
                </p>
              </div>
              <div class="login-body">
                <div class="el-form">
                  <div class="el-form-item el-form-item--feedback">
                    <label for="account" class="el-form-item__label">邮箱</label>
                    <div class="el-form-item__content">
                      <div class="el-input flex alcenter">
                        <div class="flex alcenter" @click="showCity= !showCity" v-if="isMb=='mobile'">
                          <p class="mr10">+{{areaCode}}</p>
                          <i class="iconfont icon-xiala ft12" v-if="!showCode"></i>
                          <i class="iconfont icon-shangla_icon ft12" v-else></i>
                        </div>
                        <input
                        v-if="isMb=='mobile'"
                          type="text"
                          :placeholder="$t('register.enterPhone')"
                          class="el-input__inner"
                          v-model="account_number"
                          @keyup="inputs"
                        />
                        <input
                        v-else
                          type="text"
                          :placeholder="$t('register.enterEmail')"
                          class="el-input__inner"
                          v-model="account_number"
                          @keyup="inputs"
                        />
                        <ul class="curPer input_select scroll" v-if="showCity">
                          <li
                            class="flex between alcenter bdb"
                            v-for="(item,index) in areaList"
                            @click="getCity(item.id,item.area_code)"
                          >
                            <p>{{item.name}}</p>
                            <p class="tr">{{item.area_code}}</p>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div class="el-form-item el-form-item--feedback">
                    <label for="pass" class="el-form-item__label">{{$t('login.psw')}}</label>
                    <div class="el-form-item__content">
                      <div class="el-input alcenter">
                        <input
                          type="password"
                          autocomplete="off"
                          :placeholder="$t('set.enterPsw')"
                          password="password"
                          class="el-input__inner"
                          v-model="password"
                          @keyup="inputs"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="el-form-item el-form-item--feedback" @click="login">
                    <div class="el-form-item__content">
                      <button
                        :disabled="account_number==''||password==''"
                        type="button"
                        :class="[{'active':account_number&&password}]"
                        class="el-button login-btn el-button--default"
                      >
                        <span>{{$t('lg.login')}}</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="toggle">
                <div class="toggle-container">
                  <p class="fl">
                    <router-link
                      :to="{path:'/forgetPwd'}"
                      style="cursor: pointer;color:#000000;"
                    >{{$t('set.forgetPsw')}}</router-link>
                  </p>
                  <p class="fr">
                    <router-link
                      :to="{path:'/components/register'}"
                      style="cursor: pointer;color:#000000;"
                    >
                      {{$t('lg.login5')}}
                      <span style="color:#FFFFFF;">{{$t('lg.login6')}}</span>
                    </router-link>
                  </p>
                </div>
              </div>
            </div>
          </div>
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
      isMb: "mobile", //是否为手机
      quotationList: [],
      btnSelect: 0,
      userAccount: localStorage.getItem("userAccount") || "",
      codeShow: 3,
      newsList: [],
      skins: localStorage.getItem("skin") || "days",
      swiperImgs: [],
      areaCode: "",
      areaId: "",
      showCity: false,
      showCode: false
    };
  },
  created() {
    this.account_number = "";
    this.password = "";
    this.init();
    this.getSwiper();
    this.$http({
      url: "/api/" + "news/list",
      method: "post",
      data: { c_id: 4 }
    }).then(res => {
      if (res.data.type == "ok") {
        this.newsList = res.data.message.list;
      }
    });
    this.$http({
      url: "/api/" + "area_code",
      method: "post",
      data: {}
    }).then(res => {
      if (res.data.type == "ok") {
        var datas = res.data.message;
        this.areaList = datas;
        this.areaText = datas[0].name;
        this.areaCode = datas[0].area_code;
        this.areaId = datas[0].id;
      }
    });
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
            // that.$router.push({ path: "/leverdealCenter" });
            that.$router.push({ path: "/" });
            location.reload();
          }
        })
        .catch(error => {});
    },
    login() {
      var that = this;
      let account_number = this.$utils.trim(this.account_number);
      let password = this.$utils.trim(this.password);
      if (this.account_number.length == "") {
        layer.tips(this.$t("set.enterAccount"), "#account");

        return;
      }
      if (this.password.length < 6) {
        layer.tips(this.$t("login.psw6"), "#pwd");
        return;
      }
      var  data = {};
      data.user_string = account_number;
      data.password = password;
      data.type = 1;
      if(that.isMb == 'mobile'){
        data.area_code=that.areaCode;
				data.area_code_id=that.areaId;
      }

      var i = layer.load();
      this.$http({
        url: "/api/" + "user/login",
        method: "post",
        data: data
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
            autoplay: 3000,
            // 如果需要分页器
            pagination: ".swiper-pagination02",
            paginationClickable: true,
            observer: true, //修改swiper自己或子元素时，自动初始化swiper
            observeParents: true //修改swiper的父元素时，自动初始化swiper
          });
        });
    },
    setIsMb(boo) {
      this.account = "";
      this.pwd = "";
      this.repwd = "";
      this.code = "";
      this.invite = "";
      this.isMb = boo;
      this.codeTrue = false;
      this.showList = false;
      this.provinces = [];
      this.cities = [];
      this.districts = [];
      this.province = { id: "", name: "请选择省" };
      this.city = { id: "", name: "请选择市" };
      this.district = { id: "", name: "请选择区" };

      clearInterval(this.timer);
      var codeBtn = document.querySelector(".code");
      codeBtn.disabled = false;
      codeBtn.innerHTML = this.$t("set.getCode");
    },
    // 切换区域
    getCity(ids, codes) {
      var that = this;
      this.areaCode = codes;
      this.areaId = ids;
      this.showCity = false;
    }
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
.balck-login .login-advantage .text {
  color: #fff;
}
.balck-login .about-us {
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
  // width: 1160px;
  position: relative;
  margin: 0 auto;
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
  position: absolute;
  left: calc(50% - 180px);
  top: calc(50% - 183px);
  z-index: 10;
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
  position: relative;
}
.login-title {
  padding-top: 30px;
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
  margin: 20px auto 0;
}
.login-body .el-form-item__label {
  display: none;
}
.el-input {
  border-bottom: 1px solid #a0a0a0;
  position: relative;
  margin-bottom: 14px;
}
.login-wrap input {
  background-color: hsla(0, 0%, 88%, 0);
  height: 38px;
  // border: 2px solid #a0a0a0;
  -webkit-appearance: none;
  background-color: rgba(0, 0, 0, 0);
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
  // margin-bottom: 14px;
  border: none;
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
.swiper-container {
  width: 100%;
  min-height: 698px;
}
.swiper-container img {
  width: 100%;
  min-height: 698px;
  max-height: 698px;
}
.actives {
  color: #f0b90b;
  border-bottom: 1px solid #f0b90b;
}
.pb5 {
  padding-bottom: 5px;
}
.plr10 {
  padding-left: 10px;
  padding-right: 10px;
}
.input_select {
  width: 290px;
  position: absolute;
  left: 0;
  top: 46px;
  max-height: 170px;
  overflow-y: auto;
  box-shadow: 0 2px 6px 0 #ccc;
  border-color: #d1d3df;
  background-color: #e5ebf5;
  color: #61688a;
  z-index: 10;
}
.input_select li {
  padding: 10px 15px;
  border-bottom: 1px solid #000;
}
</style>



// WEBPACK FOOTER //
// src/components/login.vue