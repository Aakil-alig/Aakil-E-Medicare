<?php

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
            header("location: ../login.php");
        }

    }else{
        header("location: ../login.php");
    }
    
    
    if($_POST){
        //import database
        include("../connection.php");
        $title = $_POST["title"];
        $docid = $_POST["docid"];
        $nop = $_POST["nop"];
        $date = $_POST["date"];
        $time = $_POST["time"];

        // Validate inputs
        if($nop <= 0) {
            header("location: schedule.php?action=add-session&error=3");
            exit;
        }

        if(strtotime($date) < strtotime(date('Y-m-d'))) {
            header("location: schedule.php?action=add-session&error=2");
            exit;
        }

        if(!is_numeric($docid)) {
            header("location: schedule.php?action=add-session&error=1");
            exit;
        }

        $sql = "INSERT INTO schedule (docid,title,scheduledate,scheduletime,nop) 
                VALUES (?,?,?,?,?)";
        
        $stmt = $database->prepare($sql);
        $stmt->bind_param("isssi", $docid, $title, $date, $time, $nop);
        
        if($stmt->execute()){
            header("location: schedule.php?action=session-added&title=$title");
        }else{
            header("location: schedule.php?action=add-session&error=1");
        }
    }

?>