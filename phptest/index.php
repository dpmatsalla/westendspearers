<!--
Reviews of equipment and skis
create a similar page for Coomera and for Noosa
Paddling tips and resources, safety guidelines, tips
draw river current on map using https://github.com/onaci/leaflet-velocity
Blog or news updates (ie. Cheese dates, AGM dates, etc)
Training plans, ie. Intervals
Member profiles
Photo upload section
Strava leaderboards standings, links

References:
- https://ckc.clubexpress.com/#top
- https://www.data.qld.gov.au/dataset/brisbane-bar-tide-gauge-predicted-interval-data/resource/8a5a5e32-2ed4-4252-8c2a-074188556d5b
- http://www.bom.gov.au/australia/tides/print.php?aac=QLD_TP138&type=tide&date=01-08-2023&region=QLD&tz=Australia/Brisbane&tz_js=AEST&days=20
- http://www.bom.gov.au/australia/tides/scripts/getNextTides.php?aac=QLD_TP138&offset=false&tz=Australia%2FBrisbane 
- https://community.home-assistant.io/t/restful-sensor-json-help-australian-tide-predictions-bom/149663/19
-->

<?php
    //convert format 19/08/2023 22:00 to a PHP timestamp
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
    $windSpeedNow = intval($matches[2]);
    preg_match('/(\d{2})\/(\d{2}|M\d{2})/', $metar, $matches);
    $temp = intval($matches[1]);
    
    //Get forecast of min temperature, wind data next seven days 
    $forecast = file('https://api.open-meteo.com/v1/forecast?latitude=-27.4679&longitude=153.0281&hourly=temperature_2m,windspeed_10m,winddirection_10m&timezone=auto');

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
    <meta name='keywords' content='West End, Spearers, kayak, kayaking, canoe, surfski, paddling, Brisbane'>
    <meta name='description' content='West End Spearers Kayaking Club'>
    <meta name='author' content='Devon Matsalla'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>West End Spearers Kayaking Club</title>
    <link rel='icon' type='image/x-icon' href='spearers.ico'>
    <link rel='stylesheet' type='text/css' href='styles.css'>
    <link rel='stylesheet' href='https://unpkg.com/leaflet@1.7.1/dist/leaflet.css' />
    <link rel="canonical" href="https://westendspearers.com.au/" />
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

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-P87WWP1YGX"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-P87WWP1YGX');
    </script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-NBBRQ6H3');</script>
    <!-- End Google Tag Manager -->
    
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NBBRQ6H3"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

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
            <th><a href='cheese.php#cheese' style='color:yellow;'>Cheese</a> </th>
            <th><a href='AGM.html#AGM' style='color:yellow;'>AGM</a></th>
            <th><a href='index.php#map' style='color:yellow;'>Map</a> </th>
            <th><a href='index.php#contact' style='color:yellow;'>Contact</a> </th>
        </tr></thead></table>
    </header>
    
    <main>
        <img src='BrisbaneRiver.jpg?v=2' style='max-width: 100%'>

        <div id="banner"><div id="photo-container">
            <img src="photos/20231002-group.jpg?v=1">
            <img src="photos/DJI_2021121551740.jpg?v=2">
            <img src="photos/DJI_2021121553936.jpg?v=2">
            <img src="photos/IMG-20230916-WA0005.jpg">
            <img src="photos/IMG-20221021-WA0001.jpg">
            <img src="photos/IMG-20221104-WA0000.jpg">
            <img src="photos/IMG-20221111-WA0001.jpg">
            <img src="photos/IMG-20221116-WA0002.jpg">
            <img src="photos/IMG-20230513-WA0005.jpg">
            <img src="photos/IMG-20230531-WA0000.jpg">
            <img src="photos/IMG-20230817-WA0023.jpg">
            <img src="photos/IMG-20230817-WA0024.jpg">
            <img src="photos/IMG-20230817-WA0026.jpg">
            <img src="photos/IMG-20230817-WA0034.jpg">
            <img src="photos/IMG-20230817-WA0035.jpg">
            <img src="photos/IMG-20230817-WA0036.jpg">
        </div></div>

        
        <section id='home'>
            <h2>Home</h2>
            <p>Since 2003, Spearers have been paddling the Brisbane River every Monday, Wednesday, and Friday at 5am.</p>
            <p><b>All are welcome!</b>  Contact us to join us and try out one of our club skis.</p>
        </section>
        <hr>
        <section id='tides'>
            <h2>Tides and Weather</h2>
            <p>Predicted tides at Brisbane's West End from 5-6am</p>
            <div id='nextTide'></div>
            <div id='canvasScroll' style='width:100%;height:250px;overflow-x:scroll;overflow-y:hidden;'>
                <canvas id='myCanvas' width='3600' height='240'></canvas>
            </div>
            <p style='font-size:7;'>Sources are <a href='http://www.bom.gov.au/australia/tides/#!/qld-brisbane-port-office'>BOM Tide Predictions</a> and <a href='http://www.bom.gov.au/fwo/IDQ65389/IDQ65389.540683.tbl.shtml'>Actual River Heights in St-Lucia</a>
        </section>
        <hr>
        <section id='calendarsection'>
            <h2>Calendar</h2>
            <p>Predicted daylight in Brisbane from 5-6am. Legend:</p>
            <table class='calendar'><tr>
                <td class='today'>Today</td>
                <td class='daypaddle'>Day paddle</td>
                <td class='dawnpaddle'>Dawn paddle</td>
                <td class='nightpaddle'>Night paddle</td>
                <td class='goodday'>Good tide</td>
            </tr></table>
            <br>
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
        <p>Our common paddle locations. Current wind from <?php echo $windDirNow; ?>° at <?php echo $windSpeedNow; ?> kts</p>
        <section id='map'>
            <div id='map'></div>
        </section>
        <hr>
        <section id='links'>
            <h2>Links</h2>
            <ul>
                <li><a href='https://www.msq.qld.gov.au/tmr_site_msq/_/media/tmronline/msqinternet/msqfiles/home/waterways/on-water-conduct-bris-river/brisbane_river_code_conduct.pdf'>Brisbane River Code</a></li>
                <li><a href='https://www.strava.com/clubs/41135'>Spearers on Strava</a></li>
                <li><a href='https://www.olympics.com.au/sports/canoe-kayak/'>Olympic Canoe Slalom / Canoe Sprint</a></li>
                <li><a href='https://paddle.org.au/'>Paddle Australia</a></li>
                <li><a href='https://paddleqld.asn.au/'>Paddle Queensland</a></li>
                <li><a href='https://www.youtube.com/@ultimatekayaks6499'>Ivan Lawler videos</a></li>
                <li><a href='https://westend.paddle.org.au/'>West End Canoe Club</a></li>
                <li><a href='https://stravaheatmap.pythonanywhere.com/'>Strava Heatmap</a></li>
            </ul>
        </section>
        <hr>
        <section id='contact'>
            <h2>Contact Us</h2>
              <form action='contact-us.php' method='post'>
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
            <th><a href='cheese.php#cheese' style='color:yellow;'>Cheese</a> </th>
            <th><a href='AGM.html#AGM' style='color:yellow;'>AGM</a></th>
            <th><a href='index.php#map' style='color:yellow;'>Map</a> </th>
            <th><a href='index.php#contact' style='color:yellow;'>Contact</a> </th>
        </tr></thead></table>
        <p>&copy; 2023 The Spearers Kayaking Club. All rights reserved.</p>
    </footer>

    <script>
        <?php
            print_r("var forecastRaw = '".$forecast[0]."';");
            echo "const windDirNow = ".$windDirNow.";";
            echo "const windSpeedNow = ".$windSpeedNow.";";
            echo "const tempNow = '".$temp."ºC';";
            echo 'var riverData = '.$json_river.';';
        ?>
        if (!tempNow) { tempNow = 'N/A'; }
        const forecast = JSON.parse(forecastRaw);
    </script>
    <script src='tide_list.js?v=2'></script>
    <script>
        <?php
            // Database connection details
            $servername = 'localhost';  //:3306
            $username = 'westends_admin';
            $password = 'Nanana11!!';
            $dbname = 'westends_cheese';
        
            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
        
            // Check connection
            if ($conn->connect_error) {
                die('Connection failed: ' . $conn->connect_error);
            }
            
            // Fetch records from the database
            $sql = 'SELECT * FROM Events ORDER BY date';
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                echo 'const events = [';
                while($row = $result->fetch_assoc()) {
                    echo '{date: "' . $row['date'] . '", event: "' . $row['event'] . '" },';
                }
                echo '];';
            } else {
                echo 'No records found.';
            }
        
            $conn->close();
        ?>
    </script>
    <script src='calendar.js?v=2'></script>
    <script src='tides.js?v=2'></script>
    <script src='data.js?v=1'></script>
    <script src='map.js?v=1'></script>

</body>
</html>
