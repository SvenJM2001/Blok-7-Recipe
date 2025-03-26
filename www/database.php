<?php

//database connection   
$dbhost = "mariadb";
$dbuser = "root";
$dbpass = "password";
$dbname = "recipes";

$conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);