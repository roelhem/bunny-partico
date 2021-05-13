<template>
    <node-card :label="emailAddress.label"
               :remarks="emailAddress.remarks"
               editable
               :edit.sync="edit"
               @start-edit="resetEditForm"
    >
        <template #header>
            {{ emailAddress.email_address }}
        </template>
        <template #edit>
            <div>
                <label>
                    Label
                    <input v-model="editForm.label" class="form-input rounded-md shadow-sm" />
                </label>
            </div>
            <div>
                <label>
                    E-mailadres
                    <input v-model="editForm.email_address" class="form-input rounded-md shadow-sm" />
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
    import Button from "@/Jetstream/Button";

    export default {
        name: "EmailAddressCard",
        components: {Button, NodeCard},
        props: {
            emailAddress: {
                required: true,
            }
        },
        fragment: gql`
            fragment EmailAddressCard on EmailAddress {
                id
                label
                email_address
                remarks
            }
`,
        data() {
            return {
                edit: false,
                editForm: {
                    label: null,
                    email_address: null,
                    remarks: null,
                },
            }
        },
        methods: {
            clearEditForm() {
                this.editForm.label = null;
                this.editForm.email_address = null;
                this.editForm.remarks = null;
            },
            resetEditForm() {
                this.editForm.label = this.emailAddress.label;
                this.editForm.email_address = this.emailAddress.email_address;
                this.editForm.remarks = this.emailAddress.remarks;
            },
            submitEdit() {
                // Get the values
                const input = {
                    id: this.emailAddress.id,
                    label: this.editForm.label,
                    email_address: this.editForm.email_address,
                    remarks: this.editForm.remarks,
                };

                // Close the form
                this.edit = false;

                // Perform the mutation
                this.$apollo.mutate({
                    mutation: gql`mutation updateEmailAddressCard ($input: UpdateEmailAddressInput!) {
                        updateEmailAddress(input: $input) {
                            id
                            label
                            email_address
                            remarks
                        }
                    }`,
                    variables: {input},
                    update: (store, {data: {updateEmailAddress}}) => {
                        store.writeFragment({
                            id: `EmailAddress:${input.id}`,
                            fragment: this.$options.fragment,
                            data: updateEmailAddress,
                        })
                    },
                    optimisticResponse: {
                        __typename: 'Mutation',
                        updateEmailAddress: {
                            __typename: 'EmailAddress',
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
        }
    }
</script>

<style scoped>

</style>
