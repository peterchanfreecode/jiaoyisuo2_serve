<template>
  <div :class="skins == 'nights'?'black notice':'notice'">
    <div class="notice-header">
      <h3 class="tc">{{$t('miscro.everyone')}}</h3>
      <p class="tc">{{$t('miscro.program')}}</p>
    </div>
    <div class="notice-invitation flex center">
      <div>
        <p>{{$t('miscro.myMine')}}{{datas.count}}</p>
      <p>{{$t('miscro.friend')}}{{datas.sum}}</p>
      </div>
      <div>
        <p>{{$t('miscro.myRank')}}{{datas.level}}</p>
      <p>{{$t('miscro.accumulated')}}{{datas.num || '0.00' |numFilters(3)}}</p>
      </div>
    </div>
    <div class="tc">
      <div id="code" class="tc mt20"></div>
      <div class="tc mt10">{{inviteCode}}</div>
      <button type="button" class="copy" @click="copy()">{{$t('miscro.copyLinks')}}</button>
    </div>
    <div class="share-center-text">
				<h4>{{$t('miscro.moneyVein')}}</h4>
				<p>{{newDetal.keyword}}</p>
			</div>
			<div class="share-center-word">
					{{newDetal.abstract}}
			</div>
		<div class="share-bottom">
			<p class="share-bottom-header">{{$t('miscro.example')}}</p>
			<div class="share-bottom-content" v-html="newDetal.content">
			
			</div>

		</div>
  </div>
</template>
<script>
import indexHeader from "@/view/indexHeader";
import indexFooter from "@/view/indexFooter";
import "@/lib/jquery.qrcode.min.js";
import "@/lib/clipboard.min.js";
export default {
  name: "invitation",
  components: { indexHeader,indexFooter},
  data() {
    return {
      	urls: '',
				datas:{},
        newDetal:{},
        token:localStorage.getItem("token"),
        skins: localStorage.getItem("skin") || "days",
        inviteCode:''
        
    };
  },
  beforeCreate() {
    document
      .querySelector("html")
      .setAttribute("style", "background-color:#f7f7f7;");
  },
  beforeDestroy() {
    document.querySelector("html").removeAttribute("style");
  },
  created() {},
  methods: {
    init() {
      var that = this;
      this.$http({
        url: "/api/" + "news/list",
        method: "post",
        data: { c_id: "20" },
         headers: { Authorization: that.token }
      }).then(res => {
         this.$http({
        url: "/api/" + "news/detail",
        method: "post",
        data: { id: res.data.message.list[0].id },
         headers: { Authorization: that.token }
      }).then(res => {
        this.newDetal = res.data.message;
      });
      });
     
      this.$http({
        url: "/api/" + "mining",
        method: "get",
        data: {},
        headers: { Authorization: that.token }
      }).then(res => {
        that.datas = res.data.message;
      });
      this.$http({
        url: "/api/user/info",
        method: "get",
        data: {},
        headers: { Authorization: that.token }
      }).then(res => {
        if(res.data.type == 'ok'){
          // that.inviteCode = res.data.message.extension_code;
          // console.log(window.location.origin)
          that.inviteCode = window.location.origin + '/mobile/register.html?code='+res.data.message.extension_code
          $("#code").qrcode({
              width: 120, //宽度
              height: 120, //高度
              text: that.inviteCode
          });
        }
      });
    },
    // 复制
    copy() {
      var that = this;
      var clipboard = new Clipboard(".copy", {
        text: function() {
          return that.inviteCode;
        }
      });
      clipboard.on("success", function(e) {
        that.flags = true;
        layer.msg(that.$t("set.copysuccess"));
      });
      clipboard.on("error", function(e) {
        that.flags = false;
        // layer.msg("请重新复制");
      });
    },
    
    
  },
  mounted() {
    var that = this;
    that.init();
    that.copy();
  }
};
</script>
<style lang="scss" scoped>
.notice{
  margin: 66px auto 40px;
  min-height: 700px;
  min-width: 1200px;
  width: 80%;
  background-color: #fff;
  position: relative;
  top: 20px;
  padding: 20px;
}
.notice-header h3{
  font-size: 20px;
}
.notice-header p{
  margin-top: 10px;
  font-size: 16px;
}
.notice-invitation div{
  margin-bottom: 10px;
}
.notice-invitation {
  margin: 20px auto 0;
  // width: 20%;
}
.notice-invitation div p:last-child{
  margin-top: 10px;
}
.notice-invitation div:first-child{
  margin-right: 60px;
}
.share-center-text h4{
  color:#f0b90b;
  line-height: 30px;
  font-size: 18px
}
.share-center-text p{
  line-height: 30px;
  letter-spacing: 2px;
}
.share-center-word{
  margin-top: 10px;
  line-height: 30px;
  letter-spacing: 2px;
}
.share-bottom-header{
  color:#f0b90b;
  line-height: 30px;
  font-size: 18px
}
.share-bottom-content{
  margin-top: 10px;
}
.share-bottom{
  margin-top: 20px;
}
.black {
  background-color: #1e2b34;
  color: #fff;
}
.copy{
  width: 120px;
  line-height: 40px;
  border-radius: 4px;
  color: #fff;
  margin: 10px auto;
  background-color: #f0b90b;
  cursor: pointer;
}
</style>




// WEBPACK FOOTER //
// src/components/invitation.vue