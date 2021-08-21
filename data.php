<?php

// add more context to location data
// https://github.com/minhealthnz/nz-covid-data/blob/main/locations-of-interest/august-2021/locations-of-interest.geojson
date_default_timezone_set('Pacific/Auckland');
$json = file_get_contents("data/locations-of-interest.geojson");
$data = json_decode($json, true);

// manually add a few missing locations
$data['features'][] = [
    'type' => 'Feature',
    'properties' => [
        'id' => '1',
        'Event' => 'BP Connect 16/08/2021',
        'Location' => '92-94 Bridge Street, Bulls, 4818',
        'City' => '',
        'Start' => '16/08/2021, 9:30 pm',
        'End' => '16/08/2021, 10:00 pm',
        'Information' => 'Staff and Patrons: Stay at home, test immediately and on or around day 5 after last exposure and continue to stay at home until you receive a negative day 5 test result. Call Healthline for what to do next.'
    ],
    'geometry' => [
        'type' => 'Point',
        'coordinates' => [175.38326241638129, -40.17724967939365]
    ]
];
$data['features'][] = [
    'type' => 'Feature',
    'properties' => [
        'id' => '1',
        'Event' => 'Waiouru Public Toilets State Highway 1 16/08/2021',
        'Location' => '15 State Highway 1, Waiouru, 4825',
        'City' => '',
        'Start' => '16/08/2021, 6:30 pm',
        'End' => '16/08/2021, 7:00 pm',
        'Information' => 'Staff and Patrons: Stay at home, test immediately and on or around day 5 after last exposure and continue to stay at home until you receive a negative day 5 test result. Call Healthline for what to do next.'
    ],
    'geometry' => [
        'type' => 'Point',
        'coordinates' => [175.66814923617642, -39.479606146809246]
    ]
];
$data['features'][] = [
    'type' => 'Feature',
    'properties' => [
        'id' => '1',
        'Event' => 'Z Petrol Station Waiouru 16/08/2021',
        'Location' => '11 State Highway 1, Waiouru, 4825',
        'City' => '',
        'Start' => '16/08/2021, 6:30 pm',
        'End' => '16/08/2021, 7:00 pm',
        'Information' => 'Staff and Patrons: Stay at home, test immediately and on or around day 5 after last exposure and continue to stay at home until you receive a negative day 5 test result. Call Healthline for what to do next.'
    ],
    'geometry' => [
        'type' => 'Point',
        'coordinates' => [175.66728161637042, -39.479578579485135]
    ]
];
$data['features'][] = [
    'type' => 'Feature',
    'properties' => [
        'id' => '1',
        'Event' => 'BP Tokoroa 16/08/2021',
        'Location' => '32 Main Road, Tokoroa, 3420',
        'City' => '',
        'Start' => '16/08/2021, 3:00 pm',
        'End' => '16/08/2021, 4:00 pm',
        'Information' => 'Staff and Patrons: Isolate at home for 14 days from date of last exposure. Test immediately, and on days 5 & 12 after last exposure. Call Healthline for what to do next.'
    ],
    'geometry' => [
        'type' => 'Point',
        'coordinates' => [175.86970251635108, -38.21555077968176]
    ]
];
$features = [];
$days = [];
foreach($data['features'] as $feature) {
    // remove the date from the event name
    $feature['properties']['Event'] = substr($feature['properties']['Event'], 0, strrpos($feature['properties']['Event'], " "));
    $start = date_create_from_format('d/m/Y, g:i a', $feature['properties']['Start']);
    $end = date_create_from_format('d/m/Y, g:i a', $feature['properties']['End']);
    $feature['properties']['day'] = $start->format("D, j M Y");
    // check if end is in the next day
    if($start->format('j') != $end->format("j") ) {
        $feature['properties']['time'] = $start->format("D, g:i a") . " - " . $end->format("D, g:i a");
    } else {
        $feature['properties']['time'] = $start->format("g:i a") . " - " . $end->format("g:i a");
    }
    $feature['properties']['timestamp'] = $start->getTimestamp();
    $feature['properties']['month'] = (int)$start->format("n");
    $feature['properties']['day_of_month'] = (int)$start->format("j");
    $feature['properties']['hour'] = (int)$start->format("G");
    if ($feature['properties']['id'] == 'a0l4a0000004F9h'){
        // fix location of Bottany Down Countdown
        $feature['geometry']['coordinates'] = [174.91092161703608, -36.930528119701215];
    }
    $features[] = $feature;
    if(isset($days[$feature['properties']['day_of_month']])) {
        $days[$feature['properties']['day_of_month']]['count'] ++;
    }else{
        $days[$feature['properties']['day_of_month']] = [
            'name' => $start->format("j D"),
            'count' => 1
        ];
    }
}

echo json_encode([
    'type' => 'FeatureCollection',
    'name' => 'locations-of-interest',
    'features' => $features,
    'days' => $days
]);


