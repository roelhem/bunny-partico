<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                OAuth2 Tokens
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <PassportTokenManager />
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from "../../Layouts/AppLayout";
    import PassportTokenManager from "./PassportTokenManager";
    export default {
        props: [
            'myProp'
        ],
        computed: {
            myPropType() { return typeof this.myProp }
        },
        components: {PassportTokenManager, AppLayout}
    }
</script>

<style scoped>

</style>
