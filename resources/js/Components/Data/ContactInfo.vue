<template>
    <div>
        <div class="my-4">
            <h1 class="text-4xl">
                <span>{{ contact.name }}</span>
                <span v-if="contact.nickname" class="text-gray-400">({{ contact.nickname }})</span>
            </h1>


            <data-table>
                <data-table-row v-if="contact.birth_date" label="Geboortedatum">
                    <date :date="contact.birth_date" />
                    <span v-if="contact.age" class="ml-2 text-gray-600 italic">({{ contact.age }} jaar)</span>
                </data-table-row>

                <data-table-row label="Groepen" v-if="contact.groups && contact.groups.edges">
                    <div class="space-y-1 space-x-1 -mt-1">
                        <group-tag v-for="edge in contact.groups.edges" :key="edge.node.id" :group="edge.node">
                        </group-tag>
                    </div>
                </data-table-row>
            </data-table>
            <p class="text-gray-800 whitespace-pre-wrap py-2">{{ contact.remarks }}</p>
        </div>
        <div class="my-4">
            <h2 class="text-xl">E-mailadressen</h2>
            <draggable handle=".handle">
                <email-address-card v-for="email_address of contact.email_addresses"
                                    :email-address="email_address"
                                    :key="email_address.id"
                                    class="my-2"
                />
            </draggable>
            <span v-if="contact.email_addresses && contact.email_addresses.length <= 0"
                  class="text-gray-400"
            >(Geen)</span>
        </div>

        <div class="my-4">
            <h2 class="text-xl">Telefoonnummers</h2>
            <draggable handle=".handle">
                <phone-number-card v-for="phone_number of contact.phone_numbers"
                                   :key="phone_number.id"
                                   :phone-number="phone_number"
                                   class="my-2"
                />
            </draggable>

            <span v-if="contact.phone_numbers && contact.phone_numbers.length <= 0"
                  class="text-gray-400"
            >(Geen)</span>
        </div>

        <div class="my-4">
            <h2 class="text-xl">Adressen</h2>
            <draggable handle=".handle">
                <postal-address-card v-for="postal_address of contact.postal_addresses"
                                     :postal-address="postal_address"
                                     :key="postal_address.id"
                                     class="my-2"
                />
            </draggable>
            <span v-if="contact.postal_addresses && contact.postal_addresses.length <= 0"
                  class="text-gray-400"
            >(Geen)</span>
        </div>

        <div class="my-4">
            <h2 class="text-xl">Talen</h2>
            <draggable handle=".handle">
                <contact-language-card v-for="language of contact.languages"
                                       :language="language"
                                       :key="language.id"
                                       class="my-2"
                />
            </draggable>
            <span v-if="contact.languages && contact.languages.length <= 0"
                  class="text-gray-400"
            >(Geen)</span>
        </div>

        <div class="my-4">
            <h2 class="text-xl">Relaties</h2>
            <draggable handle=".handle">
                <contact-relation-card v-for="relation of contact.relations"
                                       :relation="relation"
                                       :key="relation.id"
                                       class="my-2"
                />
            </draggable>
            <span v-if="contact.relations && contact.relations.length <= 0"
                  class="text-gray-400"
            >(Geen)</span>
        </div>
    </div>
</template>

<script>
    import gql from 'graphql-tag';
    import EmailAddressCard from "@/Components/Data/EmailAddressCard";
    import PostalAddressCard from "@/Components/Data/PostalAddressCard";
    import PhoneNumberCard from "@/Components/Data/PhoneNumberCard";
    import Draggable from 'vuedraggable';
    import ContactRelationCard from "@/Components/Data/ContactRelationCard";
    import ContactLanguageCard from "@/Components/Data/ContactLanguageCard";
    import AccountCard from "@/Components/Data/AccountCard";
    import DataTable from "@/Components/Display/DataTable";
    import DataTableRow from "@/Components/Display/DataTableRow";
    import Date from "@/Components/Display/Date";
    import GroupTag from "@/Components/Data/GroupTag";

    export default {
        name: "ContactInfo",
        components: {
            GroupTag,
            Date,
            DataTableRow,
            AccountCard,
            ContactLanguageCard,
            ContactRelationCard,
            PhoneNumberCard,
            PostalAddressCard,
            EmailAddressCard,
            DataTable,
            Draggable,
        },
        data() {
            return {
                contact: {},
            }
        },
        props: {
            nodeId: {
                required: true,
            }
        },
        apollo: {
            contact: {
                query: gql`
                query ContactInfo($nodeId: ID!) {
                    contact: node(id: $nodeId) {
                        id
                        ... on Contact {
                            name
                            nickname
                            birth_date
                            age
                            remarks
                            email_addresses {
                                id
                                ...EmailAddressCard
                            }
                            phone_numbers {
                                id
                                ...PhoneNumberCard
                            }
                            postal_addresses {
                                id
                                ...PostalAddressCard
                            }
                            languages {
                                id
                                ...ContactLanguageCard
                            }
                            relations {
                                id
                                ...ContactRelationCard
                            }
                            groups {
                                edges {
                                    node {
                                        id
                                        ...GroupTag
                                    }
                                }
                            }
                        }
                    }
                }
                ${GroupTag.fragment}
                ${EmailAddressCard.fragment}
                ${PhoneNumberCard.fragment}
                ${PostalAddressCard.fragment}
                ${ContactLanguageCard.fragment}
                ${ContactRelationCard.fragment}
`,
                variables() {
                    return {
                        nodeId: this.nodeId,
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>
