<!--
include sunrise data in the graphic and in calendar (i.e. dark out?)
Weather data in JSON format:  http://reg.bom.gov.au/catalogue/data-feeds.shtml#obs-ind
Marine: http://reg.bom.gov.au/marine/
Data: http://reg.bom.gov.au/catalogue/data-feeds.shtml

References:
- https://ckc.clubexpress.com/#top
- https://www.data.qld.gov.au/dataset/brisbane-bar-tide-gauge-predicted-interval-data/resource/8a5a5e32-2ed4-4252-8c2a-074188556d5b
- http://www.bom.gov.au/australia/tides/print.php?aac=QLD_TP138&type=tide&date=01-08-2023&region=QLD&tz=Australia/Brisbane&tz_js=AEST&days=20
- http://www.bom.gov.au/australia/tides/scripts/getNextTides.php?aac=QLD_TP138&offset=false&tz=Australia%2FBrisbane 
- https://community.home-assistant.io/t/restful-sensor-json-help-australian-tide-predictions-bom/149663/19
-->

<?php
    //convert format 19/08/2023 22:00
    function str_to_timestamp($dateString) {
        $dateParts = explode(' ', $dateString);
        $date = $dateParts[0];
        $time = $dateParts[1];
        list($day, $month, $year) = explode('/', $date);
        list($hour, $minute) = explode(':', $time);
        $timestamp = mktime((int)$hour, (int)$minute, 0, (int)$month, (int)$day, (int)$year);
        return $timestamp;
    }
    
    date_default_timezone_set('Australia/Brisbane');
    
    //get current winds and temperature
    $metar = file('http://tgftp.nws.noaa.gov/data/observations/metar/stations/YBBN.TXT')[1];
    preg_match('/(\d{3})(\d{2})(G\d{2})?KT/', $metar, $matches);
    $windDirNow = $matches[1];
    $windSpeedNow = $matches[2];
    preg_match('/(\d{2})\/(\d{2}|M\d{2})/', $metar, $matches);
    $temp = $matches[1];
    
    //get forecast winds
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
    $json_winds = json_encode($tafArray);
    
    //Get Brisbane River Heights at St-Lucia
    $url = "http://www.bom.gov.au/fwo/IDQ65389/IDQ65389.540683.tbl.shtml";
    $options = array(
      'http'=>array(
        'method'=>"GET",
        'header'=>"Accept-language: en\r\n" .
                  "Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
                  "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n"
    ));
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    if ($response === false) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to fetch webpage content"]);
        exit;
    }
    $doc = new DOMDocument();
    @$doc->loadHTML($response);
    $table = $doc->getElementsByTagName('table')->item(0);
    if (!$table) {
        http_response_code(404);
        echo json_encode(["error" => "Table not found in the webpage"]);
        exit;
    }
    $table_data = [];
    $rows = $table->getElementsByTagName('tr');
    foreach ($rows as $row) {
        $row_data = [];
        $cells = $row->getElementsByTagName('td');
        foreach ($cells as $cell) {
            $row_data[] = $cell->nodeValue;
        }
        if (count($row_data) > 1) {
            $row_data[0] = str_to_timestamp($row_data[0]);
            $row_data[1] = $row_data[1];
            $table_data[] = $row_data;
        }
    }
    $json_river = json_encode($table_data);

?>


<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='keywords' content='West End, Spearers, kayak, canoe, surfski, Brisbane'>
    <meta name='description' content='West End Spearers Kayak Group'>
    <meta name='author' content='Devon Matsalla'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>West End Spearers Kayaking Club</title>
    <link rel='icon' type='image/x-icon' href='spearers.png'>
    <link rel='stylesheet' type='text/css' href='styles.css'>
    <link rel='stylesheet' href='https://unpkg.com/leaflet@1.7.1/dist/leaflet.css' />
    <script src='https://unpkg.com/leaflet@1.7.1/dist/leaflet.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/polyline-encoded@0.0.9/Polyline.encoded.min.js'></script>
    <script src="suncalc.js"></script>
    <style>
#banner {
    width: 100%;
    height: 200px;
    overflow: hidden;
    position: relative;
}
#photo-container {
    position: absolute;
    top: 0;
    right: 0;
    width: auto;
    height: 100%;
    white-space: nowrap;
    animation: pan 60s linear infinite;
}
#photo-container img {
    height: 100%;
    width: auto;
    max-width: 100%;
    object-fit: cover;
    display: inline-block;
}
@keyframes pan {
    0% {
      transform: translateX(50%);
    }
    100% {
      transform: translateX(-800%); /* Adjust this multiplier based on the number of photos */
    }
}
    </style>
