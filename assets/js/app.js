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
            card.classList.add('revealed');
            event.preventDefault();
            event.stopPropagation();
        });
    });
});

// load cycles
ready(function () {
    var loadCycle = function(cycleElement) {
        cycleElement.classList.addClass('loading');
        getJson(
            cycleElement.getAttribute('data-cycle-load-url'),
            function(data) {
                console.log(data);
            },
            function () {
            },
            function () {
                cycleElement.classList.removeClass('loading');
            }
        );
    };
    var cycles = document.querySelectorAll('[data-cycle]');
    Array.prototype.forEach.call(cycles, function (cycleElement) {
        var toggle = card.querySelector('[data-reveal]');
        toggle.addEventListener('click', function (event) {
            card.classList.add('revealed');
>>>>>>> Stashed changes
            event.preventDefault();
            event.stopPropagation();
        });
    });
});
