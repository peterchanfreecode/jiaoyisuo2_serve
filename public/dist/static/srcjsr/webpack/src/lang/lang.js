import Vue from 'vue'
import VueI18n from 'vue-i18n'
Vue.use(VueI18n)
import zh from './zh'
import en from './en'
import hk from './hk'
import jp from './jp'
let locale = '';
if(window.localStorage.getItem('lang') == 'hk'){
  locale = window.localStorage.getItem('lang') 
}else if(window.localStorage.getItem('lang') == 'en'){
  locale = window.localStorage.getItem('lang') 
}else if(window.localStorage.getItem('lang') == 'jp'){
  locale = window.localStorage.getItem('lang') 
}else {
  locale = 'zh';
  window.localStorage.setItem('lang','zh');
}
export default new VueI18n({
  locale:locale,
  messages:{
    zh:zh,
    en:en,
    hk:hk,
    jp:jp
  }
})


// WEBPACK FOOTER //
// ./src/lang/lang.js