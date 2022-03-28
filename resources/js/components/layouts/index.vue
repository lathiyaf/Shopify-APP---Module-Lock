<template>
    <div>
        <menus></menus>
        <router-view></router-view>
    </div>
</template>

<script>
    import Menus from './menu';
    import helper from "../../helper";

    export default {
        components: {
            Menus,
        },
        data() {
            return {
            }
        },
        methods: {
        },
        created(){
        }
    };
</script>
