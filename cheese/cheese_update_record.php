<?php
    // Database connection details
    $servername = "localhost";  //:3306
    $username = "westends_admin";
    $password = "Nanana11!!";
    $dbname = "westends_cheese";
    
    // Create a database connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $id = $_POST['id'];
    $name = $_POST['name'];
    $handicap = $_POST['handicap'];
    $comment = $_POST['comment'];
    
    // Insert data into the database
    $sql = "UPDATE Spearers SET name='$name', handicap=$handicap, comment='$comment' WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }
    
    $conn->close();
?>
