// import 'bootstrap-css-only/css/bootstrap.min.css';
// import 'mdbvue/build/css/mdb.css';
import Vue from 'vue'
import axios from 'axios'
import App from './App.vue'
import router from './router'
import store from './store'


axios.defaults.baseURL = 'http://domainfree.site/api';
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    'nobody' : localStorage.getItem('nobody')
};


Vue.config.productionTip = false

new Vue({
    axios,
    router,
    store,
    render: h => h(App)
}).$mount('#app')
