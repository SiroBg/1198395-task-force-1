ymaps.ready(init);

function init() {
    var map = $('#map');
    var myMap = new ymaps.Map("map", {
        center: [map.data('lat'), map.data('long')],
        zoom: 17
    });
    var myPlacemark = new ymaps.Placemark([map.data('lat'), map.data('long')], {
        iconContent: 'Задание здесь!'
    }, {
        preset: 'islands#redStretchyIcon'
    });
    myMap.geoObjects.add(myPlacemark);
}