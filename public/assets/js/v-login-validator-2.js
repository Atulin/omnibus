/*
 * Copyright © 2019 by Angius
 * Last modified: 22.08.2019, 03:49
 */

// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

const app = new Vue({
    delimiters: ['${', '}'],
    el: '#login',
    data: {
        errors: [],
        step: 1,
        button_text: "Next",

        // Form data
        login: null,
        pass: null,
        remember: null,
        has_mfa: false,
        avatar: null,

        // Validation
        mfa: null,

        // Message hiding
        msg_shown: true
    },
    methods: {
        checkForm: function (e) {

            e.preventDefault();

            this.msg_shown = false;

            let token = document.getElementById('token').value;
            let form = document.getElementById('login');

            this.errors = [];

            // First step – LOGIN
            if (this.step === 1) {

                if (!this.login) {
                    this.errors.push('Name required.');
                }

                if (this.errors.length <= 0) {
                    axios.get('/login/validate', {
                        params: {
                            login: this.login,
                            token: token
                        }
                    })
                    .then(res => {
                        this.avatar = res.data.data.avatar;
                        if (res.data.data.mfa === true) {
                            this.has_mfa = res.data.data.mfa;
                        }

                        this.button_text = 'Log in';
                        this.step = 2;
                    })
                    .catch(err => {
                        console.error(err);
                        this.errors = [];
                        this.errors.push('Incorrect token. Refresh the page.')
                    })
                }


            // Second step – PASSWORD
            } else if (this.step === 2) {

                if (!this.pass) {
                    this.errors.push('Password required.')
                }
                if (!(this.has_mfa && this.mfa)) {
                    this.errors.push('2FA token required.');
                }

                if (!this.err) {
                    form.submit();
                }
            }

        },
    }
});
