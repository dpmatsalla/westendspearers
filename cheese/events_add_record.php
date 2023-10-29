<?php
    // Database connection details
    $servername = "localhost";  //:3306
    $username = "westends_admin";
    $password = "Nanana11!!";
    $dbname = "westends_cheese";

    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    //$id = $_POST['id'];
    $date = $_POST['date'];
    $event = $_POST['event'];
    $evcomment = $_POST['evcomment'];
    
    $sql = "INSERT INTO Events (id, date, event, evcomment) VALUES (NULL, '$date', '$event', '$evcomment')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: cheeseUpdate.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $conn->close();
?>
