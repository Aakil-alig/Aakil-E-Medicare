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
$userrow = $database->query("select * from doctor where docemail='$useremail'");
$userfetch=$userrow->fetch_assoc();
$userid= $userfetch["docid"];

if($_POST){
    $title = $_POST["title"];
    $nop = $_POST["nop"];
    $date = $_POST["date"];
    $time = $_POST["time"];
    
    // Insert the new session
    $sql = "INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop) VALUES ($userid, '$title', '$date', '$time', $nop)";
    
    if($database->query($sql)){
        header("location: schedule.php?action=success");
    }else{
        header("location: schedule.php?action=failed");
    }
}else{
    header("location: schedule.php");
}
?> 