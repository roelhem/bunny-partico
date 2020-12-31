<template>
    <span :class="spanClass">
        <slot />
    </span>
</template>

<script>
    import {COLORS} from "@/utils/colors";

    export const sizes = ['xs','sm','md','lg','xl'];

    export default {
        name: "Tag",
        props: {
            color: {
                type: String,
                default: 'gray',
                validator(value) {
                    return COLORS.includes(value);
                },
            },
            size: {
                type: String,
                default: 'md',
                validator(value) {
                    return sizes.includes(value);
                }
            },
            light: {
                type: Boolean,
                default: false
            },
            interactive: {
                type: Boolean,
                default: false,
            }
        },
        computed: {
            spanSizeClasses() {
                switch (this.size) {
                    case 'xs': return ['px-1.5', 'text-xs', 'rounded'];
                    case 'sm': return ['px-2', 'py-0.5', 'text-xs', 'rounded-md'];
                    case 'md': return ['px-2.5', 'py-1', 'text-sm', 'rounded-md'];
                    case 'lg': return ['px-4', 'py-1', 'text-base', 'rounded-lg'];
                    case 'xl': return ['px-6', 'py-1', 'text-lg', 'rounded-lg'];
                }
            },
            spanBackgroundColorClasses() {
                // Black and white aren't effected by the light property.
                switch (this.color) {
                    case 'black': return ['bg-black'];
                    case 'white': return ['bg-white'];
                }
                // Check the light property.
                if(this.light) {
                    switch (this.color) {
                        case 'yellow': return ['bg-yellow-200'];
                        // case 'indigo': return ['bg-indigo-400'];
                        case 'brown': return ['bg-yellow-300'];
                        default: return [`bg-${this.color}-300`];
                    }
                } else {
                    switch (this.color) {
                        case 'yellow': return ['bg-yellow-300'];
                        case 'brown': return ['bg-yellow-500'];
                        default: return [`bg-${this.color}-500`];
                    }
                }
            },
            spanHoverBackgroundColorClasses() {
                switch (this.color) {
                    case 'white': return ['hover:bg-gray-100'];
                    case 'black': return ['hover:bg-gray-800'];
                }
                // Check the light property.
                if(this.light) {
                    switch (this.color) {
                        case 'yellow': return ['hover:bg-yellow-300'];
                        // case 'indigo': return ['hover:bg-indigo-500'];
                        case 'brown': return ['hover:bg-yellow-400'];
                        default: return [`hover:bg-${this.color}-400`];
                    }
                } else {
                    switch (this.color) {
                        case 'yellow': return ['hover:bg-yellow-400'];
                        case 'brown': return ['hover:bg-yellow-600'];
                        default: return [`hover:bg-${this.color}-600`];
                    }
                }
            },
            spanHoverClasses() {
                const result = ['hover:shadow-lg'];
                if(this.interactive) {
                    result.push('cursor-pointer', ...this.spanHoverBackgroundColorClasses);
                    if(!['black','white'].includes(this.color)) {
                        if(this.light && !['black','white'].includes(this.color)) {
                            result.push('hover:text-black');
                        } else {
                            result.push('hover:text-white');
                        }
                    }
                }
                return result;
            },
            spanTextColorClasses() {

                if(this.light) {
                    switch (this.color) {
                        case 'black': return ['text-white', 'font-medium'];
                        case 'white': return ['text-gray-800', 'font-medium'];
                        case 'brown': return ['text-yellow-900',  'font-medium'];
                        default: return [`text-${this.color}-900`, 'font-medium'];
                    }
                } else {
                    switch (this.color) {
                        case 'black': return ['text-white', 'font-bold'];
                        case 'white': return ['text-gray-600', 'font-bold'];
                        default: return ['text-white', 'font-bold'];
                    }

                }
            },
            spanClass() {
                return [
                    ...this.spanSizeClasses,
                    ...this.spanBackgroundColorClasses,
                    ...this.spanTextColorClasses,
                    ...this.spanHoverClasses,
                    'select-none',
                    'inline-block',
                    'shadow-md',
                ];
            }
        }
    }
</script>

<style scoped>

</style>
