<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient = "info@westendspearers.com.au";
    $subject = "New Contact Form Submission";
    
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $message = $_POST["message"];
    
    $emailContent = "Name: $name\nEmail: $email\nPhone: $phone\nMessage: $message";
    
    $headers = "From: $email";
    
    if (mail($recipient, $subject, $emailContent, $headers)) {
        echo "Thank you for contacting us!<br>";
    } else {
        echo "There was a problem sending the email.<br>";
    }
    echo "<a href='https://westendspearers.com.au'>Back to Spearers</a>";
    
}
?>
