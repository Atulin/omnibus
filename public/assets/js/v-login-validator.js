/*
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

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
        remember: null,
        mfa: false,
        avatar: null,
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
        },

        getInfo: function (e) {
            let token = document.getElementById('token').value;

            axios.get('/login/validate', {
                params: {
                    login: this.login,
                    token: token
                }
            })
            .then(res => {
                this.avatar = res.data.data.avatar;
                this.mfa = res.data.data.mfa;
            })
            .catch(err => {
                this.errors.push('Incorrect token. Refresh the page.')
            })

        }
    }
});
