<template>
    <node-card :label="relation.label"
               :remarks="relation.remarks"
               editable
               :edit.sync="edit"
               @start-edit="resetEditForm"
    >
        <template #header>
            <span v-if="relation.related">
                {{ relation.related.name }}
                <span v-if="relation.related.nickname" class="opacity-50">({{relation.related.nickname}})</span>
            </span>
            <span v-else class="text-gray-400">(Verborgen)</span>
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
                    Contact
                    <contact-select v-model="editForm.related" />
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
    import DataTable from "@/Components/Display/DataTable";
    import DataTableRow from "@/Components/Display/DataTableRow";
    import ContactSelect from "@/Components/Forms/ContactSelect";

    export default {
        name: "ContactRelationCard",
        components: {ContactSelect, DataTableRow, DataTable, NodeCard},
        props: {
            relation: {
                type: [Object],
                required: false,
            }
        },
        fragment: gql`
            fragment ContactRelationCard on ContactRelation {
                id
                label
                remarks
                related {
                    id
                    name
                    nickname
                }
            }
`,
        data() {
            return {
                edit: false,
                editForm: {
                    label: null,
                    related: null,
                    remarks: null,
                },
            }
        },
        methods: {
            clearEditForm() {
                this.editForm.label = null;
                this.editForm.related = null;
                this.editForm.remarks = null;
            },
            resetEditForm() {
                this.editForm.label = this.relation.label;
                this.editForm.related = this.relation.related;
                this.editForm.remarks = this.relation.remarks;
            },
            submitEdit() {
                // Get the values
                const input = {
                    id: this.relation.id,
                    label: this.editForm.label,
                    related_contact_id: this.editForm.related.id,
                    remarks: this.editForm.remarks,
                };

                // Close the form
                this.edit = false;

                // Perform the mutation
                this.$apollo.mutate({
                    mutation: gql`mutation updateContactRelationCard ($input: UpdateContactRelationInput!) {
                        updateContactRelation(input: $input) {
                            ...ContactRelationCard
                        }
                    }
                    ${this.$options.fragment}`,
                    variables: {input},
                    update: (store, {data: {updateContactLanguage}}) => {
                        store.writeFragment({
                            id: `ContactRelation:${input.id}`,
                            fragment: this.$options.fragment,
                            data: updateContactLanguage,
                        })
                    },
                    optimisticResponse: {
                        __typename: 'Mutation',
                        updateContactRelation: {
                            __typename: 'ContactRelation',
                            id: input.id,
                            label: input.label,
                            related: this.editForm.related,
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
