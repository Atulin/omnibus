// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

const app = new Vue({
    delimiters: ['${', '}'],
    el: '#register',
    data: {
        errors: [],
        name: null,
        email: null,
        tos: null,
        pass1: null,
        pass2: null
    },
    methods:{
        checkForm: function (e) {
            e.preventDefault();

            let token = document.getElementById('token').value;
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
            if(!this.tos) {
                this.errors.push('You need to agree to terms of service.')
            }

            if(this.name && this.email) {
                fetch(`/register/validate?name=${encodeURIComponent(this.name)}&email=${encodeURIComponent(this.email)}&token=${encodeURIComponent(token)}`)
                    .then(res => res.json())
                    .then(res => {
                        if (!res.name) {
                            this.errors.push('Name already in use.');
                        }
                        if (!res.email) {
                            this.errors.push('Email already in use.');
                        }
                        if (!res.is_email) {
                            this.errors.push('Not a valid email address.');
                        }
                    })
                    .then(() => {
                        if (this.errors.length <= 0) {
                            form.submit();
                        }
                    })
            }
        }
    }
})
