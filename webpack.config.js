const path = require('path');

module.exports = {
    module: {
        rules: [
            {
                test: /\.(graphql|gql)$/,
                exclude: /node_modules/,
                loader: 'graphql-tag/loader',
            },
            {
                test: /\.svg$/,
                loader: 'vue-svg-loader',
            },
        ],
    },
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
        },
    },
};
