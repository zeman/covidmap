<?php
$days = json_decode('{"11":{"name":"11 Wed","count":8},"12":{"name":"12 Thu","count":11},"13":{"name":"13 Fri","count":30},"17":{"name":"17 Tue","count":20},"15":{"name":"15 Sun","count":30},"14":{"name":"14 Sat","count":31},"10":{"name":"10 Tue","count":8},"16":{"name":"16 Mon","count":17},"7":{"name":"7 Sat","count":8},"9":{"name":"9 Mon","count":4},"6":{"name":"6 Fri","count":2},"5":{"name":"5 Thu","count":3},"4":{"name":"4 Wed","count":3},"3":{"name":"3 Tue","count":2},"18":{"name":"18 Wed","count":3},"19":{"name":"19 Thu","count":2},"20":{"name":"20 Fri","count":1},"2":{"name":"2 Mon","count":1},"1":{"name":"1 Sun","count":1}}', true);
ksort($days);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>COVID Map NZ</title>
    <meta name="description" content="Filter COVID locations of interest on a map.">
    <meta property="og:title" content="COVID Map NZ">
    <meta property="og:description" content="Filter COVID locations of interest on a map.">
    <meta property="og:site_name" content="COVID Map NZ">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.covidmap.net.nz/">
    <meta property="og:image" content="https://www.covidmap.net.nz/map.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta name='viewport' content='width=device-width, initial-scale=1' />
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.4.1/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.4.1/mapbox-gl.css' rel='stylesheet' />
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, Sans-serif;
            font-size: 15px;
        }

        .mapboxgl-popup-content h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
        }

        .mapboxgl-popup-content{
            font-size: 14px;
        }

        .popup__location{
            margin-top: 10px;
            font-size: 12px;
        }

        #map {
            position: absolute;
            top: 0;
            bottom: 155px;
            width: 100%;
            z-index: 1;
        }

        .locations {
            box-sizing: border-box;
            padding-right: 10px;
            position: absolute;
            width: 100%;
            height: 40px;
            bottom: 55px;
            background-color: white;
            z-index: 3;
            display: grid;
            grid-template-columns: 80px 1fr 1fr 1fr;
            grid-gap: 5px
        }

        .location {
            padding-top: 10px;
            text-align: center;
            background-color: #e0e0e0;
            cursor: pointer;
        }

        .location--active {
            background-color: #000000;
            color: #ffffff;
        }

        .added {
            box-sizing: border-box;
            padding-right: 10px;
            position: absolute;
            width: 100%;
            height: 40px;
            bottom: 105px;
            background-color: white;
            z-index: 3;
            display: grid;
            grid-template-columns: 80px 1fr 1fr;
            grid-gap: 5px
        }

        .add {
            padding-top: 10px;
            text-align: center;
            background-color: #e0e0e0;
            cursor: pointer;
        }

        .add--active {
            background-color: #000000;
            color: #ffffff;
        }

        #time {
            box-sizing: border-box;
            padding-right: 10px;
            position: absolute;
            width: 100%;
            height: 40px;
            bottom: 5px;
            background-color: white;
            z-index: 2;
        }
        .days {
            display: grid;
            grid-template-columns: 80px repeat(<?= count($days)+1 ?>, 1fr);
            grid-gap: 5px;
        }
        @media (max-width: 450px){
            #map {
                bottom: 155px;
            }
            #time {
                height: 40px;
            }
            .days {
                grid-template-columns: 1fr 1fr 1fr 1fr;
            }
        }
        .day {
            cursor: pointer;
            padding: 10px 0;
            text-align: center;
            background-color: #ffffff;
        }
        .marker {
            background-color: #000000;
            width: 20px;
            height: 20px;
            border-radius: 10px;
            opacity: 0;
        }
        .mapboxgl-popup-close-button{
            outline: 0;
        }
        .color_0 {
            background-color: #fef0d9
        }
        .color_1 {
            background-color: #fef0d9
        }
        .color_2 {
            background-color: #fdcc8a
        }
        .color_3 {
            background-color: #fc8d59
        }
        .color_4 {
            background-color: #e34a33
        }
        .color_5 {
            color: #fff;
            background-color: #b30000
        }
        .day--active {
            background-color: #000000;
            color: white;
        }
        .label {
            padding: 10px 0 0 10px;
        }
    </style>
