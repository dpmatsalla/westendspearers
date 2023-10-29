<?php
// http://reg.bom.gov.au/catalogue/data-feeds.shtml
// https://open-meteo.com/en/docs

$forecast = file('https://api.open-meteo.com/v1/forecast?latitude=-27.4679&longitude=153.0281&daily=temperature_2m_min&timezone=auto');

print_r("var forecast = '".$forecast[0]."';");

/*
$xml = simplexml_load_file("ftp://ftp.bom.gov.au/anon/gen/fwo/IDQ10095.xml");
$temp24h = $xml->forecast->area[2]->{'forecast-period'}[1] -> element[1];
$temp48h = $xml->forecast->area[2]->{'forecast-period'}[2] -> element[1];

echo $temp24h.', '.$temp48h;

/*
echo $a -> attributes() -> {'start-time-local'} . ', ' . $a -> attributes() -> {'end-time-local'} . ', ' . $a -> element[1] . ',   ';
echo $b -> attributes() -> {'start-time-local'} . ', ' . $b -> attributes() -> {'end-time-local'} . ', ' . $b -> element[1];
if ($xml) {
    // Retrieve and display the contents of various elements
    $location = $xml->forecast->area->attributes()->description;
    $forecastDate = $xml->forecast->issueTime->attributes()->localTime;
    $maxTemperature = $xml->forecast->forecastPeriod[0]->element[1]->attributes()->max;
    $minTemperature = $xml->forecast->forecastPeriod[0]->element[2]->attributes()->min;
    $rainfall = $xml->forecast->forecastPeriod[0]->text[2];

    echo "Location: $location<br>";
    echo "Forecast Date: $forecastDate<br>";
    echo "Max Temperature: $maxTemperature°C<br>";
    echo "Min Temperature: $minTemperature°C<br>";
    echo "Rainfall: $rainfall<br>";
} else {
    echo "Failed to load XML.";
}
*/
?>
