const HtmlWebPackPlugin = require("html-webpack-plugin");

//const path = require('path');

module.exports = {

    watch: true,

    module: {

        rules: [
            {
                test: /\.(js|jsx)$/,
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader"
                }
            },
            {
                test: /\.html$/,
                use: [
                    {
                        loader: "html-loader"
                    }
                ]
            }
        ]
    },

    // plugins: [
    //     new HtmlWebPackPlugin({})
    // ],

    output: {
        filename: '[name].bundle.js',
        path: __dirname + '/../public/scripts'
    }
};