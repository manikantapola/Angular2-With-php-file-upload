<?php
 
$host = "localhost";
$username = "root";
$password = "";
$db = "angular_db";

try{
    $conn = new PDO("mysql:host=$host;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(Exception $e){
    throw $e;
}



?> 