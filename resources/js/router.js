import Vue from 'vue'
import Router from 'vue-router'
import Search from './components/Search.vue'
import Favorite from './components/Favorite.vue'
import Free from './components/Free.vue'

Vue.use(Router)


const router = new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    routes: [
        {
            path: '/drop',
            name: 'search',
            component: Search,
            meta: {
                title: 'Поиск освобождающихся доменов ru, su, рф',
            },
        },
        {
            path: '/favorite',
            name: 'favorite',
            component: Favorite,
            meta: {
                title: 'Домены в закладках',
            },
        },{
            path: '/register',
            name: 'free',
            component: Free,
            meta: {
                title: 'Поиск свободных доменов по словарю',
            },
        },
    ]
})

export default router; // Экспортируем экземпляр роутера
