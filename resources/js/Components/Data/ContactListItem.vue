<template>
    <li class="p-4">
        <h4>
            <span>{{contact.name}}</span>
            <span v-if="contact.nickname" class="text-gray-500">({{contact.nickname}})</span>
        </h4>
    </li>
</template>

<script>
    import gql from 'graphql-tag';

    export default {
        name: "ContactListItem",
        props: {
            contact: {
                required: true,
            }
        },
        fragment: gql`
            fragment ContactListItem on Contact {
                id
                name
                nickname
            }
`,
    }
</script>

<style scoped>

</style>
