<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto">
                <postal-address-input v-model="address" />
                <pre class="text-orange-600">{{ address }}</pre>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto">
                <language-select v-model="language" />
                <pre>{{ language }}</pre>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto">
                <country-select v-model="country" />
                <pre>{{ country }}</pre>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto">
                <contact-select v-model="contact" />
                <pre>{{ contact }}</pre>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="space-y-1 space-x-1" v-for="color in colors">
                    <template v-for="light in [true, false]">
                        <template v-for="size in sizes">
                            <tag interactive :light="light" :color="color" :size="size">Test</tag>
                        </template>
                    </template>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="space-y-1 space-x-1">
                    <template v-for="color in colors">
                        <template v-for="light in [true, false]">
                            <tag interactive :light="light" :color="color">Mijn tag naam</tag>
                        </template>
                    </template>
                </div>

            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <welcome />
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import Welcome from '@/Jetstream/Welcome'
    import Tag, {sizes} from "@/Components/Tag";
    import {COLORS} from "@/utils/colors";
    import LanguageSelect from "@/Components/Forms/LanguageSelect";
    import CountrySelect from "@/Components/Forms/CountrySelect";
    import ContactSelect from "@/Components/Forms/ContactSelect";
    import PostalAddressInput from "@/Components/Forms/PostalAddressInput";

    export default {
        components: {
            PostalAddressInput,
            ContactSelect,
            LanguageSelect,
            CountrySelect,
            Tag,
            AppLayout,
            Welcome,
        },
        data() {
            return {
                address: null,
                language: null,
                country: null,
                group: null,
                contact: null,
            }
        },
        computed: {
            colors() {
                return COLORS;
            },
            sizes() {
                return sizes;
            }
        }
    }
</script>
