import Vue from 'vue'
import Router from 'vue-router'
import Search from './components/Search.vue'
import Favorite from './components/Favorite.vue'
import Free from './components/Free.vue'

Vue.use(Router)

export default new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    routes: [
        {
            path: '/drop',
            name: 'search',
            component: Search
        },
        {
            path: '/favorite',
            name: 'favorite',
            component: Favorite
        },{
            path: '/register',
            name: 'free',
            component: Free
        },
    ]
})
