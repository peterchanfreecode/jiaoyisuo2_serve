<template>
  <div :class="skins=='days'?'account-main':'account-main balck'">
    
    <ul class="list">
       <li class="flex between">
        <div class="flex">
          <h3>{{$t('miscro.safetyCenter')}}</h3>
         
        </div>
       
      </li>
      <li class="flex between">
        <div class="flex">
          <h3>{{$t('set.verification')}}</h3>
          <p>{{$t('miscro.safeText1')}}</p>
        </div>
        <p>
          <span>{{phone}}</span>
        </p>
      </li>
      <li class="flex between">
        <div class="flex">
          <h3>{{$t('set.loginpwd')}}</h3>
          <p class="fl">{{$t('miscro.safeText2')}}</p>
        </div>

        <span class="base mouseDefault" @click="goPwd()">{{$t('set.reset')}}</span>
      </li>
    </ul>
      
  </div>
</template>
<script>
import "@/lib/clipboard.min.js";
export default {
  name: "accountSet",
  data() {
    return {
      top: {
        legalBalance: "",
        leverBalance: "",
        name: "",
        code: "",
        account: "",
        accountHide: "",
        type: 1
      },
      phone: "",
      extension_code: "",
      lever: "低",
      widthBar: "width: 25%",
      bar: 25,
      authen: 0,
      psrc: require("@/assets/images/icon_error.png"),
      esrc: require("@/assets/images/icon_error.png"),
      skins: localStorage.getItem("skin") || "days"
    };
  },
  created() {
    this.userInfo();
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
    goPwd() {
      this.$router.push("/forgetPwd");
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
            that.top.account = res.data.message.account_number;
            that.phone = res.data.message.phone;
          }
        })
        .catch(error => {});
    },
    
  }
};
</script>
<style lang="scss" scoped>
.account-main {
  width: 1160px;
  margin: 20px auto;
  min-height: 640px;
}
.account-top-left {
  width: 550px;
  height: 230px;
  background-color: #393e55;
  box-shadow: 0 3px 14px 1px rgba(0, 0, 0, 0.18);
  border-radius: 8px;
  padding: 20px 0 20px 30px;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
}
.account-top-right {
  width: 550px;
  height: 230px;
  background-color: #393e55;
  box-shadow: 0 3px 14px 1px rgba(0, 0, 0, 0.18);
  border-radius: 8px;
  padding: 15px 35px 0;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
}
.account-top-left img {
  width: 80px;
  padding-top: 35px;
}
.account-top-left-content {
  margin-left: 30px;
  width: 274px;
  color: #fff;
  padding-top: 30px;
}
.account-top-right div {
  color: #fff;
  padding-top: 15px;
}
.list {
  width: 100%;
  margin-top: 40px;
  background: #fff;
  margin-bottom: 40px;
  padding-bottom: 20px;
  border-radius: 8px;
  padding: 0 44px;
  min-height: 460px;
}
.list li {
  padding: 20px 0;
  border-bottom: 1px solid #ddd5bb;
}
.imgs {
  width: 480px;
}
.list li h3{
    width: 200px;
    font-weight: bold;
    font-size: 16px;
}
.list li:last-child{
    border: none;
}
.list div p{
    color: #999;
}
.balck .list{
  background-color: #1e2b34;
  color: #fff;
}
.balck .list li{
  border-bottom: 1px solid #000;
}
</style>





// WEBPACK FOOTER //
// src/view/accountSet.vue