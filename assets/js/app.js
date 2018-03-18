function ready(fn) {
    if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading") {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}

function attachEventListeners(cycleElement) {

    // revealable cards
    var revealableCards = cycleElement.querySelectorAll('.revealable-card');
    Array.prototype.forEach.call(revealableCards, function (card) {
        var toggle = card.querySelector('[data-reveal]');
        toggle.addEventListener('click', function (event) {
            card.classList.add('revealable-card--revealed');
            card.classList.add('revealed');
            event.preventDefault();
            event.stopPropagation();
        });
    });

    // complete cycle
    var cycleCompletionTriggers = cycleElement.querySelectorAll('[data-cycle-complete]');
    Array.prototype.forEach.call(cycleCompletionTriggers, function (trigger) {
        trigger.addEventListener('click', function (event) {
            // todo: save result
            unloadCycle(cycleElement, function () {
                loadCycle(cycleElement);
            });
            event.preventDefault();
            event.stopPropagation();
        })
    });

}

function unloadCycle(cycleElement, callback) {
    cycleElement.querySelector('.revealable-card').classList.remove('revealable-card--loaded');
    window.setTimeout(callback, 200);
}

function loadCycle(cycleElement) {
    if (window.cyclesData.length === 0) {
        console.log('no cycles left');
    }
    var data = window.cyclesData.shift();
    cycleElement.innerHTML = Handlebars.templates['vocabularyCard.html'](data);
    window.setTimeout(function () {
        cycleElement.querySelector('.revealable-card').classList.add('revealable-card--loaded');
    }, 300);
    attachEventListeners(cycleElement);
}

// load cycles
ready(function () {
    var cycleElement = document.querySelector('[data-cycle]');
    loadCycle(cycleElement);
});
