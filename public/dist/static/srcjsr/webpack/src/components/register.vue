<template>
  <div :class="skins=='days'?'login':'login balck-login'">
    <!-- <indexHeader></indexHeader> -->
    <div class="logins">
      <div class="login-header">
        <div class="login-header-content">
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
            <div class="login-wrap" v-show="registerStatus">
              <div class="login-title">
                <h2>{{$t('lg.register')}}</h2>
              </div>
              <div class="login-body">
                <div class="el-form">
                  <div class="el-form-item el-form-item--feedback">
                    <div class="flex register-tab between">
                      <p class="tc" @click="setIsMb('mobile')">
                        <span
                          class="pb5 plr10"
                          :class="[{'actives':isMb=='mobile'}]"
                        >{{$t('register.phoneRegister')}}</span>
                      </p>
                      <p class="tc" @click="setIsMb('email')">
                        <span
                          class="pb5 plr10"
                          :class="[{'actives':isMb=='email'}]"
                        >{{$t('register.emailRegister')}}</span>
                      </p>
                    </div>
                    <div v-if="isMb=='mobile'" class="mt10">
                      <div class="el-form-item__content flex">
                        <div class="el-input flex alcenter">
                          <div class="flex alcenter" @click="showCity= !showCity">
                            <p class="mr10">+{{areaCode}}</p>
                            <i class="iconfont icon-xiala ft12" v-if="!showCode"></i>
                            <i class="iconfont icon-shangla_icon ft12" v-else></i>
                          </div>
                          <input
                            type="text"
                            :placeholder="$t('register.enterPhone')"
                            class="el-input__inner flex2"
                            v-model="account"
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
                      <div class="el-form-item el-form-item--feedback mt10">
                        <div class="el-form-item__content">
                          <div class="el-input flex alcenter">
                            <input
                              type="text"
                              :placeholder="$t('set.code')"
                              class="el-input__inner"
                              v-model="code"
                            />
                            <button class="code" @click="sendCode">{{$t('set.getCode')}}</button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div v-if="isMb=='email'" class="mt10">
                      <div class="el-form-item__content">
                        <div class="el-input flex alcenter">
                          <input
                            type="text"
                            :placeholder="$t('register.enterEmail')"
                            class="el-input__inner"
                            v-model="account"
                          />
                        </div>
                      </div>
                      <div class="el-form-item el-form-item--feedback mt10">
                        <div class="el-form-item__content">
                          <div class="el-input flex alcenter">
                            <input
                              type="text"
                              :placeholder="$t('set.code')"
                              class="el-input__inner"
                              v-model="code"
                            />
                            <button class="code" @click="sendCode">{{$t('set.getCode')}}</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- <div class="ft12">注册即表示您同意KiBiEx的
                    <router-link
                      :to="{path:'/components/noticeDetail',query:{id:'8'}}"
                      style="cursor: pointer;color:#f0b90b;"
                    >用户协议及隐私政策</router-link>
                    及
                    <router-link
                      :to="{path:'/components/noticeDetail',query:{id:'36'}}"
                      style="cursor: pointer;color:#f0b90b;"
                    >隐私保护政策</router-link>
                  </div>-->
                  <div class="el-form-item el-form-item--feedback" @click="checkCode">
                    <div class="el-form-item__contents">
                      <button
                        :disabled="code==''||account==''"
                        type="button"
                        class="el-button login-btn el-button--default"
                        :class="[{'active':code&&account}]"
                      >
                        <span>{{$t('auth.submit')}}</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="toggle">
                <div class="toggle-container">
                  <p>
                    <router-link
                      :to="{path:'/components/login'}"
                      style="cursor: pointer;color:#000000;"
                    >
                      {{$t('lg.register1')}}
                      <span style="color:#FFFFFF;">{{$t('lg.login')}}KiBiEx</span>
                    </router-link>
                  </p>
                </div>
              </div>
            </div>
            <div class="login-wrap" v-show="!registerStatus">
              <div class="login-title">
                <h2>{{$t('lg.register')}}</h2>
              </div>
              <div class="login-body">
                <div class="el-form">
                  <div class="el-form-item el-form-item--feedback">
                    <div>
                      <div class="el-form-item__content">
                        <div class="el-input">
                          <input
                            type="password"
                            :placeholder="$t('set.enterPsw')"
                            class="el-input__inner"
                            v-model="pwd"
                          />
                        </div>
                      </div>
                      <div class="el-form-item__content">
                        <div class="el-input">
                          <input
                            type="password"
                            :placeholder="$t('set.enterPswagain')"
                            class="el-input__inner"
                            v-model="repwd"
                          />
                        </div>
                      </div>
                      <div class="el-form-item__content">
                        <div class="el-input">
                          <input
                            type="text"
                            :placeholder="$t('register.inviteCode')"
                            class="el-input__inner"
                            v-model="invite"
                          />
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="ft12 mt10">
                    {{$t('lg.register2')}}
                    <router-link
                      :to="{path:'/components/noticeDetail',query:{id:'8'}}"
                      style="cursor: pointer;color:#f0b90b;"
                    >{{$t('lg.register3')}}</router-link>
                  </div>
                  <div class="el-form-item el-form-item--feedback" @click="register">
                    <div class="el-form-item__contents">
                      <button
                        :disabled="pwd==''||repwd==''|| invite==''"
                        type="button"
                        class="el-button login-btn el-button--default"
                        :class="[{'active':pwd&&repwd&&invite}]"
                      >
                        <span>{{$t('lg.register')}}</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- <indexFooter></indexFooter> -->
  </div>
