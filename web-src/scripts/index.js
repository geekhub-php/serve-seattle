;(function () {

    var links = document.querySelectorAll('.navbar-nav a');

    // Adding active class to link on current page
    for (var i = 0; i < links.length; i++) {
        if (links[i].href === document.URL) {
            links[i].classList.add('active');
        } else {
            console.log(false);
        }
    }

})();
