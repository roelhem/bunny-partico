<template>
    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
        <table>
            <thead>
            <tr>
                <th>Name</th>
            </tr>
            </thead>
            <tbody>
            <template v-if="contacts && contacts.edges">
                <tr v-for="edge in contacts.edges">
                    <td>
                        {{edge.node.name}}
                        <span v-if="edge.node.nickname" class="text-gray-500">({{edge.node.nickname}})</span>
                    </td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
</template>

<script>
    import gql from 'graphql-tag';
    import ContactTableRow from "@/Components/Data/ContactTableRow";
    import ConnectionTable from "@/Components/Data/ConnectionTable";
    import ConnectionTableCard from "@/Components/Data/ConnectionTableCard";

    export default {
        name: "ContactTableCard",
        components: {ConnectionTableCard, ContactTableRow, ConnectionTable},
        data() {
            return {
                contacts: {
                    edges: []
                }
            };
        },
        apollo: {
            contacts: gql`
            query ContactTableCard {
                contacts {
                    edges {
                        node {
                            name
                            nickname
                        }
                    }
                }
            }
`
        },
    }
</script>

<style scoped>

</style>
