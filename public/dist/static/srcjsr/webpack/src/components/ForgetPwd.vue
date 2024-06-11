<template>
  <div :class="skins=='days'?'forget-box':'forget-box balck-login'">
    <!-- <indexHeader></indexHeader> -->
    <div class="forget-password">
      <div class="forget-password-content">
        <div class="content">
          <div class="content-list" v-if="!showReset">
            <h4>{{$t('miscro.resetpwd')}}</h4>
            <div class="content-tab flex">
              <p class="tc" @click="tabSelect('mobile')">
                <span :class="[{'active':isMb=='mobile'}]">{{$t('fat.phone')}}</span>
              </p>
              <p class="tc" @click="tabSelect('email')">
                <span :class="[{'active':isMb=='email'}]">{{$t('fat.email')}}</span>
              </p>
            </div>
            <div class="content-input">
              <div class="content-email" v-if="isMb=='email'">
                <div class="flex between">
                  <input
                    type="text"
                    :placeholder="$t('register.enterEmail')"
                    v-model="account_number"
                  />
                </div>
                <div class="flex between mt20 code">
                  <input type="text" :placeholder="$t('set.enterCode')" v-model="phoneCode" />
                  <button type="button" @click="setTime">{{$t('set.getCode')}}</button>
                </div>
              </div>
              <div class="content-phone" v-if="isMb=='mobile'">
                <div class="flex content-phone-input">
                  <div class="flex alcenter" @click="showCity= !showCity">
                    <p class="mr10">+{{areaCode}}</p>
                    <i class="iconfont icon-xiala ft12" v-if="!showCode"></i>
                    <i class="iconfont icon-shangla_icon ft12" v-else></i>
                  </div>
                  <input
                    type="text"
                    :placeholder="$t('register.enterPhone')"
                    v-model="account_number"
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
                <div class="code flex between mt20">
                  <input type="text" :placeholder="$t('set.enterCode')" v-model="phoneCode" />
                  <button type="button" @click="setTime">{{$t('set.getCode')}}</button>
                </div>
              </div>
              <div class="btns" @click="check">
                <button>{{$t('cuy.confirm')}}</button>
              </div>
            </div>
          </div>
          <div class="main" v-if="showReset">
            <div class="main_title">{{$t('set.setPsw')}}</div>
            <div class="register-input">
              <span class="register-item">{{$t('set.enterPsw')}}</span>
              <input type="password" class="input-main input-content" v-model="password" id="pwd" />
            </div>
            <div class="register-input">
              <span class="register-item">{{$t('set.enterPswagain')}}</span>
              <input
                type="password"
                class="input-main input-content"
                v-model="re_password"
                id="repwd"
              />
            </div>
            <button
              class="register-button curPer"
              type="button"
              @click="resetPass"
              style="margin-top:20px"
            >{{$t('td.confirm')}}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import indexHeader from "@/view/indexHeader";
