<template>
    <tag v-bind="$attrs" v-on="$listeners" :color="color" light>{{ group.name }}</tag>
</template>

<script>
    import Tag from "@/Components/Tag";
    import gql from 'graphql-tag';
    import {DEFAULT_COLOR, getColor} from "@/utils/COLORS";

    export default {
        name: "GroupTag",
        components: {Tag},
        fragment: gql`fragment GroupTag on Group {
            id
            name
            category {
                id
                color
            }
        }`,
        props: {
            group: {
                type: Object,
                required: true,
            }
        },
        computed: {
            color() {
                if(this.group.category) {
                    return getColor(this.group.category.color);
                } else {
                    return DEFAULT_COLOR;
                }
            }
        }
    }
</script>

<style scoped>

</style>
