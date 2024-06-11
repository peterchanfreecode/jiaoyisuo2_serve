<template>
  <!-- <div :class="['nav_bar',{'nav_bars':homeindex}]"> -->
  <div :class="skins == 'nights'?'balck-nav-bar':'nav_bar'" class="nav_bar">
    <div class="content flex between">
      <div class="flex">
        <div class="head">
          <router-link to="/">
            <!-- 首页 -->
            <img :src="srcImg1" class="navbar-logo" style="width:120px;position:relative;top:-2px;border-radius: 10px;">
          </router-link>
        </div>
        <ul class="navbar-item flex mouseDefault ml20">
          <li class="base">
            <router-link to="/leverdealCenter" active-class="active">{{$t('header.lever')}}</router-link>
          </li>
          <li class="base">
            <router-link to="/secondsOrder" active-class="active">{{$t('header.seconds')}}</router-link>
          </li>
          <li class="base">
            <router-link to="/coinCurrencyFlash" active-class="active">{{$t('miscro.flashTrading')}}</router-link>
          </li>
          <li class="base">
            <router-link to="/legalTrade" active-class="active">{{$t('header.fiat')}}</router-link>
          </li>

          <!-- <li class="base apply">
          <router-link to="/legalTrade">
            <span class="iconfont iconhuo"></span>
            <span>创新区上币申请</span>
          </router-link>
          </li>-->
          <li class="base" v-if="isShow">
            <router-link to="/myLegalShops" active-class="active">{{$t('header.myshop')}}</router-link>
          </li>
          <!-- <li class="base" v-if="address.length>0">
          <router-link to="/finance">我的资产</router-link>
          </li>-->
          <li class="base downapp" @click="showapp">
            {{$t('header.app1')}}
            <div class="appcode tl" v-show="appshow">
              <span></span>
              <div class="app-left">
                <p class="code">
                  <!-- <img src="../../static/imgs/qrcode.png" alt> -->
                </p>
              </div>
            </div>
          </li>
        </ul>
      </div>
      <div class="fr">
        <ul class="account-box flex positionR curPer flex">
          <li class="base">
            <router-link to="/helpCenter" active-class="active">{{$t('header.help')}}</router-link>
          </li>

          <!-- <li>
            <img src="../../static/imgs/a7.png" alt>
            <router-link to="/invitation">邀请返佣</router-link>
          </li>-->
          <li v-if="userAccount">
            <!-- <span>{{$t('header.assets')}}</span> -->
            <el-dropdown trigger="click">
              <span class="el-dropdown-link">
                {{$t('header.assets')}}
                <i class="el-icon-arrow-down el-icon--right"></i>
              </span>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item>
                  <router-link to="/finance">{{$t('miscro.assetCenter')}}</router-link>
                </el-dropdown-item>
                <!-- <el-dropdown-item>
                  <router-link to="/exchange">{{$t('miscro.contractInsurance')}}</router-link>
                </el-dropdown-item> -->
                <el-dropdown-item>
                  <router-link to="/financialRecords">{{$t('asset.moneyRecord')}}</router-link>
                </el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
            <!-- <router-link to="/finance">{{$t('header.assets')}}</router-link> -->
          </li>
          <li v-if="userAccount">
            <!-- <router-link to="/accountSet"></router-link> -->
            <el-dropdown trigger="click" @command="handleCommand">
              <span class="el-dropdown-link">
                {{userAccount}}
                <i class="el-icon-arrow-down el-icon--right"></i>
              </span>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item command="1">
                  <router-link to="/authentication">{{$t('header.identify')}}</router-link>
                </el-dropdown-item>
                <el-dropdown-item command="2">
                  <router-link to="/accountSet">{{$t('miscro.safetyCenter')}}</router-link>
                </el-dropdown-item>
                <el-dropdown-item command="3">
                  <router-link to="/userSetting">{{$t('seting.pmethod')}}</router-link>
                </el-dropdown-item>
                <el-dropdown-item command="4">
                  <router-link to="/invitation">{{$t('miscro.promotionCode')}}</router-link>
                </el-dropdown-item>
                <el-dropdown-item command="signOut">{{$t('jc.out')}}</el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </li>
          <li v-if="!userAccount">
            <router-link to="/components/login">{{$t('header.login')}}</router-link>
          </li>
          <li v-if="!userAccount">
            <router-link to="/components/register">{{$t('header.sign')}}</router-link>
          </li>
          <!-- <li @click="signOut" v-if="userAccount">
            <a href="javascript:;">退出</a>
          </li>-->
          <li class="language">
            <!-- <img width="26" :src="$t('logos')" alt> -->
            <!-- <span>{{$t('language')}}</span> -->
            <!-- <span class="iconfont iconiconset0417"></span> -->
            <el-dropdown trigger="click" @command="tabLange">
              <span class="el-dropdown-link">
               <img width="26" :src="$t('logos')" alt>
                <i class="el-icon-arrow-down el-icon--right lang-icon"></i>
              </span>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item :command="item" v-for="item in lang" :key="item.id">
                  <img width="26" :src="item.imgs" alt>
                  <span>{{item.label}}</span>
                </el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </li>
          <li class="switchs">
            <el-switch
              :width="40"
              v-model="value3"
              active-icon-class="iconfont iconyueliang"
              inactive-icon-class="iconfont icontaiyang"
              @change="selectedTab"
            ></el-switch>
          </li>
        </ul>
        <ul class="lang-list" v-show="modelShow">
          <li
            class="langage-item"
            v-for="item in lang"
            :key="item.id"
            @click="tabLange(item.imgs,item.label,item.value)"
          >
            <img :src="item.imgs" alt>
            <span>{{item.label}}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "indexHeader",
  data() {
    return {
      skins: localStorage.getItem("skin") || "days",
      value3: "",
      appshow: false,
      address: "",
      account_number: "",
      assets: "资产",
      orders: "订单",
      isShow: false,
      show1: false,
      show2: false,
      show3: false,
      show4: false,
      show5: false,
      current: 0,
      extension_code: "",
      homeindex: true,
      userAccount: localStorage.getItem("userAccount"),
      lang: [
        {
          id: "1",
          value: "hk",
          label: "中文繁体",
          imgs: "../../static/imgs/hk.png"
        },
        {
          id: "2",
          value: "zh",
          label: "简体中文",
          imgs: "../../static/imgs/cn.png"
        },
        {
          id: "3",
          value: "en",
          label: "English",
          imgs: "../../static/imgs/en.png"
        }
      ],
      value: "",
      modelShow: false,
      langImg: "../../static/imgs/cn.png",
      langText: "中文简体",
      srcImg1: "../../static/imgs/logob.png"
    };
  },
  watch: {
    $router(to, from) {
      if (this.$route.path !== "/") {
        this.homeindex = false;
      } else {
        this.homeindex = true;
      }
    }
  },

  filters: {
    hideFour: function(value) {
      value = value.replace(/(\d{3})\d{4}(\d{4})/, "$1****$2");
      return value;
    }
  },
  created() {
    if (
      localStorage.getItem("skin") &&
      localStorage.getItem("skin") == "nights"
    ) {
      this.value3 = false;
    } else {
      this.value3 = true;
    }
    if (this.$route.path !== "/") {
      this.homeindex = false;
    } else {
      this.homeindex = true;
    }
    if (this.skins == "nights") {
      this.srcImg1 = "../../static/imgs/logob.png";
    } else {
      this.srcImg1 = "../../static/imgs/logob.png";
    }
    this.address = localStorage.getItem("token") || "";
    let is_seller = window.localStorage.getItem("is_seller") || "";
    let status = window.localStorage.getItem("status") || "";
    if (is_seller == 1 && status == 1) {
      this.isShow = true;
    }
  },
  mounted() {
        this.getAndroidLink()
    var that = this;
    var langs = localStorage.getItem("lang") || "zh";

    this.$http({
      url: "/api/" + "set/lang",
      method: "post",
      data: {
        lang: langs
      },
      headers: { Authorization: localStorage.getItem("token") }
    })
      .then(res => {
        if (res.data.type == "ok") {
          that.$i18n.locale = langs;
        }
      })
      .catch(error => {});
    that.account_number = localStorage.getItem("accountNum") || "";
    that.extension_code = localStorage.getItem("extension_code") || "";
    let is_seller = window.localStorage.getItem("is_seller") || "";
    let status = window.localStorage.getItem("status") || "";

    if (is_seller == 1 && status == 1) {
      that.isShow = true;
    }
    eventBus.$on("seller", msg => {
      if (msg) {
        let is_seller = window.localStorage.getItem("is_seller") || "";
        let status = window.localStorage.getItem("status") || "";
        if (is_seller == 1 && status == 1) {
          that.isShow = true;
        }
      }
    });
    if (localStorage.getItem("token")) {
      this.$http({
        url: "/api/" + "user/info",
        method: "get",
        data: {},
        headers: { Authorization: localStorage.getItem("token") }
      })
        .then(res => {
          if (res.data.type == "ok") {
            if (res.data.message.is_seller == 1) {
              that.isShow = true;
            }
          }
        })
        .catch(error => {});
    }
  },
  methods: {
    // 退出登录
    signOut() {},
    // 跳转注册页
    registers() {
      var that = this;
      that.$router.push({ path: "/components/register" });
    },
    hovers() {
      var that = this;
      that.modelShow = true;
    },
    showapp() {
      this.appshow = !this.appshow;
    },
    leverHover() {
      var that = this;
      that.modelShow = false;
    },
    tabLange(command) {
      var that = this;
      that.langImg = command.imgs;
      that.langText = command.label;
      that.modelShow = false;
      var values = command.value;
      localStorage.setItem("lang", values);
      that.$i18n.locale = values;
      this.$http({
        url: "/api/" + "set/lang",
        method: "post",
        data: {
          lang: values
        },
        headers: { Authorization: localStorage.getItem("token") }
      })
        .then(res => {
          if (res.data.type == "ok") {
            location.reload();
          }
        })
        .catch(error => {});
    },
    getAndroidLink(){
         $('.code').qrcode({
            width: 100, //宽度
            height: 100, //高度
            text: "https://app.doex.live"
          });
    },
    selectedTab(val) {
      console.log(val);
      location.reload();
      if (val == true) {
        localStorage.setItem("skin", "days");
      } else {
        localStorage.setItem("skin", "nights");
      }
    },
    handleCommand(command) {
      var that = this;
      if (command == "signOut") {
        var lang = localStorage.getItem('lang');
        localStorage.clear();
        localStorage.setItem('lang',lang);
        setTimeout(function() {
          that.$router.push({ path: "/components/login" });
          location.reload();
        }, 500);
      }
    }
  }
};
</script>
<style scoped>
.head{
  font-weight: bold;
  font-size: 17px;
}
.switchs {
  position: relative;
  top: -3px;
}
.switchs >>> .el-switch__label * {
  font-size: 10px;
}
.switchs >>> .is-active {
  z-index: 0 !important;
  left: -60px !important;
  display: none !important;
}
.switchs >>> .el-switch__label--left {
  position: absolute;
  left: 5px;
  z-index: 10;
  display: block;
}
.switchs >>> .el-switch__label--left span {
  color: #fff;
}
.switchs >>> .el-switch__label--right {
  position: absolute;
  z-index: 10;
  display: block;
  left: 10px;
}
.switchs >>> .icontaiyang {
  font-size: 16px;
  color: #fff;
}
.switchs >>> .iconyueliang {
  font-size: 14px;
}
</style>

