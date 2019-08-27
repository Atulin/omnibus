/*
 * Copyright © 2019 by Angius
 * Last modified: 26.08.2019, 05:11
 */

// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

axios.defaults.headers.post['Content-Type'] = 'multipart/form-data';

const app = new Vue({
    delimiters: ['${', '}'],
    el: '#v-app',
    data: {
        errors: [],

        // Article data
        title: document.getElementById('title').value,
        body: document.getElementById('body').value,
        excerpt: document.getElementById('excerpt').value,

    },
    methods: {

        handleImage: function (e) {
            this.image = this.$refs.image.files[0];
        },

        createArticle: function (e) {
            let token = document.getElementById('token').value;

            if (!this.id) {

            } else {

            }
        }

    },

    mounted() {
    }
});
