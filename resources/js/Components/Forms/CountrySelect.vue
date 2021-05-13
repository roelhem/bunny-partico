<template>
    <connection-select :query="$apollo.queries.countries"
                       v-bind="$attrs"
                       v-on="$listeners"
                       :connection="countries"
                       connection-field="countries"
                       select-on-tab
                       placeholder="Selecteer een land..."
                       label="name"
    >
        <template #option="country">
            <country-display :country="country" show-name size="small" name-class="text-gray-700 ml-2" />
        </template>
        <template #selected-option="country">
            <country-display :country="country" show-name size="small" name-class="text-gray-700 ml-1" />
        </template>
    </connection-select>
</template>

<script>
    import ConnectionSelect from "@/Components/Forms/ConnectionSelect";
    import gql from 'graphql-tag';
    import CountryDisplay from "@/Components/Display/CountryDisplay";

    export default {
        name: "CountrySelect",
        components: {CountryDisplay, ConnectionSelect},
        props: {
            pageSize: {
                type: Number,
                default: 100,
            }
        },
        data() {
            return {
                countries: null,
            }
        },
        apollo: {
            countries: {
                query: gql`query CountrySelect($first: Int, $after: String) {
                    countries(first: $first, after: $after) {
                        edges {
                            node {
                                code
                                name
                                ...CountryDisplay
                            }
                        }
                        ...ConnectionSelect
                    }
                }
                ${ConnectionSelect.fragment}
                ${CountryDisplay.fragment}`,
                variables() {
                    return {
                        first: this.pageSize,
                        after: null,
                    }
                }
            },
        }
    }
</script>

<style scoped>

</style>
