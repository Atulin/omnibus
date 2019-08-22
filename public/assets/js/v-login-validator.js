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

        // Form data
        login: null,
        pass: null,
        remember: null,
        has_mfa: false,
        avatar: null,

        // Validation
        validated: true,
        mfa: null,

        // Message hiding
        msg_shown: true
    },
    methods: {
        checkForm: function (e) {

            e.preventDefault();

            this.validated = false;
            this.msg_shown = false;

            let token = document.getElementById('token').value;
            let form = document.getElementById('login');

            this.errors = [];

            if (!this.login) {
                this.errors.push('Name required.');
            }
            if (!this.pass) {
                this.errors.push('Password required.')
            }

            if (this.errors.length <= 0) {

                axios.get('/login/validate', {
                    params: {
                        login: this.login,
                        token: token
                    }
                })
                    .then(res => {
                        if (res.data.data.mfa === true) {
                            this.avatar = res.data.data.avatar;
                            this.has_mfa = res.data.data.mfa;
                            if (this.mfa) {
                                form.submit();
                            } else {
                                e.preventDefault();
                            }
                        } else {
                            form.submit();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        this.errors = [];
                        this.errors.push('Incorrect token. Refresh the page.')
                    })
                    .then(() => {
                        this.validated = true;
                    })

            }
        },
    }
});
