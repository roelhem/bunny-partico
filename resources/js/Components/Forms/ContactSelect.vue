<template>
    <connection-select :query="$apollo.queries.contacts"
                       v-bind="$attrs"
                       v-on="$listeners"
                       :connection="contacts"
                       connection-field="contacts"
                       select-on-tab
                       placeholder="Selecteer een contact..."
                       label="name"
    >
        <template #option="{name, nickname}">
            {{ name }} <span v-if="nickname" class="opacity-50">({{nickname}})</span>
        </template>
    </connection-select>
</template>

<script>
    import gql from 'graphql-tag';
    import ConnectionSelect from "@/Components/Forms/ConnectionSelect";

    export default {
        name: "ContactSelect",
        components: {ConnectionSelect},
        data() {
            return {
                contacts: null,
            }
        },
        apollo: {
            contacts: {
                query: gql`query ConnectionSelect($after: String) {
                    contacts(first: 20, after: $after) {
                        edges {
                            node {
                                name
                                nickname
                            }
                        }
                        ...ConnectionSelect
                    }
                }
                ${ConnectionSelect.fragment}`
            }
        }
    }
</script>

<style scoped>

</style>
