// ==ClosureCompiler==
// @compilation_level ADVANCED_OPTIMIZATIONS
// ==/ClosureCompiler==

let borgar = document.querySelector('.hamburger');
let items = document.querySelectorAll('.items');
borgar.addEventListener('click', () => {
    for (let i of items) {
        i.classList.toggle('visible');
    }
});
