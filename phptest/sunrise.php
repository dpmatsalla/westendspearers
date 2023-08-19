<?php

$api_url = "https://api.sunrise-sunset.org/json?lat=-27.4698&lng=153.0251&date=2023-08-20&formatted=0"; // Brisbane's latitude and longitude
$response = file_get_contents($api_url);
$data = json_decode($response, true);

if ($data['status'] === "OK") {
    $timestamp = strtotime($data['results']['sunrise']) + 10*3600;
    $sunrise = new DateTime("@$timestamp");
    $formattedSunrise = $sunrise->format('H:i');
}
echo "Timestamp: " . $timestamp . "<br>";
echo "Converted DateTime in UTC+10: " . $formattedSunrise;
?>