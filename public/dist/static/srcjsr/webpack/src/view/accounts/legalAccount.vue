<template>
    <div class="main">
        <p class="legal_name plr10">{{legal_name}}</p>
        <div class="legalAccount_msg flex between plr10">
          <div>
              <p class="ft12 msg_title">可用</p>
              <p>{{legal_balance}}</p>
          </div>
          <div>
              <p class="ft12 msg_title">冻结</p>
              <p>{{lock_legal_balance}}</p>
          </div>
          <div>
              <p class="ft12 msg_title">{{$t('asset.conversion')}}（CNY）</p>
              <p>{{cny_price | numFilters(8)}}</p>
          </div>
        </div>
        <div class="rec_wrap">
           <p class="rec_title flex between">
               <span>财务记录</span>
               <span class="all">全部</span>
           </p>
           <p class="list_title flex">
               <span class="ft14">{{$t('td.num')}}</span>
               <span class="ft14 tc">{{$t('asset.record')}}</span>
               <span class="ft14 tr">{{$t('td.time')}}</span>
           </p>
           <ul class="log_list">
              <li class="flex arround ft12 flex around" v-for="(item,index) in list" :key="index">
                  <span>{{item.value}}</span>
                  <span class="tc">{{item.info}}</span>
                  <span class="tr">{{item.created_time}}</span>
              </li>
           </ul>
           <p class="tc ft12 curPer mt20" @click="getMore()" v-if="more">{{$t('td.more')}}</p>
           <p class="tc ft12 curPer mt20" v-else>{{$t('td.nomore')}}</p>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return{
           currency:'',
           legal_name:'',
           legal_balance:'',
           lock_legal_balance:'',
           cny_price:'',
           page:1,
           list:[],
           more:true
        }
    },
    created(){
       this.token = localStorage.getItem('token') || '';
       this.currency=this.$route.query.currency_id;

    },
    mounted(){
        this.init();
        this.legal_log();
    },
    methods:{
        init(){
            var that = this
            this.$http({
            url: '/api/' + 'wallet/detail',
            method:'post',
            data:{
                currency:that.currency,
                type:'legal'
            },
            headers: {'Authorization':  that.token}
            }).then(res=>{  
                if(res.data.type  =='ok'){
                    var msg = res.data.message;
                    that.legal_name = msg.currency_name;
                    that.legal_balance = msg.legal_balance;
                    that.lock_legal_balance = msg.lock_legal_balance;
                    that.cny_price = (msg.legal_balance-0)*(msg.cny_price);
                }else{
                    // layer.msg(res.message);
                }
            }).catch(error=>{
                console.log(error)
            })            
        },
        legal_log(){
            var that = this
            this.$http({
            url: '/api/' + 'wallet/legal_log',
            method:'post',
            data:{
                currency:that.currency,
                type:1,
                page:that.page
            },
            headers: {'Authorization':  that.token}
            }).then(res=>{  
                if(res.data.type  =='ok'){
                    var list = res.data.message.list;
                    if(list.length>0){
                        that.list=that.list.concat(list); 
                    }else{
                        that.more=false;
                    }
                   
                }else{
                    // layer.msg(res.message);
                }
            }).catch(error=>{
                console.log(error)
            })            
        },
        getMore(){
            this.page=this.page+1;
            this.legal_log();
        }
    }
}
</script>
<style scoped>
   .legal_name{
       background: #fff;
       padding: 5px 10px;
   }
    .msg_title{
        color: #61688a;
    }
   .main{
       padding: 30px;
       background-color: #fff;
       min-height: 1000px;
   }
   .legalAccount_msg{
       background: #fff;
       padding: 6px 10px;
   }
   .legalAccount_msg div p:first-child{
       margin-bottom: 10px;
   }
   .rec_wrap{
       margin-top: 30px;
       margin-bottom: 30px;
   }
   .rec_title{
       margin-bottom: 10px;
       
   }
   .all{
        color: #61688a;
   }
   .list_title{
       padding: 10px;
       background: #fff;
   }
   .list_title span{flex: 1}
   .huazhuan{
       text-align: center;
       margin-top: 60px;
       background: #f0b90b;
       padding: 8px;
   }
   .huazhuan:hover{
       cursor: pointer;
   }
   .log_list li{padding: 10px;}
   .log_list li span{flex: 1;}
</style>



// WEBPACK FOOTER //
// src/view/accounts/legalAccount.vue