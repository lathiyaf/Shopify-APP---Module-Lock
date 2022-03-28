<template>
    <div class="Polaris-Modal-Dialog__Container popup-customer-group" id="module-lock-model" style="display: none;">
        <div>
            <div class="Polaris-Modal-Dialog__Modal" role="dialog" aria-labelledby="modal-header15" tabindex="-1" style="max-height: max-content;">
                <div class="Polaris-Modal-Header">
                    <div id="modal-header15" class="Polaris-Modal-Header__Title">
                        <h2 class="Polaris-DisplayText Polaris-DisplayText--sizeSmall text-capitalize">Add lock</h2>
                    </div><button class="Polaris-Modal-CloseButton" aria-label="Close"@click="closeModel()" ><span class="Polaris-Icon Polaris-Icon--colorInkLighter Polaris-Icon--isColored"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                        <path d="M11.414 10l6.293-6.293a.999.999 0 1 0-1.414-1.414L10 8.586 3.707 2.293a.999.999 0 1 0-1.414 1.414L8.586 10l-6.293 6.293a.999.999 0 1 0 1.414 1.414L10 11.414l6.293 6.293a.997.997 0 0 0 1.414 0 .999.999 0 0 0 0-1.414L11.414 10z" fill-rule="evenodd"></path>
                    </svg></span></button>
                </div>
                <form name="customer_broup" id="find-table" style="margin-bottom: 0px;">
                    <div class="Polaris-Modal__BodyWrapper">
                        <div class="Polaris-Modal__Body Polaris-Scrollable Polaris-Scrollable--vertical" data-polaris-scrollable="true">
                            <div class="" v-if=!is_select style="padding: 20px;">
                                <div class="Polaris-Labelled__LabelWrapper">
                                    <div class="Polaris-Label"><label id="PolarisTextField2Label" for="PolarisTextField2" class="Polaris-Label__Text">To get started, what would you like to protect?</label></div>
                                </div>
                                <div class="Polaris-Connected">
                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField Polaris-TextField--hasValue">
                                            <div class="Polaris-TextField__Prefix" id="TextField1Prefix"><span class="Polaris-Icon Polaris-Icon--colorSkyDark"><svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M8 12a4 4 0 1 1 0-8 4 4 0 0 1 0 8m9.707 4.293l-4.82-4.82A5.968 5.968 0 0 0 14 8 6 6 0 0 0 2 8a6 6 0 0 0 6 6 5.968 5.968 0 0 0 3.473-1.113l4.82 4.82a.997.997 0 0 0 1.414 0 .999.999 0 0 0 0-1.414" fill-rule="evenodd"></path></svg></span></div>
                                            <input id="PolarisTextField2" type="search" class="Polaris-TextField__Input" v-model="search" @keyup="getData()" aria-labelledby="PolarisTextField2Label" aria-invalid="false" aria-multiline="false" value="" placeholder="Search – products, collections, variants, pages, vendors, article tags, apps…">
                                            <div class="Polaris-TextField__Backdrop"></div>
                                        </div>
                                    </div>
                                </div>
                                <bullet-list-loader :width="300" :height="100" primaryColor="#f3f3f3" v-if="is_load">
                                </bullet-list-loader>
                                <div v-else-if="items.length > 0 && search !== ''">
                                    <div class="Polaris-Labelled__HelpText mb-15">Greate, We found {{items.length}} matches!</div>
                                    <div class="lock-resource-list list-model">
                                        <table class="w-100">
                                            <thead></thead>
                                            <tbody>
                                            <tr class="Polaris-ResourceItem" @click="selectItem(index)"  v-for="(item, index) in items" :key="index">
                                                <td class="" style="width: 7%;cursor: pointer;">
                                                    <div class="d-flex align-center">
                                                        <span class="mr-10 Polaris-Avatar Polaris-Avatar--styleSix Polaris-Avatar--sizeMedium Polaris-Avatar--hasImage border-radius-0 lock-image">
                                                            <img :src=item._resourceImage alt="" role="presentation" class="Polaris-Avatar__Image border-radius-0"></span>
                                                    </div>
                                                </td>
                                                <td class="" style="cursor: pointer;" >
                                                    <p class="mb-0" style="font-weight: 700;">{{item._resourceTitle}}</p>
                                                    <p class="mb-0 res-des">{{item._resourceDescription}}</p>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div v-else-if="items.length === 0 && search !== '' && !is_load" style="margin-top: 20px;">
                                    <h2 class="Polaris-DisplayText_1u0t8 Polaris-DisplayText--sizeSmall_7647q">We didn't find anything that matched "{{search}}"</h2>
                                </div>
                            </div>
                            <div class="" v-else style="padding: 20px;">
                                <table class="w-100">
                                    <thead></thead>
                                    <tbody>
                                    <tr class="Polaris-ResourceItem" style="cursor:pointer;color: #ffffff;background-color: #5c6ac4;">
                                        <td class="" style="width: 7%;padding: 10px;">
                                            <div class="d-flex align-center">
                                                        <span class="mr-10 Polaris-Avatar Polaris-Avatar--styleSix Polaris-Avatar--sizeMedium Polaris-Avatar--hasImage border-radius-0 lock-image">
                                                            <img :src=selectedItem._resourceImage alt="" role="presentation" class="Polaris-Avatar__Image border-radius-0"></span>
                                            </div>
                                        </td>
                                        <td class="" >
                                            <p class="mb-0" style="font-weight: 700;">{{selectedItem._resourceTitle}}</p>
                                            <p class="mb-0">{{selectedItem._resourceDescription}}</p>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <div style="padding: 20px;" v-if="!is_exising">
                                    <label class="Polaris-Choice" for="enable_lock" style="margin-bottom: 0px;">
                                        <span class="Polaris-Choice__Control"><span class="Polaris-Checkbox">
                                            <input id="enable_lock" v-model=selectedItem._is_enable type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="">
                                            <span class="Polaris-Checkbox__Backdrop">
                                            </span>
                                            <span class="Polaris-Checkbox__Icon">
                                                <span class="Polaris-Icon">
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                          <path d="M8.315 13.859l-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.437.437 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0">
                                                          </path>
                                    </svg></span></span></span></span><span class="Polaris-Choice__Label">Enable this lock</span></label>
                                    <div class="Polaris-Choice__Descriptions"><div class="Polaris-Choice__HelpText" id="Checkbox1HelpText">The most important part. :)</div></div>
                                </div>
                                    <div class="Polaris-Card__Footer">
                                        <div class="Polaris-ButtonGroup">
                                            <div class="Polaris-ButtonGroup__Item">
                                                <router-link type="button" :class="{'exist-style' : ( is_exising )}"  class="Polaris-Button Polaris-Button--primary" v-if="is_exising" :to="{name :'edit-lock', params: {item: selectedItem.id }}">
                                                    <span class="Polaris-Button__Content">
                                                        <span>View existing lock</span>
                                                    </span>
                                                </router-link>
                                                <button type="button" class="Polaris-Button Polaris-Button--primary" v-else @click="saveLock()" :class="{'Polaris-Button--disabled' : ( is_disable )}">
                                                    <span class="Polaris-Button__Content">
                                                        <span>Save</span>
                                                    </span>
                                                </button>
                                            </div>
                                            <div class="Polaris-ButtonGroup__Item" ><button type="button" class="Polaris-Button"  :class="{'exist-style' : ( is_exising )}" @click="is_select=false,items=[],search=''"><span class="Polaris-Button__Content"><span>Start over</span></span></button></div></div></div>
                            </div>
                        </div>
                    </div>
                    <div class="Polaris-Modal-Footer">
                        <div class="Polaris-Modal-Footer__FooterContent">
                            <div class="Polaris-Stack Polaris-Stack--alignmentCenter">
                                <div class="Polaris-Stack__Item">
                                    <div class="Polaris-ButtonGroup">
                                        <div class="Polaris-ButtonGroup__Item"><button type="button" class="Polaris-Button" id="close_group_popup" @click="closeModel()"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Close</span></span></button></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
    import {ContextualSaveBar} from "@shopify/app-bridge/actions";

    var modal = "";
    import helper from '../../helper';
    import {BulletListLoader} from 'vue-content-loader';

    export default {
        components: {
            BulletListLoader,
        },
        name: 'LockModal',
        props: ['showModel'],
        data(){
            return{
                is_disable: false,
                search: '',
                items: [],
                selectedItem: [],
                resourceData: [],
                resource: '',
                resourceList: [],
                next_page: '',
                prev_page: '',
                is_load: false,
                is_select: false,
                exist_locks: [],
                is_exising: false,
            }
        },
        methods:{
            closeModel() {
                this.search = '';
                this.items = [];
                helper.stopLoading();
                this.$emit('update', false);
            },
            async getData(s){
                let base = this;
                let url = 'search?s=' + base.search;
                helper.startLoading();
                base.is_load = true;
                base.items = [];
                await axios.get(url)
                    .then(res => {
                        base.items = [];
                        base.items = res.data.data;
                    })
                    .catch(err => {
                        console.log(err);
                    })
                    .finally(res => {
                        base.is_load = false;
                        helper.stopLoading();
                    });
            },
            selectItem(index){
                let existing = this.exist_locks;
                let base = this;
                this.selectedItem = this.items[index];

                existing.forEach( function ( elem, index ){
                    if( elem.resource_id == base.selectedItem._resourceId ){
                        base.is_exising = true;
                        base.selectedItem.id = elem.id;
                        return false;
                    }
                });
                console.log(this.selectedItem);
                this.is_select = true;
            },
            async saveLock(){
                let base = this;
                helper.startLoading();
                base.is_disable = true;
                await axios({
                    url: 'save-lock',
                    data: {
                        'data' : base.selectedItem,
                    },
                    method: 'post',
                }).then(res => {
                    helper.successToast(res.data.data.msg)
                    base.closeModel();
                    base.$router.push({name: 'edit-lock', params: {item: res.data.data.id }});
                })
                    .catch(err => {
                        console.log(err);
                    })
                    .finally(res => {
                        helper.stopLoading();
                        base.is_disable = false;
                    });
            },
            modalInit(ids){
                this.exist_locks = ids;
            }
        },
        mounted() {
            modal = document.getElementById("module-lock-model");
        },
        watch: {
            showModel(newVal, oldVal) {
                if (newVal === true) {
                    modal.style.display = "block";
                } else {
                    modal.style.display = "none";
                }
            },
        },
    }
</script>
