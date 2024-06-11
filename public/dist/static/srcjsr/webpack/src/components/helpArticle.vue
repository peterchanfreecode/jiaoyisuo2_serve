<template>
  <div :class="skins=='days'?'help':'help balck'">
    <!-- <indexHeader></indexHeader> -->
    <div class="help-center">
      <div class="top">
        <div class="bg">
          <h2>KiBiEx {{$t('header.help')}}</h2>
          <!-- <div class="search-box">
            <input type="text" placeholder="搜索" class="search">
            <i class="iconfont iconRectangleCopy"></i>
            <ul></ul>
            <ul style="display: none;">
              <li>无“”相关内容</li>
            </ul>
          </div> -->
        </div>
      </div>
      <div class="helpCenter">
        <el-breadcrumb separator="/">
          <el-breadcrumb-item :to="{ path: '/helpCenter' }">{{$t('header.help')}}</el-breadcrumb-item>
          <el-breadcrumb-item>{{datas.title}}</el-breadcrumb-item>
        </el-breadcrumb>
        <div class="help-center-content flex between">
          <div class="help-center-left">
            <ul>
              <li
                :class="[{'active':listId==item.id}]"
                v-for="item in newsList"
                :key="item.id"
                @click="linkDetail(item.id,listCid)"
              >{{item.title}}</li>
            </ul>
          </div>
          <div class="help-center-right">
            <ul class="article">
              <li class="item">
                <h3>{{datas.title}}</h3>
                <div class="crateTime">
                  {{$t('miscro.foundedOn')}}
                  <span>{{datas.update_time}}</span>
                </div>
                <div class="article-body markdown html" v-html="datas.content"></div>
              </li>
            </ul>
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
  name: "helpArticle",
  components: { indexHeader, indexFooter },
  data() {
    return {
      datas: {},
      listId: "",
      newsList: [],
      listCid: "",
      skins: localStorage.getItem("skin") || "days"
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
  mounted() {
    var that = this;
    that.listId = this.$route.query.id;
    that.listCid = this.$route.query.cId;
    that.init();
    that.getNewsList();
  },
  methods: {
    init() {
      var that = this;
      this.$http({
        url: "/api/" + "news/detail",
        method: "post",
        data: {
          id: that.listId
        }
      }).then(res => {
        if (res.data.type == "ok") {
          that.datas = res.data.message;
        }
      });
    },
    getNewsList() {
      var that = this;
      this.$http({
        url: "/api/" + "news/list",
        method: "post",
        data: {
          c_id: that.listCid
        }
      }).then(res => {
        if (res.data.type == "ok") {
          var datas = res.data.message.list;
          if (datas.length > 0) {
            that.newsList = datas;
          }
        }
      });
    },
    linkDetail(ids, cids) {
      var that = this;
      that.listId = ids;
      that.listCid = cids;
      that.init();
    }
  }
};
</script>
<style scoped>
.help-center {
  margin-top: 66px;
}
.top {
  width: 100%;
  height: 210px;
  background-color: #1e2643;
}
.top .bg {
  width: 1214px;
  height: 210px;
  background-color: #1e2643;
  background-image: url(../../static/imgs/helpCenter_bg.jpg);
  background-repeat: no-repeat;
  background-position: center 50%;
  background-size: 60%;
}
.top .bg h2 {
  font-size: 28px;
  line-height: 28px;
  letter-spacing: 1px;
  color: #f0b90b;
  padding: 50px 0 20px;
  font-weight: 400;
  text-align: center;
}
.bg .search-box {
  width: 476px;
  height: 46px;
  position: relative;
  margin: 0 auto;
}
.bg .search {
  display: block;
  margin: 0 auto;
  width: 476px;
  height: 46px;
  background-color: #fff;
  border-radius: 8px;
  border: none;
  font-size: 14px;
  text-indent: 42px;
  font-family: Microsoft YaHei;
}
.top .bg .search-box i {
  position: absolute;
  top: 10px;
  left: 12px;
  font-size: 22px;
  line-height: 30px;
  color: #a6a6a6;
}
.top .bg .search-box ul {
  width: 476px;
  background: #fff;
  position: absolute;
  z-index: 666;
  left: 0;
  top: 50px;
  
  border-radius: 8px;
  max-height: 400px;
  overflow-y: auto;
}
.search-box ul li {
  height: 30px;
  line-height: 30px;
  font-size: 14px;
  padding: 5px 20px;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.helpCenter {
  width: 1160px;
  margin: 47px auto 80px;
  overflow: hidden;
}
.help-center-content {
  min-height: 400px;
}
.listName {
  min-height: 535px;
}
.helpCenter >>> .el-breadcrumb {
  color: #000;
  padding-bottom: 25px;
}
.helpCenter >>> .el-breadcrumb__inner {
  color: #000;
}
.helpCenter >>> .is-link {
  color: #f0b90b;
  font-size: 14px;
}
.helpCenter >>> .is-link:hover {
  color: #f0b90b;
}
.help-center-left {
  width: 160px;
  background-color: #fff;
  
  border-radius: 15px;
  float: left;
  overflow: hidden;
  max-height: 500px;
}
.help-center-left .aside-top {
  width: 100%;
  height: 60px;
  line-height: 60px;
  letter-spacing: 1px;
  color: #f0b90b;
  font-size: 16px;
  text-align: center;
  background: #1e2643;
}
.help-center-left ul {
  padding: 18px 0;
  background: #fbfbfb;
  overflow-y: auto;
  overflow-x: hidden;
}
.help-center-left ul li {
  width: 100%;
  text-align: center;
  font-size: 15px;
  color: #a6a6a6;
  line-height: 22px;
  cursor: pointer;
  padding: 15px;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
}
.help-center-right {
  width: 940px;
  min-height: 535px;
}
.article {
  width: 868px;
  background-color: #fff;
  
  border-radius: 15px;
  padding: 36px 48px 61px;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  min-height: 535px;
}
.article h3 {
  font-size: 20px;
  line-height: 20px;
}
.crateTime {
  font-size: 12px;
  color: #a6a6a6;
  padding: 10px 0 16px;
}
.crateTime span {
  color: #f0b90b;
}
.article-body {
  font-size: 14px;
  line-height: 30px;
  word-wrap: break-word;
  margin-top: 6px;
}
.help-center-left ul .active {
  color: #f0b90b;
}
.balck .helpCenter >>> .el-breadcrumb__inner{
  color: #fff;
}
.balck .helpCenter >>> .is-link{
    color: #f0b90b;
}
.balck .help-center-left{
  background-color: #1e2b34;
}
.balck .help-center-left ul{
  background-color: #1e2b34;
}
.balck .article{
  background-color: #1e2b34;
}
</style>




// WEBPACK FOOTER //
// src/components/helpArticle.vue