<template>
  <div class="forget-box">
    <!-- <indexHeader></indexHeader> -->
    <div class="forget-password">
      <div class="forget-password-content">
        <div class="content">
          <img src="../../static/imgs/logo.png" alt>
          <div class="content-list">
            <h4>{{$t('set.setPsw')}}</h4>
             <div class="content-input">
              <div class="content-phone">
                <div class="flex content-phone-input">
                  <input type="password" :placeholder="$t('set.enterPsw')" v-model="password">
                </div>
                <div class="code flex between mt20">
                  <input type="password" :placeholder="$t('set.enterPswagain')" v-model="re_password">
                </div>
              </div>
              <div class="btns" @click="resetPass">
                <button>{{$t('cuy.confirm')}}</button>
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
export default {
  components: { indexHeader, indexFooter },
  data() {
    return {
      password: "",
      re_password: "",
      phoneCode:localStorage.getItem('forgetCode') || '',
      account_number:localStorage.getItem('forgetAccount') || '',
      areaCode:'',
      areaId:"",
    };
  },
  created() {
    this.areaCode = this.$route.query.areaCode;
     this.areaId = this.$route.query.areaId;
  },
  beforeCreate() {
    document
      .querySelector("html")
      .setAttribute("style", "background-color: rgb(247, 247, 247);");
  },
  beforeDestroy() {
    document.querySelector("html").removeAttribute("style");
  },
  methods: {
    resetPass() {
        var that = this;
      if (that.password == "") {
        layer.msg(that.$t('set.enterPsw'));
        return;
      } else if (that.re_password == "") {
        layer.msg(that.$t('set.enterPswagain'));
        return;
      } else if (that.password !== that.re_password) {
        layer.msg(that.$t('set.pswFalse'));
        return;
      } else {
        let data = {
          account: that.account_number,
          password: that.password,
          repassword: that.re_password,
          code: that.phoneCode,
          area_code: that.areaCode,
          area_code_id: that.areaId
        };
        this.$http({
          url: "/api/user/forget",
          method: "post",
          data: data
        }).then(res => {
          layer.msg(res.data.message);
          if (res.data.type == "ok") {
              setTimeout(function(){
                   that.$router.push("/components/login");
              },1000)
           
          }
        });
      }
    },
  }
};
</script>

<style scoped>
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
  padding: 30px 30px 40px;
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
  line-height: 20px;
  margin-top: 15px;
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
  color: #f0b90b;
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

.content-phone-input,
.code {
  width: 330px;
  line-height: 40px;
  height: 40px;
  border-bottom: 1px solid #f0b90b;
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
</style>



// WEBPACK FOOTER //
// src/components/ResetPwd.vue