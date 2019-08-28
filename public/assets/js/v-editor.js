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

        createArticle: function (e) {
            let token = document.getElementById('token').value;

            if (!this.id) {

            } else {

            }
        }

    },

    computed: {

        charsBody: function(e) {
            return this.body.length
        },
        noWhitespaceBody: function (e) {
            return this.body.replace(/\s/g,'').length
        },
        wordsBody: function (e) {
            return this.body.split(/\s/g).filter((x)=>{ return x !== '' }).length
        }

    },

    mounted() {
    }
});
