<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

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

    export default {
        components: {
            Tag,
            AppLayout,
            Welcome,
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
