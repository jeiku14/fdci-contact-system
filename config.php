<?php

$server = 'localhost';
$username = 'root';
$password = '';
$database = 'contact_system';

$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
    exit('Connection failed: '.mysqli_connect_error());
}
