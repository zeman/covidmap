<?php
// get the json so we can use it in php as well
$data = json_decode(file_get_contents('data/data.json'), true);
$days = $data['days'];
ksort($days);
// highest count
$day_max = 0;
foreach($days as $day){
    if ($day['count'] > $day_max){
        $day_max = $day['count'];
    }
}
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
            padding: 20px;
        }

        .popup__location{
            font-size: 12px;
        }

        #map {
            position: absolute;
            top: 45px;
            bottom: 160px;
            width: 100%;
            z-index: 1;
        }

        .filters {
            box-sizing: border-box;
            position: absolute;
            width: 100%;
            padding: 0 10px;
            bottom: 10px;
            z-index: 2;
        }
        @media (max-width: 768px) {
            .filters {
                bottom: 133px;
            }
        }

        .locations {
            box-sizing: border-box;
            margin-bottom: 10px;
            width: 100%;
            height: 40px;
            display: grid;
            grid-template-columns: 80px 1fr 1fr 1fr 1fr;
            grid-gap: 5px
        }
        @media (max-width: 768px) {
            .locations {
                grid-template-columns: 1fr 1fr 1fr 1fr;
            }
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
            margin-bottom: 10px;
            width: 100%;
            height: 40px;
            display: grid;
            grid-template-columns: 80px 1fr 1fr 1fr;
            grid-gap: 5px
        }
        @media (max-width: 768px) {
            .added {
                grid-template-columns: 1fr 1fr 1fr 1fr;
            }
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
            width: 100%;
            height: 40px;
            background-color: white;
            z-index: 2;
        }
        .days {
            display: grid;
            grid-template-columns: 80px repeat(<?= count($days)+1 ?>, 1fr);
            grid-gap: 5px;
        }
        @media (max-width: 768px){
            #map {
                bottom: 285px;
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
            padding: 10px 5px;
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
        .color_none {
            background-color: #e0e0e0
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
            color: #fff;
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
        .nav {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .nav h1 {
            font-size: 22px;
            margin: 0;
            padding: 0;
        }
        @media (max-width: 768px){
            .nav {
                grid-template-columns: 160px 1fr;
            }
            .nav h1 {
                font-size: 18px;
            }
        }
        @media (max-width: 320px){
            .nav {
                grid-template-columns: 140px 1fr;
            }
            .nav h1 {
                font-size: 15px;
            }
            .links {
                font-size: 12px;
            }
        }
        .title {
            padding: 10px 0 0 10px;
        }
        .links {
            text-align: right;
            padding: 12px 10px 0 0;
        }
        .links a {
            color: #000000;
        }
        @media (max-width: 768px) {
            .mobile_hide {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="nav">
        <div class="title"><h1>COVID Map NZ</h1></div>
        <div class="links"><a href="https://www.health.govt.nz/our-work/diseases-and-conditions/covid-19-novel-coronavirus/covid-19-health-advice-public/contact-tracing-covid-19/covid-19-contact-tracing-locations-interest/covid-19-contact-tracing-locations-interest-map" target="_blank" rel="nofollow">Updated <?= $data['data_update_time'] ?></a></div>
    </div>
    <div id='map'></div>
    <div class="filters">
        <div class="added">
            <div class="label">Added:</div>
            <div class="add add--active" data-add="0">All</div>
            <div class="add" data-add="21">21 Sat</div>
            <div class="add" data-add="22">22 Sun</div>
        </div>
        <div class="locations">
            <div class="label">Location:</div>
            <div class="location location--active" data-loc="all">All</div>
            <div class="location" data-loc="auck">Auckland</div>
            <div class="location mobile_hide" data-loc="coro">Coromandel</div>
            <div class="location" data-loc="welly">Wellington</div>
        </div>
        <div id="time" class="days">
            <div class="label">Day:</div>
            <div class="day day--active color_none" data-day="0">All</div>
            <?php
            foreach ($days as $day => $value) {
                $color = "color_" .  round($value['count']/($day_max/5));
                echo "<div class='day $color' data-day='$day'>{$value['name']}</div>";
            }
            ?>
        </div>
    </div>
    <script>

        mapboxgl.accessToken = 'pk.eyJ1IjoiemVtYW4iLCJhIjoiY2tzbThnYno0MzF1NTJ6bnEyOW95YWI3NSJ9.qUnc5pP_7w2aN9Y2lfKUqw';

        var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/light-v10',
            center: [174.763336, -38.848461],
            zoom: 6
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

            let visits = "";
            marker.properties.visits.forEach(visit => {
                visit.forEach(line => {
                   visits += line + '<br>';
                });
                visits += "<br>";
            });
            // make a marker for each feature and add it to the map
            new mapboxgl.Marker(el)
                .setLngLat(marker.geometry.coordinates)
                .setPopup(
                    new mapboxgl.Popup({ offset: 25 }) // add popups
                        .setHTML(
                            '<h3>' + marker.properties.Event + '</h3>' +
                            '<div class="popup__time">' + visits + '</div>' +
                            '<div class="popup__location">' + marker.properties.Location + '</div>'
                        )
                )
                .addTo(map);
        });

        flyTo('all');

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
                    flyTo('City', 'Wellington');
                } else if (e.target.dataset.loc === 'coro') {
                    flyTo('City', 'Coromandel');
                } else if (e.target.dataset.loc === 'auck') {
                    flyTo('City', 'Auckland');
                } else if (e.target.dataset.loc === 'all') {
                    flyTo('all');
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
                    map.setFilter('locations', ['==', ['number', ['get', 'added_day']], parseInt(e.target.dataset.add)]);
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

        function flyTo(type, value) {
            var bounds = new mapboxgl.LngLatBounds();
            geojson.features.forEach(function(feature) {
                if (type === 'all') {
                    bounds.extend(feature.geometry.coordinates);
                }else {
                    if (feature.properties[type] === value) {
                        bounds.extend(feature.geometry.coordinates);
                    }
                }
            });
            map.fitBounds(bounds, { padding: 50 });
        }

    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-SEY4HB06ZB"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-SEY4HB06ZB');
    </script>
</body>
</html>