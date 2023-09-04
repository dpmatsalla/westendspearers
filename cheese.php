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
    <style>
        .picture {
          max-height: 300px;
          max-width: 100%;
          flex-shrink: 1;
          margin-right: 3px;
        }
    </style>
</head>
<body>
    <script>
        function checkPassword() {
            var password = document.getElementById("password").value;
            if (password === "ngu") {
                window.location.href = "https://westendspearers.com.au/cheese/cheeseUpdate.php"; // Replace with your desired URL
            } else {
                document.getElementById("message").innerHTML = "Wrong! The Timelord shuns you.";
            }
        }
    </script>
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
            <th><a href='cheese.html#cheese' style='color:yellow;'>Cheese</a> </th>
            <th><a href='AGM.html#AGM' style='color:yellow;'>AGM</a></th>
            <th><a href='index.php#map' style='color:yellow;'>Map</a> </th>
            <th><a href='index.php#contact' style='color:yellow;'>Contact</a> </th>
        </tr></thead></table>
    </header>
    
    <main>
        <img src='BrisbaneRiver.jpg' style='max-width: 100%'>

        <section id='cheese'>
            <table width='100%'><tr>
                <td style='text-align:left;'><h2>The Cheese</h2></td>
                <td style='text-align:center;'><img class='picture' src='cheese/cheese.jpg' style='max-height:200px;'></td>
                <td style='text-align:right;'><h2>The Cheese</h2></td>
            </tr></table>
            <h3>Some Winners:</h3>
            <table style='width:100%;border-collapse:collapse;'><tr>
                <td><img class='picture' src='cheese/IMG-20230817-WA0016.jpg'></td>
                <td><img class='picture' src='cheese/IMG-20230817-WA0017.jpg'></td>
                <td><img class='picture' src='cheese/IMG-20230817-WA0018.jpg'></td>
                <td><img class='picture' src='cheese/IMG-20230817-WA0019.jpg'></td>
                <td><img class='picture' src='cheese/IMG-20230817-WA0025.jpg'></td>
            </tr></table>
            <table style='width:100%;border-collapse:collapse;'><tr>
                <td><img class='picture' src='cheese/IMG-20230817-WA0005.jpg'></td>
                <td><img class='picture' src='cheese/IMG-20230817-WA0006.jpg'></td>
                <td><img class='picture' src='cheese/IMG-20230817-WA0010.jpg'></td>
                <td><img class='picture' src='cheese/IMG-20230817-WA0012.jpg'></td>
                <td><img class='picture' src='cheese/IMG-20230817-WA0013.jpg'></td>
            </tr></table>
            <table style='width:100%;border-collapse:collapse;'><tr>
                <td><img class='picture' src='cheese/IMG-20221223-WA0004.jpg'></td>
                <td><img class='picture' src='cheese/IMG-20230317-WA0000.jpg'></td>
                <td><img class='picture' src='cheese/IMG-20230501-WA0000.jpg'></td>
                <td><img class='picture' src='cheese/IMG-20230721-WA0002.jpg'></td>
                <td><img class='picture' src='cheese/IMG-20221111-WA0009.jpg'></td>
            </tr></table>
        </section>
        <hr>
        <section id='athletes'>
            <h2>The Spearers</h2>

            <?php
                // Database connection details
                $servername = "localhost";  //:3306
                $username = "westends_admin";
                $password = "Nanana11!!";
                $dbname = "westends_cheese";
            
                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);
            
                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
            
                // Fetch records from the database
                $sql = "SELECT * FROM Spearers ORDER BY handicap";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    echo "<table>";
                    echo "<tr><th>Name</th><th>Handicap</th></tr>";
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row['name']."</td>";
                        echo "<td><center>".number_format($row['handicap'],1)."</center></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "No records found.";
                }
            
                $conn->close();
            ?>

            <button onclick="document.getElementById('popup').style.display='block'" style="font-size:12px;padding:2px">Timelord Update</button>
            <div id="popup" style="display:none;">
                Enter Password
                <input type="password" id="password" size="10">
                <button onclick="checkPassword()" style="font-size:12px;padding:2px">Submit</button>
                <p id="message"></p>
            </div>
        </section>
        <hr>
        <section id='commandments'>
            <h2>The 10 Commandments of the Cheese</h2>
            <center><img src="https://qph.cf2.quoracdn.net/main-qimg-222d3d41c39053f89225d5bc1eaaf19d-lq" height="200"></center>
            <ol type='1' style="font-size:18px;">
                <li>Thou shalt not take thy brother's car park</li>
                <li>Thou shalt not tell thy brother where to paddle</li>
                <li>Thou shalt shut the F up while paddling</li>
                <li>Thou shalt turn up for coffee after paddling</li>
                <li>Thou shalt attend the AGM</li>
                <li>Thou shalt not covet thy neighbour's boat</li>
                <li>Thou shalt not covet thy neighbour's paddle</li>
                <li>Thou shalt not use a leaf-blower at 4 am</li>
                <li>Thou shalt prioritise the cheese above all else in thy life</li>
                <li>There are no rules in the cheese...  win at all cost!</li>
            </ol>
        </section>
        <hr>
        <section id='rules'>
            <h2>Spearers Cheese Rules & Disclaimer</h2>
            <ol type='1'>
                <li>The Cheese is called by the Timelord for any Friday he/she decides.</li>
                <li>The Cheese is a handicap race with times decided and posted by the Timelord (discussion is encouraged on times but will be totally at the discretion of the Timelord).</li>
                <li>All Spearers can encourage the Timelord to call a cheese if the Timelord is not doing his/her job (such as realising there is an encouraging high tide on any given Friday being perfect for a Cheese).</li>
                <li>Only two competitors required for a Cheese at any time on any given Friday (those two competitors can call a Cheese any time but only Fridays and only a with a handycap unless it’s the first Friday of the month in which case it can be a scratch Cheese).</li>
                <li>By default the first Friday of each month is a scratch Cheese. All those who race will start on the line together. </li>
                <li>The Cheese is only run on one course, that being from the pontoon at West End to the rock and return (there is no city Cheese). </li>
                <li>The Cheese turn at the rock should be anti-clockwise so as to turn away from the pesky rowers circuit (safety first). </li>
                <li>Depending on tides a competitor can choose to turn clockwise but must in all circumstances give way to paddlers turning anti-clockwise  and of course those pesky rowers. </li>
                <li>Lead skis have the directional right of way in all circumstances. A trailing or wash-riding paddler shall not tell the lead paddler where they should go (direction is the lead paddlers prerogative) </li>
                <li>Single file always under citycat bridges and a word of warning to watch you head on higher tides (if the tide is too high then go around the citycat stop). </li>
                <li>A washriding ski must not makes contact with the lead skis blade on exit. If this occurs and lead paddler has a swim, the wash-riding ski must stop and assist the paddler in the water, essentially forfeiting any right to win The Cheese. </li>
                <li>Non- competitors of The Cheese should stay well away from Cheese competitors and defer their line under Citycat bridges even if they are slightly leading entering the citycat zones.</li>
                <li>A Cheese competitor cannot washride or benefit in any way from a non-competitor (both competitor and non-competitor have a responsibility here)</li>
                <li>The previous winner of The Cheese must ensure the Cheese trophy makes it to coffee presentation on the day of The Cheese provided 24 hours notice is given prior to The Cheese being run. Failure to arrange for the safe delivery of The Cheese trophy will cost the Spearer a full round of coffee on the day he/she returns The Cheese. </li>
                <li>All Spearers must have front and rear facing flashing white lights as determined by the Brisbane River Code. </li>
                <li>By choosing to race the Cheese each Spearer accepts in full the following Disclaimer. </li>
                </ol>
            <h2>Disclaimer</h2>
            <p>Participating in The Cheese exposes the participant to inherent risks and dangers associated with paddling and water-based activities. While The Spearers have taken reasonable precautions to ensure the safety of all participants, it is important to acknowledge and understand the potential risks involved. By agreeing to compete in a Cheese race, all participating Spearers agree to the following terms and conditions:</p>
            <ol type='1'>
                <li>Assumption of Risk: <br>
                I am aware that participating in a Cheese race involves risks such as capsizing, collisions, exposure to adverse weather conditions, rowers, tinnies, Citycats and other hazards associated with paddling on the Brisbane River. By competing in a Cheese Race, I understand that these risks cannot be completely eliminated.</li>
                <li>Physical Fitness and Health: <br>
                I certify that as a Spearer, I am physically fit to participate in The Cheese race and have not been advised otherwise by a qualified medical professional. I understand that it is my responsibility to consult with a medical professional if I have any health concerns that might affect my ability to safely participate in a Cheese race.</li>
                <li>Safety Instructions: <br>
                I agree to follow all safety instructions as listed in The Spearers Cheese Rules including wearing appropriate safety gear such as life jackets, front and rear facing white flashing lights and following the designated Cheese routes to and from the rock, and adhering to any Brisbane River Code or rules and other race rules and guidelines that The Spearers may include from time-to-time in the Cheese Race.</li>
                <li>Release of Liability: <br>
                In consideration of being permitted to participate in the Cheese race, I hereby release, waive, discharge, and covenant not to sue any Spearers and or the Timelord, for any and all liabilities, claims, demands, injuries, damages, or actions arising out of or in any way connected with my participation in the Cheese race, even if caused by my/their negligence.</li>
                <li>Indemnification: <br>
                By choosing to race The Cheese, I agree to indemnify and hold harmless the Spearers, and other Spearer participants from any and all liabilities or claims made by third parties arising out of my participation in the Cheese race.</li>
                <li>Photography and Publicity: <br>
                I grant permission to the Spearers to use any photographs, videos, or other media of me taken during the Cheese race for promotional, marketing, website and other Spearer media.</li>
                <li>Personal Injury & Property: <br>
                    By choosing to race The Cheese, I acknowledge that I am responsible for my personal belongings and equipment during the race, and I release the Spearers from any liability for personal injury, loss of life, including loss or damage of my personal property.</li>
                <li>Emergency Medical Treatment: <br>
                In the event of a medical emergency, I authorize the Spearers to seek and provide medical treatment as deemed necessary and I will be responsible for any medical expenses incurred.</li>
            </ol>
            <p>By choosing to race the Cheese, I confirm that I have read and understood this disclaimer and fully accept its terms. I acknowledge that by agreeing to participate in the Cheese race that it is purely voluntary, and I assume all risks associated with my participation. I agree that this disclaimer will be binding upon my heirs, assigns, and legal representatives.</p>
        </section>
    </main>
    
    <footer>
        <table style='width:100%;border-collapse:collapse;'><tr>
            <td><img class='picture' src='cheese/cheese2.jpg'></td>
            <td><img class='picture' src='cheese/cheese3.jpg'></td>
            <td><img class='picture' src='cheese/cheese4.jpg'></td>
        </tr></table>
        <table class='calendar' style='table-layout: auto;'><thead><tr>
            <th><a href='index.php#home' style='color:yellow;'>Home</a> </th>
            <th><a href='index.php#tides' style='color:yellow;'>Tides</a> </th>
            <th><a href='index.php#calendarsection' style='color:yellow;'>Calendar</a> </th>
            <th><a href='cheese.html#cheese' style='color:yellow;'>Cheese</a> </th>
            <th><a href='AGM.html#AGM' style='color:yellow;'>AGM</a></th>
            <th><a href='index.php#map' style='color:yellow;'>Map</a> </th>
            <th><a href='index.php#contact' style='color:yellow;'>Contact</a> </th>
        </tr></thead></table>
        <p>&copy; 2023 The Spearers Kayaking Club. All rights reserved.</p>
    </footer>

</body>
</html>
