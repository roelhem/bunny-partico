import gql from "graphql-tag";
<template>
    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
        <ul v-if="contacts && contacts.edges">
            <li v-if="contacts.pageInfo.hasPreviousPage" class="p-4">
                meer...
            </li>
            <contact-list-item v-for="edge in contacts.edges"
                               :contact="edge.node"
                               :key="edge.node.id"
                               class="hover:bg-gray-100"
                               @click.native="selectEdge(edge)"
            />
            <li @click="getNextPage" v-if="contacts.pageInfo.hasNextPage" class="p-4 hover:bg-gray-100 text-center">
                meer...
            </li>
        </ul>
    </div>
</template>

<script>
    import gql from 'graphql-tag';
    import ContactListItem from "@/Components/Data/ContactListItem";

    export default {
        name: "ContactList",
        components: {ContactListItem},
        data() {
            return {
                contacts: {
                    pageInfo: {
                        hasNextPage: false,
                        hasPreviousPage: false,
                    },
                    edges: []
                }
            };
        },
        apollo: {
            contacts: {
                query: gql`
            query ContactList($nextCursor: String) {
                contacts(after: $nextCursor) {
                    pageInfo {
                        hasNextPage
                        hasPreviousPage
                        endCursor
                    }
                    edges {
                        node {
                            id
                            ...ContactListItem
                        }
                    }
                }
            }
            ${ContactListItem.fragment}
`,
                variables: {
                    nextCursor: null,
                }
            },
        },
        methods: {
            getNextPage () {
                this.$apollo.queries.contacts.fetchMore({
                    variables: {
                        nextCursor: this.contacts.pageInfo.endCursor,
                    },
                    updateQuery: (previousQueryResult, { fetchMoreResult }) => {
                        const newEdges = fetchMoreResult.contacts.edges;
                        const newPageInfo = fetchMoreResult.contacts.pageInfo;

                        return {
                            contacts: {
                                __typename: previousQueryResult.contacts.__typename,
                                pageInfo: {
                                    ...previousQueryResult.contacts.pageInfo,
                                    endCursor: newPageInfo.endCursor,
                                    hasNextPage: newPageInfo.hasNextPage,
                                },
                                edges: [...previousQueryResult.contacts.edges, ...newEdges],
                            }
                        }
                    }
                })
            },
            selectEdge (edge) {
                this.$emit('edge-selected', edge)
            },
        }
    }
</script>

<style scoped>

</style>
