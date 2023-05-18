<?php

require_once '../vendor/autoload.php';

use Msankhala\ZipcodebasePhp\ZipcodebaseClient;

$apiKey = 'YOUR_API_KEY';
$apiBase = 'https://app.zipcodebase.com/api/v1/';
$client = new ZipcodebaseClient($apiKey, $apiBase);

// Use Case 1: Get location information by postal code
$postalCodeInfo = $client->getPostalCodeInfo('10001');
print_r($postalCodeInfo);

// Use Case 2: Calculate distance between postal codes
$distance = $client->calculateDistance('10001', '20001');
print_r($distance);

// Use Case 3: Get postal codes within a radius
$postalCodesWithinRadius = $client->getPostalCodesWithinRadius('10001', 10);
print_r($postalCodesWithinRadius);

// Use Case 4: Get postal codes within a certain distance
$postalCodesWithinDistance = $client->getPostalCodesWithinDistance('10001,10005,10006', 10);
print_r($postalCodesWithinDistance);

// Use Case 5: Get postal codes by city
$postalCodesByCity = $client->getPostalCodesByCity('Bikaner', 'IN');
print_r($postalCodesByCity);

// Use Case 6: Get postal codes by state
$postalCodesByState = $client->getPostalCodesByState('Rajasthan', 'IN', 200);
print_r($postalCodesByState);

// Use Case 7: Get the list of states
$states = $client->getStatesByCountry('IN');
print_r($states);

// Use Case 8: Get the remaining credits
$credits = $client->getCredits();
print_r($credits);
