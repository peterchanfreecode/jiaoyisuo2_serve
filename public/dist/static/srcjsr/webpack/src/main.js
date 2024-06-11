// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import Axios from "axios"
import Util from './lib/utils'
import VueAxios from 'vue-axios'
import Qs from 'qs'
import "@/assets/style/common.scss"
import VueSocketio from 'vue-socket.io'
import {Slider,Select,Option,Collapse,CollapseItem,Pagination,Breadcrumb,BreadcrumbItem,Dialog,Button,MessageBox,Switch,Dropdown,DropdownMenu,DropdownItem} from 'element-ui'
import lang from 'element-ui/lib/locale/lang/en'
import locale from 'element-ui/lib/locale'
import i18n from './lang/lang'
Vue.component(Slider.name, Slider)
Vue.component(Select.name, Select)
Vue.component(Option.name, Option)
Vue.component(Collapse.name, Collapse)
Vue.component(CollapseItem.name, CollapseItem)
Vue.component(Pagination.name, Pagination)
Vue.component(Breadcrumb.name, Breadcrumb)
Vue.component(BreadcrumbItem.name, BreadcrumbItem)
Vue.component(Dialog.name, Dialog)
Vue.component(Button.name, Button)
Vue.component(MessageBox.name, MessageBox)
Vue.component(Switch.name, Switch)
Vue.component(Dropdown.name, Dropdown)
Vue.component(DropdownMenu.name, DropdownMenu)
Vue.component(DropdownItem.name, DropdownItem)
locale.use(lang)
// import echarts from 'echarts'
import VueAwesomeSwiper from 'vue-awesome-swiper'
import store from './store'
import 'babel-polyfill'
Vue.use(VueAwesomeSwiper)
// Vue.prototype.$echarts = echarts 
window.eventBus = new Vue();
// var url;
// if(process.env.NODE_ENV=='development'){
// 	url="www.nowex.io"
// }else{
// 	url=location.host;
// }
var url;
var url1;
if(process.env.NODE_ENV=='development'){
	url="www.tspt.hk";
	url1="http://www.tspt.hk"
}else{
	url=location.host;
	url1 = location.origin;
}
Vue.use(VueSocketio, url);

Vue.config.productionTip = false
Axios.interceptors.request.use(function (config) {
	if (config.url.indexOf('?') === -1) {
		config.url = config.url + '?_timespan=' + (new Date()).getTime()
	} else {
		config.url = config.url + '&_timespan=' + (new Date()).getTime()
	}
	// 在发送请求之前做些什么
	return config
}, function (error) {
	// 对请求错误做些什么
	return Promise.reject(error)
})
Axios.interceptors.response.use(function(response){
	if(response.data.type == '999'){
		
		var lang = localStorage.getItem('lang');
		if(lang == 'en'){
			layer.msg('Logon is out of date, please login again');
		}else{
			layer.msg('登录过时，请重新登录');
		}
		localStorage.clear();
		localStorage.setItem('lang',lang);
		if(window.location.hash == '#/' || window.location.hash == '#/components/login'){
			window.location.reload()
		}else{
			setTimeout(() => {
				router.push("/components/login");
			}, 500);
		}
		
	}
	return response
})
//Axios.defaults.baseURL = ''
// Axios.defaults.headers = { 'Content-Type': 'application/json;charset=UTF-8' }application/x-www-form-urlencoded
// Axios.defaults.withCredentials = true;
Vue.config.productionTip = false;
Axios.defaults.transformRequest = [(data) => {
	return Qs.stringify(data)
}]
Vue.use(VueAxios, Axios);
Vue.use(Util);
Vue.filter('numFilter', function (value) {
	//截取当前数据到小数点后五位
	let transformVal = Number(value).toFixed(5)
	return Number(transformVal)
	})
	Vue.filter('numFilters', function (value,numbers) {
		let val=Number(value);
			let num=Number(numbers);
			let nums = Number(numbers-0+1);
			let base='10';
			let decimal=base.padEnd(nums,0)-0;
			var vals = (Math.floor(val*decimal)/decimal).toFixed(num)
			return vals;
	})

let bus = new Vue()
Vue.prototype.bus = bus
// router.beforeEach((to,from,next) => {
// 	Axios({
// 		url:'/api/currency/get_usdt_price'
// 	}).then(res => {
// 		var p  = 6.9;
// 		if(res.data.type == 'ok'){
// 			p = res.data.message;
// 		}
// 		Vue.prototype.usprice = p;
// 		next()
		
// 	})
	
	
	
// })
//Vue.use(Ws, 'http://test.maxf.pub/users/chatRoom');
/* eslint-disable no-new */
new Vue({
	el: '#app',
	i18n,
	router,
	store,
	components: { App },
	template: '<App/>'
})



// WEBPACK FOOTER //
// ./src/main.js