<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 table-responsive">
                <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>Домен</th>
                                <th>id пользователя</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in favorite">
                                <td>{{ index+1 }}</td>
                                <td>{{ item.domain }}</td>
                                <td>{{ item.user_id }}</td>
                            </tr>
                        </tbody>
                </table>
            </div>
        </div>


    </div>
</template>

<script>

    export default {
        components: {

        },
        data(){
            return {

            }
        },
        mounted: function()
        {
            this.getFavorite();
        },
        computed: {
            favorite() {
                return this.$store.getters.favoriteList;
            },
        },
        methods: {
            getFavorite() {
                this.$store.dispatch('favoriteList', {});
            },

        }
    }
</script>
