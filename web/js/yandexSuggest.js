ymaps.ready(function () {
    var userCity = $('#task-location').data('user-city');
    ymaps.geocode(userCity, {results: 1}).then(function (res) {
        var firstGeoObject = res.geoObjects.get(0),
            bounds = firstGeoObject.properties.get('boundedBy');
        var suggestView = new ymaps.SuggestView('task-location', {
            provider: {
                suggest: function (request, options) {
                    var customQuery = userCity + ", " + request;
                    return ymaps.suggest(customQuery, {provider: 'yandex#map', boundedBy: bounds, strictBounds: true,});
                }
            },
        });
    });
});
