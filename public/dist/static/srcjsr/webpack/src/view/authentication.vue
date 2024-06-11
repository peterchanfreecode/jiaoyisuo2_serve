<template>
  <div :class="skins=='days'?'account-main wrap':'account-main wrap balck'">
    <div class="title">{{$t('header.identify')}}</div>
    <div class="main-content mt20">
      <div v-show="review_status==0">
        <div class="main-input">
          <div class="flex alcenter center">
            <span>{{$t('ctc.name')}}：</span>
            <input type="text" :placeholder="$t('seting.pname')" id="name" v-model="name">
          </div>
          <div class="flex alcenter center mt20">
            <span>{{$t('auth.idcard')}}</span>
            <input type="text" :placeholder="$t('auth.pidcard')" id="card" v-model="card_id">
          </div>
        </div>
        <div class="mt40 ft14 tc">{{$t('auth.upimgs')}}</div>
        <div class="idimg flex center mt40">
          <div>
            <img :src="src1" alt>
            <input type="file" accept="image/*" name="file" @change="file1">
          </div>
          <div>
            <img :src="src2" alt>
            <input type="file" accept="image/*" name="file" @change="file2">
          </div>
          <!-- <div>
            <img :src="src3" alt>
            <input type="file" accept="image/*" name="file" @change="file3">
          </div>-->
        </div>
        <div class="updata tc">
          <input type="button" :value="$t('auth.submit')" @click="updata" class="curPer">
        </div>
      </div>
      <div class="auth-status" v-show="review_status==1">
        <div class="tc ft30 au-statue">{{$t('auth.auditing')}}</div>
        <!-- <div>认证后可提升提币额度</div>
        <div>认证后可进行资产交易</div> -->
      </div>
      <div class="auth" v-show="review_status==2">
        <div class="tc ft30 au-statue">{{$t('auth.certified')}}</div>
        <div>
          <span>{{$t('ctc.name')}}：</span>
          <span>{{authData.name}}</span>
        </div>
        <div>
          <span>{{$t('set.account')}}：</span>
          <span>{{authData.account}}</span>
        </div>
        <div>
          <span>{{$t('auth.idcard')}}：</span>
          <span>{{authData.card_id}}</span>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "authentication",
  data() {
    return {
      name: "",
      card_id: "",
      src1: "../../static/imgs/addimg.png",
      src2: "../../static/imgs/addimg.png",
      src3: "../../static/imgs/addimg.png",
      review_status: 3,
      skins: localStorage.getItem("skin") || "days",
      authData:{}
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
    this.token = localStorage.getItem("token");
  },
  methods: {
    file1() {
      var that = this;
      var reader = new FileReader();
      reader.readAsDataURL(event.target.files[0]);
      reader.onload = function(e) {
        // that.src1 = e.target.result;
      };
      var formData = new FormData();
      formData.append("file", event.target.files[0]);
      $.ajax({
        url: "/api/" + "upload",
        type: "post",
        data: formData,
        processData: false,
        contentType: false,
        success: function(msg) {
          if (msg.type == "ok") {
            that.src1 = msg.message;
          } else {
            layer.msg(msg.message);
          }
        }
      });
    },
    file2() {
      var that = this;
      var reader = new FileReader();
      reader.readAsDataURL(event.target.files[0]);
      reader.onload = function(e) {
        // that.src2 = e.target.result;
      };
      var formData = new FormData();
      formData.append("file", event.target.files[0]);
      $.ajax({
        url: "/api/" + "upload",
        type: "post",
        data: formData,
        processData: false,
        contentType: false,
        success: function(msg) {
          if (msg.type == "ok") {
            that.src2 = msg.message;
          } else {
            layer.msg(msg.message);
          }
        }
      });
    },
    file3() {
      var that = this;
      var reader = new FileReader();
      reader.readAsDataURL(event.target.files[0]);
      reader.onload = function(e) {
        // that.src3 = e.target.result;
      };
      var formData = new FormData();
      formData.append("file", event.target.files[0]);
      $.ajax({
        url: "/api/" + "upload",
        type: "post",
        data: formData,
        processData: false,
        contentType: false,
        success: function(msg) {
          if (msg.type == "ok") {
            that.src3 = msg.message;
          } else {
            layer.msg(msg.message);
          }
        }
      });
    },
    updata() {
      var that = this;
      let name = this.$utils.trim(that.name);
      let card_id = this.$utils.trim(that.card_id);
      if (this.name.length == "") {
        layer.tips(that.$t('auth.pname'), "#name");
        return;
      }
      if (this.card_id.length == "") {
        layer.tips(that.$t('auth.pidcard'), "#card");
        return;
      }

      if (that.src1 == "../../static/imgs/addimg.png" || that.src2 == "../../static/imgs/addimg.png") {
        layer.tips(that.$t('auth.pimg'), "#name");
        return;
      }
      this.$http({
        url: "/api/" + "user/real_name",
        method: "post",
        data: {
          name: name,
          card_id: card_id,
          front_pic: that.src1,
          reverse_pic: that.src2
          // hand_pic: that.src3
        },
        headers: { Authorization: that.token }
      })
        .then(res => {
          layer.msg(res.data.message);
          if (res.data.type == "ok") {
            setTimeout(function() {
              that.$router.push("/");
            }, 500);
          }
        })
        .catch(error => {});
    },
    Info() {
      var that = this;
      this.$http({
        url: "/api/" + "user/center",
        method: "get",
        data: {},
        headers: { Authorization: that.token }
      })
        .then(res => {
          if(res.data.type == 'ok'){
            that.review_status = res.data.message.review_status;
            that.authData = res.data.message;
          }
          
        })
        .catch(error => {});
    }
  },
  mounted() {
    this.Info();
  }
};
</script>
<style scoped lang="scss">
.account-main {
  background: #fff;
  .title {
    width: 100%;
    line-height: 60px;
    background: #fff;
    padding-left: 20px;
    font-size: 18px;
    font-weight: bold;
    border-bottom: 1px solid #efefef;

  }
  .main-content {
    background: #fff;
    min-height: 700px;
    border-radius: 2px;
    width: 450px;
    margin: 0 auto;
    .main-input {
      margin: 0 auto;
      text-align: center;
      padding-top: 50px;
      span {
        width: 82px;
        text-align: left;
      }
      input {
        width: 320px;
        min-height: 36px;
        border: 1px solid #f0b90b;
        padding: 0 20px;
        color: #333;
        font-size: 14px;
        border-radius: 3px;
        background-color: #fff;
      }
    }
    .idimg {
      div {
        width: 160px;
        height: 160px;
        overflow: hidden;
        position: relative;
        background-size: 100% 100%;
        margin-left: 50px;
        input {
          position: absolute;
          z-index: 11110;
          opacity: 0;
          width: 100%;
          height: 160px;
          top: 0;
          cursor: pointer;
          left:0;
        }
        img {
          width: 100%;
        }
      }
    }
    .updata {
      input {
        width: 402px;
        height: 45px;
        border-radius: 4px;
        color: #fff;
        font-size: 14px;
        margin: 0 auto;
        margin-top: 60px;
        background: #f0b90b;
      }
    }
    .au-statue {
      padding-top: 100px;
      font-size: 30px;
    }
  }
}
.balck{
    background-color: #1e2b34;
}
.balck .title{
  background-color: #1e2b34;
  color: #fff;
  border-bottom: 1px solid #000;
}
.balck .main-content{
  background-color: #1e2b34;
  color: #fff;
}
.balck .main-content .main-input input{
  background-color: #1e2b34;
  color: #fff;
}
.auth{
  margin-top: 15px
}
.auth div{
  margin-bottom: 10px;
}
.auth-status div{
  margin-top: 10px;
  font-size: 16px;
  text-align: center;
}
</style>



// WEBPACK FOOTER //
// src/view/authentication.vue