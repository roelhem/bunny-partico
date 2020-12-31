<template>
    <table>
        <thead>
            <tr>
                <th>Cursor</th>
                <th>ID</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="connection.pageInfo ? connection.pageInfo.hasPreviousPage : false">
                <td>Meer...</td>
            </tr>
            <tr v-for="{node, cursor} in connection.edges">
                <td>{{ cursor }}</td>
                <td><pre>{{ node.id }}</pre></td>
            </tr>
            <tr v-if="connection.pageInfo ? connection.pageInfo.hasNextPage : false">
                <td>Meer...</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
    import gql from 'graphql-tag';

    export default {
        name: "ConnectionTable",
        fragment: gql`
            fragment ConnectionTable on Connection {
                pageInfo {
                    hasNextPage
                    hasPreviousPage
                },
                edges {
                    node {
                        id
                    }
                    cursor
                }
            }
        `,
        props: {
            connection: {
                type: Object,
                default: () => ({
                    pageInfo: { hasNextPage: false },
                    edges: [],
                }),
            },
        },
    }
</script>

<style scoped>

</style>
