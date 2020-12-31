<template>
    <node-card :label="language.label" :remarks="language.remarks" editable deletable>
        <template #header>
            {{ language.language.name }}
            <span class="text-gray-400 ml-2">[{{ language.language.code }}]</span>
        </template>
    </node-card>
</template>

<script>
    import NodeCard from "@/Components/Cards/NodeCard";
    import gql from 'graphql-tag';

    export default {
        name: "ContactLanguageCard",
        components: {NodeCard},
        props: {
            language: {
                required: true,
            }
        },
        fragment: gql`
            fragment ContactLanguageCard on ContactLanguage {
                id
                label
                language {
                    code
                    name
                }
                remarks
            }
`
    }
</script>

<style scoped>

</style>
