<template>
    <span>{{ dateString }}</span>
</template>

<script>
    export default {
        name: "Date",
        props: {
            date: {
                default: null,
            },
            format: {
                type: String,
                default() {
                    return 'D MMMM YYYY'
                }
            }
        },
        computed: {
            dateMoment() {
                return this.date ? moment(this.date) : null;
            },
            dateString() {
                return this.dateMoment ? this.dateMoment.format(this.format) : null;
            }
        }
    }
</script>

<style scoped>

</style>
