// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

const limit = 140;

const app = new Vue({
    delimiters: ['${', '}'],
    el: '#comments',
    data: {
        error: null,
        comment_body: '',
        chars_left: limit
    },
    methods: {
        sendComment: function(e) {
            this.errors = [];

            if (this.comment_body.length > limit) {
                this.error = 'Your message is ' + Math.abs(this.chars_left) + ' characters too long'
            }

            if (this.error === null) return true;

            e.preventDefault();
        }
    },
    computed: {
        charsLeft() {
            let chars = this.comment_body.length;

            this.chars_left = limit - chars;
            return limit - chars;
        }
    }
});
