<template>
    <card v-bind="$attrs">
        <div class="flex border-gray-200" :class="{
            'border-b': this.expanded
        }">
            <div class="flex-none p-2 w-8 text-center"
                 :class="{'cursor-pointer hover:bg-gray-100': this.expandable, 'text-gray-200': !this.expandable}"
                 @click="toggle">
                <span v-if="!this.expanded">+</span>
                <span v-else>-</span>
            </div>
            <slot name="header">
                <div class="flex-grow-1">
                    <h4>{{ title }}</h4>
                </div>
            </slot>
        </div>
        <div v-if="this.expanded">
            <slot></slot>
        </div>
    </card>
</template>

<script>
    import Card from "@/Components/Cards/Card";
    export default {
        name: "ExpandableCard",
        components: {Card},
        props: {
            expand: {
                type: Boolean,
                default: false,
            },
            expandable: {
                type: Boolean,
                default: true,
            },
            title: {
                type: String,
                default: '',
            }
        },
        watch: {
            expand(value) {
                this.expanded = value;
            }
        },
        data() {
            return {
                expanded: this.expand
            }
        },
        methods: {
            toggle() {
                if(this.expandable) {
                    this.expanded = !this.expanded;
                    this.$emit('update:expand', this.expanded);
                }
            }
        }
    }
</script>

<style scoped>

</style>
