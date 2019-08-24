/*
 * Copyright © 2019 by Angius
 * Last modified: 24.08.2019, 13:26
 */

// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

const app = new Vue({
    delimiters: ['${', '}'],
    el: '#v-app',
    data: {
        errors: [],
        modal: false,
        isLoading: true,
        canDelete: false,

        // Form data
        id: null,
        name: null,
        description: null,
        token: null,

        // All cats
        tags: null,
    },
    methods: {
        toggleModal: function (e) {
            if (e) e.stopPropagation();

            this.modal = !this.modal;

            if (!this.modal && this.id) {
                this.id = this.name = this.image = this.description = null;
            }
        },

        createTag: function (e) {
            e.preventDefault();
            this.isLoading = true;

            let token = document.getElementById('token').value;

            if (!this.id) {
                // Create POST data
                let data = new FormData();
                data.append('name', this.name);
                data.append('description', this.description);
                data.append('token', token);

                axios.post('/admin/tags/create', data,)
                    .then(res => {
                        this.fetchTags();
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
                data.append('description', this.description);
                data.append('token', token);

                axios.post('/admin/tags/update', data,)
                    .then(res => {
                        this.fetchTags();
                        this.modal = false;
                    })
                    .catch(err => {
                        console.error(err);
                        this.isLoading = false
                    });
            }
        },

        deleteTag: function (c) {
            if (this.canDelete) {
                this.isLoading = true;
                let token = document.getElementById('token').value;

                let data = new FormData();
                data.append('id', c.id);
                data.append('token', token);

                axios.post('/admin/tags/delete', data)
                    .then(res => {
                        console.log(res);
                        this.fetchTags();
                    })
                    .catch(err => {
                        console.error(err);
                        this.isLoading = false
                    })
                    .then(() => {
                        this.canDelete = false;
                    })
            } else {
                this.canDelete = true;
            }
        },

        editTag: function (c) {
            console.log(c);
            this.id = c.id;
            this.name = c.name;
            this.description = c.description;

            this.toggleModal();
        },

        fetchTags: function (e) {
            this.isLoading = true;
            axios.get('/admin/tags/fetch')
                .then(res => {
                    this.tags = res.data.data;
                    console.log(res.data.data)
                    this.isLoading = false
                })
        }

    },
    mounted() {
        this.fetchTags()
    }
});
