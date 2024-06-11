<template>
  <div :class="skins=='days'?'complaint':'complaint balck'">
    <div class="content">
      <h3>{{$t('miscro.complaint')}}</h3>
      <div class="content">
        <textarea v-model="texts" :placeholder="$t('miscro.reply')"></textarea>
        <button type="button" @click="submits()">{{$t('auth.submit')}}</button>
      </div>
    </div>
    <div class="content mt40 content-bottom">
      <h3>{{$t('miscro.complaintList')}}</h3>
      <ul class="list">
        <li v-for="item in list" :key="item.id">
          <div class="flex between">
            <p>{{item.account_number}}</p>
            <p>{{item.create_time}}</p>
          </div>
          <div class="mt10">{{item.content}}</div>
          <div class="mt10" v-if="item.reply_content">{{$t('miscro.complaintReply')}}{{item.reply_content}}</div>
        </li>
      </ul>
      <el-pagination
        class="tc mt10 pb10"
        layout="prev, pager, next"
        v-if="total > 10"
        :total="total"
        @current-change="handleCurrentChange"
      ></el-pagination>
    </div>
  </div>
</template>
<script>
import indexHeader from "@/view/indexHeader";
import indexFooter from "@/view/indexFooter";
export default {
  name: "complaint",
  components: { indexHeader, indexFooter },
  data() {
    return {
      title: "",
      content: "",
      abstract: "",
      update_time: "",
      id: "",
      token: "",
      list: [],
      page: 1,
      total: 0,
      texts: "",
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
    document.getElementsByClassName('kefu')[0].removeAttribute("style");
  },
  created() {
    let that = this;
    that.token = localStorage.getItem("token") || "";
    that.init();
  },
  mounted() {
    document.getElementsByClassName('kefu')[0].setAttribute("style", "display: none;");
  },
  methods: {
    init() {
      let that = this;
      this.$http({
        url: "/api/" + "feedback/list",
        method: "post",
        data: { page: that.page },
        headers: { Authorization: localStorage.getItem("token") }
      })
        .then(res => {
          res = res.data;
          if (res.type === "ok") {
            that.list = res.message.list;
            that.total = res.message.count;
          } else {
            // layer.msg(res.message);
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    // 分页
    handleCurrentChange(val) {
      var that = this;
      that.page = val;
      that.init();
    },

    // 提交建议
    submits() {
      var that = this;
      if (that.texts) {
        this.$http({
          url: "/api/" + "feedback/add",
          method: "post",
          data: { content: that.texts },
          headers: { Authorization: localStorage.getItem("token") }
        })
          .then(res => {
            res = res.data;
            layer.msg(res.message);
            if (res.type === "ok") {
              setTimeout(function() {
                location.reload();
              }, 1000);
            }
          })
          .catch(error => {
            console.log(error);
          });
      } else {
        layer.msg(that.$t('miscro.complaintDescription'));
      }
    }
  }
};
</script>
<style scoped>
.complaint {
  width: 970px;
  margin: 66px auto 60px;
  min-height: 600px;
  background-color: #fff;
  position: relative;
  top: 18px;
}
.content h3 {
  line-height: 60px;
  background: #fff;
  padding-left: 20px;
  font-size: 18px;
  font-weight: bold;
  border-bottom: 1px solid #efefef;
}
.content textarea {
  resize: none;
  border: 1px solid #efefef;
  margin-top: 20px;
  width: 930px;
  margin-left: 20px;
  height: 100px;
  border-radius: 4px;
  padding: 6px 10px;
  line-height: 1.3;
}
.content button {
  width: 400px;
  height: 45px;
  border-radius: 4px;
  color: #fff;
  font-size: 14px;
  margin-left: 285px;
  margin-top: 60px;
  background: #f0b90b;
  border: none;
  outline: none;
}
.content >>> .el-pagination .btn-prev,
.content >>> .el-pagination .btn-next {
  display: none;
}
.content >>> .el-pager .active{
  color: #f0b90b;
}
.list {
  padding-bottom: 20px;
}
.list li {
  border-bottom: 1px solid #efefef;
  padding: 10px 20px;
}
.list div {
  line-height: 1.3;
}
.content-bottom {
  padding-bottom: 20px;
}
.complaint >>> .el-pager li{
  background-color: rgba(0, 0, 0, 0);
  color: #fff;
}
.complaint >>> .el-pager .active{
  color: #f0b90b;
}
.balck{
  background-color: #1e2b34;
}
.balck .content h3{
  background-color: #1e2b34;
  color: #fff;
  border-bottom: 1px solid #000;
}
.balck .content textarea{
  background-color: #1e2b34;
  color: #fff;
  border: 1px solid #000;
}

</style>






// WEBPACK FOOTER //
// src/components/complaint.vue