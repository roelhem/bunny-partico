<template>
    <node-card :label="postalAddress.label"
               :remarks="postalAddress.remarks"
               editable
               :edit.sync="edit"
               @start-edit="resetEditForm"
    >
        <template #header>
            {{ postalAddress.address_line_1 }}
            <span class="text-gray-600 ml-4">{{ postalAddress.locality }}</span>
        </template>


        <data-table>
            <data-table-row label="Adres">
                <div class="data-table-address" v-html="postalAddress.format" />
            </data-table-row>
            <data-table-row label="Land">
                <country-display :country="postalAddress.country" show-name rounded size="small" />
            </data-table-row>
        </data-table>


        <template #edit>
            <div>
                <label>
                    Label
                    <input v-model="editForm.label" class="form-input rounded-md shadow-sm" />
                </label>
            </div>
            <div>
                <label>
                    Adres
                    <postal-address-input v-model="editForm.address" :address-format="postalAddress.country.address_format" />
                </label>
            </div>
            <div>
                <label>
                    Remarks
                    <textarea v-model="editForm.remarks" class="form-input rounded-md shadow-sm" />
                </label>
            </div>
            <div>
                <button @click="submitEdit">Opslaan</button>
                <button @click="edit = false">Annuleren</button>
            </div>
        </template>
    </node-card>
</template>

<script>
    import gql from 'graphql-tag';

    import NodeCard from "@/Components/Cards/NodeCard";
    import CountryDisplay from "@/Components/Display/CountryDisplay";
    import DataTable from "@/Components/Display/DataTable";
    import DataTableRow from "@/Components/Display/DataTableRow";
    import PostalAddressInput from "@/Components/Forms/PostalAddressInput";
    export default {
        name: "PostalAddressCard",
        components: {PostalAddressInput, DataTableRow, DataTable, CountryDisplay, NodeCard},
        props: {
            postalAddress: {
                required: true
            }
        },
        fragment: gql`
            fragment PostalAddressCard on PostalAddress {
                id
                label
                address_line_1
                address_line_2
                organisation
                administrative_area
                locality
                dependent_locality
                postal_code
                sorting_code
                locality
                country {
                    code
                    ...CountryDisplay
                    address_format {
                        ...PostalAddressInput
                    }
                }
                format(html: true)
                remarks
            }
            ${PostalAddressInput.fragment}
`,
        data() {
            return {
                edit: false,
                editForm: {
                    label: null,
                    address: null,
                    remarks: null,
                },
            }
        },
        computed: {
            addressInput() {
                return {
                    address_line_1: this.postalAddress.address_line_1,
                    address_line_2: this.postalAddress.address_line_2,
                    organisation: this.postalAddress.organisation,
                    administrative_area: this.postalAddress.administrative_area,
                    locality: this.postalAddress.locality,
                    dependent_locality: this.postalAddress.dependent_locality,
                    postal_code: this.postalAddress.postal_code,
                    sorting_code: this.postalAddress.sorting_code,
                    country_code: this.postalAddress.country.code,
                }
            }
        },
        methods: {
            clearEditForm() {
                this.editForm.label = null;
                this.editForm.address = null;
                this.editForm.remarks = null;
            },
            resetEditForm() {
                this.editForm.label = this.postalAddress.label;
                this.editForm.address = this.addressInput;
                this.editForm.remarks = this.postalAddress.remarks;
            },
            submitEdit() {
                // Get the values
                const input = {
                    id: this.postalAddress.id,
                    label: this.editForm.label,
                    ...this.editForm.address,
                    remarks: this.editForm.remarks,
                };

                // Close the form
                this.edit = false;

                // Perform the mutation

                this.$apollo.mutate({
                    mutation: gql`mutation updatePostalAddressCard ($input: UpdatePostalAddressInput!) {
                        updatePostalAddress(input: $input) {
                            ...PostalAddressCard
                        }
                    }
                    ${this.$options.fragment}`,
                    variables: {input},
                    update: (store, {data: {updatePostalAddress}}) => {
                        store.writeFragment({
                            id: `PostalAddress:${input.id}`,
                            fragment: this.$options.fragment,
                            fragmentName: 'PostalAddressCard',
                            data: updatePostalAddress,
                        })
                    },
                    optimisticResponse: {
                        __typename: 'Mutation',
                        updatePostalAddress: {
                            ...this.postalAddress,
                            ...input,
                        }
                    }
                }).then(data => {
                    console.log('UPDATE SUCCESSFUL', data);
                })
                .catch(data => {
                    console.log('UPDATE FAILED', data);

                    this.edit = true;
                })
            }
        },
    }
</script>

<style>
    .data-table-address .given-name {
        display: none;
    }

    .data-table-address .family-name {
        display: none;
    }

    .data-table-address .family-name + br {
        display: none;
    }

    .data-table-address .country {
        display: none;
    }
</style>
