<template>
    <div class="module-content">
        <div class="mt-20">
            <!--        <div class="Polaris-Page__Navigation">-->
            <!--            <nav role="navigation">-->
            <!--                <label class="back-dashboard">-->
            <!--                <router-link data-polaris-unstyled="true" class="back-arrow" style="cursor: pointer;"-->
            <!--                             :to="{name :'locks'}" tag="span">-->
            <!--                        <span class="_2-hnq">-->
            <!--                            <svg viewBox="0 0 20 20" class="v3ASA" focusable="false" aria-hidden="true">-->
            <!--                                <path-->
            <!--                                    d="M12 16a.997.997 0 0 1-.707-.293l-5-5a.999.999 0 0 1 0-1.414l5-5a.999.999 0 1 1 1.414 1.414L8.414 10l4.293 4.293A.999.999 0 0 1 12 16"-->
            <!--                                    fill-rule="evenodd">-->
            <!--                                </path>-->
            <!--                            </svg>-->
            <!--                        </span>-->
            <!--                </router-link>-->
            <!--                <router-link data-polaris-unstyled="true" class="back-text" style="cursor: pointer;"-->
            <!--                             :to="{name :'locks'}" tag="span"> Locks-->
            <!--                </router-link>-->
            <!--            </label>-->
            <!--            </nav>-->
            <!--        </div>-->
            <div class="app-javascript-common-EmbeddedPage__titles--1iT4c">
                <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">{{ form.title }}</h1>
                <h2 class="Polaris-DisplayText Polaris-DisplayText--sizeSmall">{{ form.subtitle }}</h2>
            </div>

            <div class="d-flex justify-content-between mt-20">
                <div class="w-70 mr-20">
                    <div class="Polaris-Card" style="overflow: visible;">
                        <div class="Polaris-Card__Header d-flex justify-content-between">
                            <h2 class="Polaris-Heading">Keys</h2>
                            <div class="Polaris-Connected" style="overflow: visible;">
                                <div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented w-100">
                                    <div class="Polaris-ButtonGroup__Item">
                                        <div>
                                            <button type="button" class="Polaris-Button" tabindex="0"
                                                    aria-controls="Polarispopover2" aria-owns="Polarispopover2"
                                                    aria-expanded="true"
                                                    @click="is_show_key = !is_show_key,main_index=-1"><span
                                                class="Polaris-Button__Content"><span
                                                class="Polaris-Button__Text">Add key</span><span
                                                class="Polaris-Button__Icon">
                                                        <div class="Polaris-Button__DisclosureIcon"><span
                                                            class="Polaris-Icon"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                  <path d="M5 8l5 5 5-5z" fill-rule="evenodd"></path>
                                                                </svg></span></div>
                                                          </span></span>
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="is_show_key"
                                        class="Polaris-PositionedOverlay scriptTagfilter Polaris-Popover__PopoverOverlayPolaris-Popover__PopoverOverlay--open"
                                        style="top: 37px;border-radius: 2px;width: 100%;border: 1px solid #dadcdd;">
                                        <div class="Polaris-Popover__Wrapper" style="width: 130px;padding: 5px;cursor: pointer;">
                                            <p @click="addKey('is_signed_in')">is signed in</p>
                                            <p @click="is_has_purchased = true;">has purchased...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="Polaris-Card__Section" v-if="form.key.length > 0">
                            <div class="mb-10" v-for="(item, index) in form.key" style="">
                                <div v-if="item.length > 0" v-for="(subitem, sindex) in item">
                                    <div v-if="sindex == 0" class="d-flex justify-content-between">
                                        <span><span v-if="index != 0">Or, </span>Permit if the customer {{subitem}} <span v-if="item.length == 1" @click="is_show_key = !is_show_key,main_index=index" class="Polaris-Link ">--and...</span></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;cursor:pointer;" viewBox="0 0 20 20" @click="removeKey('main', index)"><path fill-rule="evenodd" fill="#637381" d="M17 4h-3V2c0-1.103-.897-2-2-2H8C6.897 0 6 .897 6 2v2H3a1 1 0 1 0 0 2v13a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V6a1 1 0 1 0 0-2zM5 18h10V6H5v12zM8 4h4V2H8v2zm0 12a1 1 0 0 0 1-1V9a1 1 0 1 0-2 0v6a1 1 0 0 0 1 1m4 0a1 1 0 0 0 1-1V9a1 1 0 1 0-2 0v6a1 1 0 0 0 1 1"/></svg>
                                    </div>
                                    <div v-if="sindex != 0" class="d-flex justify-content-between">
                                        <span>
                                            <svg @click="removeKey('sub', index, sindex)" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer;width:16px;" viewBox="0 0 20 20"><path fill-rule="evenodd" fill="#637381" d="M17 4h-3V2c0-1.103-.897-2-2-2H8C6.897 0 6 .897 6 2v2H3a1 1 0 1 0 0 2v13a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V6a1 1 0 1 0 0-2zM5 18h10V6H5v12zM8 4h4V2H8v2zm0 12a1 1 0 0 0 1-1V9a1 1 0 1 0-2 0v6a1 1 0 0 0 1 1m4 0a1 1 0 0 0 1-1V9a1 1 0 1 0-2 0v6a1 1 0 0 0 1 1"/></svg> ... and
                                            the customer {{subitem}}
                                            <span v-if="item.length - 1 == sindex" @click="is_show_key = !is_show_key, main_index=index" class="Polaris-Link ">--and...</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="Polaris-Card" style="">
                        <div class="Polaris-Card__Header"><h2 class="Polaris-Heading">Messages</h2></div>
                        <div class="Polaris-Card__Section">
                            <div class="Polaris-FormLayout">
                                <div class="Polaris-FormLayout__Item">
                                    <div class="Polaris-Label"><label id="guest_message_content" for="guest_message" class="Polaris-Label__Text">Guest user
                                        content</label></div>
                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                        <textarea id="guest_message" class="Polaris-TextField__Input"
                                                  :placeholder="setting" v-model="form.guest_user_content">{{ form.guest_user_content }}</textarea>
                                        <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                </div>
                                <div class="Polaris-FormLayout__Item">
                                    <div class="Polaris-Label"><label id="PolarisTextField2Label" for="AccessDeniedText" class="Polaris-Label__Text">Access denied
                                        content</label></div>
                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                        <textarea id="AccessDeniedText" class="Polaris-TextField__Input"
                                                  :placeholder="setting" v-model="form.access_denied_content">{{ form.access_denied_content }}</textarea>
                                        <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-30">
                    <div class="Polaris-Card" style="">
                        <div class="Polaris-Card__Header"><h2 class="Polaris-Heading">Settings</h2></div>
                        <div class="Polaris-Card__Section">
                            <label class="Polaris-Choice" for="enable_lock" style="margin-bottom: 0px;">
                                            <span class="Polaris-Choice__Control"><span class="Polaris-Checkbox">
                                                <input id="enable_lock" v-model=form.is_enable type="checkbox"
                                                       class="Polaris-Checkbox__Input" aria-invalid="false"
                                                       role="checkbox" aria-checked="false" value="">
                                                <span class="Polaris-Checkbox__Backdrop">
                                                </span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                             focusable="false" aria-hidden="true">
                                                              <path
                                                                  d="M8.315 13.859l-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.437.437 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0">
                                                              </path>
                                        </svg></span></span></span></span><span class="Polaris-Choice__Label">Enable this lock</span></label>
                            <div class="Polaris-Choice__Descriptions">
                                <div class="Polaris-Choice__HelpText" id="Checkbox1HelpText">The most important part.
                                    :)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!--        START:: has purchased modal-->
        <div v-if="is_has_purchased">
            <div class="Polaris-Modal-Dialog__Container" data-polaris-layer="true" data-polaris-overlay="true">
                <div>
                    <div role="dialog" aria-labelledby="Polarismodal-header16" tabindex="-1" class="Polaris-Modal-Dialog">
                        <div class="Polaris-Modal-Dialog__Modal has-purchased">
                            <div class="Polaris-Modal-Header">
                                <div id="Polarismodal-header16" class="Polaris-Modal-Header__Title">
                                    <h2 class="Polaris-DisplayText Polaris-DisplayText--sizeSmall">… if the customer has purchased…</h2>
                                </div><button class="Polaris-Modal-CloseButton" aria-label="Close" @click="is_has_purchased = false;addKey('product_sku')"><span class="Polaris-Icon Polaris-Icon--colorInkLighter Polaris-Icon--isColored"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                  <path d="M11.414 10l6.293-6.293a.999.999 0 1 0-1.414-1.414L10 8.586 3.707 2.293a.999.999 0 1 0-1.414 1.414L8.586 10l-6.293 6.293a.999.999 0 1 0 1.414 1.414L10 11.414l6.293 6.293a.997.997 0 0 0 1.414 0 .999.999 0 0 0 0-1.414L11.414 10z" fill-rule="evenodd"></path>
                </svg></span></button>
                            </div>
                            <div class="Polaris-Modal__BodyWrapper">
                                <div class="Polaris-Modal__Body Polaris-Scrollable Polaris-Scrollable--vertical" data-polaris-scrollable="true">
                                    <section class="Polaris-Modal-Section">
                                        <div class="Polaris-Stack Polaris-Stack--vertical">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="product" for="PolarisTextField2" class="Polaris-Label__Text">Look for products matching…</label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue"><input id="PolarisTextField2" v-model="has_purchased" class="Polaris-TextField__Input" aria-labelledby="PolarisTextField2Label" aria-invalid="false" aria-multiline="false">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="Polaris-Labelled__HelpText" id="PolarisTextField4HelpText">Enter a SKU, title, tag to check for any purchase product.
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                            <div class="Polaris-Modal-Footer">
                                <div class="Polaris-Modal-Footer__FooterContent">
                                    <div class="Polaris-Stack Polaris-Stack--alignmentCenter">
                                        <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"></div>
                                        <div class="Polaris-Stack__Item">
                                            <div class="Polaris-ButtonGroup">
                                                <div class="Polaris-ButtonGroup__Item"><button type="button" class="Polaris-Button Polaris-Button--primary" @click="is_has_purchased = false;addKey('product_sku')"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Close</span></span></button></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<!--        END:: has purchased modal-->
    </div>
