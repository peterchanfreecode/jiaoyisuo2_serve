<template>
    <div :class="skins=='days'?'indexBlackes':'blackBg indexBlackes'">
        <indexHeader></indexHeader>
        <router-view></router-view>
        <indexFooter></indexFooter>      
    </div>  
</template>
<script>
import indexHeader from '@/view/indexHeader'
import login from '@/components/index'
import indexFooter from '@/view/indexFooter'
export default {
    name:'home', 
    data(){
       return{
           skins: localStorage.getItem("skin") || "days"
       } 
    }, 
    methods:{
       },
    components:{login,indexHeader,indexFooter},
    created(){
       
    }
}
</script>
<style>
.indexBlackes{
    background-color: #f8f6f6;
}
.blackBg{
    background-color: #000;
}
</style>








// WEBPACK FOOTER //
// src/components/home.vue