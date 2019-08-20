module.exports = {
    map: false,
    plugins: [
        require('autoprefixer')(),
        require('postcss-discard-comments')({ removeAll: true }),
        require('cssnano')({ preset: 'default' }),
        require('postcss-zindex')(),
        require('css-mqpacker')()
    ],
};