</template>

<script>
import helper from "../../helper";
import {ContextualSaveBar} from "@shopify/app-bridge/actions";

export default {
    data() {
        return {
            form:[],
            setting: '',
            is_show_key: false,
            is_has_purchased: false,
            main_index: -1,
            has_purchased: '',
            temp_prisine: '',
            watch: false,
            id: '',
        }
    },
    methods: {
        createContextualSaveBar() {
            let base = this;
            let options = {
                saveAction: {
                    disabled: false,
                    loading: false,
                },
                discardAction: {
                    disabled: false,
                    loading: false,
                    discardConfirmationModal: true,
                },
            };
            var contextualSaveBar = helper.contextualSaveBar(options);

            contextualSaveBar.dispatch(ContextualSaveBar.Action.SHOW);

            contextualSaveBar.subscribe(ContextualSaveBar.Action.DISCARD, function () {
                base.getLock();
                contextualSaveBar.dispatch(ContextualSaveBar.Action.HIDE);
            });
            contextualSaveBar.subscribe(ContextualSaveBar.Action.SAVE, function () {
                contextualSaveBar.set({saveAction: {loading: true}, discardAction: {disabled: true}});
                base.sendForm();
            });
        },
        async getLock() {
            let base = this;
            let url = 'edit-lock?id=' + base.id;
            helper.startLoading();
            await axios.get(url)
                .then(res => {
                    base.form = res.data.data.lock;
                    base.setting = res.data.data.access_denied_content;
                    this.temp_prisine = JSON.stringify(base.form);
                    base.watch = true;
                })
                .catch(err => {
                    console.log(err);
                })
                .finally(res => {
                    helper.stopLoading();
                });
        },
        addKey(key){
            let msg = '';
            if( key === 'is_signed_in' ){
                msg = 'is signed in';
            }else if(key === 'product_sku'){
                if(this.has_purchased != ''){
                    msg = 'has purchased "'+ this.has_purchased + '"';
                }
            }
            if( this.form.key.length == 0 ){
                let k = [];
                k.push(msg);
                (msg !== '') ? this.form.key.push(k) : '';
            }else{
                if(this.main_index >= 0){
                    (msg !== '') ? this.form.key[this.main_index].push(msg) : '';
                }else{
                    let k = [];
                    k.push(msg);
                    (msg !== '') ? this.form.key.push(k) : '';
                }
            }
            this.is_show_key = false;
            this.has_purchased = '';
        },
        removeKey(type, main_index, sindex = ''){
            if( type === 'main' ){
               this.form.key.splice(main_index, 1);
            }else if( type === 'sub' ){
                this.form.key[main_index].splice(sindex, 1);
            }
        },
        async sendForm(){
            let base = this;
            let url = 'save-lock';
            helper.startLoading();
            await axios({
                url: url,
                data: {
                    'data' : base.form,
                },
                method: 'post',
            }).then(res => {
                helper.successToast(res.data.data.msg)
                console.log(res.data.data.msg);
                base.getLock();
            })
                .catch(err => {
                    console.log(err);
                })
                .finally(res => {
                    helper.stopLoading();
                    var contextualSaveBar = helper.contextualSaveBar();
                    contextualSaveBar.dispatch(ContextualSaveBar.Action.HIDE);
                });
        }
    },
    mounted() {
        this.id = this.$route.params.item;
        this.getLock();
    },
    watch: {
        form: {
            immediate: true,
            deep: true,
            handler: function () {
                if( this.watch ){
                    if (_.isEqual(this.temp_prisine, JSON.stringify(this.form))) {
                        let contextualSaveBar = helper.contextualSaveBar();
                        contextualSaveBar.dispatch(ContextualSaveBar.Action.HIDE);
                    } else {
                        this.createContextualSaveBar();
                    }
                }
            }
        }
    },
}
</script>
