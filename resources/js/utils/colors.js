export const DEFAULT_COLOR = 'gray';

export const COLORS = [
    'black',
    'white',
    'gray',
    'red',
    'orange',
    'yellow',
    'brown',
    'green',
    'teal',
    'blue',
    'indigo',
    'purple',
    'pink',
];

export function getColor(input) {
    if(typeof input !== 'string') {
        return 'gray'
    } else {
        return input.toLocaleLowerCase()
    }
}
