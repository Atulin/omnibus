/*
 * Copyright © 2019 by Angius
 * Last modified: 24.08.2019, 04:01
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
        modal: false,
        isLoading: true,

        // Form data
        id: null,
        name: null,
        image: null,
        description: null,
        token: null,

        // All cats
        categories: null,
    },
    methods: {
        toggleModal: function (e) {
            if (e) e.stopPropagation();

            this.modal = !this.modal;

            if (!this.modal && this.id) {
                this.id = this.name = this.image = this.description = null;
            }
        },

        handleImage: function (e) {
            this.image = this.$refs.image.files[0];
        },

        createCategory: function (e) {
            e.preventDefault();
            this.isLoading = true;

            let token = document.getElementById('token').value;

            if (!this.id) {
                // Create POST data
                let data = new FormData();
                data.append('name', this.name);
                data.append('image', this.image);
                data.append('description', this.description);
                data.append('token', token);

                axios.post('/admin/categories/create', data,)
                    .then(res => {
                        this.fetchCategories();
                        this.modal = false;
                    })
                    .catch(err => {
                        console.error(err)
                        this.isLoading = false
                    });
            } else {
                // Create POST data
                let data = new FormData();
                data.append('id', this.id);
                data.append('name', this.name);
                data.append('image', this.image);
                data.append('description', this.description);
                data.append('token', token);

                axios.post('/admin/categories/update', data,)
                    .then(res => {
                        this.fetchCategories();
                        this.modal = false;
                    })
                    .catch(err => {
                        console.error(err);
                        this.isLoading = false
                    });
            }
        },

        deleteCategory: function (c) {
            if (confirm('Are you sure?')) {
                this.isLoading = true;
                let token = document.getElementById('token').value;

                let data = new FormData();
                data.append('id', c.id);
                data.append('token', token);

                axios.post('/admin/categories/delete', data)
                    .then(res => {
                        console.log(res);
                        this.fetchCategories();
                    })
                    .catch(err => {
                        console.error(err);
                        this.isLoading = false
                    })
            }
        },

        editCategory: function (c) {
            console.log(c);
            this.id = c.id;
            this.name = c.name;
            this.description = c.description;

            this.toggleModal();
        },

        fetchCategories: function (e) {
            this.isLoading = true;
            axios.get('/admin/categories/fetch')
                .then(res => {
                    this.categories = res.data.data;
                    this.isLoading = false
                })
        }

    },
    mounted() {
        this.fetchCategories()
    }
});
