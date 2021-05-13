<template>
    <span :v-tooltip="countryName">
        <country-flag class="align-baseline"
                      :class="{
                        'ml-8': showName,
                        'mr-16': showName,
                      }"
                      :country="countryCode"
                      :size="size"
                      :rounded="rounded"
        />
        <span v-if="showName" :class="nameClass">{{ countryName }}</span>
    </span>
</template>

<script>
    import CountryFlag from 'vue-country-flag';
    import gql from 'graphql-tag';

    export default {
        name: "CountryDisplay",
        components: { CountryFlag },
        props: {
            country: {
                required: true,
                type: [String, Object],
            },
            nameClass: {
                default: () => ['text-gray-700'],
            },
            size: {
                type: String,
                default: 'normal',
            },
            rounded: {
                type: Boolean,
                default: false,
            },
            showName: {
                type: Boolean,
                default: false
            }
        },
        fragment: gql`
            fragment CountryDisplay on Country {
                code
                name
            }
`,
        computed: {
            countryCode() {
                if(typeof this.country === 'string') {
                    return this.country;
                } else {
                    return this.country.code;
                }
            },
            countryName() {
                if(typeof this.country === 'object') {
                    return this.country.name;
                } else {
                    return null;
                }
            }
        }
    }
</script>

<style scoped>

</style>
