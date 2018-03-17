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
    var renderCycle = function (cycleElement, data) {
        cycleElement.innerHTML = Handlebars.templates['vocabularyCard.html'](data);
    };
    var cycles = document.querySelectorAll('[data-cycle]');
    Array.prototype.forEach.call(cycles, function (cycleElement) {
        renderCycle(cycleElement, window.initialCycleData[cycleElement.getAttribute('data-cycle')]);
    });
});
