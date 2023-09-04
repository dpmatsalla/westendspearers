<?php

    // Get the tides from the BOM website for the entire year, and put them into JSON format, so that they're available for the Spearers website
    // Just adjust the dates in the two urls and execute.  Cut and paste the result into the tide_list.js file.  
    // Remember to replace all of the single quotes with double  ' --> "
    
    
    //Get Brisbane Tides for this year
    $lastWeek = date('Y-m-d', strtotime('-14 days'));
    $url = 'http://www.bom.gov.au/australia/tides/print.php?aac=QLD_TP138&type=tide&date='.$lastWeek.'&region=QLD&tz=Australia/Brisbane&tz_js=AEST&days=367';

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

    $tide_info_list = array();
    
    // Find all td elements
    $elements = $doc->getElementsByTagName('td');
    
    $i = 0;
    while ($i < count($elements)) {
        // Get the inner HTML content of the td element
        $height = substr($elements[$i+1]->nodeValue, 0, -2);
        if (strlen($height) > 2) {
            $time_local = $elements[$i]->getAttribute('data-time-local');
            if (str_contains($elements[$i]->getAttribute('class'),'high-tide')) {
                $tide_type = 'HIGH';
            } else {
                $tide_type = 'LOW';
            }
            // Store the content and attributes in the array
            $tide_info_list[] = array(
                'tide' => $tide_type,
                'time_local' => $time_local,
                'height' => $height,
            );
        }
        $i += 2;
    }
    
    
    //Now get the tides for next year
    $today = new DateTime();
    $firstDayNextYear = new DateTime((int)$today->format('Y') + 1 . '-01-01');
    $nextYear = $firstDayNextYear->format('Y-m-d');
    $url = 'http://www.bom.gov.au/australia/tides/print.php?aac=QLD_TP138&type=tide&date='.$nextYear.'&region=QLD&tz=Australia/Brisbane&tz_js=AEST&days=367';

    $response = file_get_contents($url, false, $context);
    if ($response === false) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to fetch webpage content"]);
        exit;
    }
    $doc = new DOMDocument();
    @$doc->loadHTML($response);

    // Find all td elements
    $elements = $doc->getElementsByTagName('td');
    
    $i = 0;
    while ($i < count($elements)) {
        // Get the inner HTML content of the td element
        $height = substr($elements[$i+1]->nodeValue, 0, -2);
        if (strlen($height) > 2) {
            $time_local = $elements[$i]->getAttribute('data-time-local');
            if (str_contains($elements[$i]->getAttribute('class'),'high-tide')) {
                $tide_type = 'HIGH';
            } else {
                $tide_type = 'LOW';
            }
            // Store the content and attributes in the array
            $tide_info_list[] = array(
                'tide' => $tide_type,
                'time_local' => $time_local,
                'height' => $height,
            );
        }
        $i += 2;
    }


    // Print the array of td contents and attributes
    echo "let tide_list = " . json_encode($tide_info_list) . ';';

?>