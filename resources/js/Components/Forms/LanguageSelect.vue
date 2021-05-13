<template>
    <connection-select :query="$apollo.queries.languages"
                       v-bind="$attrs"
                       v-on="$listeners"
                       :connection="languages"
                       connection-field="languages"
                       label="name"
                       select-on-tab
                       placeholder="Selecteer een taal..."
    >
    </connection-select>
</template>

<script>
    import gql from 'graphql-tag';
    import ConnectionSelect from "@/Components/Forms/ConnectionSelect";

    export default {
        name: "LanguageSelect",
        components: {ConnectionSelect},
        props: {
            pageSize: {
                type: Number,
                default: 100,
            }
        },
        data() {
            return {
                languages: null,
            };
        },
        apollo: {
            languages: {
                query: gql`query LanguageSelect($first: Int, $after: String) {
                    languages(first: $first, after: $after) {
                        edges {
                            node {
                                id
                                code
                                name
                            }
                        },
                        pageInfo {
                            hasNextPage
                            endCursor
                        }
                    }
                }`,
                variables() {
                    return {
                        first: this.pageSize,
                        after: null,
                    };
                }
            }
        },
    }
</script>

<style scoped>

</style>
