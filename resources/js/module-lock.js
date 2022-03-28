const host = process.env.MIX_APP_URL;
const apiEndPoint = host + '/api';

window.Vue = require('vue');
import axios from "axios";
if (typeof window.jQuery == 'undefined') {
    var script = document.createElement('script');
    script.type = "text/javascript";
    script.src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js";
    document.getElementsByTagName('head')[0].appendChild(script);
}

const app = new Vue({
    template: '<div></div>',
    data: {
        shopifyDomain: '',
        shop_data: {},
        main_content: '',
    },
    methods: {
        init() {
            let base = this;
            let url = window.location.href;
            if (url.includes("/")) {
                let params = this.getParams('module-lock');
                this.shopifyDomain = params['shop'];
            }
            this.shop_data = JSON.parse(document.getElementById('module-lock-data').innerHTML);
            this.template = this.shop_data.template;

            // this.main_content =  document.getElementsByTagName('main')[0].innerHTML;
            // document.getElementsByTagName('main')[0].innerHTML = ''
            this.checkLock();
        },
        async checkLock(){
            let base = this;
            let template = base.template;
            let is_logged_in = ml_customer;
            let resource = [];
            if( template === 'product' || template === 'collection') {
                resource = base.shop_data[`${template}`];
            }else if( template === 'page' ) {
                let url = window.location.href;
                resource = url. substring(url. lastIndexOf('/')+1);
            }

            let aPIEndPoint = `${apiEndPoint}/check-lock`;
            await axios({
                url: aPIEndPoint,
                data: {
                    'shop': base.shopifyDomain,
                    'resource': resource,
                    'template': template,
                    'is_logged_in': is_logged_in
                },
                method: 'post',
            }).then(res => {
                let data = res.data.data;
                if( data.is_hide ){
                    document.getElementsByTagName('main')[0].innerHTML = data.message;
                }else{
                    document.getElementsByTagName('main')[0].innerHTML =
                        main_content;
                }
            })
            .catch(err => {
                console.log(err);
            })
        },
        getParams(script_name) {
            // Find all script tags
            var scripts = document.getElementsByTagName("script");
            // Look through them trying to find ourselves
            for (var i = 0; i < scripts.length; i++) {
                if (scripts[i].src.indexOf("/" + script_name) > -1) {
                    // Get an array of key=value strings of params
                    var pa = scripts[i].src.split("?").pop().split("&");
                    // Split each key=value into array, the construct js object
                    var p = {};
                    for (var j = 0; j < pa.length; j++) {
                        var kv = pa[j].split("=");
                        p[kv[0]] = kv[1];
                    }
                    return p;
                }
            }

            // No scripts match

            return {};
        }
    },
    created() {
        this.init();
    },
});

Window.crawlapps_module_lock = {
    init: function () {
        app.$mount();
    },
};

window.onload = Window.crawlapps_module_lock.init();