</head>
<body>
    <div id='map'></div>
    <div class="locations">
        <div class="label">Location:</div>
        <div class="location location--active" data-loc="auck">Auckland</div>
        <div class="location" data-loc="coro">Coromandel</div>
        <div class="location" data-loc="welly">Wellington</div>
    </div>
    <div id="time" class="days">
        <div class="label">Day:</div>
        <div class="day day--active" data-day="0">All</div>
        <?php
        foreach ($days as $day => $value) {
            $color = "color_" .  round($value['count']/6);
            echo "<div class='day $color' data-day='$day'>{$value['name']}</div>";
        }
        ?>
    </div>
    <div class="added">
        <div class="label">Added:</div>
        <div class="add add--active" data-add="0">All</div>
        <div class="add" data-add="21">Sat 21</div>
    </div>
    <script>

        mapboxgl.accessToken = 'pk.eyJ1IjoiemVtYW4iLCJhIjoiY2tzbThnYno0MzF1NTJ6bnEyOW95YWI3NSJ9.qUnc5pP_7w2aN9Y2lfKUqw';

        var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/light-v10',
            center: [174.763336, -36.848461],
            zoom: 10
        });

        var geojson = <?= file_get_contents('data/data.json'); ?>

        map.on('load', function() {
            map.addLayer({
                id: 'locations',
                type: 'circle',
                source: {
                    type: 'geojson',
                    data: geojson
                },
                'paint': {
                    'circle-color': 'rgba(171,7,7,0.5)'
                }
            });
        });

        map.addControl(new mapboxgl.NavigationControl());

        // add markers to map
        geojson.features.forEach(function (marker) {
// create a HTML element for each feature
            var el = document.createElement('div');
            el.className = 'marker';

// make a marker for each feature and add it to the map
            new mapboxgl.Marker(el)
                .setLngLat(marker.geometry.coordinates)
                .setPopup(
                    new mapboxgl.Popup({ offset: 25 }) // add popups
                        .setHTML(
                            '<h3>' + marker.properties.Event + '</h3>' +
                            '<div class="popup__day">' + marker.properties.day + '</div>' +
                            '<div class="popup__time">' + marker.properties.time + '</div>' +
                            '<div class="popup__location">' + marker.properties.Location + '</div>'
                        )
                )
                .addTo(map);
        });

        document.querySelectorAll('.day').forEach(item => {
            item.addEventListener('click', e => {
                if (e.target.dataset.day === '0') {
                    map.setFilter('locations', null);
                } else {
                    map.setFilter('locations', ['==', ['number', ['get', 'day_of_month']], parseInt(e.target.dataset.day)]);
                }
                var days = document.getElementsByClassName("day");
                for (var i = 0; i < days.length; i++) {
                    days[i].classList.remove("day--active");
                }
                e.target.classList.add('day--active');
                // can only have single filter at the mo
                var added = document.getElementsByClassName("add");
                for (var i = 0; i < added.length; i++) {
                    added[i].classList.remove("add--active");
                }
                document.querySelector('.add').classList.add('add--active');
            })
        });

        document.querySelectorAll('.location').forEach(item => {
            item.addEventListener('click', e => {
                if (e.target.dataset.loc === 'welly') {
                    map.flyTo({center:[174.7787,-41.1924], zoom: 10})
                } else if (e.target.dataset.loc === 'coro') {
                    map.flyTo({center:[175.4981,-36.7087], zoom: 11})
                } else if (e.target.dataset.loc === 'auck') {
                    map.flyTo({center:[174.763336, -36.848461], zoom: 10})
                }
                var locations = document.getElementsByClassName("location");
                for (var i = 0; i < locations.length; i++) {
                    locations[i].classList.remove("location--active");
                }
                e.target.classList.add('location--active');
            });
        });

        document.querySelectorAll('.add').forEach(item => {
            item.addEventListener('click', e => {
                if (e.target.dataset.add === '0') {
                    map.setFilter('locations', null);
                } else {
                    map.setFilter('locations', ['==', ['number', ['get', 'added']], parseInt(e.target.dataset.add)]);
                }
                var added = document.getElementsByClassName("add");
                for (var i = 0; i < added.length; i++) {
                    added[i].classList.remove("add--active");
                }
                e.target.classList.add('add--active');
                // can only have single filter at the mo
                var days = document.getElementsByClassName("day");
                for (var i = 0; i < days.length; i++) {
                    days[i].classList.remove("day--active");
                }
                document.querySelector('.day').classList.add('day--active');
            })
        });

    </script>
    <script>
        window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
        ga('create', 'G-SEY4HB06ZB', 'auto');
        ga('send', 'pageview');
    </script>
    <script async src='https://www.google-analytics.com/analytics.js'></script>
</body>
</html>