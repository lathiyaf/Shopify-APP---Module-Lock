<template>
    <div class="module-content">
        <div class="d-flex justify-content-between Polaris-Card__Header Polaris-Card__Header-meta">
            <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                <h2 class="Polaris-Heading">Locks</h2>
            </div>
            <div v-if="allLocks.length > 0"><button @click="openModal()" type="button" class="Polaris-Button Polaris-Button--primary Polaris-Button--sizeLarge"><span class="Polaris-Button__Content"><span><span class="Polaris-Page__ActionContent">
                + Add lock </span></span></span></button></div>
        </div>
        <div>
            <ul role="tablist" class="Polaris-Tabs">
                <li role="presentation" class="Polaris-Tabs__TabContainer">
                    <button class="Polaris-Tabs__Tab" v-bind:class="{'Polaris-Tabs__Tab--selected' : ( lockList === 'all' )}"  @click="filterLock('all')">
                        <span class="Polaris-Tabs__Title">All({{allLocks.length}})</span>
                    </button>
                    <button class="Polaris-Tabs__Tab " @click="filterLock('disabled')" v-bind:class="{'Polaris-Tabs__Tab--selected' : ( lockList === 'disabled' )}">
                        <span class="Polaris-Tabs__Title">Disabled({{disablelocks}})</span>
                    </button>
                </li>
            </ul>
        </div>
        <div v-if="filteredLocks.length > 0 && checkedIDs.length > 0" style="padding: 20px 0px 0px 20px;" class="">

            <!--                        Start multidelete checkbox-->
            <label class="Polaris-Choice" for="multicheck"><span
                class="Polaris-Choice__Control"><span
                class="Polaris-Checkbox"><input id="multicheck" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="0" :checked="isMultiCheck" @click="checkAll"><span
                class="Polaris-Checkbox__Backdrop"></span><span
                class="Polaris-Checkbox__Icon"><span
                class="Polaris-Icon"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                  <path
                                                      d="M8.315 13.859l-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.437.437 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                                                </svg></span></span></span></span><span
                class="Polaris-Choice__Label"></span>
            </label>
            <span v-if="checkedIDs.length > 0" style="font-weight: 500;">{{checkedIDs.length}} lock selected.</span>
            <div v-if="checkedIDs.length > 0"  style="position: relative;display: inline-block;">
                <div>
                    <button type="button" @click="showOption = !showOption" class="Polaris-Button lockFilter" tabindex="0" aria-controls="Polarispopover2" aria-owns="Polarispopover2" aria-expanded="true"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">More actions</span><span class="Polaris-Button__Icon">
                        <div class="Polaris-Button__DisclosureIcon"><span class="Polaris-Icon"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                              <path d="M5 8l5 5 5-5z" fill-rule="evenodd"></path>
                            </svg></span></div>
                      </span></span></button>
                </div>


                <div class="Polaris-PositionedOverlay Polaris-Popover__PopoverOverlay Polaris-Popover__PopoverOverlay--open" style="top: 36px; left: 0;right: 0;" v-if="showOption">
                    <div class="Polaris-Popover" data-polaris-overlay="true" style="margin: 0;">
                        <div class="Polaris-Popover__FocusTracker" tabindex="0"></div>
                        <div class="Polaris-Popover__Wrapper">
                            <div id="Polarispopover2" tabindex="-1" class="Polaris-Popover__Content">
                                <div class="Polaris-Popover__Pane Polaris-Scrollable Polaris-Scrollable--vertical" data-polaris-scrollable="true">
                                    <div class="Polaris-ActionList" style="padding: 0;">
                                        <div class="Polaris-ActionList__Section--withoutTitle">
                                            <ul class="Polaris-ActionList__Actions">
                                                <li>
                                                    <button type="button" class="Polaris-ActionList__Item nh" @click="lockStatus('enable')">
                                                        <div class="Polaris-ActionList__Content">Enable Lock</div>
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="Polaris-ActionList__Item nh" @click="lockStatus('disable')">
                                                        <div class="Polaris-ActionList__Content">Disable Lock</div>
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="Polaris-ActionList__Item nh" @click="confirmDelete()">
                                                        <div class="Polaris-ActionList__Content">Delete</div>
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="Polaris-Popover__FocusTracker" tabindex="0"></div>
                    </div>
                </div>
            </div>
            <!--                        End multidelete checkbox-->
        </div>
        <div v-if="filteredLocks.length > 0">
            <div class="Polaris-Card" style="margin-top: 20px;">
                <div class="Polaris-Card__Section">
                    <div v-if="filteredLocks.length > 0" class="">
                        <div class="Polaris-ResourceList app-javascript-common-ResourceList-ResourceList__list--3KUBK">
                            <div class="lock-list">
                                <table class="w-100">
                                    <thead></thead>
                                    <tbody>
                                    <tr class="Polaris-ResourceItem module-header-divide" v-for="(item, index) in filteredLocks" :key="index">
                                        <td style="width: 18px;">
                                            <label class="Polaris-Choice" :for="`chkbox`+ index">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="" :id="`chkbox`+ index" :checked="item.is_checked" @click="checkSingle(item.id,index)">
                                                <span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
              <path d="M8.315 13.859l-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.437.437 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
            </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                            </label>
                                        </td>

                                        <router-link class="" :to="{name :'edit-lock', params: {item: item.id }}" tag="td" style="width: 7%;cursor: pointer;">
                                            <div class="d-flex align-center">
                                                        <span
                                                            class="mr-10 Polaris-Avatar Polaris-Avatar--styleSix Polaris-Avatar--sizeMedium Polaris-Avatar--hasImage border-radius-0 lock-list-image">
                                                            <img :src=item.image alt="" role="presentation" class="Polaris-Avatar__Image border-radius-0"></span>
                                            </div>
                                        </router-link>
                                        <router-link class="" :to="{name :'edit-lock', params: {item: item.id }}" tag="td"  style="cursor: pointer;">
                                            <p class="mb-0" style="font-weight: 700;">{{item.title}}</p>
                                            <p class="mb-0" style="color: #637381;">{{item.subtitle}}</p>
                                            <p class="mb-0" v-if="item.is_sh_deleted == 1" style="color: #bf0711;">This lock's {{item.resource}} no longer exists.</p>
                                            <p class="mb-0" v-else-if="item.is_enable == 0" style="color: #c05717;">This lock is disabled.</p>
                                            <p class="mb-0" v-else-if="item.key.length === 0" style="color: #c05717;">This lock has no keys – nobody can open it!</p>
                                                <div class="mb-10" v-else v-for="(ikey, index) in item.key" style="">
                                                    <div v-if="ikey.length > 0" v-for="(subitem, sindex) in ikey">
                                                        <div v-if="sindex == 0" class="d-flex justify-content-between">
                                                            <span><span v-if="index != 0">Or, </span>Permit if the customer {{subitem}} <span v-if="ikey.length == 1" >--and...</span></span>
                                                        </div>
                                                        <div v-if="sindex != 0" class="d-flex justify-content-between"><span>
                                            ... and
                                            the customer {{subitem}}
                                            <span v-if="ikey.length - 1 == sindex">--and...</span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                        </router-link>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="lockList == 'all'">
            <div class="Polaris-EmptyState Polaris-EmptyState--withinPage">
                <div class="Polaris-EmptyState__Section">
                    <div class="Polaris-EmptyState__DetailsContainer">
                        <div class="Polaris-EmptyState__Details">
                            <div class="Polaris-TextContainer">
                                <p class="Polaris-DisplayText Polaris-DisplayText--sizeMedium">Protect your shop</p>
                                <div class="Polaris-EmptyState__Content">
                                    <p>Use locks and keys to manage access.</p>
                                </div>
                            </div>
                            <div class="Polaris-EmptyState__Actions">
                                <div class="Polaris-Stack Polaris-Stack--alignmentCenter">
                                    <div class="Polaris-Stack__Item">
                                        <button type="button"
                                                class="Polaris-Button Polaris-Button--primary Polaris-Button--sizeLarge"
                                                @click="openModal()"><span class="Polaris-Button__Content"><span
                                            class="Polaris-Button__Text"> + Add lock</span></span></button>
                                    </div>
                                    <div class="Polaris-Stack__Item"><a class="Polaris-Button Polaris-Button--plain" href="https://help.shopify.com" data-polaris-unstyled="true"><span
                                        class="Polaris-Button__Content"><span
                                        class="Polaris-Button__Text">Learn more</span></span></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="Polaris-EmptyState__ImageContainer"><img
                        src="https://cdn.shopify.com/s/files/1/0757/9955/files/empty-state.svg" role="presentation"
                        alt="" class="Polaris-EmptyState__Image"></div>
                </div>
            </div>
        </div>
        <div v-else-if="lockList == 'disabled'">
            <div class="Polaris-Card" style="margin-top: 20px;">
                <div class="Polaris-Card__Section">
                    <div>Nothing to see here. :)</div>
                </div>
            </div>
        </div>
        <add-lock-model :show-model.sync="showModel" ref="add_lock" @update="closeModel"></add-lock-model>
    </div>
