<?php

//Get min temperature next two days -- also get wind data 
$forecast = file('https://api.open-meteo.com/v1/forecast?latitude=-27.4679&longitude=153.0281&hourly=temperature_2m,precipitation_probability,windspeed_10m,winddirection_10m&timezone=Australia%2FSydney');

?>

<html>
<body>
<h2>Forecast</h2>
<pre id="test1"></pre>
<pre id="test2"></pre>
<pre id="test3"></pre>

<script>

<?php
print_r("var forecastRaw = '".$forecast[0]."';");
?>
const forecast = JSON.parse(forecastRaw);

function i5am(t) {
    //find where 5am is in the forecast array
    var i5am=0;
    while(new Date(forecast.hourly.time[i5am]).getTime() < t && i5am < forecast.hourly.time.length) {
        i5am++;
    }
    if (i5am == forecast.hourly.time.length) {
        i5am = 0;
    }
    return i5am;
}

    const zeroPad = (num, places) => String(num).padStart(places, '0');
    const day = 24*3600*1000;
    const currentDate = new Date();
    const now = currentDate.getTime();

    // get next5am timestamp
    let nextTime = new Date(now);
    nextTime.setUTCHours(19,0,0,0); //7pm UTC = 5am Brisbane
    let next5am = nextTime.getTime();
    if (next5am <= now) {
        next5am += day;
    }
    
    let t = now-day;
    document.getElementById("test1").innerHTML = new Date(t)+ ', ' + i5am(t) + ', ' + forecast.hourly.time.length;
    t = now;
    document.getElementById("test2").innerHTML = new Date(t)+ ', ' + i5am(t) + ', ' + forecast.hourly.time.length;
    t = now+7*day;
    document.getElementById("test3").innerHTML = new Date(t)+ ', ' + i5am(t) + ', ' + forecast.hourly.time.length;

//    const temp24h = Math.round(forecast.hourly.temperature_2m[i5am]) + 'ºC';
//    const temp48h = Math.round(forecast.hourly.temperature_2m[i5am+24]) + 'ºC';
//    const winds24h = zeroPad(Math.round(forecast.hourly.winddirection_10m[i5am]/10)*10, 3) + 'º ' + Math.round(forecast.hourly.windspeed_10m[i5am]) + 'kts';
//    const winds48h = zeroPad(Math.round(forecast.hourly.winddirection_10m[i5am+24]/10)*10, 3) + 'º ' + Math.round(forecast.hourly.windspeed_10m[i5am+24]) + 'kts';
    
    
//    document.getElementById("test2").innerHTML += i5am + "," + new Date(d) + ", " + temp24h + ", " + winds24h;

</script>

</body>    
</html>
