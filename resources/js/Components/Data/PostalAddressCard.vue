<template>
    <node-card :label="postalAddress.label" :remarks="postalAddress.remarks" editable>
        <template #header>
            {{ postalAddress.address_line_1 }}
            <span class="text-gray-600 ml-4">{{ postalAddress.locality }}</span>
        </template>
        <data-table>
            <data-table-row label="Adres">
                <div class="data-table-address" v-html="postalAddress.format" />
            </data-table-row>
            <data-table-row label="Land">
                <country-display :country="postalAddress.country" show-name rounded size="small" />
            </data-table-row>
        </data-table>
    </node-card>
</template>

<script>
    import gql from 'graphql-tag';

    import NodeCard from "@/Components/Cards/NodeCard";
    import CountryDisplay from "@/Components/Display/CountryDisplay";
    import DataTable from "@/Components/Display/DataTable";
    import DataTableRow from "@/Components/Display/DataTableRow";
    export default {
        name: "PostalAddressCard",
        components: {DataTableRow, DataTable, CountryDisplay, NodeCard},
        props: {
            postalAddress: {
                required: true
            }
        },
        fragment: gql`
            fragment PostalAddressCard on PostalAddress {
                id
                label
                address_line_1
                locality
                country {
                    ...CountryDisplay
                }
                format(html: true)
                remarks
            }
            ${CountryDisplay.fragment}
`
    }
</script>

<style>
    .data-table-address .given-name {
        display: none;
    }

    .data-table-address .family-name {
        display: none;
    }

    .data-table-address .family-name + br {
        display: none;
    }

    .data-table-address .country {
        display: none;
    }
</style>
