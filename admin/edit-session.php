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
        include("../connection.php");
        
        $scheduleid = $_POST["scheduleid"];
        $docid = $_POST["docid"];
        $title = $_POST["title"];
        $nop = $_POST["nop"];
        $date = $_POST["date"];
        $time = $_POST["time"];

        // Validate inputs
        if($nop <= 0) {
            header("location: schedule.php?action=edit&error=3&id=".$scheduleid);
            exit;
        }

        if(strtotime($date) < strtotime(date('Y-m-d'))) {
            header("location: schedule.php?action=edit&error=2&id=".$scheduleid);
            exit;
        }

        $sql = "UPDATE schedule SET 
                title = '$title',
                scheduledate = '$date',
                scheduletime = '$time',
                nop = $nop
                WHERE scheduleid = $scheduleid AND docid = $docid";

        if($database->query($sql)){
            header("location: schedule.php?action=edit&error=4&id=".$scheduleid);
        }else{
            header("location: schedule.php?action=edit&error=1&id=".$scheduleid);
        }
    }
?> 