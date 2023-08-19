<?php
date_default_timezone_set('Australia/Brisbane');

//get current winds and temperature
$metar = file('http://tgftp.nws.noaa.gov/data/observations/metar/stations/YBBN.TXT')[1];
preg_match('/(\d{3})(\d{2})(G\d{2})?KT/', $metar, $matches);
$windDirNow = $matches[1];
$windSpeedNow = $matches[2];
preg_match('/(\d{2})\/(\d{2}|M\d{2})/', $metar, $matches);
$temp = $matches[1];

$taf = file('https://tgftp.nws.noaa.gov/data/forecasts/taf/stations/YBBN.TXT', FILE_IGNORE_NEW_LINES);
$tafArray = array();
$r = 1;

// Extract wind info from the second line
while (!preg_match('/(\d{3})(\d{2})(G\d{2})?KT/', $taf[$r], $matches) AND $r<3) { $r += 1; }
$tafArray[] = array(
    'timestamp' => time(),
    'wind_direction' => $matches[1],
    'wind_speed' => $matches[2]
);
// Extract wind info from the remaining "FM" lines
foreach ($taf as $line) {
    $line = trim($line);
    if (strpos($line, 'FM') !== 0) { continue; }
    if (preg_match('/FM(\d{6}) (\d{3})(\d{2})(G\d{2})?KT/', $line, $matches)) {
        $day = intval(substr($matches[1], 0, 2));
        $hour = intval(substr($matches[1], 2, 2));
        $minute = intval(substr($matches[1], 4, 2));
        $currentYear = date('Y');
        $currentMonth = date('m');
        if ($day < date('d')) { $currentMonth += 1; }
        $timestamp = strtotime("$currentYear-$currentMonth-$day $hour:$minute:00 UTC");
        $tafArray[] = array(
            'timestamp' => $timestamp,
            'wind_direction' => $matches[2],
            'wind_speed' => $matches[3]
        );
    }
}

print_r($taf);
echo json_encode($tafArray);
?>
