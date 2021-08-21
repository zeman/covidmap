<?php
$days = json_decode('{"11":{"name":"11 Wed","count":8},"12":{"name":"12 Thu","count":11},"13":{"name":"13 Fri","count":30},"17":{"name":"17 Tue","count":20},"15":{"name":"15 Sun","count":29},"14":{"name":"14 Sat","count":31},"10":{"name":"10 Tue","count":8},"16":{"name":"16 Mon","count":17},"7":{"name":"7 Sat","count":8},"9":{"name":"9 Mon","count":4},"6":{"name":"6 Fri","count":2},"5":{"name":"5 Thu","count":3},"4":{"name":"4 Wed","count":3},"3":{"name":"3 Tue","count":2},"18":{"name":"18 Wed","count":3},"19":{"name":"19 Thu","count":2},"20":{"name":"20 Fri","count":1},"2":{"name":"2 Mon","count":1},"1":{"name":"1 Sun","count":1}}', true);
ksort($days);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>COVID Map</title>
    <meta name='viewport' content='width=device-width, initial-scale=1' />
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.4.1/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.4.1/mapbox-gl.css' rel='stylesheet' />
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, Sans-serif;
        }

        #map {
            position: absolute;
            top: 0;
            bottom: 50px;
            width: 100%;
            z-index: 1;
        }

        #time {
            position: absolute;
            width: 100%;
            height: 50px;
            bottom: 0;
            background-color: white;
            z-index: 2;
        }
        .days {
            display: grid;
            grid-template-columns: repeat(<?= count($days)+1 ?>, 1fr);
            grid-gap: 5px;
        }
        @media (max-width: 450px){
            #map {
                bottom: 100px;
            }
            #time {
                height: 100px;
            }
            .days {
                grid-template-columns: 1fr 1fr 1fr;
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
    </style>
</head>
<body>
    <div id='map'></div>
    <div id="time" class="days">
        <div class="day" data-day="0">All</div>
        <?php
        foreach ($days as $day => $value) {
            $color = "color_" .  round($value['count']/6);
            echo "<div class='day $color' data-day='$day'>{$value['name']}</div>";
        }
        ?>
    </div>
    <script>

        mapboxgl.accessToken = 'pk.eyJ1IjoiemVtYW4iLCJhIjoiY2tza3RwaHU5MDFrODJ2bzJ5Y2Y4bHRwNSJ9.Uj9faEe4FKVJTEQ8yhz3_Q';

        var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/light-v10',
            center: [174.763336, -36.848461],
            zoom: 11
        });

        var geojson = <?= file_get_contents('data/data.json'); ?>

        map.on('load', function() {
            map.addLayer({
                id: 'locations',
                type: 'circle',
                source: {
                    type: 'geojson',
                    data: 'data.php'
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
                            '<h3>' +
                            marker.properties.Event +
                            '</h3><p>' +
                            marker.properties.Location + '<br>Start:' + marker.properties.Start + '<br>End:' + marker.properties.End +
                            '</p>'
                        )
                )
                .addTo(map);
        });

        document.querySelectorAll('.day').forEach(item => {
            item.addEventListener('click', e => {
                if (e.target.dataset.day == 0) {
                    map.setFilter('locations', null);
                } else {
                    map.setFilter('locations', ['==', ['number', ['get', 'day_of_month']], parseInt(e.target.dataset.day)]);
                }
            })
        })

    </script>
</body>
</html>