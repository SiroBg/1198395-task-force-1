ymaps.ready(function () {
    ymaps.geocode($('#task-location').data('user-city'), {results: 1}).then(function (res) {
        var firstGeoObject = res.geoObjects.get(0),
            bounds = firstGeoObject.properties.get('boundedBy');

        var suggestView = new ymaps.SuggestView('task-location', {
            boundedBy: bounds,
            strictBounds: true
        });
        suggestView.events.add('select', function (e) {
            var address = e.get('item').value;
            $('#yandex-suggest').val(address);
            ymaps.geocode(address).then(function (res) {
                var firstGeoObject = res.geoObjects.get(0);
                var coords = firstGeoObject.geometry.getCoordinates();
                $('#task-lat').val(coords[0]);
                $('#task-long').val(coords[1]);
                $('#task-city-id').val($('#task-location').data('user-city-id'));
            });
        });
    });
});