</head>
<body>
    <header>
        <nav class='navigation'>
            <table style='width:100%;'><tr>
                <th>
                    <div class='title'>
                        <img src='spearerslogo.png' alt='Logo' height='100'>
                    </div>
                </th>
            </tr></table>
        </nav>
        <table class='calendar' style='table-layout: auto;'><thead><tr>
            <th><a href='index.php#home' style='color:yellow;'>Home</a> </th>
            <th><a href='index.php#tides' style='color:yellow;'>Tides</a> </th>
            <th><a href='index.php#calendarsection' style='color:yellow;'>Calendar</a> </th>
            <th><a href='cheese.html' style='color:yellow;'>Cheese</a> </th>
            <th><a href='AGM.html' style='color:yellow;'>AGM</a></th>
            <th><a href='index.php#map' style='color:yellow;'>Map</a> </th>
            <th><a href='index.php#contact' style='color:yellow;'>Contact</a> </th>
        </tr></thead></table>
    </header>
    
    <main>
        <img src='BrisbaneRiver.jpg' style='max-width: 100%'>

        <div id="banner"><div id="photo-container">
            <img src="photos/DJI_2021121551740.jpg">
            <img src="photos/DJI_2021121553936.jpg">
            <img src="photos/IMG-20221021-WA0001.jpg">
            <img src="photos/IMG-20221104-WA0000.jpg">
            <img src="photos/IMG-20221111-WA0001.jpg">
            <img src="photos/IMG-20221116-WA0002.jpg">
            <img src="photos/IMG-20230513-WA0005.jpg">
            <img src="photos/IMG-20230531-WA0000.jpg">
            <img src="photos/IMG-20230817-WA0023.jpg">
            <img src="photos/IMG-20230817-WA0024.jpg">
            <img src="photos/IMG-20230817-WA0026.jpg">
        </div></div>

        
        <section id='home'>
            <h2>Home</h2>
            <p>Since 2003, Spearers have been paddling the Brisbane River every Monday, Wednesday, and Friday at 5am.</p>
            <p><b>All are welcome!</b>  Contact us to join us and try out one of our club skis.</p>
        </section>
        <hr>
        <section id='tides'>
            <h2>Tides and Weather</h2>
            <div id='nextTide'></div>
            <div id='canvasScroll' style='width:100%;height:250px;overflow-x:scroll;overflow-y:hidden;'>
                <canvas id='myCanvas' width='3600' height='240'></canvas>
            </div>
            <p style='font-size:7;'>Sources are <a href='http://www.bom.gov.au/australia/tides/#!/qld-brisbane-port-office'>BOM Tides</a> and <a href='http://www.bom.gov.au/fwo/IDQ65389/IDQ65389.540683.tbl.shtml'>BOM River Heights</a>
        </section>
        <hr>
        <section id='calendarsection'>
            <h2>Calendar</h2>
            <div style='width:100%;height:300px;overflow-x:hidden;overflow-y:scroll;'>
                <table class='calendar' style='table-layout:fixed;' id='calendar'>
                  <thead>
                    <tr>
                      <th>Sun</th>
                      <th>Mon</th>
                      <th>Tue</th>
                      <th>Wed</th>
                      <th>Thu</th>
                      <th>Fri</th>
                      <th>Sat</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- JavaScript will populate this section -->
                  </tbody>
                </table>
            </div>
        </section>
        <hr>
        <h2>Map</h2>
        <section id='map'>
            <div id='map'></div>
        </section>
        <hr>
        <section id='links'>
            <h2>Links</h2>
            <ul>
                <li><a href='https://www.msq.qld.gov.au/tmr_site_msq/_/media/tmronline/msqinternet/msqfiles/home/waterways/on-water-conduct-bris-river/brisbane_river_code_conduct.pdf'>Brisbane River Code</a></li>
                <li><a href='https://www.olympics.com.au/sports/canoe-kayak/'>Olympic Canoe Slalom / Canoe Sprint</a></li>
                <li><a href='https://stravaheatmap.pythonanywhere.com/'>Strava Heat Map</a></li>
                <li><a href='https://www.youtube.com/@ultimatekayaks6499'>Ivan Lawler videos</a></li>
            </ul>
        </section>
        <hr>
        <section id='contact'>
            <h2>Contact Us</h2>

  <form action='process_form.php' method='post'>
    <label for='name'>Name:</label>
    <input type='text' id='name' name='name' required><br>
    <label for='email'>Email Address:</label>
    <input type='email' id='email' name='email' required><br>
    <label for='phone'>Phone Number:</label>
    <input type='tel' id='phone' name='phone' required><br>
    <label for='message'>Message:</label><br>
    <textarea id='message' name='message' rows='4' cols='40' required></textarea><br>
    <input type='submit' value='Submit'>
  </form>

        </section>

    </main>

    <footer>
        <table class='calendar' style='table-layout: auto;'><thead><tr>
            <th><a href='index.php#home' style='color:yellow;'>Home</a> </th>
            <th><a href='index.php#tides' style='color:yellow;'>Tides</a> </th>
            <th><a href='index.php#calendarsection' style='color:yellow;'>Calendar</a> </th>
            <th><a href='cheese.html' style='color:yellow;'>Cheese</a> </th>
            <th><a href='AGM.html' style='color:yellow;'>AGM</a></th>
            <th><a href='index.php#map' style='color:yellow;'>Map</a> </th>
            <th><a href='index.php#contact' style='color:yellow;'>Contact</a> </th>
        </tr></thead></table>
        <p>&copy; 2023 The Spearers Kayaking Club. All rights reserved.</p>
    </footer>


    <script src='tide_list.js'></script>
    <script src='events.js'></script>
    <script src='calendar.js'></script>
    <!-- <script src='tides.php'></script> -->

