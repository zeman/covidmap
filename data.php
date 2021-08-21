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
        'id' => '2',
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
        'id' => '3',
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
        'id' => '4',
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
$data['features'][] = [
    'type' => 'Feature',
    'properties' => [
        'id' => '5',
        'added' => 21,
        'Event' => 'AUT City campus 11/08/2021',
        'Location' => '55 Wellesley Street East, Auckland Central, Auckland 1010',
        'City' => '',
        'Start' => '11/08/2021, 9:30 am',
        'End' => '11/08/2021, 8:30 pm',
        'Information' => '3-5pm WA220 COMP501/52 Computing Technology in Society- Isolate at home for 14 days from date of last exposure. Test immediately, and on days 5 & 12 after last exposure. Call Healthline for what to do next.
5-8pm WG707/708 DIGD507/51 Mahi tahi: Collaborative Practices- Isolate at home for 14 days from date of last exposure. Test immediately, and on days 5 & 12 after last exposure. Call Healthline for what to do next.
All other ares of City Campus - Self-monitor for COVID-19 symptoms for 14 days. If symptoms develop, get a test and stay at home until you get a negative test result AND until 24 hours after symptoms resolve.'
    ],
    'geometry' => [
        'type' => 'Point',
        'coordinates' => [174.76521561657182, -36.853330787471]
    ]
];
$data['features'][] = [
    'type' => 'Feature',
    'properties' => [
        'id' => '6',
        'added' => 21,
        'Event' => 'Ulutoa and Sons 16/08/2021',
        'Location' => '87 Mangere Road, Otahuhu, Auckland',
        'City' => '',
        'Start' => '16/08/2021, 9:30 am',
        'End' => '16/08/2021, 9:45 am',
        'Information' => 'Staff and Patrons: Isolate at home for 14 days from date of last exposure. Test immediately, and on days 5 & 12 after last exposure. Call Healthline for what to do next.'
    ],
    'geometry' => [
        'type' => 'Point',
        'coordinates' => [174.83731521657342, -36.951671592979764]
    ]
];
$data['features'][] = [
    'type' => 'Feature',
    'properties' => [
        'id' => '7',
        'added' => 21,
        'Event' => 'Pinati\'s Keke Pua\'a 16/08/2021',
        'Location' => '19A Queen Street, Otahuhu, Auckland, 1062',
        'City' => '',
        'Start' => '16/08/2021, 10:00 am',
        'End' => '16/08/2021, 10:30 am',
        'Information' => 'Staff and Patrons: Isolate at home for 14 days from date of last exposure. Test immediately, and on days 5 & 12 after last exposure. Call Healthline for what to do next.'
    ],
    'geometry' => [
        'type' => 'Point',
        'coordinates' => [174.84152571657327, -36.94570029264488]
    ]
];

// init our data arrays
$features = [];
$days = [];

foreach($data['features'] as $feature) {

    // add the day added
    $added = 0;
    switch ($feature['properties']['id']) {
        case 1:
        case 2:
        case 3:
        case 4:
        case 5:
        case 6:
        case 7:
        case 'a0l4a0000004GTc':
        case 'a0l4a0000004GUL':
        case 'a0l4a0000004GUk':
        case 'a0l4a0000004GXt':
        case 'a0l4a0000004Etj':
        case 'a0l4a0000004FKf':
        case 'a0l4a0000004GLs':
        case 'a0l4a0000004FJc':
        case 'a0l4a0000004FXo':
        case 'a0l4a0000004GE3':
        case 'a0l4a0000004GCb':
            $added = 21;
            break;
    }
    $feature['properties']['added'] = $added;

    // remove the date from the event name
    $feature['properties']['Event'] = substr($feature['properties']['Event'], 0, strrpos($feature['properties']['Event'], " "));

    // convert into DateTime
    $start = date_create_from_format('d/m/Y, g:i a', $feature['properties']['Start']);
    $end = date_create_from_format('d/m/Y, g:i a', $feature['properties']['End']);
    $feature['properties']['day'] = $start->format("D, j M Y");

    // Check if end is in the next day, and then format time
    if($start->format('j') != $end->format("j") ) {
        $feature['properties']['time'] = $start->format("D, g:i a") . " - " . $end->format("D, g:i a");
    } else {
        $feature['properties']['time'] = $start->format("g:i a") . " - " . $end->format("g:i a");
    }

    // Add metadata for filtering
    $feature['properties']['timestamp'] = $start->getTimestamp();
    $feature['properties']['month'] = (int)$start->format("n");
    $feature['properties']['day_of_month'] = (int)$start->format("j");
    $feature['properties']['hour'] = (int)$start->format("G");

    // manual location fix
    if ($feature['properties']['id'] == 'a0l4a0000004F9h'){
        // fix location of Bottany Down Countdown
        $feature['geometry']['coordinates'] = [174.91092161703608, -36.930528119701215];
    }

    // add the data
    $features[] = $feature;

    // collect stats on how many locations per day
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


