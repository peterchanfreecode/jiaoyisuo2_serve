<template>
  <div id="user-setting" :class="skins=='days'?'whites':'whites balck'">
    <!-- <indexHeader></indexHeader> -->
    <!-- <div class="setting-l">
            <div class="setting-item">
                <div class="item-title">收款方式</div>
                <router-link to="/userSetting">收款方式</router-link>
            </div>
    </div>-->
    <div class="setting-r">
      <h3>{{$t('seting.pmethod')}}</h3>
      <router-view></router-view>
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
     
      skins: localStorage.getItem("skin") || "days"
    };
  },
};
</script>

<style lang='scss' scoped>
.whites {
  margin-top: 66px;
  min-height: 700px;
  position:relative;
  margin-bottom:30px;
  top:18px;
  > .setting-l {
    margin: 0 10px;
    padding: 0 30px;
    line-height: 40px;
    background: #fff;
    width: 23%;
    .router-link-active {
      color: #f0b90b;
    }
  }
  .setting-r {
    background: #fff;
    width: 700px;
    margin: 0 auto;
    margin-bottom: 50px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding-bottom: 10px;
    h3 {
      width: 100%;
      line-height: 60px;
      background: #fff;
      padding-left: 20px;
      font-size: 18px;
      font-weight: bold;
      border-bottom: 1px solid #efefef;
    }
  }
}

.balck .setting-r {
  background-color: #1e2b34;
  columns: #fff;
}
.balck .setting-r h3{
  background-color: #1e2b34;
  color: #fff;
  border-bottom: 1px solid #000;

}
</style>



// WEBPACK FOOTER //
// src/components/userSetting.vue