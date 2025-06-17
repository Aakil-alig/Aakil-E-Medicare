<?php
session_start();
include("../connection.php");

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
        header("location: ../login.php");
        exit();
    }
} else {
    header("location: ../login.php");
    exit();
}

$userrow = $database->query("select * from patient where pemail='".$_SESSION["user"]."'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];

// Set current page for menu
$page = 'reports';

if(!isset($_GET['id'])) {
    header("location: reports.php");
    exit();
}

$report_id = $_GET['id'];
$report = $database->query("SELECT pr.*, p.pname, p.pemail, d.docname, d.specialties as docspec 
                          FROM patient_reports pr 
                          JOIN patient p ON pr.patient_id = p.pid 
                          JOIN doctor d ON pr.doctor_id = d.docid 
                          WHERE pr.report_id='$report_id' AND pr.patient_id='$userid'")->fetch_assoc();

// Security check - make sure the report belongs to the logged-in patient
if(!$report) {
    header("location: reports.php");
    exit();
}

// Check if PDF download is requested
if(isset($_GET['download']) && $_GET['download'] == 'pdf') {
    // Set headers for PDF download
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="medical_report_'.$report_id.'.html"');
    
    // Create the HTML content
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Medical Report - '.$report['disease'].'</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                margin: 40px;
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
            }
            .section {
                margin-bottom: 20px;
            }
            .section h2 {
                color: #0083b0;
                border-bottom: 1px solid #eee;
                padding-bottom: 5px;
            }
            .patient-info {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Medical Report</h1>
            <p>Generated on: '.date('F j, Y').'</p>
        </div>

        <div class="section patient-info">
            <h2>Patient Information</h2>
            <p><strong>Name:</strong> '.$report['pname'].'</p>
            <p><strong>Email:</strong> '.$report['pemail'].'</p>
            <p><strong>Doctor:</strong> Dr. '.$report['docname'].' ('.$report['docspec'].')</p>
            <p><strong>Date:</strong> '.date('F j, Y', strtotime($report['report_date'])).'</p>
        </div>

        <div class="section">
            <h2>Medical Details</h2>
            <p><strong>Disease/Condition:</strong> '.$report['disease'].'</p>
        </div>

        <div class="section">
            <h2>Symptoms</h2>
            <p>'.nl2br($report['symptoms']).'</p>
        </div>

        <div class="section">
            <h2>Diagnosis</h2>
            <p>'.nl2br($report['diagnosis']).'</p>
        </div>

        <div class="section">
            <h2>Treatment Plan</h2>
            <p>'.nl2br($report['treatment']).'</p>
        </div>

        <div class="section">
            <h2>Prescription</h2>
            <p>'.nl2br($report['prescription']).'</p>
        </div>
    </body>
    </html>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Medical Report</title>
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0083b0;
            --secondary-color: #00b4db;
            --text-color: #333;
            --light-gray: #f8f9fa;
        }

        .report-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .report-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            text-align: center;
        }

        .report-header h2 {
            margin: 0;
            font-size: 1.5em;
        }

        .report-content {
            padding: 20px;
        }

        .patient-info {
            background: var(--light-gray);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .report-section {
            margin-bottom: 25px;
        }

        .report-section h3 {
            color: var(--primary-color);
            border-bottom: 2px solid var(--light-gray);
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-secondary {
            background: #666;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .actions {
            text-align: center;
            padding: 20px;
            background: var(--light-gray);
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .report-container {
                margin: 10px;
                border-radius: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include("menu.php"); ?>
        <div class="dash-body" style="margin-top: 15px">
            <div class="report-container">
                <div class="report-header">
                    <h2><i class="fas fa-file-medical"></i> Medical Report</h2>
                    <p style="margin: 10px 0 0">Date: <?php echo date('F j, Y', strtotime($report['report_date'])); ?></p>
                </div>

                <div class="report-content">
                    <div class="patient-info">
                        <h3><i class="fas fa-user"></i> Patient Information</h3>
                        <p><strong>Name:</strong> <?php echo $report['pname']; ?></p>
                        <p><strong>Email:</strong> <?php echo $report['pemail']; ?></p>
                        <p><strong>Doctor:</strong> Dr. <?php echo $report['docname']; ?> (<?php echo $report['docspec']; ?>)</p>
                    </div>

                    <div class="report-section">
                        <h3><i class="fas fa-disease"></i> Disease/Condition</h3>
                        <p><?php echo $report['disease']; ?></p>
                    </div>

                    <div class="report-section">
                        <h3><i class="fas fa-notes-medical"></i> Symptoms</h3>
                        <p><?php echo nl2br($report['symptoms']); ?></p>
                    </div>

                    <div class="report-section">
                        <h3><i class="fas fa-stethoscope"></i> Diagnosis</h3>
                        <p><?php echo nl2br($report['diagnosis']); ?></p>
                    </div>

                    <div class="report-section">
                        <h3><i class="fas fa-hand-holding-medical"></i> Treatment Plan</h3>
                        <p><?php echo nl2br($report['treatment']); ?></p>
                    </div>

                    <div class="report-section">
                        <h3><i class="fas fa-prescription"></i> Prescription</h3>
                        <p><?php echo nl2br($report['prescription']); ?></p>
                    </div>

                    <div class="actions">
                        <a href="reports.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Reports
                        </a>
                        <a href="?id=<?php echo $report_id; ?>&download=pdf" class="btn btn-primary">
                            <i class="fas fa-download"></i> Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 