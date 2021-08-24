<?php

// add more context to location data
$data_url = 'https://raw.githubusercontent.com/minhealthnz/nz-covid-data/main/locations-of-interest/august-2021/locations-of-interest.geojson';
date_default_timezone_set('Pacific/Auckland');

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $data_url);
$result = curl_exec($ch);
curl_close($ch);

$data = json_decode($result, true);

// init our data arrays
$features = [];
$days = [];
$updated = [];
// track the latest data update time
$data_updated = 0;
$data_update_time = "";
// look for duplicate locations
$locations = [];

foreach($data['features'] as $feature) {

    // the day added
    $added = 0;
    // manually added for the 21st Aug
    switch ($feature['properties']['id']) {
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
        case 'a0l4a0000004GJX':
        case 'a0l4a0000004GJ8':
        case 'a0l4a0000004G7C':
        case 'a0l4a0000004GK1':
        case 'a0l4a0000004GKL':
        case 'a0l4a0000004GKa':
        case 'a0l4a0000004GKk':
            $added = 21;
            break;
    }
    // check for new Added property
    // 2021-08-22 09:40:43
    if (isset($feature['properties']['Added']) && $feature['properties']['Added'] != "") {
        $add = date_create_from_format('j/m/Y G:i', $feature['properties']['Added']);
        if(!$add) {
            $add = date_create_from_format('Y-m-d H:i:s', $feature['properties']['Added']);
        }
        $added = $add->format('j');
        if($add->getTimestamp() > $data_updated) {
            $data_updated = $add->getTimestamp();
            $data_update_time = $add->format("g:i a, D j M");
        }
        // collect the number of days with updates
        if (!isset($updated[$added])) {
           $updated[$added] = ['day' => $add->format("j"), 'name' => $add->format("j D")];
        }
    }
    $feature['properties']['day_updated'] = (int)$added;

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
    //$feature['properties']['month'] = (int)$start->format("n");
    $feature['properties']['day_of_month'] = (int)$start->format("j");
    $feature['properties']['hour'] = (int)$start->format("G");

    if ($feature['properties']['hour'] < 12) {
        $feature['properties']['time_period'] = "morning";
    } elseif ($feature['properties']['hour'] < 18) {
        $feature['properties']['time_period'] = "afternoon";
    } else {
        $feature['properties']['time_period'] = "evening";
    }

    // manual location fix
    if ($feature['properties']['id'] == 'a0l4a0000004F9h'){
        // fix location of Bottany Down Countdown
        $feature['geometry']['coordinates'] = [174.91092161703608, -36.930528119701215];
    }

    // track locations in case of duplicates
    if (isset($locations[$feature['properties']['Location']])) {
        $locations[$feature['properties']['Location']][$feature['properties']['timestamp']] = [$feature['properties']['day'], $feature['properties']['time']];
    } else {
        $locations[$feature['properties']['Location']] = [
            $feature['properties']['timestamp'] => [$feature['properties']['day'], $feature['properties']['time']]
        ];
    }

    // remove a bunch of fields we don't use in the front end
    unset($feature['properties']['id']);
    unset($feature['properties']['Advice']);
    unset($feature['properties']['Start']);
    unset($feature['properties']['End']);
    unset($feature['properties']['Added']);
    unset($feature['properties']['hour']);
    unset($feature['properties']['timestamp']);

    // add the data
    $features[] = $feature;

    // collect stats on how many locations per day
    if(isset($days[$feature['properties']['day_of_month']])) {
        $days[$feature['properties']['day_of_month']]['count'] ++;
    }else{
        $days[$feature['properties']['day_of_month']] = [
            'day' => $feature['properties']['day_of_month'],
            'name' => $start->format("j D"),
            'count' => 1
        ];
    }

}

// now update all features with merged visits
foreach ($features as $key => $feature) {
    if (isset($locations[$feature['properties']['Location']])) {
        // sort visits by timestamp
        $visits = $locations[$feature['properties']['Location']];
        ksort($visits);
        $features[$key]['properties']['visits'] = array_values($visits);
    }
}

// sort days with updates
ksort($updated);
$updated = array_values($updated);

$json = json_encode([
    'type' => 'FeatureCollection',
    'name' => 'locations-of-interest',
    'features' => $features,
    'days' => $days,
    'updated' => $updated,
    'data_update_time' => $data_update_time
]);

file_put_contents('data.json', $json);

echo "data.json saved, last updated at " . $data_update_time;
