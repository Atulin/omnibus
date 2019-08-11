// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

const title_length = 20;
const bio_length = 2000;

const app = new Vue({
    delimiters: ['${', '}'],
    el: '#profile',
    data: {
        errors: [],
        title: document.getElementById('title').value,
        bio: document.getElementById('bio').value,
        chars_left: {
            title: title_length,
            bio: bio_length
        }
    },
    methods: {
        checkForm: function(e) {
            this.errors = [];

            if (this.title && this.title.length > title_length) {
                this.errors.push(`Title can't be longer than ${title_length}.`);
            }
            if(this.bio && this.bio.length > bio_length) {
                this.errors.push(`Bio can't be longer than ${bio_length}.`)
            }

            if (this.errors.length <= 0) return true;

            e.preventDefault();
        }
    },

    computed: {
        TitleCharsLeft() {
            let chars = this.title.length;
            this.chars_left.title = title_length - chars;

            // if (this.comment_body.length > limit) {
            //     this.error = 'Your message is ' + Math.abs(this.chars_left) + ' characters too long'
            // } else {
            //     this.error = null;
            // }

            return this.chars_left.title;
        },
        BioCharsLeft() {
            let chars = this.bio.length;
            this.chars_left.bio = bio_length - chars;

            return this.chars_left.bio;
        }
    }
});
