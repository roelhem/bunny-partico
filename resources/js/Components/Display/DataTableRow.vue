<template>
    <tr class="border-b border-gray-200 text-gray-800 hover:bg-gray-100">
        <th class="p-2 pl-4 text-right text-gray-400 font-bold uppercase text-xs w-32">{{ label }}</th>
        <td class="p-2"><slot /></td>
    </tr>
</template>

<script>
    export default {
        name: "DataTableRow",
        props: {
            label: {
                type: [String]
            }
        }
    }
</script>

<style scoped>

</style>