<script>

function nextTide() {
    <?php 
        echo 'var windsData = '.$json_winds.';';
    ?>
    function getWinds(t) {
        var i,j;
        for (i=0; i<windsData.length-1; i++) {
            if (t < windsData[i+1].timestamp) {
                return windsData[i].wind_direction + 'º ' + windsData[i].wind_speed + 'kts';
            }
        }
        i = windsData.length-1;
        return windsData[i].wind_direction + 'º ' + windsData[i].wind_speed + 'kts';
    }
    let nextTide = document.getElementById('nextTide');
    const day = 24*3600*1000;
    const currentDate = new Date();
    const now = currentDate.getTime();
    
    // get next5am timestamp
    let nextTime = new Date(now);
    nextTime.setHours(5,0,0,0);   
    let next5am = nextTime.getTime();
    if (next5am <= now) {
        next5am += day;
    }

    const brisbane = [-27.4698, 153.0251]; // Latitude and Longitude of Brisbane
    var times = SunCalc.getTimes(currentDate, ...brisbane);
    const sunrise1 = times.sunrise;
    const sunriseFormat1 = sunrise1.toLocaleTimeString('en-US', { timeZone: 'Australia/Brisbane', hour: 'numeric', minute: 'numeric' });
    times = SunCalc.getTimes(new Date(next5am + day), ...brisbane);
    const sunrise2 = times.sunrise;
    const sunriseFormat2 = sunrise2.toLocaleTimeString('en-US', { timeZone: 'Australia/Brisbane', hour: 'numeric', minute: 'numeric' });

    nextTide.innerHTML = '<table><tr> \
          <th>Date</th> \
          <th>Tide</th> \
          <th>Wind</th> \
          <th>Temp</th> \
          <th>Sunrise</th> \
        </tr><tr> \
          <td>Now</td> \
          <td>' + tideText(now) + '</td> \
          <td><?php echo $windDirNow."º ".$windSpeedNow."kts"; ?></td> \
          <td><?php echo $temp."ºC"; ?></td> \
          <td></td> \
        </tr><tr> \
          <td>' + formatDay(next5am) + '</td> \
          <td>' + tideText(next5am) + '</td> \
          <td>' + getWinds(next5am) + '</td> \
          <td></td> \
          <td>' + sunriseFormat1 + '</td> \
        </tr><tr> \
          <td>' + formatDay(next5am + day) + '</td> \
          <td>' + tideText(next5am + day) + '</td> \
          <td>' + getWinds(next5am + day) + '</td> \
          <td></td> \
          <td>' + sunriseFormat2 + '</td> \
      </tr></table>';
}

