<template>
    <div class="box">
        <!-- <indexHeader></indexHeader> -->
        <div class="account">
            <!-- <left v-bind:lang="lang" :current="0"></left> -->
            <router-view></router-view>
        </div>
        <!-- <indexFooter></indexFooter> -->
    </div>
</template>
<script>
    import indexHeader from '@/view/indexHeader'
    import indexFooter from '@/view/indexFooter'
    export default {
        name:'account',
        data(){
            return {
                lang:''
            }
        },
        components:{
            indexHeader,
            indexFooter
        },
        // mounted(){
        //     this.bus.$on('nav_name',cur=>{
        //         this.array.findIndex()
        //     })
        // }
    }
</script>
<style scoped >
    .box .account{
        width: 1200px;
        padding-top: 30px;
        min-height: 640px;
        margin: 66px auto 0;
        overflow: hidden;
    }
   
</style>





// WEBPACK FOOTER //
// src/components/account.vue