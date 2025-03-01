<template>
     <div id="layoutSidenav">
          <div id="layoutSidenav_nav">
               <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
               <div class="sb-sidenav-menu">
                    <div class="nav">

                        <router-link :to="{ name: 'search' }" class="nav-link">
                            <div class="sb-nav-link-icon">
                                <i class="bi bi-search"></i>
                            </div>
                            <span>Поиск доменов</span>
                        </router-link>
                        <router-link :to="{ name: 'favorite' }" class="nav-link">
                            <div class="sb-nav-link-icon">
                                <i class="bi bi-bookmarks"></i>
                            </div>
                            <span>Домены в закладках
                                &nbsp;
                                <span v-if="favoriteCount > 0" class="translate-middle badge rounded-pill bg-danger">
                                    {{ favoriteCount }}
                                </span>
                            </span>
                        </router-link>
<!--                        <router-link :to="{ name: 'alerts' }" class="nav-link">-->
<!--                            <div class="sb-nav-link-icon">-->
<!--                                <i class="bi bi-bell-fill"></i>-->
<!--                            </div>-->
<!--                            <span>Уведомления о новых доменах</span>-->
<!--                        </router-link>-->
                        <router-link :to="{ name: 'free' }" class="nav-link">
                            <div class="sb-nav-link-icon">
                                <i class="bi bi-magic"></i>
                            </div>
                            <span>Домены ключевики <sup style="font-size: 10px;">свободные</sup></span>
                        </router-link>

                    </div>
               </div>
               </nav>
          </div>

          <div id="layoutSidenav_content">
               <div class="container-fluid px-4">
<!--                    <h2 class="mt-3">Заголовок</h2>-->
                    <ol class="breadcrumb mb-4">
                         <li class="breadcrumb-item"> </li>
                    </ol>
                    <div class="card">
                         <div class="card-body">
                              <router-view></router-view>
                         </div>
                    </div>
               </div>
               <footer class="py-4 bg-light mt-auto">
               <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-center small">
                         <div class="text-muted">&copy; 2023</div>
                    </div>
               </div>
               </footer>
          </div>
     </div>
</template>



<script>
    import axios from 'axios';
    import {getFavoriteCount, getNobody} from "./api";

    export default {
        name: "App",
        components: {
            axios
        },
        mounted() {
            if(localStorage.getItem('nobody') === null){
                getNobody()
                    .then(res => {
                        localStorage.setItem('nobody', res.data);
                        axios.defaults.headers.common.nobody = res.data;
                    })
                    .catch(error => console.log(error))
            }
            this.$store.dispatch('favoriteCount', {});
        },
        computed: {
            favoriteCount() {
                return this.$store.getters.favoriteCount.all;
            },
        },
    }
</script>
