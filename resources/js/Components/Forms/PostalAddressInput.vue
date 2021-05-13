<template>
    <div class="rounded-md shadow-md">
        <template v-if="format">
            <div class="flex" v-for="(line, lineIndex) in format.format">
                <template v-for="(field, rowIndex) in line">
                    <label class="flex w-full">
                        <span v-if="getFieldPrefix(field)"
                              class="text-gray-500 form-input pr-0 rounded-r-none border-r-0 rounded-b-none border-b-0"
                              :class="{
                                   'rounded-t-none border-t-0': lineIndex !== 0,
                                   'rounded-l-none border-l-0': rowIndex !== 0,
                               }"
                        >{{getFieldPrefix(field)}}</span>
                        <input :name="`${name}[${getFieldName(field)}]`"
                               :placeholder="getFieldPlaceholder(field)"
                               class="form-input rounded-md w-full rounded-b-none border-b-0"
                               :class="{
                                   'pl-1 rounded-l-none border-l-0': !!getFieldPrefix(field),
                                   'rounded-t-none border-t-0': lineIndex !== 0,
                                   'rounded-l-none border-l-0': rowIndex !== 0,
                                   'rounded-r-none border-r-0': rowIndex !== line.length -1,
                               }"
                               v-model="$data[getFieldName(field)]"
                               @input="updateValue"
                        />
                    </label>
                </template>
            </div>
        </template>

        <!-- Country select -->
        <connection-select :query="$apollo.queries.addressFormats"
                           :connection="addressFormats"
                           connection-field="addressFormats"
                           select-on-tab
                           class="form-input rounded-md"
                           :class="{
                               'rounded-t-none border-t-0': !!format,
                           }"
                           v-model="format"
                           @input="updateAddressFormat"
                           placeholder="Kies het land voor dit adres..."
                           label="id"
        >
            <template #option="addressFormat">
                <country-display :country="addressFormat.country" show-name size="small" name-class="text-gray-700 ml-2" />
            </template>
            <template #selected-option="addressFormat">
                <country-display :country="addressFormat.country" show-name size="small" name-class="text-gray-700 ml-1" />
            </template>
        </connection-select>
    </div>
</template>

<script>
    import Input from "@/Jetstream/Input";
    import gql from 'graphql-tag';
    import CountryDisplay from "@/Components/Display/CountryDisplay";
    import ConnectionSelect from "@/Components/Forms/ConnectionSelect";

    const fragment = gql`fragment PostalAddressInput on AddressFormat {
            id
            format
            administrative_area_type
            locality_type
            dependent_locality_type
            postal_code_type
            postal_code_prefix
            postal_code_pattern
            required_fields
            used_fields
            country {
                ...CountryDisplay
            }
        }
        ${CountryDisplay.fragment}`;

    export default {
        name: "PostalAddressInput",
        components: {Input, CountryDisplay, ConnectionSelect},
        props: {
            name: {
                type: String,
                default: "postalAddressInput",
            },
            value: {
                type: Object,
                default:() => ({}),
            },
            pageSize: {
                type: Number,
                default: 500,
            },
            addressFormat: {
                type: Object,
                default: null,
            }
        },
        data() {
            return {
                addressFormats: null,
                format: this.addressFormat,
                address_line_1: null,
                address_line_2: null,
                administrative_area: null,
                locality: null,
                dependent_locality: null,
                postal_code: null,
                sorting_code: null,
                organisation: null,
            };
        },
        apollo: {
            addressFormats: {
                query: gql`query AddressFormatSelect($first: Int, $after: String) {
                    addressFormats(first: $first, after: $after) {
                        edges {
                            node {
                                ...PostalAddressInput
                            }
                        }
                        ...ConnectionSelect
                    }
                }
                ${fragment}
                ${ConnectionSelect.fragment}`,
                variables() {
                    return {
                        first: this.pageSize,
                        after: null,
                    }
                }
            },
        },
        fragment,
        methods: {
            getFieldName(enumValue) {
                return enumValue.toLowerCase(enumValue);
            },
            getFieldPlaceholder(enumValue) {
                if(enumValue === "ADMINISTRATIVE_AREA" && this.format.administrative_area_type) {
                    return this.format.administrative_area_type;
                }

                if(enumValue === "LOCALITY" && this.format.locality_type) {
                    return this.format.locality_type;
                }

                if(enumValue === "DEPENDENT_LOCALITY" && this.format.dependent_locality_type) {
                    return this.format.dependent_locality_type;
                }

                if(enumValue === "POSTAL_CODE" && this.format.postal_code_type) {
                    return this.format.postal_code_type;
                }

                return this.getFieldName(enumValue);
            },
            getFieldPrefix(enumValue) {
                if(enumValue === "POSTAL_CODE" && this.format.postal_code_prefix) {
                    return this.format.postal_code_prefix;
                }

                return null;
            },
            fillFromValue(value) {
                if(value === undefined) {
                    value = this.value
                }

                value = this.filterUsedFields(value);

                Object.entries(value).forEach(([key, x]) => {
                    this[key] = x || null;
                });
            },
            filterUsedFields(input) {
                if(!input) {
                    return {};
                }
                return Object.fromEntries(
                    Object.entries(input).filter(([key]) => this.usedFields.includes(key))
                )
            },
            updateAddressFormat() {
                this.updateValue();
                this.$emit('update:address-format', this.format);
            },
            updateValue() {
                this.$emit('input', this.outputValue)
            }
        },
        computed: {
            usedFields() {
                if(this.format) {
                    return (this.format.used_fields || []).map(v => this.getFieldName(v));
                } else {
                    return [
                        'organisation',
                        'address_line_1',
                        'address_line_2',
                        'locality',
                        'administrative_area',
                        'dependent_locality',
                        'postal_code',
                        'sorting_code'
                    ];
                }
            },
            outputValue() {
                // Null when no format was set
                if(!this.format) {
                    return null
                }

                // Get the format.
                return {
                    ...this.filterUsedFields({
                        organisation: this.organisation || null,
                        address_line_1: this.address_line_1 || null,
                        address_line_2: this.address_line_2 || null,
                        locality: this.locality || null,
                        administrative_area: this.administrative_area || null,
                        dependent_locality: this.dependent_locality || null,
                        postal_code: this.postal_code || null,
                        sorting_code: this.sorting_code || null,
                    }),
                    country_code: this.format && this.format.country ? this.format.country.code : null
                };
            }
        },
        watch: {
            value(newValue) {
                this.fillFromValue(newValue);
            },
            addressFormat(newValue) {
                this.format = newValue;
            }
        },
        mounted() {
            return this.fillFromValue();
        }
    }
</script>

<style scoped>

</style>
