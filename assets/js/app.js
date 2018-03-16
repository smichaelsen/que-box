require('../../node_modules/pure/libs/pure.js');
require('../css/app.scss');
require('../images/paper.png');

function ready(fn) {
    if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading") {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}

function getJson(url, onsuccess, onerror) {
    var request = new XMLHttpRequest();
    request.open('GET', url, true);
    request.onload = function() {
        if (request.status >= 200 && request.status < 400) {
            var data = JSON.parse(request.responseText);
            onsuccess(data);
        } else {
            onerror();
        }
    };
    request.onerror = function() {
        onerror();
    };
    request.send();
}

// init revealable cards
ready(function () {
    var revealableCards = document.querySelectorAll('[data-revealable]');
    Array.prototype.forEach.call(revealableCards, function (card) {
        var toggle = card.querySelector('[data-reveal]');
        toggle.addEventListener('click', function (event) {
            card.classList.add('revealed');
            event.preventDefault();
            event.stopPropagation();
        });
    });
});

// completing cycles
ready(function () {
    var loadCycle = function(cycleElement) {
        getJson(
            cycleElement.getAttribute('data-cycle-load-url'),
            function(data) {
            },
            function () {
            }
        );
    };
    var cycles = document.querySelectorAll('[data-cycle]');
    Array.prototype.forEach.call(cycles, function (cycleElement) {
        var toggle = card.querySelector('[data-reveal]');
        toggle.addEventListener('click', function (event) {
            card.classList.add('revealed');
            event.preventDefault();
            event.stopPropagation();
        });
    });
});
