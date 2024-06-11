<template>
  <div :class="skins=='days'?'exchange':'exchange balck'">
    <!-- <div class="title fColor1">交易所</div> -->
    <div class="content fColor1">
      <div class="new_price">
        <span class="ft14 ">{{$t('market.newprice')}}</span>
      </div>
      <div class="exchange_title ft12 clear tc">
        <span class="tc">{{$t('miscro.category')}}</span>
        <span>{{$t('cuy.price')}}</span>
        <span class="tc">{{$t('td.num')}}</span>
      </div>
      <ul class="list-item ft12 tc">
        <li class="curPer" v-for="(out,index) in datas.sellList" :key="out.id" @click="price(out.price)">
          <span class="redColor tc">{{$t('cuy.sell')}} {{datas.sellList.length-index}}</span>
          <span>{{out[0] | numFilters(4)}}</span>
          <span class="tc">{{out[1] *100 | numFilters(2)}}</span>
        </li>
        <li class="line"></li>
        <li class="curPer" v-for="(buy,index) in datas.buyList" :key="index" @click="price(buy.price)">
          <span class="greenColor tc">{{$t('cuy.buy')}}  {{index+1}}</span>
          <span>{{buy[0] | numFilters(4)}}</span>
          <span class="tc">{{buy[1] *100 | numFilters(2)}}</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: "exchange",
  props: {
    topBuy: {
      type: Object,
      required: true
    },
    symbol: {
      type: String,
      required: true
		},
		
  },
  data() {
    return {
      outlist: [],
      inlist: [],
      load: 1,
      newData: 0,
      currency_name: "",
      legal_name: "",
      currency_id: "",
      legal_id: "",
      datas:{},
      skins: localStorage.getItem("skin") || "days"
    };
  },
  watch: {
    topBuy: {
      immediate: true, // 这句重要
      handler(val) {
        this.datas = val;
      },
      deep:true
    }
  },
  computed: {
    switchStatus: function () {
      return this.datas // 直接监听props里的status状态
    }
  },
  mounted: function() {
    var that = this;
  },
  created: function() {
    
    
  },
  methods: {

  },
};
</script>

<style scoped>
.title {
  height: 48px;
  line-height: 48px;
  padding: 0 10px 0 30px;
}
.content {
  padding: 0 5px;
}
.new_price {
  height: 40px;
  line-height: 40px;
  border-bottom: 1px solid #2f3d45;
}
.exchange_title {
  line-height: 25px;
  position: relative;
  color: #becbc6;
}
.list-item{
  padding-bottom: 10px;
}
.list-item li {
  line-height: 25px;
  overflow: hidden;
}
.list-item li span,
.exchange_title span {
  width: 33.3%;
  display: block;
  float: left;
}
.green {
  color: #55a067;
}
.red {
  color: #e3046f;
}
.exchange .list-item li:hover {
  background: #f8f6f6;
}
.balck .list-item li:hover {
  background: #1d3d5d;
}
.line {
  height: 5px;
  border-bottom: 1px solid #2f3d45;
}
</style>



// WEBPACK FOOTER //
// src/view/lever/lever_exchange.vue