<template>
    <div>
        <div class="left-side">
<!--            <div class="logo">-->
<!--                <div class="position-relative d-flex align-items-center" style="max-width: 100px;">-->
<!--                    <i class="bi bi-bookmarks"></i> избранное-->
<!--                    <span class="position-absolute top-10 start-100 translate-middle badge rounded-pill bg-danger">0</span>-->
<!--                </div>-->
<!--            </div>-->

            <button @click="favoriteAll()" type="button"
                    :class="this.favoriteButton ? 'btn btn-primary btn-dark btn-sm position-relative' : 'btn btn-outline-primary btn-outline-dark btn-sm position-relative'">
                <i class="bi bi-bookmarks"></i> Закладки
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ favoriteCount }}
                </span>
            </button>

            <button v-if="showResetFilterBtn" @click="clearAllFilter" type="button" class="btn btn-outline-primary btn-outline-dark btn-sm position-relative ms-3">
                <i class="bi bi-close"></i> Сбросить все фильтры
            </button>
        </div>


<!--        <div class="right-side">-->
<!--            <button type="button" class="btn btn-outline-primary btn-outline-dark btn-sm position-relative">-->
<!--                <i class="bi bi-close"></i> Сбросить фильтры-->
<!--            </button>-->
<!--        </div>-->

<!--        <div class="right-side">-->
<!--            <DxSelectBox-->
<!--                :items="age"-->
<!--                :value="age[0]"-->
<!--                @value-changed="onValueChanged"-->
<!--            />-->
<!--        </div>-->

        <DxDataGrid
            id="searchContainer"
            :ref="dataGridRefName"
            :data-source="dataSource"
            :remote-operations="true"
            :column-auto-width="true"
            :show-borders="true"
            :hover-state-enabled="true"
            :on-content-ready="contentReady"
        >
            <DxHeaderFilter :visible="true" />

            <DxScrolling row-rendering-mode="virtual"/>
            <DxPaging :page-size="15"/>
            <DxPager
                :visible="true"
                :allowed-page-sizes="pageSizes"
                display-mode="full"
                infoText="страница {0} из {1}  (найдено {2} доменов)"
                :show-page-size-selector="showPageSizeSelector"
                :show-info="showInfo"
                :show-navigation-buttons="showNavButtons"
            />

            <DxFilterRow
                :visible="true"
            />


            <DxColumn
                :width="80"
                data-field="agr__id"
                data-type="number"
                capttion="id"
                :visible="false"
            />

            <DxColumn
                :width="70"
                data-field="favorite"
                data-type="nosearch"
                caption="Избранное"
                header-cell-template="headerFavorite"
                cell-template="cellFavorite"
            />
                <template #headerFavorite>
                    <div><i class="bi bi-bookmarks"></i></div>
                </template>
                <template #cellFavorite="{ data }">
                    <div @click="favorite(data)" style="cursor: pointer;">
                        <i v-if="(data.data.favorite !== null)" class="bi bi-bookmark-check-fill" title="Удалить из закладок"></i>
                        <i v-else class="bi bi-bookmark-plus" title="Добавить в закладки"></i>
                    </div>
                </template>

            <DxColumn
                data-field="agr__domain"
                data-type="string"
                caption="Домен"
                :allow-header-filtering="false"
                cell-template="cellDomain"
            />
                <template #cellDomain="{ data }">
                    <div class="cell-domain">
                        <div>{{ data.data.agr__domain }}</div>
                        <div class="cell-domain-tools">
                            <a href="#" @click="linkAction(data.data.agr__domain, 'copy')" class="bi bi-copy" title="Копировать"></a>
                            <a href="#" @click="linkAction(data.data.agr__domain, 'go')" class="bi bi-box-arrow-up-right" title="Перейти на сайт"></a>
                            <a href="#" class="bi bi-globe-americas" title="Посмотреть в WebArchive" @click="linkAction(data.data.agr__domain, 'webarchive')"></a>
                        </div>
                    </div>
                </template>
            <DxColumn
                :width="80"
                data-field="zones__zone_alias"
                data-type="nosearch"
                caption="Зона"
            />
            <DxColumn
                :width="120"
                data-field="agr__expired_iks"
                data-type="number"
                caption="Яндекс ИКС"
            />
            <DxColumn
                :width="150"
                data-field="agr__expired_links"
                data-type="number"
                caption="Ссылок на домен"
            />
            <DxColumn
                :width="150"
                data-field="agr__is_keyword"
                data-type="string"
                caption="Домен-ключевик"
                cell-template="cellKeyword"
            />
                <template #cellKeyword="{ data }">
                    <div v-if="(data.data.agr__is_keyword !== null)">{{ data.data.agr__keyword_word }}</div>
                </template>

            <DxColumn
                :width="100"
                data-field="agr__age"
                data-type="number"
                caption="Возраст"
            />
            <DxColumn
                :width="100"
                data-field="agr__litera_count"
                data-type="number"
                caption="Символов"
            />
            <DxColumn
                :width="150"
                data-field="agr__litera_attr"
                data-type="nosearch"
                caption="Тире или цифры"
                cell-template="cellLiteraAttr"
                :allow-header-search="false"
            />
                <template #cellLiteraAttr="{ data }">
                    <div v-if="(data.data.agr__litera_attr !== 'без цифр и тире')">{{ data.data.agr__litera_attr }}</div>
                </template>
            <DxColumn
                :width="150"
                data-field="agr__date_free"
                data-type="date"
                format="dd.MM.yyyy"
                caption="Дата освобождения"
                :allow-header-filtering="false"
            />
            <DxColumn
                :width="150"
                data-field="agr__days_for_free"
                data-type="string"
                caption="Осталось дней"
                cell-template="cellDays"
            />
            <template #cellDays="{ data }">
                <div style="font-weight: bold;" :style="{ color: data.data.agr__days_for_free < 6 ? 'red' : 'orange' }">{{ data.data.agr__days_for_free }}</div>
            </template>
        </DxDataGrid>
    </div>
