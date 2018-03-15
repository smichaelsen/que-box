require('../css/app.scss');
require('../images/paper.png');

function ready(fn) {
    if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading") {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}

// init revealable cards
ready(function () {
    var revealableCards = document.querySelectorAll('.revealable-card');
    Array.prototype.forEach.call(revealableCards, function (card) {
        var toggle = card.querySelector('[data-reveal]');
        toggle.addEventListener('click', function (event) {
            card.classList.add('revealable-card--revealed');
            event.preventDefault();
            event.stopPropagation();
        });
    });
});
