/*
 * Copyright © 2019 by Angius
 * Last modified: 24.08.2019, 13:48
 */

// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

const app = new Vue({
    delimiters: ['${', '}'],
    el: '#v-app',
    data: {
        errors: [],
        isLoading: false,
        canDelete: false,
        canApprove: false,

        // All comments
        comments: null,
    },
    methods: {

        deleteComment: function (c) {
            if (this.canDelete) {
                let token = document.getElementById('token').value;

                let data = new FormData();
                data.append('id', c.id);
                data.append('token', token);

                axios.post('/admin/comments/delete', data)
                    .then(res => {
                        console.log(res);
                        this.fetchReports();
                    })
                    .catch(err => {
                        console.error(err);
                    })
            } else {
                this.canDelete = true;
            }
        },

        approveComment: function (c) {
            if (this.canApprove) {
                let token = document.getElementById('token').value;

                let data = new FormData();
                data.append('id', c.id);
                data.append('token', token);

                axios.post('/admin/comments/accept', data)
                    .then(res => {
                        console.log(res);
                        this.fetchReports();
                    })
                    .catch(err => {
                        console.error(err);
                    })
            } else {
                this.canApprove = true;
            }
        },

        fetchReports: function (e) {
            this.isLoading = true;
            axios.get('/admin/comments/reports')
                .then(res => {
                    this.comments = res.data.data;
                    console.log(res.data.data);
                    this.isLoading = false
                })
        }

    },
    mounted() {
        this.fetchReports()
    }
});
