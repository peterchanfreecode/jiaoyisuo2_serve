<template>
  <div id="pay-opts" :class="skins=='days'?'whites':'whites balck'">
    <div class="inp-item">
      <div>{{$t('fat.realName')}}</div>
      <input type="text" class="请输入真实姓名" v-model="name">
    </div>
    <div class="inp-item">
      <div>{{$t('seting.opening')}}</div>
      <input type="text" class="请输入开户行名称" v-model="bankName">
    </div>
    <div class="inp-item">
      <div>{{$t('seting.bank')}}</div>
      <input type="number" class="请输入银行卡号" v-model="bankNum">
    </div>
    <div class="inp-item">
      <div>{{$t('seting.alipay')}}</div>
      <input type="text" class="请输入支付宝账号" v-model="ali">
    </div>
    <div class="inp-item">
      <div>{{$t('seting.wename')}}</div>
      <input type="text" class="请输入微信昵称" v-model="weChatName">
    </div>
    <div class="inp-item">
      <div>{{$t('seting.wechat')}}</div>
      <input type="text" class="微信账号" v-model="weChatAccount">
    </div>
    <div class="uploads flex ftw ml15 mr10">
      <div class="uploads-list tc">
        <p class="title ft16">{{$t('miscro.alipayCode')}}</p>
        <div>
          <img :src="src1" alt class="alipay-img">
          <!-- <img class="alipay-img" src="images/myimg/688660269851391423.png" alt> -->
          <input type="file" id="alipay" @change="uploads(1)">
        </div>
      </div>
      <div class="uploads-list tc">
        <p class="title ft16">{{$t('miscro.wechatCode')}}</p>
        <div>
          <img :src="src2" alt class="wechat-img">
          <!-- <img  src="images/myimg/688660269851391423.png" alt> -->
          <input type="file" name id="wechat" @change="uploads(2)">
        </div>
      </div>
    </div>
    <div class="btn bgRed" @click="add">{{$t('td.confirm')}}</div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      token: "",
      name: "",
      bankName: "",
      bankNum: "",
      ali: "",
      weChatAccount: "",
      weChatName: "",
      src1: "../../static/imgs/addimg.png",
      src2: "../../static/imgs/addimg.png",
      skins: localStorage.getItem("skin") || "days"
    };
  },
  created() {
    this.token = window.localStorage.getItem("token") || "";
    if (this.token == "") {
      this.$router.push("/components/login");
    }
    this.getInfo();
  },
  methods: {
    getInfo() {
      this.$http({
        url: "/api/user/cash_info",
        method: "post",
        headers: { Authorization: this.token }
      }).then(res => {
        if (res.data.type == "ok") {
          if (res.data.message.account_number != null) {
            let data = res.data.message;
            this.name = data.real_name;
            this.bankName = data.bank_name;
            this.bankNum = data.bank_account;
            this.ali = data.alipay_account;
            this.weChatAccount = data.wechat_account;
            this.weChatName = data.wechat_nickname;
            this.src1 = data.alipay_qr_code;
            this.src2 = data.wechat_qr_code;
          }
        }
      });
    },
    add() {
      // if (this.name == "") {
      //   layer.mag("真实姓名必须填写");
      //   return;
      // }
      // if (this.bank_name == "") {
      //   layer.mag("请填写开户行名称");
      //   return;
      // }
      // if (this.bank_account == "") {
      //   layer.mag("请填写银行卡号");
      //   return;
      // }
      // if (this.alipay_account == "") {
      //   layer.mag("请填写支付宝账号");
      //   return;
      // }
      // if (this.wechat_nickname == "") {
      //   layer.mag("请填写微信昵称");
      //   return;
      // }
      // if (this.wechat_account == "") {
      //   layer.mag("请填写微信账号");
      //   return;
      // }
      // if (this.src1 == "../../static/imgs/addimg.png") {
      //   layer.mag("请上传支付宝收款码");
      //   return;
      // }
      // if (this.src2 == "../../static/imgs/addimg.png") {
      //   layer.mag("请上传微信收款码");
      //   return;
      // }
      var src11 = '';
      var src22 = ''
      if (this.src1 == "../../static/imgs/addimg.png") {
        src11 = '';
      }else{
        src11 = this.src1;
      }
      if (this.src2 == "../../static/imgs/addimg.png") {
        src22 = ''
      }else{
        src22 = this.src2;
      }
      this.$http({
        url: "/api/user/cash_save",
        method: "post",
        data: {
          real_name: this.name,
          bank_name: this.bankName,
          bank_account: this.bankNum,
          alipay_account: this.ali,
          wechat_nickname: this.weChatName,
          wechat_account: this.weChatAccount,
          alipay_qr_code: src11,
					wechat_qr_code: src22
        },
        headers: { Authorization: this.token }
      }).then(res => {
        layer.msg(res.data.message);
      });
    },
     // 上传图片
    uploads(type) {
      let that = this;
      var formData = new FormData();
      if (type == 1) {
        formData.append("file", $("#alipay")[0].files[0]);
      } else {
        formData.append("file", $("#wechat")[0].files[0]);
      }
      var i = layer.load();
      $.ajax({
        url: "/api/" + "upload",
        type: "post",
        data: formData,
        processData: false,
        contentType: false,
        success: function(msg) {
          layer.close(i);
          if (msg.type == "ok") {
            if (type == 1) {
              that.src1 = msg.message;
            } else {
              that.src2 = msg.message;
            }
          } else {
            layer.msg(msg.message);
            if (type == 1) {
              that.src1 = "../../static/imgs/addimg.png";
            } else {
              that.src2 = "../../static/imgs/addimg.png";
            }
          }
        }
      });
    }
  }
};
</script>

<style lang='scss' scoped>
.whites {
  width: 600px;
  margin: 66px auto 50px;
  padding-bottom: 30px;
  min-height: 600px;
  
  > .inp-item {
    position: relative;
    margin-bottom: 20px;
    background: none;
    padding-left: 120px;
    height: 42px;
    > div {
      position: absolute;
      top: 0;
      left: 0;

      width: 120px;
      height: 36px;
      line-height: 36px;
      // text-align: center;
    }
    > input {
      display: block;
      padding: 0 20px;
      width: 100%;
      line-height: 36px;
      height: 36px;
      border: 1px solid #f0b90b;
      background: none;
    }
  }
  .btn {
    margin: 30px 0 0 120px;
    width: 480px;
    line-height: 40px;
    text-align: center;
    background: #f0b90b;;
    color: #fff;
    cursor: pointer;
    border-radius: 4px;
  }
  .uploads {
    margin-left: 125px;
    margin-top: 30px;
  }
  .uploads-list {
    width: 120px;
    margin-right: 20px;
    position: relative;
  }
  .uploads-list img {
    width: 120px;
    height: 120px;
  }
  .uploads-list p {
    line-height: 1.5;
    margin-bottom: 10px;
  }
  .uploads-list input {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 120px;
    height: 120px;
    opacity: 0;
  }
}
.balck{
  color: #fff;
}
.balck input{
  color: #fff;
}
</style>



// WEBPACK FOOTER //
// src/view/payOpts.vue