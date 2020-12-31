<template>
    <node-card :label="relation.label" :remarks="relation.remarks" editable>
        <template #header>
            <span v-if="relation.related">{{ relation.related.name }}</span>
            <span v-else class="text-gray-400">(Verborgen)</span>
        </template>
    </node-card>
</template>

<script>
    import NodeCard from "@/Components/Cards/NodeCard";
    import gql from 'graphql-tag';
    import DataTable from "@/Components/Display/DataTable";
    import DataTableRow from "@/Components/Display/DataTableRow";

    export default {
        name: "ContactRelationCard",
        components: {DataTableRow, DataTable, NodeCard},
        props: {
            relation: {
                type: [Object],
                required: false,
            }
        },
        fragment: gql`
            fragment ContactRelationCard on ContactRelation {
                id
                label
                remarks
                related {
                    name
                }
            }
`
    }
</script>

<style scoped>

</style>
