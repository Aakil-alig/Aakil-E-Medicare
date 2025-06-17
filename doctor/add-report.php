<?php
session_start();
include("../connection.php");

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
        header("location: ../login.php");
        exit();
    }else{
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
    exit();
}

$userrow = $database->query("select * from doctor where docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["docid"];
$username = $userfetch["docname"];

if($_POST) {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $userid;
    $appointment_id = $_POST['appointment_id'] ?? null;
    $disease = $database->real_escape_string($_POST['disease']);
    $symptoms = $database->real_escape_string($_POST['symptoms']);
    $diagnosis = $database->real_escape_string($_POST['diagnosis']);
    $treatment = $database->real_escape_string($_POST['treatment']);
    $prescription = $database->real_escape_string($_POST['prescription']);

    $sql = "INSERT INTO patient_reports (patient_id, doctor_id, appointment_id, disease, symptoms, diagnosis, treatment, prescription) 
            VALUES ('$patient_id', '$doctor_id', '$appointment_id', '$disease', '$symptoms', '$diagnosis', '$treatment', '$prescription')";
    
    if($database->query($sql)) {
        // Don't reset session variables here as they're already set
        header("location: patient.php?action=report_added");
        exit();
    } else {
        $error = $database->error;
        header("location: add-report.php?pid=$patient_id&error=".urlencode($error));
        exit();
    }
}

// Ensure session is active and valid
if(!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'd') {
    header("location: ../login.php");
    exit();
}

$patient_id = $_GET['pid'];
$patient_info = $database->query("SELECT * FROM patient WHERE pid='$patient_id'")->fetch_assoc();

// Set the current page for menu highlighting
$page = 'patient';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Patient Report</title>
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

        body {
            background-color: var(--light-gray);
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .dash-body {
            flex: 1;
            margin-left: 280px;
            padding: 20px;
        }

        .report-form {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-header h2 {
            margin: 0;
            font-size: 1.5em;
        }

        .patient-info {
            background: var(--light-gray);
            padding: 20px;
            border-radius: 10px;
            margin: 20px;
        }

        .patient-info h3 {
            color: var(--primary-color);
            margin-top: 0;
        }

        .form-content {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-group input[type="text"]:focus,
        .form-group textarea:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,131,176,0.1);
        }

        .form-group textarea {
            height: 120px;
            resize: vertical;
        }

        .form-actions {
            padding: 20px;
            background: var(--light-gray);
            text-align: center;
            border-top: 1px solid #eee;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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

        .btn-primary:hover {
            background: var(--secondary-color);
        }

        .btn-secondary:hover {
            background: #555;
        }

        @media (max-width: 768px) {
            .dash-body {
                margin-left: 0;
                padding: 10px;
            }
            
            .report-form {
                margin: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include("menu.php"); ?>
        <div class="dash-body">
            <div class="report-form">
                <div class="form-header">
                    <h2><i class="fas fa-file-medical"></i> Create Patient Report</h2>
                    <a href="patient.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                
                <div class="patient-info">
                    <h3><i class="fas fa-user-injured"></i> Patient Information</h3>
                    <p><strong>Name:</strong> <?php echo $patient_info['pname']; ?></p>
                    <p><strong>Email:</strong> <?php echo $patient_info['pemail']; ?></p>
                    <p><strong>Contact:</strong> <?php echo $patient_info['ptel']; ?></p>
                </div>

                <form action="" method="POST">
                    <div class="form-content">
                        <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                        <input type="hidden" name="appointment_id" value="<?php echo $_GET['appointment_id'] ?? ''; ?>">
                        
                        <div class="form-group">
                            <label for="disease">Disease/Condition</label>
                            <input type="text" name="disease" id="disease" required 
                                   placeholder="Enter the primary disease or condition">
                        </div>

                        <div class="form-group">
                            <label for="symptoms">Symptoms</label>
                            <textarea name="symptoms" id="symptoms" required
                                      placeholder="List all reported symptoms"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="diagnosis">Diagnosis</label>
                            <textarea name="diagnosis" id="diagnosis" required
                                      placeholder="Enter your medical diagnosis"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="treatment">Treatment Plan</label>
                            <textarea name="treatment" id="treatment" required
                                      placeholder="Describe the recommended treatment plan"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="prescription">Prescription</label>
                            <textarea name="prescription" id="prescription" required
                                      placeholder="Enter medication details and instructions"></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 