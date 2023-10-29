<!--
Reviews of equipment and skis
Paddling tips and resources, safety guidelines, tips
draw animated kayaker and ferry leaving at 5am
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
    //check attributes for what location we get tides and weather and store in $loc = westend,breakfast,noosa,coomera
    $loc = $_GET["loc"];
    if (empty($loc)) {
        $loc = 'westend';
    }

    //function to convert format 19/08/2023 22:00 to a PHP timestamp
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
    
    //get current winds ($windDirNow and $windSpeedNow) and temperature ($temp)
    $metar = file('http://tgftp.nws.noaa.gov/data/observations/metar/stations/YBBN.TXT')[1];
    preg_match('/(\d{3})(\d{2})(G\d{2})?KT/', $metar, $matches);
    $windDirNow = $matches[1];
    $windSpeedNow = intval($matches[2]);
    preg_match('/(\d{2})\/(\d{2}|M\d{2})/', $metar, $matches);
    $temp = intval($matches[1]);
    
    //Populate $forecast with min temperature, wind data next seven days
    if ($loc == 'westend' || $loc == 'breakfast') {
        $forecast = file('https://api.open-meteo.com/v1/forecast?latitude=-27.4679&longitude=153.0281&hourly=temperature_2m,precipitation_probability,windspeed_10m,winddirection_10m&timezone=Australia%2FBrisbane&forecast_days=14');
    } else if ($loc == 'noosa') {
        $forecast = file('https://api.open-meteo.com/v1/forecast?latitude=-26.3943&longitude=153.0901&hourly=temperature_2m,precipitation_probability,windspeed_10m,winddirection_10m&timezone=Australia%2FBrisbane&forecast_days=14');
    } else if ($loc == 'coomera') {
        $forecast = file('https://api.open-meteo.com/v1/forecast?latitude=-27.8489&longitude=153.3894&hourly=temperature_2m,precipitation_probability,windspeed_10m,winddirection_10m&timezone=Australia%2FBrisbane&forecast_days=14');
    } else {
        header("Location: https://westendspearers.com.au");
        die();
    }
    
    //Get Brisbane River Heights at St-Lucia or Hawthorne or Noosa and put in $json_river 
    if ($loc == 'westend') {
        $url = "http://www.bom.gov.au/fwo/IDQ65389/IDQ65389.540683.tbl.shtml";
    } else if ($loc == 'breakfast') {
        $url = "http://www.bom.gov.au/fwo/IDQ65389/IDQ65389.540685.tbl.shtml";
    } else if ($loc == 'noosa') {
        $url = "http://www.bom.gov.au/fwo/IDQ65390/IDQ65390.540311.tbl.shtml";
    } else if ($loc == 'coomera') {
        $url = "http://www.bom.gov.au/fwo/IDQ65388/IDQ65388.540269.tbl.shtml";   //"http://www.bom.gov.au/fwo/IDQ65388/IDQ65388.540673.tbl.shtml";
    }
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
    <title>Spearers Brisbane Kayaking Club</title>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <meta name='keywords' content='West End, Spearers, kayak, kayaking, canoe, surfski, ocean ski, paddling, Brisbane, Queensland, Australia'/>
    <meta name='description' content='The Spearers are a Club that promotes fitness and health by paddling on the Brisbane River three times per week from 5-6am.  We paddle surfskis, ocean skis and k1 kayaks, and we welcome anyone that would like to join us.'/>
    <meta name='author' content='Devon Matsalla'/>
    <meta property="og:title" content="Spearers Brisbane Kayaking Club" />
    <meta property="og:description" content="The Spearers are a Club that promotes fitness and health by kayaking on the Brisbane River from West End three times per week from 5-6am." />
    <meta property="og:image" content="https://westendspearers.com.au/images/westendspearers.jpg?v=1" />
    <meta property="og:url" content="https://westendspearers.com.au/" />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary_large_image" />
    <link rel='icon' type='image/x-icon' href='/images/spearers.ico' />
    <link rel='canonical' href='https://westendspearers.com.au/' />

    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'></script>
    <link rel='stylesheet' type='text/css' href='styles.css?v=8'>
    
    <link rel='stylesheet' href='https://unpkg.com/leaflet@1.7.1/dist/leaflet.css' />
    <script src='https://unpkg.com/leaflet@1.7.1/dist/leaflet.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/polyline-encoded@0.0.9/Polyline.encoded.min.js'></script>

    <script src='suncalc.js'></script>
    <style>
        $theme-colors: (
            "info": #00FFFF
        );
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
    <script async src='https://www.googletagmanager.com/gtag/js?id=G-P87WWP1YGX'></script>
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
    <noscript><iframe src='https://www.googletagmanager.com/ns.html?id=GTM-NBBRQ6H3'
    height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <header class='darkbg'>
        <nav class='navbar navbar-expand-lg navbar-dark'>
            <div class='container'>
                <a class='navbar-brand' href='/'><img src='/images/SpearersLogoClearDark.png' alt='Logo' width='250'></a>
                <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#collapsibleNavbar'>
                    <span class='navbar-toggler-icon'></span>
                </button>
                <div class='collapse navbar-collapse' id='collapsibleNavbar'>
                    <ul class='navbar-nav'>
                        <li class='nav-item'><a class='nav-link' href='/'>Home</a></li>
                        <li class='nav-item'><a class='nav-link' href='/#tides'>Tides</a></li>
                        <li class='nav-item'><a class='nav-link' href='/#calendarsection'>Calendar</a></li>
                        <li class='nav-item dropdown'>
                            <a class='nav-link dropdown-toggle' href='cheese.php' role='button' data-bs-toggle='dropdown'>Cheese</a>
                            <ul class='dropdown-menu dropdown-menu-dark'>
                                <li><a class='dropdown-item' href='cheese.php'>The Cheese</a></li>
                                <li><a class='dropdown-item' href='cheese.php#athletes'>The Spearers</a></li>
                                <li><a class='dropdown-item' href='cheese.php#commandments'>Commandments</a></li>
                                <li><a class='dropdown-item' href='cheese.php#rules'>Rules</a></li>
                            </ul>
                        </li>
                        <li class='nav-item'><a class='nav-link' href='photos.php#home'>Photo Gallery</a></li>
                        <li class='nav-item dropdown'>
                            <a class='nav-link dropdown-toggle' href='AGM.php' role='button' data-bs-toggle='dropdown'>AGM</a>
                            <ul class='dropdown-menu dropdown-menu-dark'>
                                <li><a class='dropdown-item' href='AGM.php#'>AGM</a></li>
                                <li><a class='dropdown-item' href='AGM.php#tshirts'>T-shirts</a></li>
                                <li><a class='dropdown-item' href='AGM.php#gallery'>Photo Gallery</a></li>
                                <li><a class='dropdown-item' href='AGM.php#mapsection'>Map</a></li>
                            </ul>
                        </li>
                        <li class='nav-item'><a class='nav-link' href='/#mapsection'>Map</a></li>
                        <li class='nav-item'><a class='nav-link' href='/#contact'>Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class='youtube-container'>
            <iframe id='video' src='https://www.youtube.com/embed/qzhuTbeSdmA?autoplay=1&mute=1&loop=1&color=white&controls=0&modestbranding=1&playsinline=1&rel=0&enablejsapi=1&playlist=qzhuTbeSdmA' title='Spearers Brisbane River' frameborder='0' allowfullscreen></iframe>
        </div>
    </header>
    
    <script type="text/javascript">
        function redirect() {
            // Redirect to the same site with the chosen attribute
            window.location.href = window.location.pathname + "?loc=" + document.getElementById("location").value + "#tides";
        }
    </script>
    <main>
        <section id='home' class='darkbg'>
            <div class='container p-3'>
                <h1>Spearers, West End</h1>
                <p>Since 2004, Spearers have paddled on the Brisbane River every Monday, Wednesday, and Friday from 5am to 6am.  We meet in the <a href='#mapsection'>Orleigh Park Carpark</a>. </p>
                <p>We paddle in surfskis, ocean skis, and K1 kayaks.  We run a race called <a href='cheese.php'>The Cheese</a>: a 9-km course around <a href='https://goo.gl/maps/sdDeoHEhbGUynhx58'>The Rock</a>.</p>
                <p>We do an annual pilgrimage to <a href='AGM.php'>The AGM</a> in November.  On holidays, we paddle from Breakfast Creek then head to our favourite spot, <a href='https://www.theblackalbion.com.au/'>The Black</a>.</p>
                <p><b>All are welcome!</b>  <a href='#contact'>Contact us</a> to join us and try out one of our club skis.</p>
            </div>
        </section>
        
        <section id='tides' class='whitebg'>
            <div class='container p-3'>
                <h2>Tides and Weather</h2>
                <table><tr>
                    <td>Predicted tides at </td>
                    <td><form id="redirectForm">
                        <select class="form-select" id="location" onchange="redirect()">
                            <option value="westend" <?php if ($loc == 'westend') echo 'selected'; ?>>West End</option>
                            <option value="breakfast" <?php if ($loc == 'breakfast') echo 'selected'; ?>>Breakfast Creek</option>
                            <option value="noosa" <?php if ($loc == 'noosa') echo 'selected'; ?>>Noosa</option>
                            <option value="coomera" <?php if ($loc == 'coomera') echo 'selected'; ?>>Coomera</option>
                        </select>
                    </form></td>
                </tr></table>
                <p>All times in AEST (UTC+10)</p>
            </div>
            <div class='container-fluid' id='canvasScroll' style='width:100%;height:360px;overflow-x:scroll;overflow-y:hidden;'>
                <canvas id='myCanvas' width='7200' height='350'></canvas>
            </div>
            <div class='container p-3'>
                Data Sources:
                <ul>
                    <li><a href='http://www.bom.gov.au/australia/tides/#!/qld-brisbane-port-office'>BOM Tide Predictions</a></li>
                    <li><a href='http://www.bom.gov.au/fwo/IDQ65389/IDQ65389.540683.tbl.shtml'>Actual River Heights in St-Lucia</a></li>
                    <li><a href='https://open-meteo.com/en/docs#latitude=-27.4679&longitude=153.0281&hourly=temperature_2m,precipitation_probability,windspeed_10m,winddirection_10m&timezone=Australia%2FSydney'>Open-Meteo Free Weather API</a></li>
                </ul>
            </div>
        </section>
        
        <section id='calendarsection' class='graybg'>
            <div class='container p-3'>
                <h2>Calendar</h2>
                <p>Predicted daylight in Brisbane from 5-6am. <br><strong>Legend:</strong></p>
                <table class='calendar'><tr>
                    <td class='today'>Today</td>
                    <td class='daypaddle'>Day paddle</td>
                    <td class='dawnpaddle'>Dawn paddle</td>
                    <td class='nightpaddle'>Night paddle</td>
                    <td class='goodday'>Good tide</td>
                </tr></table>
                <br>
            </div>
            <div class='container-fluid' style='width:100%;height:300px;overflow-x:hidden;overflow-y:scroll;padding:0px;'>
                <table class='calendar' style='table-layout:fixed;' id='calendar'>
                    <thead><tr>
                        <th>Sun</th>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                    </tr></thead>
                    <tbody>
                        <!-- JavaScript will populate this section -->
                    </tbody>
                </table>
            </div>

        </section>

        <section id='mapsection' class='whitebg'>
            <div class='container p-3'>
                <h2>Map</h2>
                <p>Our common paddle locations. Current wind from <?php echo $windDirNow; ?>° at <?php echo $windSpeedNow; ?> kph</p>
                <div id='map'></div>
            </div>
        </section>
       
        <section id='links' class='darkbg'>
            <div class='container p-3'>
                <h2>Links</h2>
                <ul>
                    <li><a href='https://www.msq.qld.gov.au/tmr_site_msq/_/media/tmronline/msqinternet/msqfiles/home/waterways/on-water-conduct-bris-river/brisbane_river_code_conduct.pdf'>Brisbane River Code</a></li>
                    <li><a href='https://www.strava.com/clubs/41135'>Spearers on Strava</a></li>
                    <li><a href='https://www.olympics.com.au/sports/canoe-kayak/'>Olympic Canoe Slalom / Canoe Sprint</a></li>
                    <li><a href='https://paddle.org.au/'>Paddle Australia</a></li>
                    <li><a href='https://paddleqld.asn.au/'>Paddle Queensland</a></li>
                    <li><a href='https://www.youtube.com/@ultimatekayaks6499'>Ivan Lawler videos</a></li>
                    <li><a href='https://westend.paddle.org.au/'>West End Canoe Club</a></li>
                    <li><a href='https://surfski.info/forum/19-boats/19935-vajda-makai-43-elite-vs-epic-v11-elite.html'>Vajda Makai 43 Elite vs Epic v11 Elite</a></li>
                    <li><a href='https://stravaheatmap.pythonanywhere.com/'>Strava Heatmap</a></li>
                    <li><a href='https://www.theblackalbion.com.au/'>The Black</a></li>
                    <li><a href='https://www.youtube.com/watch?v=9QFrcrjJ95I'>Ken Wallace Gold Men's K1 500m Final Beijing 2008</a></li>
                </ul>
            </div>
        </section>
        
        <section id='contact' class='graybg'>
            <div class='container p-3'>
                <h2>Contact Us</h2>
                    <form action='contact-us.php' method='post'>
                        <div class="d-grid gap-3">
                            <input class='form-control' type='text' id='name' name='name' placeholder='Enter name' required>
                            <input class='form-control' type='email' id='email' name='email' placeholder='Enter email' required>
                            <input class='form-control' type='tel' id='phone' name='phone' placeholder='Telephone' required>
                            <textarea class='form-control' id='message' name='message' rows='4' placeholder='Enter Message' required></textarea>
                            <input class='btn' type='submit' value='Submit'>
                        </div>
                    </form>
            </div>
        </section>
    </main>

    <footer class='darkbg'>
        <div class='container' style='padding:0;'>
            <table class='calendar' style='table-layout: auto;'><thead><tr>
                <th><a href='/#home'>Home</a> </th>
                <th><a href='/#tides'>Tides</a> </th>
                <th><a href='cheese.php#cheese'>Cheese</a> </th>
                <th><a href='photos.php#home'>Photos</a> </th>
                <th><a href='AGM.php#AGM'>AGM</a></th>
                <th><a href='/#mapsection'>Map</a> </th>
                <th><a href='/#contact'>Contact</a> </th>
            </tr></thead></table>
            <br>
            <div class='row'>
                <div class='col'>
                    <a class='navbar-brand' href='/'><img src='/images/SpearersLogoClearDark.png' class='d-block' alt='Logo' width='200'></a>
                </div>
                <div class='col'>
                    <p>&copy; 2023 Spearers. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <?php
        //read javascript file for tides (i.e. tides_westend.js)
        echo "<script src='tides_".$loc.".js?v=3'></script>";
    ?>
    <script>
        <?php
            // input the $forecast, $wind, $temp and $json_river variables into javascript variables
            print_r("var forecastRaw = '".$forecast[0]."';");
            echo "const windDirNow = ".$windDirNow.";";
            echo "const windSpeedNow = ".$windSpeedNow.";";
            echo "const tempNow = '".$temp."ºC';";
            echo 'var riverData = '.$json_river.';';
        ?>
        if (!tempNow) { tempNow = 'N/A'; }
        const forecast = JSON.parse(forecastRaw);

        <?php
            // Setup the 'events' array for the calendar
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
    <script src='calendar.js?v=7'></script>
    <script src='tides.js?v=21'></script>
    <script src='mapdata.js?v=2'></script>
    <script src='map.js?v=6'></script>
    <script>
        //now display all data from javascript files
        adjustTides();      //add a timestamp field, and add/subtract a bit to adjust tides for current (change this per location)
        generateCalendar(); //populate the calendar table tbody - functions in calendar.js
        drawCurve();        //populate the tides canvas - functions in tides.js
        document.getElementById('canvasScroll').scrollLeft = document.getElementById('myCanvas').width * 6.66 / 20;        //adjust scrollbar

        // Initialize the map centered on Orleigh Park
        var map = L.map('map').setView([-27.488299, 152.996411], 15);
        plotMap(data);
        
        //draw the windlines on the map.  Also, at 1 minute, drawcurve as well
        var oldMinutes = new Date().getMinutes();
        setInterval(() => {
            createLines(70); //execute every 300 ms

            const newMinutes = new Date().getMinutes();
            if (newMinutes != oldMinutes) {
                drawCurve();                // Execute at the start of each minute
                oldMinutes = newMinutes;
            }
            
        }, 300); // Adjust the interval duration as needed for the wind lines on map
    </script>
</body>
</html>
