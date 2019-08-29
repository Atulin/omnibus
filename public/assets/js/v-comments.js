/*
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

const limit = 300;

const app = new Vue({
    delimiters: ['${', '}'],
    el: '#comments',

    data: {
        error: null,
        comment_body: '',
        chars_left: limit,
        comments_list: [],

        report_modal: false,
        report_id: null,
        reason: null,
    },

    methods: {
        sendComment: function (e) {
            e.preventDefault();

            let token = document.getElementById('token').value;
            let thread = document.getElementById('thread').value;

            if (this.comment_body.length > limit) {
                this.error = 'Your message is ' + Math.abs(this.chars_left) + ' characters too long'
            } else if (this.comment_body.replace(/\s/g, "").length <= 0) {
                this.error = 'Message cannot be empty.'
            } else {

                // Create POST data
                let data = new FormData();
                data.append('body', this.comment_body);
                data.append('thread', thread);
                data.append('token', token);

                // Add comment
                axios.post('/api/comments', data)
                    .then(response => {

                        // Clear textarea
                        this.comment_body = '';
                        this.fetchComments()

                    })
                    .catch(err => {
                        console.error(err)
                    });

                return true;
            }
        },

        fetchComments: function(e) {
            let thread = document.getElementById('thread').value;

            // Create GET data
            let data = new FormData();
            data.append('thread', thread);

            // Get comments
            axios.get('/api/comments', {params: {thread: thread}})
                .then(response => {
                    console.log(response);
                    this.comments_list = [];

                    for (let c of response.data.data) {
                        this.comments_list.push(c);
                    }

                })
                .catch(err => {
                    console.error(err)
                });
        },

        reportModal: function(event, c) {
           if (this.report_modal) {
               this.report_modal = false;
               this.report_id = null;
               this.reason = null;
           } else {
               this.report_modal = true;
               this.report_id = c;
           }
        },

        reportComment: function (event) {
            let token = document.getElementById('token').value;

            console.log(event, this.report_id);

            let data = new FormData();
            data.append('comment', this.report_id);
            data.append('reason', this.reason);
            data.append('token', token);

            // Report comment
            axios.post('/api/comments/report', data)
                .then(response => {
                    console.log('Reported!');
                    console.info(response.data);
                })
                .catch(err => {
                    console.error(err);
                })
                .then(x => {
                    this.report_modal = false;
                    this.report_id = null;
                    this.reason = null;
                });
        },

        insertMention: function (c, e) {
            console.log(c, e);

            if (this.comment_body.endsWith(' ') || this.comment_body.length <= 0) {
                this.comment_body += `@${c} `;
            } else {
                this.comment_body += ` @${c} `;
            }
        },

        dismissError: function () {
            this.error = null;
        }
    },

    computed: {
        charsLeft() {
            let chars = this.comment_body.length;

            this.chars_left = limit - chars;

            if (this.comment_body.length > limit) {
                this.error = 'Your message is ' + Math.abs(this.chars_left) + ' characters too long'
            } else {
                this.error = null;
            }

            return limit - chars;
        }
    },

    mounted() {
        this.fetchComments();
    }
});
