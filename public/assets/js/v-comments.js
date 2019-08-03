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

            if (this.comment_body.length > limit) {
                this.error = 'Your message is ' + Math.abs(this.chars_left) + ' characters too long'
            } else {

                axios.post('/api/comments', {
                    body: this.comment_body,
                    token: token
                })
                .then(response =>{
                    console.log(response);
                    this.comments_list.push(
                        {
                            user: 'A',
                            body: 'aaaa',
                            date: 'a.a.a'
                        }
                    );
                    this.comments_list.push(
                        {
                            user: 'B',
                            body: 'bbbb',
                            date: 'b.b.b'
                        }
                    )
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