import indexFooter from "@/view/indexFooter";
export default {
  components: { indexHeader, indexFooter },
  data() {
    return {
      isMb: "mobile",
      account_number: "",
      phoneCode: "",
      showReset: false,
      password: "",
      re_password: "",
      value6: "",
      cities: [],
      skins: localStorage.getItem("skin") || "days",
      areaCode: "",
      areaId: "",
      showCity: false,
      showCode: false
    };
  },
  created() {
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
    sendCode(url) {},
    setTime(e) {
      var that = this;
      var data = {};
      if (e.target.disabled) {
        return;
      } else {
        var url = "sms_send";
        if (that.account_number == "") {
          layer.tips(that.$t("set.enterAccount"), "#account");
          return;
        }
        if (that.isMb == "mobile") {
          url = "sms_send";
          data = {
            user_string: this.account_number,
            type: "forget",
            area_code: that.areaCode,
            area_code_id: that.areaId
          };
        } else {
          url = "sms_mail";
          data = {
            user_string: this.account_number,
            type: "forget"
          };
        }
        console.log()
        this.$http({
          url: "/api/" + url,
          method: "post",
          data: data
        }).then(res => {
          layer.msg(res.data.message);
          if (res.data.type == "ok") {
            var time = 60;
            var timer = null;
            timer = setInterval(function() {
              e.target.innerHTML = time + that.$t("fat.second");
              e.target.disabled = true;
              if (time == 0) {
                clearInterval(timer);
                e.target.innerHTML = that.$t("set.code");
                e.target.disabled = false;
                return;
              }
              time--;
            }, 1000);
          }
        });
        // this.sendCode(url);
      }
    },
    check() {
      var that = this;
      var reg = /^[0-9]\d*$/;
      let user_string = this.account_number;
      var isMobile = reg.test(user_string);
      // var isEmail = emreg.test(user_string);
      var url = "user/check_mobile";
      var data = {};

      if (user_string == "") {
        layer.tips(that.$t("set.enterAccount"), "#account");
        return;
      } else if (this.phoneCode == "") {
        layer.tips(that.$t("set.enterCode"), "#pwd");
        return;
      } else if (user_string.indexOf('@')!=-1) {
        url = "user/check_email";
        data.email_code = this.phoneCode;
      } else if (isMobile) {
        url = "user/check_mobile";
        data.mobile_code = this.phoneCode;
      } else {
        layer.tips(that.$t("set.accountFalse"), "#account");
        return;
      }

      this.$http({
        url: "/api/" + url,
        method: "post",
        data: data
      }).then(res => {
        layer.msg(res.data.message);
        if (res.data.type == "ok") {
          // this.showReset = true;
          localStorage.setItem("forgetAccount", that.account_number);
          localStorage.setItem("forgetCode", that.phoneCode);
          setTimeout(function() {
            that.$router.push({ path: "/ResetPwd", query:{areaId:that.areaId,areaCode:that.areaCode}});
            // area_code: that.areaCode,
            // area_code_id: that.areaId
          }, 1000);
          // window.location.href = "resetpass.html?user_string=" + names + "&" + "code=" + verify;
          // this.$router.push({path:'/resetPwd',params:{user_string:user_string,code:this.phoneCode}})
        }
      });
    },
    resetPass() {
      if (this.password == "") {
        layer.msg(this.$t("set.enterPsw"));
        return;
      } else if (this.re_password == "") {
        layer.msg(this.$t("set.enterPswagain"));
        return;
      } else if (this.password !== this.re_password) {
        layer.msg(this.$t("set.pswFalse"));
        return;
      } else {
        let data = {
          account: this.account_number,
          password: this.password,
          repassword: this.re_password,
          code: this.phoneCode,
          area_code: this.areaCode,
          area_code_id: this.areaId
        };
        this.$http({
          url: "/api/user/forget",
          method: "post",
          data: data
        }).then(res => {
          layer.msg(res.data.message);
          if (res.data.type == "ok") {
            this.$router.push("/components/login");
          }
        });
      }
    },
    // 选择验证账号
    tabSelect(type) {
      var that = this;
      that.isMb = type;
      that.account_number = "";
      that.phoneCode = "";
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
.balck-login .content {
  background-color: #1e2b34;
}
.balck-login .content h4 {
  background-color: #1e2b34;
  color: #fff;
}
.balck-login .content-input input {
  background-color: #1e2b34;
  color: #fff;
}
.balck-login .code button {
  color: #fff;
}
.forget-password {
  width: 100%;
  margin-top: 66px;
  margin-bottom: 50px;
}
.forget-password-content {
  width: 1160px;
  margin: 0 auto;
  min-height: 603px;
}
.content {
  width: 920px;
  padding: 0 30px 40px;
  background: #fff;
  position: absolute;
  left: 50%;
  top: 50%;
  text-align: center;
  transform: translate(-50%, -50%);
  margin-top: -41px;
  box-shadow: 0 3px 14px 1px rgba(4, 0, 0, 0.03);
  border-radius: 8px;
}
.content img {
  width: 100px;
}
.content h4 {
  padding: 0 30px;
  background: #fff;
  position: relative;
  z-index: 99;
  color: #1f2a48;
  font-size: 20px;
  line-height: 30px;
}
.content-list {
  width: 330px;
  margin: 20px auto 0;
}
.content-tab {
  width: 100%;
}
.content-tab p {
  width: 50%;
  font-size: 16px;
}
.content-tab span {
  padding-bottom: 3px;
  display: inline-block;
}
.content-tab .active {
  border-bottom: 2px solid #f0b90b;
}
.content-input {
  width: 330px;
}
.content-email div {
  width: 330px;
  line-height: 40px;
  height: 40px;
  border-bottom: 1px solid #f0b90b;
}
.content-input input {
  padding: 0 10px;
  border: none;
  text-align: left;
  font-size: 14px;
  border-radius: 0;
}
.content-phone {
  width: 330px;
}
.content-phone >>> .el-select {
  height: 26px;
  line-height: 26px;
  position: relative;
  top: 4px;
}
.content-phone >>> .el-input__icon {
  line-height: 26px;
  width: auto;
  position: relative;
  top: 4px;
}
.content-phone >>> .el-input--suffix .el-input__inner {
  padding-right: 10px;
  padding: 0 3px;
}
.content-phone >>> .el-select .el-input__inner {
  border: none;
  max-width: 70px;
  height: 26px;
  line-height: 26px;
  position: relative;
  top: 4px;
}
.content-phone-input,
.code {
  width: 330px;
  line-height: 40px;
  height: 40px;
  border-bottom: 1px solid #f0b90b;
  position: relative;
}
.code button {
  background-color: rgba(0, 0, 0, 0);
  border: none;
  outline: none;
  cursor: pointer;
}
.btns button {
  width: 330px;
  height: 35px;
  box-sizing: border-box;
  text-align: center;
  display: block;
  margin: 40px auto 20px;
  border: none;
  font-size: 16px;
  border-radius: 4px;
  background: #f0b90b;
  border-color: #f0b90b;
  cursor: pointer;
  color: #fff;
}
.add {
  color: #606266;
  font-size: 16px;
  position: relative;
  top: 3px;
  margin-left: 15px;
}
.pb5 {
  padding-bottom: 5px;
}
.plr10 {
  padding-left: 10px;
  padding-right: 10px;
}
.input_select {
  width: 330px;
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
  padding: 0 15px;
  border-bottom: 1px solid #000;
}
</style>



// WEBPACK FOOTER //
// src/components/ForgetPwd.vue