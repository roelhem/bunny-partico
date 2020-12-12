<template>
    <div>
        <table>
            <thead>
                <tr>
                    <th>Contacts</th>
                </tr>
            </thead>
            <tbody>
                <contact-table-row v-for="contact in contactList" :key="contact.id" :contact="contact" />
            </tbody>
        </table>
    </div>
</template>

<script>
    import gql from 'graphql-tag';
    import ContactTableRow from "@/Components/Data/ContactTableRow";

    export default {
        name: "ContactTableCard",
        components: {ContactTableRow},
        apollo: {
            contacts: gql`
            query ContactTableCard {
               contacts {
                edges {
                  node {
                    ...ContactTableRow
                  }
                }
              }
            }

            ${ContactTableRow.fragment}
`
        },
        computed: {
            contactList() {
                const edges = this.contacts ? this.contacts.edges : []
                return edges.map((edge) => edge.node);
            }
        }
    }
</script>

<style scoped>

</style>
