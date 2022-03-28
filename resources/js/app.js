require('./bootstrap');
window.Vue = require('vue');

import App from './components/layouts';
//import './components'
import router from './routes';
import helper from './helper';
const plugin = {
    install() {
        Vue.prototype.$helpers = helper
    }
}
Vue.use(plugin);
const app = new Vue({
    el: '#app',
    router,
    render: h => h(App),
});
