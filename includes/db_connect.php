<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname ="neighborhood_helper";

//connection

$conn = new mysqli($host,$user,$pass,$dbname);

//checking connection
if($conn->connect_error){
    die("connection failed .$conn->connect_error");
}
?>