<?php

$host = 'localhost';
$db = 'bruno_db';
$user = 'root';
$password = '';

// lets connect to the database
try{
    // /we create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    // set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Upon success with no exception
    //echo " Database connection succcessful";

} catch (PDOException $e){
    die("Database connection Failed: " . $e->getMessage());
}

?>