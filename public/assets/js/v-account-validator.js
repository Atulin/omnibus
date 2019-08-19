/*
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

const app = new Vue({
    delimiters: ['${', '}'],
    el: '#account',
    data: {
        errors: [],
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        new_password: null,
        password: null
    },
    methods:{
        checkForm: function (e) {
            e.preventDefault();

            let token = document.getElementById('token').value;
            let form  = document.getElementById('account');

            this.errors = [];

            if(this.new_password.length < 10) {
                this.errors.push('Password has to be at least 10 characters long.')
            }
            if(!/[_\W]/g.test(this.new_password)) {
                this.errors.push('Password needs at least one special character.')
            }
            if(!/[_0-9]/g.test(this.new_password)) {
                this.errors.push('Password needs at least one number.')
            }
            if(!/[_A-Z]/g.test(this.new_password)) {
                this.errors.push('Password needs at least one capital letter.')
            }

            if (this.email) {
                axios.get('/account/validate', {
                    params: {
                        name: this.name,
                        email: this.email,
                        token: token
                    }
                })
                .then(res => {
                    if (!res.data.email) {
                        this.errors.push('Email already in use.');
                    }
                    if (!res.data.is_email) {
                        this.errors.push('Not a valid email address.');
                    }
                    if (this.errors.length <= 0) {
                        form.submit();
                    }
                })
                .catch(err => {
                    console.error(err);
                });

            }
        }
    }
});