<style scoped lang="scss">
.nav_bar {
  line-height: 66px;
  height: 66px;
  width: 100%;
  min-width: 1200px;
  padding: 0 30px;
  background: #f8f6f6;
  position: fixed;
  top: 0;
  z-index: 11000;
  border-bottom: 1px solid #e6ecf2;
}
.balck-nav-bar {
  background: #1e2b34;
  border-bottom: 1px solid #000;
}
.navbar-logo {
  vertical-align: middle;
}
.content {
  margin: 0 auto;
  position: relative;
}
.content li {
  height: 66px;
  line-height: 66px;
  padding: 0 11px;
  display: block;
  text-align: center;
  font-size: 16px;
  cursor: pointer;
  color: #1e2643;
}
.balck-nav-bar .content li {
  color: #becbc6;
}

.content .active {
  color: #fff;
}
.content li a:hover {
  color: #f0b90b;
}
.content .base .active {
  color: #f0b90b;
}
.nav_bars {
  background-color: rgba(0, 0, 0, 0.15);
  position: absolute;
  top: 0;
  left: 0;
  z-index: 99;
}
.iconhuo {
  color: #0068ff;
  font-size: 26px;
  position: relative;
  top: 3px;
}
.apply {
  position: relative;
  top: -6px;
}

// .language {
//   position: relative;
//   top: -3px;
// }
.lang-list {
  font-size: 16px;
  background-color: #263346;
  box-shadow: 0 6px 14px 0 rgba(0, 0, 0, 0.41);
  color: #ced1d1;
  position: absolute;
  right: 0;
  top: 67px;
  z-index: 66;
}
.lang-list li {
  line-height: 40px;
  height: 40px;
  border-bottom: 1px solid #393e55;
}
.lang-list li:last-child {
  border: none;
}
.langage-item img {
  width: 22px;
  height: 15px;
}
.langage-item span {
  color: #fff;
}
.downapp {
  position: relative;
  .appcode {
    position: absolute;
    top: 60px;
    z-index: 10;
    left: -21px;
    width: 120px;
    background-color: #fff;
    height: 110px;
    border-radius: 4px;
    span {
      position: absolute;
      top: -20px;
      left: 46px;
      display: inline-block;
      width: 0;
      height: 0;
      border-width: 10px;
      border-style: solid;
      border-color: transparent transparent #fff transparent;
    }
    .app-left {
      width: 100px;
      float: left;
      text-align: center;
      position: relative;
      .code {
        position: relative;
        top: 5px;
        left: 10px;
        img {
          width: 100px;
          height: 100px;
        }
        p:last-child {
          position: absolute;
          bottom: 0;
        }
      }
    }
    .app-right {
      float: right;
      width: 110px;
      text-align: center;
      margin-right: 10px;
      padding-right: 10px;
      margin-top: 5px;
      p {
        top: -10px;
        img {
          width: 20px;
          position: relative;
          top: 4px;
          margin-top: 0;
        }
      }
      img {
        width: 40px;
        margin-top: 30px;
      }
      b {
        display: block;
        width: 100px;
        height: 100px;
        border: 1px solid #f7f6f6;
        text-align: center;
        top: 0;
        border-radius: 4px;
      }
    }
    .texts {
      position: relative;
      top: -30px;
      color: #333;
    }
  }
}
.el-dropdown {
  font-size: 16px;
}
.el-dropdown-link:hover {
  color: #f0b90b;
}
.balck-nav-bar .el-dropdown-link {
  color: #becbc6;
}
.lang-icon {
  position: relative;
  top: 1px;
}
</style>



// WEBPACK FOOTER //
// src/view/indexHeader.vue