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
    $name = $_POST['name'];
    $handicap = $_POST['handicap'];
    $comment = $_POST['comment'];
    
    $sql = "INSERT INTO Spearers (id, name, handicap, comment) VALUES (NULL, '$name', '$handicap', '$comment')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: cheeseUpdate.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $conn->close();
?>