</template>

<script>
import indexHeader from "@/view/indexHeader";
import indexFooter from "@/view/indexFooter";
import Swiper from "swiper";
import "swiper/dist/css/swiper.min.css";
export default {
  components: {
    indexHeader,
    indexFooter
  },
  data() {
    return {
      codeTrue: false, //验证码是否正确
      isMb: "mobile", //是否为手机注册
      account: "", //用户名
      pwd: "", //密码
      repwd: "", //重复密码
      code: "", //验证码
      invite: "", //邀请码
      timer: "", //倒计时timer
      showList: false, //是否显示地址列表
      province: { id: "", name: "请选择省" }, //所选省份
      provinces: [], //省份列表
      city: { id: "", name: "请选择市" }, //所选城市
      cities: [], //城市列表
      district: { id: "", name: "请选择区" }, //所选地区
      districts: [],
      quotationList: [], //地区列表
      registerStatus: true,
      codeImg: false,
      newsList: [],
      skins: localStorage.getItem("skin") || "days",
      codeShow: 3,
      swiperImgs: [],
      areaCode: "",
      areaId: "",
      showCity: false,
      showCode: false
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
    var invite = this.get_all_params().extension_code;
    if (invite) {
      this.invite = invite;
    }
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
    get_all_params() {
      var url = location.href;
      var nameValue;
      var paraString = url
        .substring(url.indexOf("?") + 1, url.length)
        .split("&");
      var paraObj = {};
      for (var i = 0; (nameValue = paraString[i]); i++) {
        var name = nameValue.substring(0, nameValue.indexOf("=")).toLowerCase();
        var value = nameValue.substring(
          nameValue.indexOf("=") + 1,
          nameValue.length
        );
        if (value.indexOf("#") > -1) {
          value = value.split("#")[0];
        }
        paraObj[name] = decodeURI(value);
      }
      return paraObj;
    },
    // 获取地区列表
    getRegion(id, type, name) {
      if (type == "") {
        this.showList = false;
        this.district = { id: id, name: name };
        return;
      } else if (type == "cities") {
        if (name == this.province.name) {
          this.showList = "cities";
          return;
        }
      } else if (type == "districts") {
        if (name == this.city.name) {
          this.showList = "districts";
          return;
        }
      }
      var pId = "";
      //  if(id != ''){
      //    data.parent_id = id;
      //  }
      if (id !== "") {
        pId = "?parent_id=" + id;
      }

      // this.$http({
      //   url: "/api/region" + pId,
      //   method: "get"
      // }).then(res => {
      //   if(res.data.type == 'ok'&&res.data.message.length != 0){

      //     if (type == "provinces") {
      //       this.provinces = res.data.message;
      //     } else if (type == "cities") {
      //       this.province = { name: name, id: id };
      //       this.city = { id: "", name: "请选择市" };
      //       this.district = { id: "", name: "请选择区" };
      //       this.showList = "cities";
      //       this.cities = res.data.message;
      //     } else {
      //       this.district = { id: "", name: "请选择区" };
      //       this.showList = "districts";
      //       this.city = { name: name, id: id };
      //       this.showCities = false;
      //       this.districts = res.data.message;
      //     }
      //   }
      // });
    },
    // 切换注册方式
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
    // 发送验证码
    sendCode(e) {
      var that = this;
      let isMb = that.isMb;
      let url = "sms_send";
      var data = {};
      if (that.account == "") {
        layer.msg(that.$t("set.enterAccount"));
        return false;
      }
      if (that.isMb == "email") {
        url = "sms_mail";
         data = { user_string: that.account, mobile_code: that.code };
      } else {
        url = "sms_send";
        data = { user_string: that.account, mobile_code: that.code,area_code_id: that.areaId,
                        area_code: that.areaCode};
      }

      this.$http({
        url: "/api/" + url,
        method: "post",
        data: data
      }).then(res => {
        layer.msg(res.data.message);
        if (res.data.type == "ok") {
          var time = 60;
          that.timer = setInterval(function() {
            e.target.innerHTML = time + that.$t("fat.second");
            e.target.disabled = true;
            if (time == 0) {
              clearInterval(that.timer);
              e.target.innerHTML = that.$t("set.getCode");
              e.target.disabled = false;
              return;
            }
            time--;
          }, 1000);
        }
      });
    },
    // 验证验证码
    checkCode() {
      let code = this.code;
      if (this.account == "") {
        layer.msg(this.$t("set.enterAccount"));
        return;
      } else if (this.code == "") {
        layer.msg(this.$t("set.enterCode"));
        return;
      } else {
        let data = {};
        let url = "user/check_email";
        if (this.isMb == "mobile") {
          data = { mobile_code: this.code };
          url = "user/check_mobile";
        } else {
          url = "user/check_email";
          data = { email_code: this.code };
        }

        this.$http({
          url: "/api/" + url,
          method: "post",
          data: data
        }).then(res => {
          layer.msg(res.data.message);

          if (res.data.type == "ok") {
            this.registerStatus = false;
            // this.getRegion("", "provinces");
          } else {
          }
        });
      }
    },
    // 注册
    register() {
      var that = this;
      if (that.pwd == "") {
        layer.msg(that.$t("set.enterPsw"));
        return;
      } else if (that.pwd.length < 6 || that.pwd.length > 16) {
        layer.msg(that.$t("register.psw16"));
        return;
      } else if (that.repwd == "") {
        layer.msg(that.$t("set.enterPswagain"));
        return;
      } else if (that.pwd !== that.repwd) {
        layer.msg(that.$t("set.pswFalse"));
        return;
      } else if (that.invite == "") {
        layer.msg(that.$t("register.inviteCode"));
        return;
      }
      var data = {};
      var isMb = that.isMb;
      data.type = that.isMb;
      data.user_string = that.account;
      data.code = that.code;
      data.password = that.pwd;
      data.re_password = that.repwd;
      data.extension_code = that.invite;
      var btnStatus = true;
      if(that.isMb == 'mobile'){
        data.area_code=that.areaCode;
					data.area_code_id=that.areaId;
      }
      if (btnStatus) {
        btnStatus = false;
        this.$http({
          url: "/api/" + "user/register",
          data: data,
          method: "post"
        }).then(res => {
          layer.msg(res.data.message);
          if (res.data.type == "ok") {
            setTimeout(function() {
              that.$router.push("/components/login");
            }, 500);
          }
        });
      } else {
        layer.msg(that.$t("miscro.submitRepeatedly"));
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
    // 切换区域
    getCity(ids, codes) {
      var that = this;
      this.areaCode = codes;
      this.areaId = ids;
      this.showCity = false;
    }
  }
};
</script>

<style scoped>
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
.logins {
  margin-top: 66px;
}
.login-header {
  width: 100%;
  /* height: 688px; */
  width: 100%;
  /* background: url(../../static/imgs/banner.jpg); */
  position: relative;
}
.login-header-content {
  /* height: 688px; */
  /* width: 1160px; */
  position: relative;
  margin: 0 auto;
}
.el-form-item__content .active {
  background-color: #f0b90b;
  border-color: #f0b90b;
  color: #fff;
  opacity: 1;
}
.el-form-item__contents .active {
  background-color: #f0b90b;
  border-color: #f0b90b;
  color: #fff;
  opacity: 1;
}
.actives {
  color: #f0b90b;
  border-bottom: 1px solid #f0b90b;
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
  width: 290px;
  margin: 20px auto 0;
}
.login-body .el-form-item__label {
  display: none;
}
.el-form-item__content {
  border-bottom: 2px solid #a0a0a0;
}
.login-wrap input {
  background-color: hsla(0, 0%, 88%, 0);
  height: 38px;
  border: none;
  border-radius: 0;

  -webkit-appearance: none;
  background-color: rgba(0, 0, 0, 0);
  background-image: none;
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
  margin-top: 58px;
  background-color: rgba(37, 43, 71, 0.43);
  height: 56px;
}

.toggle p {
  color: #252b47;
  vertical-align: middle;
  padding: 17px 0;
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
  height: 698px;
}
.code {
  position: absolute;
  top: 7px;
  right: 0;
  background-color: rgba(0, 0, 0, 0);
  cursor: pointer;
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
// src/components/register.vue