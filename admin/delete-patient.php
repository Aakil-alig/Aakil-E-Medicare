<?php
    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
            header("location: ../login.php");
        }
    }else{
        header("location: ../login.php");
    }
    
    if($_GET){
        //import database
        include("../connection.php");
        $id=$_GET["id"];
        
        // Get patient email first
        $result001= $database->query("select * from patient where pid=$id;");
        $email=($result001->fetch_assoc())["pemail"];
        
        // Delete appointments for this patient
        $sql= $database->query("delete from appointment where pid=$id;");
        
        // Delete from webuser table
        $sql= $database->query("delete from webuser where email='$email';");
        
        // Delete from patient table
        $sql= $database->query("delete from patient where pemail='$email';");
        
        header("location: patient.php");
    }
?> 