<template>
    <tr>
        <td>{{contact.name}}</td>
    </tr>
</template>

<script>
    import gql from 'graphql-tag';

    export default {
        name: "ContactTableRow",
        fragment: gql`
        fragment ContactTableRow on Contact {
            id
            name
        }
`,
        props: {
            contact: {
                type: Object,
                default:function () {
                    return {
                        id: null,
                        name: null,
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>
