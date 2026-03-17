<?php

$conn = new mysqli("localhost","root","","apms_db");

if($conn->connect_error){
die("Database connection failed");
}

?>