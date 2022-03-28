<template>
    <div class="module-content">
        <div class="Polaris-Card__Header Polaris-Card__Header-meta">
            <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                <h2 class="Polaris-Heading">Settings</h2>
            </div>

<!--             basic section-->
            <div class="module-header-divide d-flex setting-section">
                <div class="w-30 section-heading" style="padding: 20px 0px;">
                    Basics
                </div>
                <div class="w-70 Polaris-Card">
                    <div class="Polaris-Card__Section">
                        <div class="Polaris-Stack Polaris-Stack--alignmentCenter d-flex justify-content-between">
                            <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                                <p style="margin-bottom: 0px;" v-if="form.is_enable === 1">Module lock is enabled, and protecting your shop.</p>
                                <p style="margin-bottom: 0px;color: red;" v-else>Module lock is disabled, and is not protecting your shop.</p>
                            </div>
                            <div class="Polaris-Stack__Item">
                                <button class="Polaris-Button app-disable-btn" @click="chng_app_status('disable')" v-if="form.is_enable === 1">
                                    <span class="Polaris-Button__Content">
                                        <span class="Polaris-Button__Text">Disable module lock</span>
                                    </span>
                                </button>
                                <button class="Polaris-Button Polaris-Button--primary" @click="changeStatus(1)" v-else>
                                    <span class="Polaris-Button__Content">
                                        <span class="Polaris-Button__Text">Enable module lock</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<!--            content section-->
            <div class="module-header-divide d-flex setting-section">
                <div class="w-30 section-heading" style="padding: 20px 0px;">
                    Content
                </div>
                <div class="w-70 Polaris-Card">
                    <div class="Polaris-Card__Section">
                    <div class="w-30 section-heading">
                        Lock messages
                    </div>
<!--                        lock msg content-->
                        <div class="" style="margin: 20px 0px;">
                            <div class="Polaris-Labelled__LabelWrapper">
                                <div class="Polaris-Label"><label id="PolarisTextField2Label" for="guestMsgText" class="Polaris-Label__Text">Guest message content</label></div>
                            </div>
                            <div class="Polaris-Connected">
                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                        <textarea id="guestMsgText" v-model="form.guest_user_content" class="Polaris-TextField__Input" >{{form.guest_user_content}}</textarea>
                                        <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

<!--                        access denied content-->
                        <div class="" style="margin: 20px 0px;">
                            <div class="Polaris-Labelled__LabelWrapper">
                                <div class="Polaris-Label"><label id="accessDenied" for="accessDeniedMsgText" class="Polaris-Label__Text">Access denied content</label></div>
                            </div>
                            <div class="Polaris-Connected">
                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                        <textarea id="accessDeniedMsgText" class="Polaris-TextField__Input" v-model="form.access_denied_content">{{form.access_denied_content}}</textarea>
                                        <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {Button, ContextualSaveBar, Modal} from "@shopify/app-bridge/actions";
    import helper from '../../helper';

    export default {
        data() {
            return {
                form: {},
                temp_prisine: '',
                watch: false,
            }
        },
        methods: {
            async getSettings() {
                let base = this;
                let url = 'settings';
                helper.startLoading();
                await axios.get(url)
                    .then(res => {
                        base.form = res.data.data;
                        base.temp_prisine = JSON.stringify(base.form);
                        base.watch = true;
                    })
                    .catch(err => {
                        console.log(err);
                    })
                    .finally(res => {
                        helper.stopLoading();
                    });
            },
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
                    base.getSettings();
                    contextualSaveBar.dispatch(ContextualSaveBar.Action.HIDE);
                });
                contextualSaveBar.subscribe(ContextualSaveBar.Action.SAVE, function () {
                    contextualSaveBar.set({saveAction: {loading: true}, discardAction: {disabled: true}});
                    base.sendForm();
                });
            },
            chng_app_status(){
                let base = this;
                let title = 'Disable Module lock';
                let msg = 'You have 3 active locks – are you sure you want to disable Module lock?';
                let okBtnText = 'Yes, disable it';
                let cancelBtnText = 'No, keep it active';

                const okButton = Button.create(shopify_app_bridge, {label: okBtnText, style: Button.Style.Danger });
                const cancelButton = Button.create(shopify_app_bridge, {label: cancelBtnText});

                const modalOptions = {
                    title: title,
                    message: msg,
                    footer: {
                        buttons: {
                            primary: okButton,
                            secondary: [cancelButton],
                        },
                    },
                };
                const myModal = Modal.create(shopify_app_bridge, modalOptions);
                myModal.dispatch(Modal.Action.OPEN);
                okButton.subscribe(Button.Action.CLICK, data => {
                    myModal.dispatch(Modal.Action.CLOSE);
                    base.changeStatus(0);
                });
                cancelButton.subscribe(Button.Action.CLICK, data => {
                    myModal.dispatch(Modal.Action.CLOSE);
                });
            },
            async changeStatus(status){
                let base = this;
                let url = 'app-status?status=' + status;
                helper.startLoading();
                await axios.get(url)
                    .then(res => {
                        helper.successToast(res.data.data);
                        base.getSettings();
                    })
                    .catch(err => {
                        console.log(err);
                    })
                    .finally(res => {
                        helper.stopLoading();
                    });
            },
            async sendForm(){
                let base = this;
                let url = 'settings';
                helper.startLoading();
                await axios({
                    url: url,
                    data: {
                        'data' : base.form,
                    },
                    method: 'post',
                }).then(res => {
                        helper.successToast(res.data.data)
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
        created() {
            this.getSettings();
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