</template>

<script>
    import AddLockModel from "./AddLockModel";
    import helper from '../../helper';
    import {Button, Modal} from "@shopify/app-bridge/actions";

    export default {
        components: {
            AddLockModel,
        },
        data() {
            return {
                showOption: false,
                isMultiCheck: false,
                checkedIDs: [],
                filteredLocks: [],
                allLocks: [],
                showModel: false,
                disablelocks: 0,
                lockList: 'all',
            }
        },
        methods: {
            openModal(){
                this.$refs.add_lock.modalInit(this.allLocks);
                this.showModel=true;
            },
            checkSingle(id, index){
                let base = this;
                var i = base.checkedIDs.indexOf(id);
                if(i !== -1){
                    base.checkedIDs.splice(i, 1);
                } else{
                    base.checkedIDs.push(id);
                }
                base.isMultiCheck = false;
                base.filteredLocks[index].is_checked = !base.filteredLocks[index].is_checked;

                // base.isMultiCheck = base.checkedIDs.length > 0;
            },
            confirmDelete(){
                let base = this;
                const okButton = Button.create(shopify_app_bridge, {label: 'Yes, Delete it!', style: Button.Style.Danger});
                const cancelButton = Button.create(shopify_app_bridge, {label: 'Cancel'});
                const modalOptions = {
                    title: 'Delete Lock!!',
                    message: 'Are you sure you want to delete these locks?',
                    footer: {
                        buttons: {
                            primary: okButton,
                            secondary: [cancelButton],
                        },
                    },
                };
                const myModal = Modal.create(shopify_app_bridge, modalOptions);
                myModal.dispatch(Modal.Action.OPEN);
                cancelButton.subscribe(Button.Action.CLICK, data => {
                    myModal.dispatch(Modal.Action.CLOSE);
                });
                okButton.subscribe(Button.Action.CLICK, data => {
                    myModal.dispatch(Modal.Action.CLOSE);
                    base.lockStatus('delete');
                });
            },
            checkAll(){
                let base = this;
                let lcks = this.filteredLocks;
                base.checkedIDs = [];
                lcks.forEach(function ( element ){
                     base.checkedIDs.push(element.id);
                     element.is_checked = !base.isMultiCheck;
                    if( base.isMultiCheck === true ){
                        base.checkedIDs = [];
                    }
                });
                base.isMultiCheck = base.checkedIDs.length > 0;
            },
            closeModel() {
                this.showModel = false;
                this.getLocks();
            },
            filterLock(type){
                let base = this;
                this.lockList = type;
                if(type === 'all') {
                    this.filteredLocks = this.allLocks;
                }else{
                    let lcks = this.allLocks;
                    this.filteredLocks = [];
                    lcks.forEach(function ( element, index ){
                        if( element.is_enable == 0 ){
                            base.filteredLocks.push(element);
                        }
                    });
                }
            },
            async getLocks() {
                let base = this;
                let url = 'lock';
                helper.startLoading();
                await axios.get(url)
                    .then(res => {
                        base.allLocks = res.data.data.lock;
                        base.filteredLocks = res.data.data.lock;
                        base.disablelocks = res.data.data.disableLock;
                    })
                    .catch(err => {
                        console.log(err);
                    })
                    .finally(res => {
                        helper.stopLoading();
                    });
            },
            documentClick(e){
                let base = this;
                let filter_selector = $('.lockFilter');
                if (!filter_selector.is(e.target) && filter_selector.has(e.target).length === 0) {
                    base.showOption=false; // problem is this value false for first match dropdown only.
                }
            },
            async lockStatus(action){
                let base = this;
                let url = 'lock-status';
                helper.startLoading();
                await axios({
                    url: url,
                    data: {
                        'action' : action,
                        'ids': base.checkedIDs,
                    },
                    method: 'post',
                }).then(res => {
                    helper.successToast(res.data.data);
                    base.getLocks();
                })
                    .catch(err => {
                        console.log(err);
                    })
                    .finally(res => {
                        helper.stopLoading();
                    });
            },
        },
        created() {
            this.getLocks();
            document.onclick = this.documentClick;
        },
    }
</script>
