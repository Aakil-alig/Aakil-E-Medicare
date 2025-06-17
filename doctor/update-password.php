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
    $currentpassword = $_POST['currentpassword'];
    $newpassword = $_POST['newpassword'];
    $confirmpassword = $_POST['confirmpassword'];
    
    // First verify the current password
    $result = $database->query("SELECT * FROM doctor WHERE docemail='$useremail' AND docpassword='$currentpassword'");
    
    if($result->num_rows == 1){
        // Current password is correct
        if($newpassword == $confirmpassword){
            // New passwords match, proceed with update
            $update_result = $database->query("UPDATE doctor SET docpassword='$newpassword' WHERE docemail='$useremail'");
            
            if($update_result){
                header("location: settings.php?action=success");
            }else{
                header("location: settings.php?action=error");
            }
        }else{
            // New passwords don't match
            header("location: settings.php?action=error");
        }
    }else{
        // Current password is incorrect
        header("location: settings.php?action=error");
    }
}else{
    // If accessed without POST data, redirect to settings
    header("location: settings.php");
}
?> 