<?php
date_default_timezone_set('Pacific/Auckland');
// get the json so we can use it in php as well
$data = json_decode(file_get_contents('data.json'), true);
$days = $data['days'];
ksort($days);
$days = array_values($days);
// highest count
$day_max = 0;
foreach($days as $day){
    if ($day['count'] > $day_max){
        $day_max = $day['count'];
    }
}
// check if latest update is today and replace label if it is
$updated_label = $data['updated'][count($data['updated'])-1]['name'];
if (date("j D") === $updated_label) {
    $updated_label = "Today";
}
$latest = 0;
$latest_update = $data['updated'][count($data['updated'])-1]['day'];
if (isset($_GET['latest'])) {
   $latest = 1;
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

        .map {
            position: absolute;
            top: 45px;
            bottom: 231px;
            width: 100%;
            z-index: 1;
        }

        .filters {
            box-sizing: border-box;
            position: absolute;
            width: 100%;
            padding: 0 20px;
            margin: 20px 0;
            bottom: 0;
            z-index: 2;
            background-color: #ffffff;
        }

        .locations {
            box-sizing: border-box;
            margin-bottom: 10px;
            width: 100%;
            height: 40px;
            display: grid;
            grid-template-columns: 90px 1fr 1fr 1fr 1fr;
        }
        @media (max-width: 768px) {
            .locations {
                grid-template-columns: 1fr 1fr 1fr 1fr;
            }
        }

        .locations, .times {
            box-sizing: border-box;
            width: 100%;
            height: 40px;
            display: grid;
            grid-template-columns: 90px 1fr 1fr 1fr 1fr;
        }
        @media (max-width: 768px) {
            .locations {
                grid-template-columns: 1fr 1fr 1fr 1fr;
            }
        }

        .added {
            box-sizing: border-box;
            margin-bottom: 10px;
            width: 100%;
            height: 40px;
            display: grid;
            grid-template-columns: 90px 1fr 1fr 1fr;
        }
        @media (max-width: 768px) {
            .added {
                grid-template-columns: 1fr 1fr 1fr 1fr;
            }
        }

        .days {
            display: grid;
            grid-template-columns: 90px repeat(<?= count($days)+1 ?>, 1fr);
            margin-bottom: 10px;
        }
        @media (max-width: 768px){
            .filters {
                position: relative;
            }
            .map {
                position: relative;
                top: auto;
                bottom: auto;
                height: 400px;
            }
            .days {
                grid-template-columns: 1fr 1fr 1fr 1fr;
            }
        }
        .marker {
            background-color: rgba(171,7,7,0.4);
            width: 16px;
            height: 16px;
            border-radius: 8px;
        }
        .marker:hover {
            background-color: #000000;
            cursor: pointer;
        }
        .mapboxgl-popup-close-button{
            outline: 0;
        }
        .label {
            padding: 10px 0 0 10px;
        }
        .nav {
            display: grid;
            height: 40px;
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
            padding: 10px 0 0 20px;
        }
        @media (max-width: 768px) {
            .title {
                padding: 10px 0 0 10px;
            }
        }
        .links {
            text-align: right;
            padding: 12px 20px 0 0;
        }
        @media (max-width: 768px) {
            .links {
                padding: 12px 10px 0 0;
            }
        }
        .links a {
            color: #000000;
        }
        @media (max-width: 768px) {
            .mobile_hide {
                display: none;
            }
        }
        .toggle {
            outline: solid 1px #cccccc;
            padding-top: 10px;
            text-align: center;
            background-color: #ffffff;
            cursor: pointer;
            height: 30px;
        }
        .toggle:hover {
            background-color: #ececec;
        }
        @media (max-width: 768px) {
            .toggle:hover {
                background-color: #d8d8d8;
            }
        }
        .toggle:focus, .toggle:active {
            background-color: #d8d8d8;
        }
        .toggle--first {
            border-bottom-left-radius: 5px;
            border-top-left-radius: 5px;
            outline: none;
            border-top: solid 1px #cccccc;
            border-bottom: solid 1px #cccccc;
            border-left: solid 1px #cccccc;
            margin-top: -1px;
        }
        .toggle--last {
            border-bottom-right-radius: 5px;
            border-top-right-radius: 5px;
            outline: none;
            border: solid 1px #cccccc;
            margin-top: -1px;
        }
        .toggle--active {
            background-color: #d8d8d8;
        }

        .mobile {
            display: none;
        }
        @media (max-width: 768px) {
            .mobile {
                display: block;
            }
            .desktop {
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
    <div id="map" class='map'></div>
    <div class="filters">
        <div class="added">
            <div class="label">Updated</div>
            <div class="update toggle toggle--first <?php if($latest === 0){ echo "toggle--active"; } ?>" data-update="0">All</div>
            <div class="update toggle" data-update="<?= $data['updated'][count($data['updated'])-2]['name'] ?>"><?= $data['updated'][count($data['updated'])-2]['name'] ?></div>
            <div class="update toggle toggle--last <?php if($latest === 1){ echo "toggle--active"; } ?>" data-update="<?= $data['updated'][count($data['updated'])-1]['day'] ?>"><?= $updated_label ?></div>
        </div>
        <div class="locations">
            <div class="label">Location</div>
            <div class="location toggle toggle--first toggle--active" data-loc="all">All</div>
            <div class="location toggle" data-loc="auck">Auckland</div>
            <div class="location toggle mobile_hide" data-loc="coro">Coromandel</div>
            <div class="location toggle toggle--last" data-loc="welly">Wellington</div>
        </div>
        <div class="days">
            <div class="label">Day</div>
            <div class="day toggle toggle--first toggle--active" data-day="0">All</div>
            <?php
            foreach ($days as $value) {
                $class = "";
                if ($value['name'] === $days[count($days)-1]['name']) {
                   $class = " toggle--last";
                }
                echo "<div class='day toggle{$class}' data-day='{$value['day']}'>{$value['name']}</div>";
            }
            ?>
        </div>
        <div class="times">
            <div class="label">Time</div>
            <div class="time toggle toggle--first toggle--active" data-time="all">All</div>
            <div class="time toggle" data-time="morning">
                <div class="desktop">Morning</div>
                <div class="mobile">AM</div>
            </div>
            <div class="time toggle" data-time="afternoon">
                <span class="desktop">Afternoon</span>
                <span class="mobile">PM</span>
            </div>
            <div class="time toggle toggle--last" data-time="evening">
                <span class="desktop">Evening</span>
                <span class="mobile">EVE</span>
            </div>
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

        var geojson = <?= file_get_contents('data.json'); ?>

        map.addControl(new mapboxgl.NavigationControl());

        // markers saved here
        var currentMarkers=[];
        var filter_updated = 0;
        var filter_location = 'all';
        var filter_day = 0;
        var filter_time = 'all';
        var latest = <?= $latest ?>;
        var latest_update = <?= $latest_update ?>;

        if(latest === 1){
            filter_updated = latest_update;
            flyTo('latest');
        } else {
            flyTo('all');
        }
        updateMarkers();


        // add markers to map
        function updateMarkers() {

            // first remove all markers
            if (currentMarkers!==null) {
                for (let i = currentMarkers.length - 1; i >= 0; i--) {
                    currentMarkers[i].remove();
                }
            }

            let markers = geojson.features;

            if (filter_updated !== 0) {
                markers = markers.filter(x => x.properties.day_updated === filter_updated);
            }
            if (filter_day !== 0) {
                markers = markers.filter(x => x.properties.day_of_month === filter_day);
            }
            if (filter_time !== 'all') {
                markers = markers.filter(x => x.properties.time_period === filter_time);
            }

            // add markers and popup to the map
            markers.forEach(function (marker) {

                let el = document.createElement('div');
                el.className = 'marker';

                // create html for mutiple visits
                let visits = "";
                marker.properties.visits.forEach(visit => {
                    visit.forEach(line => {
                        visits += line + '<br>';
                    });
                    visits += "<br>";
                });

                // make a marker for each feature and add it to the map
                let new_marker = new mapboxgl.Marker(el)
                    .setLngLat(marker.geometry.coordinates)
                    .setPopup(
                        new mapboxgl.Popup({offset: 12}) // add popups
                            .setHTML(
                                '<h3>' + marker.properties.Event + '</h3>' +
                                '<div class="popup__time">' + visits + '</div>' +
                                '<div class="popup__location">' + marker.properties.Location + '</div>'
                            )
                    )
                    .addTo(map);

                // keep a track of the current markers
                currentMarkers.push(new_marker);

            });

        }

        document.querySelectorAll('.update').forEach(item => {
            item.addEventListener('click', e => {
                filter_updated = parseInt(e.target.dataset.update);
                updateMarkers();
                // zoom in if today/latest has been clicked
                if (filter_updated === latest_update) {
                    flyTo('latest');
                }
                var updated = document.getElementsByClassName("update");
                for (var i = 0; i < updated.length; i++) {
                    updated[i].classList.remove("toggle--active");
                }
                e.target.classList.add('toggle--active');
            })
        });

        document.querySelectorAll('.day').forEach(item => {
            item.addEventListener('click', e => {
                filter_day = parseInt(e.target.dataset.day);
                updateMarkers();
                var days = document.getElementsByClassName("day");
                for (var i = 0; i < days.length; i++) {
                    days[i].classList.remove("toggle--active");
                }
                e.target.classList.add('toggle--active');
            })
        });

        document.querySelectorAll('.location').forEach(item => {
            item.addEventListener('click', e => {
                filter_location = e.target.dataset.loc;
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
                    locations[i].classList.remove("toggle--active");
                }
                e.target.classList.add('toggle--active');
            });
        });

        document.querySelectorAll('.time').forEach(item => {
            item.addEventListener('click', e => {
                console.log("click time");
                filter_time = e.currentTarget.dataset.time;
                updateMarkers();
                var times = document.getElementsByClassName("time");
                for (var i = 0; i < times.length; i++) {
                    times[i].classList.remove("toggle--active");
                }
                e.currentTarget.classList.add('toggle--active');
            });
        });


        function flyTo(type, value) {
            var bounds = new mapboxgl.LngLatBounds();
            geojson.features.forEach(function(feature) {
                if (type === 'all') {
                    bounds.extend(feature.geometry.coordinates);
                } else if (type === 'latest') {
                    if (feature.properties.day_updated === filter_updated) {
                        bounds.extend(feature.geometry.coordinates);
                    }
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