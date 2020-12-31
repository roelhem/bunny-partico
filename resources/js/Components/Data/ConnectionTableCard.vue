<template>
    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
        <div>Connection Table Card</div>

        <connection-table :connection="connection" />
    </div>
</template>

<script>
    import ConnectionTable from "@/Components/Data/ConnectionTable";
    import gql from 'graphql-tag';

    export default {
        name: "ConnectionTableCard",
        components: {ConnectionTable},
        props: {
            name: {
                type: String,
            },
            node: {
                type: String,
            },
            connection: {
                type: Object,
            },
        },
        fragment: gql`
            fragment ConnectionTableCard on Connection {
                pageInfo {
                    count
                }
                ...ConnectionTable
            }
            ${ConnectionTable.fragment}
        `,
        computed: {
            nodeId() {
                if(this.node) {
                    return this.node;
                } else if (this.data && typeof this.data === 'object' && typeof this.data.id === 'string') {
                    return this.data.id;
                } else {
                    return null;
                }
            },
        }
    }
</script>

<style scoped>

</style>
