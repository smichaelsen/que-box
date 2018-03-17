(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['vocabularyCard.tmpl'] = template({"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : (container.nullContext || {}), alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "<div class=\"revealable-card vocabulary-card\">\n    <div class=\"revealable-card__flipper\">\n        <div class=\"revealable-card__front vocabulary-card__face\">\n            <p class=\"vocabulary-card__question\">"
    + alias4(((helper = (helper = helpers.question || (depth0 != null ? depth0.question : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"question","hash":{},"data":data}) : helper)))
    + "</p>\n            <a data-reveal href=\"#\">Lösung zeigen</a>\n        </div>\n        <div class=\"revealable-card__back vocabulary-card__face\">\n            <p class=\"vocabulary-card__answer\">"
    + alias4(((helper = (helper = helpers.answer || (depth0 != null ? depth0.answer : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"answer","hash":{},"data":data}) : helper)))
    + "</p>\n            <p>\n                <a class=\"btn btn--success\" data-cycle-success>Richtig</a>\n                <a class=\"btn btn--failure\" data-cycle-failure>Falsch</a>\n            </p>\n        </div>\n    </div>\n</div>";
},"useData":true});
})();