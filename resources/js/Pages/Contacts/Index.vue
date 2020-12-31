<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Contacts
            </h2>
        </template>

        <div class="container mx-auto py-12">
            <div class="flex space-x-8">
                <contact-list class="flex-initial" @edge-selected="openContact" />
                <template v-if="selectedId">
                    <contact-info  class="flex-1" :node-id="this.selectedId" />
                </template>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from "@/Layouts/AppLayout";
    import ContactTableCard from "@/Components/Data/ContactTableCard";
    import ConnectionTable from "@/Components/Data/ConnectionTable";
    import ConnectionTableCard from "@/Components/Data/ConnectionTableCard";
    import ContactCard from "@/Components/Data/ContactCard";
    import ContactList from "@/Components/Data/ContactList";
    import ContactInfo from "@/Components/Data/ContactInfo";
    export default {
        name: "Index",
        components: {
            ContactInfo,
            ContactList, ContactCard, ConnectionTableCard, ConnectionTable, ContactTableCard, AppLayout},
        data() {
            return {
                selectedId: null
            }
        },
        methods: {
            openContact({node}) {
                this.selectedId = node.id;
            }
        }
    }
</script>

<style scoped>

</style>
