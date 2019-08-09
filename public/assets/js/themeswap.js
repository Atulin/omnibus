(function() {

    let style = document.getElementById('style');
    let themeButtons = document.querySelectorAll('.theme');

    let cDate = new Date;
    cDate.setFullYear(cDate.getFullYear() + 1);

    for (let tb of themeButtons) {
        tb.addEventListener('click', function() {
            let theme = tb.dataset.theme.match(/\/([a-z]+)\./g);
            theme = `${theme}`.replace(/[^\w]/gi, '');
            document.cookie = `theme=${theme}; expires=${cDate.toUTCString()}; path=/`;
            style.href = `assets/${tb.dataset.theme}.min.css`;
        })
    }

})();
