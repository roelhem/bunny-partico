<template>
    <app-layout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Contacts
                <span class="mx-2 text-gray-300">|</span>
                <span class="text-gray-500">{{contact.name}}</span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1>{{contact.name}}
                    <span v-if="contact.nickname" class="text-gray-500">({{ contact.nickname }})</span>
                </h1>
                <date :date="contact.birth_date" />
            </div>
        </div>

        <div class="py-12">
            <pre>{{ contact }}</pre>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from "@/Layouts/AppLayout";
    import Date from "@/Components/Display/Date";
    export default {
        name: "Contact",
        props: {
            contact: Object
        },
        components: {Date, AppLayout}
    }
</script>

<style scoped>

</style>
