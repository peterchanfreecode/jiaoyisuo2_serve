webpackJsonp([7],{cbCf:function(t,s){},ecfj:function(t,s,e){"use strict";Object.defineProperty(s,"__esModule",{value:!0});var i={data:function(){return{list:[],filterPms:{type:"none",id:"",page:1,isSure:"none"}}},filters:{toFixeds:function(t){return(t=Number(t)).toFixed(3)}},created:function(){var t=window.localStorage.getItem("token")||"";t&&(this.token=t,this.filterPms.id=this.$route.query.id||"",this.getList())},methods:{getList:function(){var t=this,s=arguments.length>0&&void 0!==arguments[0]&&arguments[0],e={};s||(this.filterPms.page=1),e.id=this.filterPms.id,e.page=this.filterPms.page,"none"!=this.filterPms.type&&(e.type=this.filterPms.type),"none"!=this.filterPms.isSure&&(e.is_sure=this.filterPms.isSure);var i=layer.load();this.$http({url:"/api/legal_send_deal_list",params:e,headers:{Authorization:this.token}}).then(function(e){if(layer.close(i),"ok"==e.data.type){var a=e.data.message;s?a.data.length?t.list=t.list.concat(a.data):layer.msg(t.$t("td.nomore")):t.list=a.data,a.data.length&&(t.filterPms.page+=1)}})}}},a={render:function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"whites",attrs:{id:"legal-record"}},[e("div",{staticClass:"title bgf8"},[t._v(t._s(t.$t("fat.orderLog")))]),t._v(" "),e("div",{staticClass:"filter-box"},[e("div",[e("span",[t._v(t._s(t.$t("fat.odStatus"))+"：")]),t._v(" "),e("span",{class:{select:0==t.filterPms.isSure},on:{click:function(s){t.filterPms.isSure=0,t.getList()}}},[t._v(t._s(t.$t("td.nofinish")))]),t._v(" "),e("span",{class:{select:1==t.filterPms.isSure},on:{click:function(s){t.filterPms.isSure=1,t.getList()}}},[t._v(t._s(t.$t("td.finished")))]),t._v(" "),e("span",{class:{select:2==t.filterPms.isSure},on:{click:function(s){t.filterPms.isSure=2,t.getList()}}},[t._v(t._s(t.$t("td.ceiled")))]),t._v(" "),e("span",{class:{select:3==t.filterPms.isSure},on:{click:function(s){t.filterPms.isSure=3,t.getList()}}},[t._v(t._s(t.$t("td.payed")))]),t._v(" "),e("span",{class:{select:4==t.filterPms.isSure},on:{click:function(s){t.filterPms.isSure=4,t.getList()}}},[t._v(t._s(t.$t("fat.protection")))])])]),t._v(" "),e("ul",{staticClass:"bgf8"},t._l(t.list,function(s,i){return e("li",{key:i,staticClass:"bod_bc bdb pdtb5"},[e("div",{staticClass:"flex li-t pdtb5"},[e("div",["sell"==s.type?e("span",[t._v("购买")]):e("span",[t._v("出售")]),t._v(" "),e("span",[t._v(t._s(s.currency_name))])]),t._v(" "),e("div",{staticClass:"status"},[0==s.is_sure?e("router-link",{attrs:{to:{path:"/shopLegalPayDetail",query:{id:s.id}}}},[t._v(t._s(t.$t("td.nofinish"))+" >")]):1==s.is_sure?e("router-link",{attrs:{to:{path:"/shopLegalPayDetail",query:{id:s.id}}}},[t._v(t._s(t.$t("td.finished"))+" >")]):2==s.is_sure?e("router-link",{attrs:{to:{path:"/shopLegalPayDetail",query:{id:s.id}}}},[t._v(t._s(t.$t("td.ceiled"))+" >")]):3==s.is_sure?e("router-link",{attrs:{to:{path:"/shopLegalPayDetail",query:{id:s.id}}}},[t._v(t._s(t.$t("td.payed"))+" >")]):e("router-link",{attrs:{to:{path:"/shopLegalPayDetail",query:{id:s.id}}}},[t._v(t._s(t.$t("fat.protection"))+" >")])],1)]),t._v(" "),e("div",{staticClass:"flex li-b"},[e("div",[e("div",{staticClass:"tc ft14"},[t._v(t._s(t.$t("td.time")))]),t._v(" "),e("div",{staticClass:"tc"},[t._v(t._s(s.create_time))])]),t._v(" "),e("div",[e("div",{staticClass:"tc ft14"},[t._v(t._s(t.$t("td.num"))+"("+t._s(s.currency_name)+")")]),t._v(" "),e("div",{staticClass:"tc"},[t._v(t._s(t._f("toFixeds")(s.number||"0.000")))])]),t._v(" "),e("div",[e("div",{staticClass:"tc ft14"},[t._v(t._s(t.$t("td.tradeTotal"))+"("+t._s(s.coin_code)+")")]),t._v(" "),e("div",{staticClass:"tc"},[t._v(t._s(t._f("toFixeds")(s.deal_money||"0.000")))])])])])})),t._v(" "),t.list.length?e("div",{staticClass:"more",on:{click:function(s){t.getList(!0)}}},[t._v(t._s(t.$t("td.more")))]):e("div",{staticClass:"nomore"},[t._v(t._s(t.$t("td.nomore")))])])},staticRenderFns:[]};var l=e("VU/8")(i,a,!1,function(t){e("cbCf")},null,null);s.default=l.exports}});
//# sourceMappingURL=7.8b8b52b305ded6d317a0.js.map