<template>
    <div class="module-header-divide">
        <div class="d-flex module-heading">
            <div v-for="item in items" class="mr-10">
                <router-link :to="{name :item.name}" tag="button" class="Polaris-Button">
                <span class="Polaris-Button__Content">
                    <span class="Polaris-Button__Text">{{item.title}}</span>
                </span>
                </router-link>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                items: [],
            }
        },
        created() {
            let base = this;
            this.$router.options.routes.forEach(route => {
                if (route.meta.ignoreInMenu == 0) {
                    this.items.push({
                        title: route.meta.title,
                        name: route.name,
                        path: route.path,
                    })
                }
            })
        },
        methods: {}
    };
</script>
