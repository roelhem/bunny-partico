<template>
    <div>
        <h1>{{node.name}}</h1>
    </div>
</template>

<script>
    import gql from 'graphql-tag'

    export default {
        name: "ContactCard",
        props: {
            contact: {
                type: [Object, String],
                default() { return null },
            },
        },
        data() {
            return {
                node: {}
            }
        },
        apollo: {
            node() {
                return {
                    query: gql`
                        query GetContact($id: ID!) {
                            node(id: $id) {
                                ... on Contact {
                                    id
                                    name
                                }
                            }
                        }
`,
                    variables: {
                        id: this.contact,
                    },

                    skip() {
                        return !this.contact;
                    }
                }
            }
        },
        fragment: gql`
            fragment ContactCard on Contact {
                id
                name
            }
`,

    }
</script>

<style scoped>

</style>
