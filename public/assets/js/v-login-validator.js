// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

const app = new Vue({
    delimiters: ['${', '}'],
    el: '#login',
    data: {
        errors: [],
        login: null,
        pass: null,
        remember: null
    },
    methods: {
        checkForm: function(e) {
            this.errors = [];

            if (!this.login) {
                this.errors.push('Name required.');
            }
            if(!this.pass) {
                this.errors.push('Password required.')
            }

            if (this.errors.length <= 0) return true;

            e.preventDefault();
        }
    }
})
