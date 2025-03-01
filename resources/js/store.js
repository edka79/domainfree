import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

import {getFavorite, getFavoriteCount} from './api.js'

export default new Vuex.Store({
    state: {
        favoriteList: null,
        favoriteCount: {
            all: 0,
            search: 0,
            free: 0
        },
    },
    getters: {
        favoriteList: state => {
            return state.favoriteList;
        },
        favoriteCount: state => {
            return state.favoriteCount;
        },
    },
    mutations: {
        favoriteList: (state, data) => {
            state.favoriteList = data
        },
        favoriteCount: (state, data) => {
            state.favoriteCount = data
        },
    },
    actions: {
        favoriteList: (context, payload) => {
            getFavorite()
                .then(res => {
                    context.commit('favoriteList', res.data);
                })
                .catch(error => console.log(error))
        },
        favoriteCount: (context, payload) => {
            getFavoriteCount()
                .then(res => {
                    context.commit('favoriteCount', res.data);
                })
                .catch(error => console.log(error))
        },

    }
})
