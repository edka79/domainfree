<template>
    <div>
        <DxDataGrid
            id="freeContainer"
            :ref="dataGridRefName"
            :data-source="favorite"
            :remote-operations="true"
            :column-auto-width="true"
            :show-borders="true"
            :hover-state-enabled="true"
            :on-content-ready="contentReady"
            :width="1000"
        >
            <DxHeaderFilter :visible="true" />

            <DxScrolling row-rendering-mode="virtual"/>
            <DxPaging :page-size="10"/>
            <DxPager
                :visible="false"
                :allowed-page-sizes="pageSizes"
                display-mode="compact"
                :show-page-size-selector="showPageSizeSelector"
                :show-info="showInfo"
                :show-navigation-buttons="showNavButtons"
            />

            <DxFilterRow
                :visible="true"
            />


            <DxColumn
                :width="80"
                data-field="id"
                data-type="number"
                capttion="id"
                :visible="false"
            />

            <DxColumn
                :width="200"
                data-field="datecreate"
                data-type="date"
                date-format="dd.MM.yyyy HH:mm"
                format="dd.MM.yyyy HH:mm"
                caption="Дата добавления"
            />

            <DxColumn
                data-field="domain"
                data-type="string"
                caption="Домен"
            />

            <DxColumn
                data-field="area"
                data-type="string"
                caption="Состояние домена"
                cell-template="cellArea"
            />
                <template #cellArea="{ data }">
                    <div v-if="(data.data.area === 'search')" style="color: orange; font-weight: bold;">Освобождается</div>
                    <div v-else style="color: green; font-weight: bold;">Свободен сейчас</div>
                </template>

            <DxColumn
                :width="50"
                data-field="remove"
                data-type="string"
                caption=""
                cell-template="cellRemove"
            />
            <template #cellRemove="{ data }">
                <div style="text-align: right">
                    <button class="btn btn-sm btn-danger">X</button>
                </div>
            </template>
        </DxDataGrid>
    </div>
</template>

<script>

import {
    DxColumn,
    DxDataGrid,
    DxFilterRow,
    DxHeaderFilter,
    DxPager,
    DxPaging,
    DxScrolling
} from "devextreme-vue/data-grid";

export default {
        components: {
            DxHeaderFilter, DxFilterRow, DxPager, DxScrolling, DxPaging, DxColumn, DxDataGrid

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
