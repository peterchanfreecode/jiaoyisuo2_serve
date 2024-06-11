<template>
  <div class="box">
    <!-- <indexHeader></indexHeader> -->
    <div class="account">
      <div class="rightcontent">
        <router-view></router-view>
      </div>
    </div>
    <!-- <indexFooter></indexFooter> -->
  </div>
</template>
<script>
import indexHeader from "@/view/indexHeader";
import indexFooter from "@/view/indexFooter";
// import left from '@/view/accounts/left_account'
export default {
  name: "account",
  data() {
    return {
      lang: "",
      currentIndex: 0
    };
  },
  components: {
    indexHeader,
    indexFooter
  },
  beforeCreate() {
    document
      .querySelector("html")
      .setAttribute("style", "background-color:#f7f7f7;");
  },
  beforeDestroy() {
    document.querySelector("html").removeAttribute("style");
  },
  mounted() {}
};
</script>
<style scoped >
.box .account {
  width: 100%;
  min-width: 1200px;
  margin-top: 66px;
}
.topcontent {
  height: 66px;
  line-height: 60px;
  padding: 0 30px;
  margin: 20px 0;
  font-size: 20px;
}
.leftcontent {
  width: 220px;
  margin-right: 20px;
  height: 350px;
  border-radius: 3px;
  padding: 4px;
  float: left;
}
.rightcontent {
  min-height: 680px;
  width: 1200px;
  margin: 0 auto;
  
}
</style>





// WEBPACK FOOTER //
// src/components/new_account.vue