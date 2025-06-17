<?php
session_start();

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
        header("location: ../login.php");
    }else{
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
}

//import database
include("../connection.php");

if($_POST){
    $specialties = $_POST['specialties'];
    
    $error = false;

    // Update the doctor's professional details - specialties is stored as an integer ID
    $result = $database->query("UPDATE doctor SET specialties='$specialties' WHERE docemail='$useremail'");
    
    if($result){
        header("location: settings.php?action=success");
    }else{
        header("location: settings.php?action=error");
    }
}else{
    // If accessed without POST data, redirect to settings
    header("location: settings.php");
}
?> 