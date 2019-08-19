/*
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

const app = new Vue({
    delimiters: ['${', '}'],
    el: '#recover',
    data: {
        errors: [],
        name: null,
        email: null,
        pass1: null,
        pass2: null
    },
    methods:{
        checkForm: function (e) {
            let form  = document.getElementById('register');

            this.errors = [];

            if (!this.name) {
                this.errors.push('Name required.');
            }
            if (!this.email) {
                this.errors.push('Email required.');
            }
            if(this.pass1 !== this.pass2) {
                this.errors.push('Passwords are different.')
            }
            if(this.pass1.length < 10) {
                this.errors.push('Password has to be at least 10 characters long.')
            }
            if(!/[_\W]/g.test(this.pass1)) {
                this.errors.push('Password needs at least one special character.')
            }
            if(!/[_0-9]/g.test(this.pass1)) {
                this.errors.push('Password needs at least one number.')
            }
            if(!/[_A-Z]/g.test(this.pass1)) {
                this.errors.push('Password needs at least one capital letter.')
            }

            if (this.errors.length > 0) {
                e.preventDefault();
            } else {
                return true;
            }
        }
    }
})
