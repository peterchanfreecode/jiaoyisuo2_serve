<template>
  <div :class="skins=='days'?'noticeDetail':'noticeDetail balck'">
    <!-- <indexHeader></indexHeader> -->
    <div class="account-wrap">
      <div class="account-content">
        <div class="detailBig">
          <div class="clear">
            <span>{{title}}</span>
          </div>

          <div class="detailContent" v-html="content">
            <!-- <p v-html="content" ref="con"></p> -->
          </div>
          <!-- <div class="fColor2 mt40">
            <p class="tr">{{abstract}}</p>
            <p class="tr mt5">{{update_time}}</p>
          </div>-->
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
  name: "noticeDetail",
  components: { indexHeader, indexFooter },
  data() {
    return {
      title: "",
      content: "",
      abstract: "",
      update_time: "",
      id: "",
      token: "",
      skins: localStorage.getItem("skin") || "days"
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
  watch: {
    $route(to, from) {
      this.id = to.query.id;
      return this.init();
    }
  },
  created() {
    let that = this;
    that.address = localStorage.getItem("token") || "";
    that.token = localStorage.getItem("token") || "";
    that.id = this.$route.query.id;
    that.init();
  },
  mounted() {},
  methods: {
    init() {
      let that = this;
      this.$http({
        url: "/api/" + "news/detail",
        method: "post",
        data: { id: that.id },
        headers: { Authorization: localStorage.getItem("token") }
      })
        .then(res => {
          res = res.data;
          if (res.type === "ok") {
            that.title = res.message.title;
            that.content = res.message.content;
            that.abstract = res.message.abstract;
            that.update_time = res.message.update_time;
            that.setProperty();
          } else {
            // layer.msg(res.message);
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    goBefore() {
      this.$router.back(-1);
    },
    setProperty() {
      var tags = document.getElementsByTagName("p");
      HTMLCollection.prototype.forEach = function(callback) {
        [].slice.call(this).forEach(callback);
      };
      tags.forEach(function(e, i) {
        e.style.backgroundColor = "#666 !important";
      });
    }
  }
};
</script>
<style lang="scss" scoped>
.clear {
  width: 73%;
  margin: 0 auto;
  border-bottom: 1px solid #000;
  padding: 0 20px;
}
.clear span {
  display: block;
  padding: 12px 0 10px;
  font-size: 18px;
}
.account-wrap {
  margin-top: 66px;
  padding-top: 10px;
}
.nav-after {
  display: block;
  height: 10px;
  background-color: #f2f3f8;
}
.mb30 {
  margin-bottom: 30px;
}
span {
  color: #333 !important;
}
.detailContent {
  line-height: 26px;
  width: 73%;
  margin: 0 auto;
  padding: 10px 20px;
}
.mt5 {
  margin-top: 5px;
}
.account {
  width: 1200px;
  margin: 0 auto;
  padding-top: 30px;
  overflow: hidden;
  min-height: 880px;
}

.account-content {
  width: 100%;
  min-height: 750px;
  background-color: #fff;
}
.balck{
  background-color: #000;
}
.balck .account-content{
    background-color: #000;
}
.balck .clear{
  background-color: #1e2b34;
}
.balck .detailContent{
  background-color: #1e2b34;
  margin-bottom: 20px;
}
.balck span {
  color: #fff !important;
}
</style>






// WEBPACK FOOTER //
// src/components/noticeDetail.vue