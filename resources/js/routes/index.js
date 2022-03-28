import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

const routes = [
    // {
    //     path:'/dashboard',
    //     component: require('../components/pages/Dashboard').default,
    //     name:'dashboard',
    //     meta: {
    //         title: 'Dashboard',
    //         ignoreInMenu: 0,
    //         displayRight: 0,
    //         dafaultActiveClass: '',
    //     },
    // },
    {
        path:'/',
        component: require('../components/pages/LockList').default,
        name:'locks',
        meta: {
            title: 'Locks',
            ignoreInMenu: 0,
            displayRight: 0,
            dafaultActiveClass: '',
        },
    },
    {
        path:'/setting',
        component: require('../components/pages/Setting').default,
        name:'setting',
        meta: {
            title: 'Setting',
            ignoreInMenu: 0,
            displayRight: 0,
            dafaultActiveClass: '',
        },
    },
    {
        path:'/edit-lock',
        component: require('../components/pages/editLock').default,
        name:'edit-lock',
        meta: {
            title: 'Edit Lock',
            ignoreInMenu: 1,
            displayRight: 0,
            dafaultActiveClass: '',
        },
    },
];


// This callback runs before every route change, including on page load.


const router = new VueRouter({
    mode:'history',
    routes,
    scrollBehavior() {
        return {
            x: 0,
            y: 0,
        };
    },

});

export default router;
