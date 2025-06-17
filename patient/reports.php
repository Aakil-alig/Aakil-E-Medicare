<?php
session_start();
include("../connection.php");

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
        header("location: ../login.php");
    }
} else {
    header("location: ../login.php");
}

$userrow = $database->query("select * from patient where pemail='".$_SESSION["user"]."'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];

// Get all reports for this patient
$sqlmain = "select pr.*, d.docname, d.specialties as docspec 
            from patient_reports pr 
            join doctor d on pr.doctor_id = d.docid 
            where pr.patient_id=$userid 
            order by pr.report_date desc";
$result = $database->query($sqlmain);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
    <title>My Medical Reports</title>
    <style>
        .report-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .report-card:hover {
            transform: translateY(-5px);
        }
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .report-title {
            font-size: 1.2em;
            color: #0083b0;
        }
        .report-date {
            color: #666;
            font-size: 0.9em;
        }
        .report-doctor {
            color: #444;
            margin-bottom: 10px;
        }
        .report-content {
            margin: 15px 0;
        }
        .report-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .action-btn {
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }
        .view-btn {
            background: #0083b0;
        }
        .download-btn {
            background: #00b4db;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .no-reports {
            text-align: center;
            padding: 50px 20px;
        }
        .no-reports img {
            width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include("menu.php"); ?>
        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                <tr>
                    <td colspan="2" style="padding-top:30px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">
                            My Medical Reports (<?php echo $result->num_rows; ?>)
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <center>
                            <div class="abc scroll" style="padding: 0 20px;">
                                <?php
                                if($result->num_rows == 0) {
                                    echo '<div class="no-reports">
                                            <img src="../img/notfound.svg" alt="No Reports">
                                            <p class="heading-main12" style="font-size:18px;color:rgb(49, 49, 49)">
                                                No medical reports available yet
                                            </p>
                                        </div>';
                                } else {
                                    while($row = $result->fetch_assoc()) {
                                        echo '<div class="report-card">
                                                <div class="report-header">
                                                    <div class="report-title">
                                                        '.$row["disease"].'
                                                    </div>
                                                    <div class="report-date">
                                                        '.date('F j, Y', strtotime($row["report_date"])).'
                                                    </div>
                                                </div>
                                                <div class="report-doctor">
                                                    <i class="fas fa-user-md"></i> Dr. '.$row["docname"].' ('.$row["docspec"].')
                                                </div>
                                                <div class="report-content">
                                                    <p><strong>Symptoms:</strong> '.substr($row["symptoms"], 0, 100).'...</p>
                                                </div>
                                                <div class="report-actions">
                                                    <a href="view-report.php?id='.$row["report_id"].'" class="action-btn view-btn">
                                                        <i class="fas fa-eye"></i> View Full Report
                                                    </a>
                                                    <a href="view-report.php?id='.$row["report_id"].'&download=pdf" class="action-btn download-btn">
                                                        <i class="fas fa-download"></i> Download PDF
                                                    </a>
                                                </div>
                                            </div>';
                                    }
                                }
                                ?>
                            </div>
                        </center>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html> 