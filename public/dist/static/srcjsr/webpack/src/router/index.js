import Vue from 'vue'
import Router from 'vue-router'
import home from '@/components/home'
import payOpts from '@/view/payOpts'
import userSetting from '@/components/userSetting'
import leverdealCenter from '@/components/lever_dealCenter'
import secondsOrder from '@/components/secondsOrder'
import login from '@/components/login'
import index from '@/components/index'
import register from '@/components/register'
import account from '@/components/account'
import accountSet from '@/view/accountSet'
import authentication from '@/view/authentication'
import helpCenter from '@/components/helpCenter'
import invitation from '@/components/invitation'
import noticeDetail from '@/components/noticeDetail'
import complaint from '@/components/complaint'
import helpArticle from '@/components/helpArticle'
import new_account from '@/components/new_account'
import finance from '@/view/accounts/finance'
import coinCurrencyFlash from '@/view/accounts/coinCurrencyFlash'
import exchange from '@/view/accounts/exchange'
import legalAccount from '@/view/accounts/legalAccount'
import leverTransactions from '@/view/lever/lever_transactions'
import leverList from '@/view/lever/leverList'
import transferRecord from '@/view/lever/transferRecord'

import ForgetPwd from '@/components/ForgetPwd'  //忘记密码
import ResetPwd from '@/components/ResetPwd'  //重置密码密码
import bindEmail from '../view/bindEmail.vue'
import financialRecords from '@/view/accounts/financialRecords.vue'
Vue.use(Router)
export default new Router({
	routes: [

		{
			path: '/',
			name: 'home',
			component: home,
			
			children:[
				{
					path:'',
					name:'index',
					component:index
					
				},
				{
					path:'/legalTrade',
					component:() => import ('../components/LegalTrade.vue')
				},
				{
					path:'/legalPay',
					component:() => import ('../components/LegalPay.vue')
				},
				{
					path:'/legalPayDetail',
					component:() => import ('../components/LegalPayDetail.vue')
				},
				{
					path:'/shopLegalPayDetail',
					component:() => import ('../components/shop_legal_pay_detail.vue')
				},
				{
					path:'/legalRecord',
					component:() => import ('../components/LegalRecord.vue')
				},
				{
					path:'/legalDoneRecord',
					component:() => import ('../components/legalDoneRecord.vue')
				},
				{
					path:'/myLegalShops',
					component:() => import ('../components/MyLegalShops.vue')
				},
				{
					path:'/legalShopDetail',
					component:() => import ('../components/LegalShopDetail.vue')
				},
				{
					path:'/shopLegalRecord',
					component:() => import ('../components/shop_legal_record.vue')
				},
				{
					path:'/legalApplication',
					component:() => import ('../components/legalApplication.vue')
				},
				{
					path:'/legalApplicationData',
					component:() => import ('../components/legalApplicationData.vue')
				},
				{
					path:'/userSetting',
					name:'userSetting',
					component:userSetting,
					children:[
						{
							path:'',
							component:payOpts
						}
					]
				},
				{
					path:'/bindEmail',
					component:bindEmail
				},
				{
					path:'/leverTransactions',
					name:'leverTransactions',
					component:leverTransactions
				},
				{
					path:'/leverList',
					name:'leverList',
					component:leverList
				},
				{
					path:'/transferRecord',
					name:'transferRecord',
					component:transferRecord
				},
				{
					path:'/components/login',
					name: 'login',
					component: login
				},
				{
					path:'/forgetPwd',
					name: 'forgetPwd',
					component: ForgetPwd
				},
				{
					path:'/resetPwd',
					name: 'resetPwd',
					component: ResetPwd
				},
				{
					path:'/components/register',
					name: 'register',
					component: register
				},
				{
					path:'/components/noticeDetail',
					name:'noticeDetail',
					component:noticeDetail
				},
				{
					path:'/helpArticle',
					name:'helpArticle',
					component:helpArticle
				},
				{
					path:'/helpCenter',
					name:'helpCenter',
					component:helpCenter
				},
				{
					path:'/invitation',
					name:'invitation',
					component:invitation
				},
				{
					path:'/complaint',
					name:'complaint',
					component:complaint
				},
				{
					path:'/account',
					name:'account',
					component:account,
					children: [
						{
							path: '/accountSet',
								name: 'accountSet',
								component: accountSet
						},
						{
							path:'/authentication',
							name:'authentication',
							component:authentication
						},
						
						{
							path:'/financialRecords',
							name:'financialRecords',
							component:financialRecords
						},
						
					]
		
				},
				{
					path:'/new_account',
					name:'new_account',
					component:new_account,
					children: [
					
						{
							path:'/finance',
							name:'finance',
							component:finance
						},
						{
							path:'/coinCurrencyFlash',
							name:'coinCurrencyFlash',
							component:coinCurrencyFlash
						},
						{
							path:'/exchange',
							name:'exchange',
							component:exchange
						},
					
						{
							path:'/legalAccount',
							name:'legalAccount',
							component:legalAccount
						},
					]
		
				},
				{
					path:'/secondsOrder',
					name:'secondsOrder',
					component:secondsOrder
				},
				{
					path:'/leverdealCenter',
					name:'leverdealCenter',
					component:leverdealCenter
				},
				
				
			]
		},
	]
})



// WEBPACK FOOTER //
// ./src/router/index.js