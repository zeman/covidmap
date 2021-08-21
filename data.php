<?php

// add more context to location data
// https://github.com/minhealthnz/nz-covid-data/blob/main/locations-of-interest/august-2021/locations-of-interest.geojson
date_default_timezone_set('Pacific/Auckland');
$json = file_get_contents("data/locations-of-interest.geojson");
$data = json_decode($json, true);

$features = [];
$days = [];
foreach($data['features'] as $feature) {
    $start = date_create_from_format('d/m/Y, g:i a', $feature['properties']['Start']);
    $feature['properties']['timestamp'] = $start->getTimestamp();
    $feature['properties']['month'] = (int)$start->format("n");
    $feature['properties']['day_of_month'] = (int)$start->format("j");
    $feature['properties']['hour'] = (int)$start->format("G");
    $features[] = $feature;
    if(isset($days[$feature['properties']['day_of_month']])) {
        $days[$feature['properties']['day_of_month']]['count'] ++;
    }else{
        $days[$feature['properties']['day_of_month']] = [
            'name' => $start->format("j D"),
            'count' => 1
        ];
    }
    //print_r($feature);
}

echo json_encode([
    'type' => 'FeatureCollection',
    'name' => 'locations-of-interest',
    'features' => $features,
    'days' => $days
]);


