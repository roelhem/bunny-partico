<template>
    <expandable-card class="animate__flip animate__animated"
                     :class="{
                        'bg-blue-100': this.editing,
                     }"
                     :expandable="expandable"
                     :expand.sync="expand"
                     v-on="$listeners">
        <template #header>
            <template v-if="view">
                <div v-if="label" class="cursor-move handle flex-none text-gray-400 text-xs font-black min-w-label p-2 pt-3 text-right uppercase align-baseline">
                    {{ label }}
                </div>
                <div class="flex-grow-1 p-2 w-full">
                    <slot name="header"></slot>
                </div>
                <div v-if="editable"
                     class="flex-none p-2 text-gray-400 hover:bg-gray-100 cursor-pointer"
                     @click="toggleEdit"
                >
                    bewerken
                </div>
                <div v-if="deletable" class="flex-none p-2 text-gray-400 hover:bg-gray-100 cursor-pointer">
                    verwijderen
                </div>
            </template>
            <template v-else-if="edit">
                <div class="text-gray-400 text-xs font-black p-2 pt-3 uppercase">Bewerken</div>
            </template>
        </template>

        <template v-if="view">
            <slot></slot>
            <p v-if="remarks" class="font-sans text-gray-500 p-2 whitespace-pre-wrap">{{ remarks }}</p>
        </template>
        <template v-else-if="editing">
            <slot name="edit"></slot>
        </template>
    </expandable-card>
</template>

<script>
    import ExpandableCard from "@/Components/Cards/ExpandableCard";
    export default {
        name: "NodeCard",
        components: {ExpandableCard},
        props: {
            label: {
                type: String,
                default: null,
            },
            editable: {
                type: Boolean,
                default: false,
            },
            deletable: {
                type: Boolean,
                default: false,
            },
            remarks: {
                type: String,
                default: null,
            },
            edit: {
                type: Boolean,
                default: false,
            }
        },
        watch: {
            edit(value) {
                this.editing = value;
            }
        },
        data() {
            return {
                expanded: false,
                editing: this.editing,
            }
        },
        computed: {
            expand: {
                get() {
                    return this.editing || this.expanded;
                },
                set(newValue) {
                    this.expanded = newValue;
                }
            },
            expandable() {
                return !this.editing;
            },
            view() {
                return !this.editing;
            },
        },
        methods: {
            toggleEdit() {
                this.editing = !this.editing;
                this.$emit('update:edit', this.editing);
                if(this.editing) {
                    this.$emit('start-edit');
                }
            }
        }
    }
</script>

<style scoped>

</style>