function drawCurve() {
    <?php 
        echo 'var riverData = '.$json_river.';';
    ?>
    const canvas = document.getElementById('myCanvas');
    const ctx = canvas.getContext('2d');
    const amp = 80; // amplitude
    const xx = canvas.width;
    const yy = canvas.height;

    const day = 24*3600*1000;
    const currentDate = new Date();
    const now = currentDate.getTime();
    const timeStart = now - 7*day;  //1 week ago
    const days = 20;
    const duration = days*day;
    const timeEnd = timeStart + duration;

    // get next midnight & noon & 5am
    let nextTime = new Date(timeStart);
    nextTime.setHours(0, 0, 0, 0);
    const midnight = nextTime.getTime() + day;
    nextTime = new Date(timeStart);
    nextTime.setHours(12,0,0,0);
    let noon = nextTime.getTime();
    if (noon <= timeStart) {
        noon += day;
    }
    nextTime = new Date(timeStart);
    nextTime.setHours(5,0,0,0);   
    let next5am = nextTime.getTime();
    if (next5am <= timeStart) {
        next5am += day;
    }

    ctx.clearRect(0, 0, xx, yy);

    // draw horizontal lines 
    ctx.beginPath();
    ctx.strokeStyle = 'grey';
    ctx.lineWidth = 1;
    ctx.rect(0, yy - 3*amp, xx, 2*amp);
    ctx.rect(0, yy - 2*amp, xx, 2*amp);
    ctx.stroke();

    // draw text
    ctx.font = "18px Arial";
    ctx.fillStyle = 'gray';
    ctx.fillText("3 m", 5, yy - 3*amp +18);
    ctx.fillText("2 m", 5, yy - 2*amp +18);
    ctx.fillText("1 m", 5, yy - amp +18);
    ctx.font = "12px Arial";
    ctx.fillStyle = 'red';
    ctx.fillText("Now", (now - timeStart)*xx/duration - 12, yy/5);

    for (var i=0; i<days; i++) {
        // draw midnight vertical lines
        ctx.beginPath();
        ctx.strokeStyle = "blue";
        ctx.lineWidth = 2;
        ctx.moveTo((midnight + i*day - timeStart)*xx/duration, yy - 3*amp);  //replace
        ctx.lineTo((midnight + i*day - timeStart)*xx/duration, yy);
        ctx.stroke();
    
        // draw noon vertical lines
        ctx.beginPath();
        ctx.strokeStyle = "blue";
        ctx.lineWidth = 0.5;
        ctx.moveTo((noon + i*day - timeStart)*xx/duration, yy - 3*amp);  //replace
        ctx.lineTo((noon + i*day - timeStart)*xx/duration, yy);
        ctx.stroke();
    
        // draw 5am boxes
        ctx.fillStyle = "#E0E0C0";
        ctx.fillRect((next5am + i*day - timeStart)*xx/duration, yy - 3*amp, 1*3600*1000*xx/duration, 3*amp);

        // text
        ctx.font = "12px Arial";
        ctx.fillStyle = 'brown';
        ctx.fillText("5-6am", (next5am + i*day - timeStart)*xx/duration - 13, 15);
        ctx.fillStyle = 'blue';
        ctx.fillText("00:00", (midnight + i*day - timeStart)*xx/duration - 15, 15);
        ctx.fillText("12:00", (noon + i*day - timeStart)*xx/duration - 15, 15);

        ctx.font = "18px Arial";
        var nextDate = formatDay(noon + i*day);
        ctx.fillText(nextDate, (noon + i*day - timeStart)*xx/duration - 40, yy - 10);
    }
    
    // draw predicted tides, 15 min intervals 
    ctx.beginPath();
    for (let x = timeStart; x < timeEnd; x += 0.25*3600*1000) {
      const y = tideHeight(x);
      ctx.lineTo((x - timeStart)*xx/duration, yy - y*amp);
    }
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = 3;
    ctx.stroke();

    // draw historical tides, 15 min intervals 
    ctx.beginPath();
    let x = timeStart;
    let r = 0;
    while (r < riverData.length) {
        x = riverData[r][0]*1000;
        y = parseFloat(riverData[r][1]) + 1;
        ctx.lineTo((x - timeStart)*xx/duration, yy - y*amp);
        r++;
    }
    ctx.strokeStyle = 'blue';
    ctx.setLineDash([1,1]);
    ctx.lineWidth = 3;
    ctx.stroke();

    // draw now line
    ctx.beginPath();
    ctx.moveTo((now - timeStart)*xx/duration, yy);
    ctx.lineTo((now - timeStart)*xx/duration, yy - 3*amp);
    ctx.strokeStyle = "red";
    ctx.setLineDash([]);
    ctx.lineWidth = 4;
    ctx.stroke();
    
    //adjust scrollbar
    document.getElementById('canvasScroll').scrollLeft = 3600 * 6.75 / 20;
}

nextTide();
drawCurve();

</script>

    <script src='data.js'></script>
    <script src='map.js'></script>

    
</body>
</html>
