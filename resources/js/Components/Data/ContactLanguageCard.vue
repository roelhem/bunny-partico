<template>
    <node-card :label="language.label"
               :remarks="language.remarks"
               editable
               :edit.sync="edit"
               @start-edit="resetEditForm"
    >
        <template #header>
            {{ language.language.name }}
            <span class="text-gray-400 ml-2">[{{ language.language.code }}]</span>
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
                    Taal
                    <language-select v-model="editForm.language" />
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
    import NodeCard from "@/Components/Cards/NodeCard";
    import gql from 'graphql-tag';
    import LanguageSelect from "@/Components/Forms/LanguageSelect";

    export default {
        name: "ContactLanguageCard",
        components: {LanguageSelect, NodeCard},
        props: {
            language: {
                required: true,
            }
        },
        data() {
            return {
                edit: false,
                editForm: {
                    label: null,
                    language: null,
                    remarks: null,
                },
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
`,
        methods: {
            clearEditForm() {
                this.editForm.label = null;
                this.editForm.language = null;
                this.editForm.remarks = null;
            },
            resetEditForm() {
                this.editForm.label = this.language.label;
                this.editForm.language = this.language.language;
                this.editForm.remarks = this.language.remarks;
            },
            submitEdit() {
                // Get the values
                const input = {
                    id: this.language.id,
                    label: this.editForm.label,
                    language_code: this.editForm.language.code,
                    remarks: this.editForm.remarks,
                };

                // Close the form
                this.edit = false;

                // Perform the mutation
                this.$apollo.mutate({
                    mutation: gql`mutation updateContactLanguageCard ($input: UpdateContactLanguageInput!) {
                        updateContactLanguage(input: $input) {
                            ...ContactLanguageCard
                        }
                    }
                    ${this.$options.fragment}`,
                    variables: {input},
                    update: (store, {data: {updateContactLanguage}}) => {
                        store.writeFragment({
                            id: `ContactLanguage:${input.id}`,
                            fragment: this.$options.fragment,
                            data: updateContactLanguage,
                        })
                    },
                    optimisticResponse: {
                        __typename: 'Mutation',
                        updateContactLanguage: {
                            __typename: 'ContactLanguage',
                            id: input.id,
                            label: input.label,
                            language: this.editForm.language,
                            remarks: input.remarks,
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
