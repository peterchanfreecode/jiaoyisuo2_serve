<template>
  <div :class="skins=='days'?'help':'black help'">
    <!-- <indexHeader></indexHeader> -->
    <div class="help-center">
      <div class="top">
        <div class="bg">
          <!-- <h2>您好！请问需要什么帮助？</h2> -->
          <!-- <div class="search-box">
            <input type="text" placeholder="搜索" class="search">
            <i class="iconfont iconRectangleCopy"></i>
            <ul></ul>
            <ul style="display: none;">
              <li>无“”相关内容</li>
            </ul>
          </div> -->
          <!-- <div class="hot-link">
            热门问题：
            <a href="javascript:;" class>如何充值</a>
            <a href="javascript:;" class>如何提现</a>
            <a href="javascript:;" class>如何交易</a>
          </div> -->
        </div>
      </div>
      <div class="helpCenter">
        <el-collapse v-model="activeName" accordion>
          <el-collapse-item :title="$t('miscro.commonProblem')" name="1">
            <div :class="[{'active':datasId == '18'}]" @click="tabs(18)">{{$t('miscro.statement')}}</div>
            <div :class="[{'active':datasId == '19'}]" @click="tabs(19)">{{$t('miscro.about')}}</div>
          </el-collapse-item>
          <!-- <el-collapse-item title="投研报告" name="2">
            <div>每日快讯</div>
            <div>运营月报</div>
          </el-collapse-item>-->
        </el-collapse>
        <div class="content">
          <ul>
            <li class="item">
              <h3>{{datasId=='18'?$t('miscro.statement'):$t('miscro.about')}}</h3>
              <ul class="listName">
                <li
                  @click="goDetail(item.id,item.c_id)"
                  v-for="item in newList"
                  :key="item.id"
                >{{item.title}}</li>
              </ul>
            </li>
          </ul>
          <el-pagination v-if="newList.length>10" layout="prev, pager, next" :total="newList.length"></el-pagination>
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
  name: "helpCenter",
  components: { indexHeader, indexFooter },
  data() {
    return {
      newList: [],
      activeName: "1",
      datasId: "18",
      skins: localStorage.getItem("skin") || "days"
    };
  },
  beforeCreate() {
    document
      .querySelector("html")
      .setAttribute("style", "background-color:#f2f3f8;");
  },
  beforeDestroy() {
    document.querySelector("html").removeAttribute("style");
  },
  created() {},
  methods: {
    getNotice() {
      var that = this;
      this.$http({
        url: "/api/" + "news/list",
        method: "post",
        data: {}
      }).then(res => {
        if (res.data.type == "ok") {
          var datas = res.data.message.list;
          if (datas.length > 0) {
            var arr = [];
            for (var i = 0; i < datas.length; i++) {
              if (datas[i].c_id == that.datasId) {
                arr.push(datas[i]);
              }
            }
            that.newList = arr;
          }
        }
      });
    },
    goBefore() {
      this.$router.back(-1);
    },
    getMore() {},
    goDetail(ids, cids) {
      var that = this;
      that.$router.push({
        path: "/helpArticle",
        query: { id: ids, cId: cids }
      });
      // let routeUrl = this.$router.resolve({
      //   name: "helpCenter",
      //   query: { id: id,cId:cid}
      // });
      // window.open(routeUrl.href, "_blank");
    },
    tabs(num){
      var that = this;
      that.datasId = num;
      that.getNotice();
    }
  },
  mounted() {
    var that = this;
    that.getNotice();
  }
};
</script>
<style scoped>
.help-center {
  margin-top: 66px;
}
.top {
  width: 100%;
  height: 305px;
  background-color: #1e2643;
}
.top .bg {
  width: 1214px;
  height: 100%;
  margin: 0 auto;
  background-image: url(../../static/imgs/helpCenter_bg.jpg);
  background-repeat: no-repeat;
  background-position: 50%;
}
.top .bg h2 {
  letter-spacing: 1px;
  color: #f0b90b;
  font-size: 28px;
  line-height: 28px;
  text-align: center;
  padding: 90px 0 30px;
  font-weight: 400;
}
.listName {
  min-height: 535px;
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
  box-shadow: 0 3px 14px 1px rgba(0, 0, 0, 0.03);
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
.top .bg .hot-link {
  padding-top: 16px;
  text-align: center;
  color: #fff;
  font-size: 14px;
}
.top .bg .hot-link a {
  color: #fff;
  margin-right: 5px;
}
.helpCenter {
  width: 1160px;
  margin: 53px auto 68px;
  overflow: hidden;
}
.helpCenter >>> .el-collapse {
  width: 155px;
  background-color: #fff;
  border-radius: 15px;
  float: left;
  overflow: hidden;
  text-align: center;
  border-top: 1px solid #ebeef5;
  border-bottom: 1px solid #ebeef5;
}

.helpCenter >>> .el-collapse-item__header {
  height: 48px;
  line-height: 48px;
  background-color: #fff;
  color: #303133;
  cursor: pointer;
  border-bottom: 1px solid #ebeef5;
  font-size: 13px;
  font-weight: 500;
  transition: border-bottom-color 0.3s;
  outline: 0;
}
.helpCenter >>> .el-collapse .is-active {
  background-color: #1e2643;
  color: #f0b90b;
  font-size: 16px;
}
.helpCenter >>> .el-collapse-item__wrap {
  will-change: height;
  background-color: #fff;
  overflow: hidden;
  box-sizing: border-box;
  border-bottom: 1px solid #ebeef5;
}
.helpCenter >>>.el-collapse-item__content .active {
  color: #f0b90b;
}
.helpCenter >>> .el-collapse-item__content {
  font-size: 13px;
  color: #303133;
  line-height: 1.769230769230769;
}
.helpCenter >>> .el-collapse-item__content {
  padding: 18px 0;
  background: #fbfbfb;
}
.helpCenter >>> .el-collapse-item__content div {
  width: 100%;
  text-align: center;
  font-size: 15px;
  color: #a6a6a6;
  line-height: 22px;
  cursor: pointer;
  padding: 15px 16px;
  box-sizing: border-box;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  -ms-word-wrap: break-word;
  word-wrap: break-word;
}

.content {
  float: right;
  width: 950px;
  background-color: #fff;
  
  border-radius: 15px;
  padding: 30px 36px;
  min-height: 613px;
  position: relative;
}
.content h3 {
  font-size: 20px;
  font-weight: 400;
  letter-spacing: 2px;
  color: #1e2643;
  padding-bottom: 25px;
}
.content .item ul li {
  font-size: 15px;
  color: #1e2643;
  line-height: 56px;
  border-bottom: 1px solid #e9e9e9;
  text-indent: 4px;
  padding-top: 10px;
  cursor: pointer;
}
.el-pagination{
  text-align: center;
  margin-top: 30px;
}
.content >>> .el-pagination .btn-prev,.content >>> .el-pagination .btn-next{
  display: none;
}
.content >>> .el-pager .active{
  color: #f0b90b;
}
.black .content{
  background-color: #1e2b34;
}
.black .content h3{
  color: #fff;
}
.black .content .item ul li{
  color: #fff;
  border-bottom: 1px solid #000;
}
.black .helpCenter >>> .el-collapse{
  background-color: #1e2b34;
  border: none;
}
.black .helpCenter >>> .el-collapse-item__wrap{
  background-color: #1e2b34;
}
.black .helpCenter >>> .el-collapse-item__content{
    background-color: #1e2b34;
}
.black .helpCenter >>> .el-collapse-item__header{
  border-bottom: 1px solid #000;
}

</style>




// WEBPACK FOOTER //
// src/components/helpCenter.vue