</template>
<script>

//import axios from './axios';
import {searching, setFavorite} from "./../api";

import {
    DxHeaderFilter,
    DxColumn,
    DxDataGrid,
    DxFilterRow,
    DxScrolling,
    DxPager,
    DxPaging
} from 'devextreme-vue/data-grid';
import DxSelectBox from 'devextreme-vue/select-box';

// simple data
import CustomStore from 'devextreme/data/custom_store';
function isNotEmpty(value) {
    return value !== undefined && value !== null && value !== '';
}
const store = new CustomStore({
    key: 'agr__id',
    load(loadOptions) {
        let params = '?area=search&';
        [
            'skip', 'take', 'requireTotalCount', 'requireGroupCount',
            'sort', 'filter', 'totalSummary', 'group', 'groupSummary',
        ].forEach((i) => {
            if (i in loadOptions && isNotEmpty(loadOptions[i])) { params += `${i}=${JSON.stringify(loadOptions[i])}&`; }
        });
        params = params.slice(0, -1);

        return searching(params)
            .then((res) => ({
                data: res.data.data,
                totalCount: res.data.totalCount,
                summary: res.data.summary,
                groupCount: res.data.groupCount,
            }))
            .catch(error => console.log(error))
    },
});

import 'devextreme/dist/css/dx.light.css';

export default {
    components: {
        DxHeaderFilter,
        DxSelectBox,
        DxColumn,
        DxDataGrid,
        DxFilterRow,
        DxScrolling,
        DxPager,
        DxPaging
    },
    data() {
        return {
            dataSource: store,
            age: [1, 2, 3, 4, 5],
            dataGridRefName: 'dataGrid',
            applyFilter: true,
            pageSizes: [10, 15, 20, 50, 100],
            showPageSizeSelector: true,
            showInfo: true,
            showNavButtons: true,
            // add
            favoriteButton: false,
            showResetFilterBtn: false,
        };
    },
    computed: {
        favoriteCount() {
            return this.$store.getters.favoriteCount.search;
        },
    },
    methods: {
        contentReady(){
            const dataGrid = this.$refs[this.dataGridRefName].instance;
            let filter = dataGrid.getCombinedFilter();
            //console.log(filter);
            this.showResetFilterBtn =  (typeof(filter) !== 'undefined' && filter !== null) ? true : false;
        },
        clearAllFilter({ value }) {
            const dataGrid = this.$refs[this.dataGridRefName].instance;
            dataGrid.clearFilter();
            this.favoriteButton = false;
        },
        onValueChanged({ value }) {
            const dataGrid = this.$refs[this.dataGridRefName].instance;

            if (value === 'All') {
                dataGrid.clearFilter();
            } else {
                dataGrid.filter(['age', '=', value]);
            }
        },
        favoriteAll(){
            const dataGrid = this.$refs[this.dataGridRefName].instance;
            if (!this.favoriteButton) {
                dataGrid.filter(['favorite', '=', 'Да']);
            } else {
                dataGrid.clearFilter();
            }
            this.favoriteButton = !this.favoriteButton;
        },
        favorite(data){
            console.log(data);
            setFavorite({
                area: 'search',
                domain: data.data.agr__domain
            })
                .then((res) => {
                    const dataGrid = this.$refs[this.dataGridRefName].instance;
                    // пишем в row напрямую
                    dataGrid.cellValue(data.rowIndex, 'favorite', res.data.action === 'add' ? 'Да' : null);
                    this.$store.dispatch('favoriteCount', {});
                })
                .catch(error => console.log(error))
        },
        linkAction(domain, action){
            if(action === 'copy'){
                navigator.clipboard.writeText(domain)
                    .then(() => {
                        //
                    })
                    .catch((err) => {
                        //
                    });
            }
            if(action === 'go'){
                window.open('https://' + domain, '_blank');
            }
            if(action === 'webarchive'){
                window.open('https://web.archive.org/web/2025*/' + domain, '_blank');
            }
        }
    },
};
</script>


<style scoped>
#searchContainer {
    margin: 20px 0;
    height: 100%!important;
}

.right-side {
    position: absolute;
    right: 1px;
    top: 6px;
}

.logo {
    line-height: 48px;
}

.logo img {
    vertical-align: middle;
    margin: 0 5px;
}

.logo img:first-child {
    margin-right: 9px;
}

.dx-row.dx-data-row .employee {
    color: #bf4e6a;
    font-weight: bold;
}

#searchContainer {
    margin: 20px 0;
    height: 100%!important;
}


</style>
