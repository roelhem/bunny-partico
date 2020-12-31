<template>
    <node-card>
        <template #header>
            Gebruiker
        </template>
    </node-card>
</template>

<script>
    import gql from 'graphql-tag';
    import NodeCard from "@/Components/Cards/NodeCard";

    export default {
        name: "AccountCard",
        components: {NodeCard},
        fragment: gql`
            fragment AccountCard on User {
                id
                name
                email
            }
`
    }
</script>

<style scoped>

</style>
