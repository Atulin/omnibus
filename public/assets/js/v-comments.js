// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

const limit = 140;

const app = new Vue({
    delimiters: ['${', '}'],
    el: '#comments',

    data: {
        error: null,
        comment_body: '',
        chars_left: limit,
        comments_list: []
    },

    methods: {
        sendComment: function(e) {
            e.preventDefault();

            let token = document.getElementById('token').value;
            let thread = document.getElementById('thread').value;

            if (this.comment_body.length > limit) {
                this.error = 'Your message is ' + Math.abs(this.chars_left) + ' characters too long'
            } else {

                // Create POST data
                let data = new FormData();
                data.append('body', this.comment_body);
                data.append('thread', thread);
                data.append('token', token);

                // Add comment
                axios.post('/api/comments', data)
                .then(response =>{

                    // Create GET data
                    let data = new FormData();
                    data.append('thread', thread);

                    // Get comments
                    axios.get('/api/comments', {params:{thread: thread}})
                        .then(response => {
                            console.log(response);
                            this.comments_list = [];

                            for (let c of response.data.comments) {
                                this.comments_list.push(c);
                            }

                        })
                        .catch(err => {
                            console.error(err)
                        });

                })
                .catch(err => {
                    console.error(err)
                });

                return true;
            }
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
    }
});