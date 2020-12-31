import gql from "graphql-tag";
<template>
    <node-card :label="phoneNumber.label" :remarks="phoneNumber.remarks" editable>
        <template #header>
            {{ phoneNumber.number }}
        </template>

        <data-table>
            <data-table-row label="Type">{{ phoneNumber.number_type }}</data-table-row>
            <data-table-row label="Locatie">
                {{ phoneNumber.location }}
                <span class="text-gray-400">-</span>
                <country-display show-name :country="phoneNumber.country" size="small" />
            </data-table-row>
        </data-table>
    </node-card>
</template>

<script>
    import gql from 'graphql-tag';
    import NodeCard from "@/Components/Cards/NodeCard";
    import DataTable from "@/Components/Display/DataTable";
    import DataTableRow from "@/Components/Display/DataTableRow";
    import CountryDisplay from "@/Components/Display/CountryDisplay";
    export default {
        name: "PhoneNumberCard",
        components: {CountryDisplay, DataTableRow, DataTable, NodeCard},
        props: {
            phoneNumber: {
                type: Object,
                default() {
                    return {};
                }
            }
        },
        fragment: gql`
            fragment PhoneNumberCard on PhoneNumber {
                id
                label
                remarks
                country {
                    ...CountryDisplay
                }
                location
                number_type
                number(format: FOR_MOBILE)
            }
            ${CountryDisplay.fragment}
`
    }
</script>

<style scoped>

</style>
