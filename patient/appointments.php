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

// Get upcoming appointments
$upcoming_query = "SELECT a.*, s.*, d.docname, d.specialties 
                  FROM appointment a 
                  JOIN schedule s ON a.scheduleid = s.scheduleid 
                  JOIN doctor d ON s.docid = d.docid 
                  WHERE a.pid = $userid AND s.scheduledate >= CURDATE() 
                  ORDER BY s.scheduledate ASC, s.scheduletime ASC";
$upcoming = $database->query($upcoming_query);

// Get past appointments
$past_query = "SELECT a.*, s.*, d.docname, d.specialties 
              FROM appointment a 
              JOIN schedule s ON a.scheduleid = s.scheduleid 
              JOIN doctor d ON s.docid = d.docid 
              WHERE a.pid = $userid AND s.scheduledate < CURDATE() 
              ORDER BY s.scheduledate DESC, s.scheduletime DESC";
$past = $database->query($past_query);

// Set current page for menu
$page = 'appointments';
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
    <title>My Appointments</title>
    <style>
        .appointment-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .appointment-header h2 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }

        .new-appointment-btn {
            background: #0083b0;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .new-appointment-btn:hover {
            background: #00b4db;
            transform: translateY(-2px);
        }

        .appointment-tabs {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .tab {
            padding: 10px 20px;
            border: none;
            background: none;
            color: #666;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            border-bottom: 2px solid transparent;
        }

        .tab.active {
            color: #0083b0;
            border-bottom-color: #0083b0;
        }

        .appointment-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .appointment-card:hover {
            transform: translateY(-5px);
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .appointment-date {
            font-size: 1.2em;
            color: #0083b0;
        }

        .appointment-time {
            color: #666;
        }

        .appointment-doctor {
            margin: 10px 0;
        }

        .appointment-doctor i {
            color: #0083b0;
            margin-right: 5px;
        }

        .appointment-specialty {
            color: #666;
            font-size: 0.9em;
        }

        .appointment-actions {
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
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .view-btn {
            background: #0083b0;
        }

        .cancel-btn {
            background: #dc3545;
        }

        .reschedule-btn {
            background: #ffc107;
            color: #000;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .no-appointments {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .no-appointments img {
            width: 200px;
            margin-bottom: 20px;
        }

        .no-appointments h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .no-appointments p {
            color: #666;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .appointment-container {
                padding: 10px;
            }
            
            .appointment-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .appointment-tabs {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include("menu.php"); ?>
        <div class="dash-body">
            <div class="appointment-container">
                <div class="appointment-header">
                    <h2>My Appointments</h2>
                    <a href="schedule.php" class="new-appointment-btn">
                        <i class="fas fa-plus"></i>
                        Book New Appointment
                    </a>
                </div>

                <div class="appointment-tabs">
                    <button class="tab active" onclick="showTab('upcoming')">Upcoming Appointments</button>
                    <button class="tab" onclick="showTab('past')">Past Appointments</button>
                </div>

                <div id="upcoming" class="tab-content">
                    <?php if($upcoming->num_rows == 0): ?>
                        <div class="no-appointments">
                            <img src="../img/calendar.svg" alt="No Appointments">
                            <h3>No Upcoming Appointments</h3>
                            <p>You don't have any upcoming appointments scheduled.</p>
                            <a href="schedule.php" class="new-appointment-btn">
                                <i class="fas fa-plus"></i>
                                Book an Appointment
                            </a>
                        </div>
                    <?php else: ?>
                        <?php while($row = $upcoming->fetch_assoc()): ?>
                            <div class="appointment-card">
                                <div class="appointment-header">
                                    <div class="appointment-date">
                                        <?php echo date('l, F j, Y', strtotime($row['scheduledate'])); ?>
                                        <span class="appointment-time">
                                            <?php echo date('g:i A', strtotime($row['scheduletime'])); ?>
                                        </span>
                                    </div>
                                    <div class="appointment-number">
                                        #<?php echo $row['apponum']; ?>
                                    </div>
                                </div>
                                <div class="appointment-doctor">
                                    <i class="fas fa-user-md"></i>
                                    Dr. <?php echo $row['docname']; ?>
                                    <span class="appointment-specialty">(<?php echo $row['specialties']; ?>)</span>
                                </div>
                                <div class="appointment-title">
                                    <?php echo $row['title']; ?>
                                </div>
                                <div class="appointment-actions">
                                    <a href="?action=view&id=<?php echo $row['appoid']; ?>" class="action-btn view-btn">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </a>
                                    <button onclick="confirmCancel(<?php echo $row['appoid']; ?>)" class="action-btn cancel-btn">
                                        <i class="fas fa-times"></i>
                                        Cancel
                                    </button>
                                    <a href="schedule.php?action=reschedule&id=<?php echo $row['appoid']; ?>" class="action-btn reschedule-btn">
                                        <i class="fas fa-calendar-alt"></i>
                                        Reschedule
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>

                <div id="past" class="tab-content" style="display: none;">
                    <?php if($past->num_rows == 0): ?>
                        <div class="no-appointments">
                            <img src="../img/history.svg" alt="No Past Appointments">
                            <h3>No Past Appointments</h3>
                            <p>You haven't had any appointments yet.</p>
                        </div>
                    <?php else: ?>
                        <?php while($row = $past->fetch_assoc()): ?>
                            <div class="appointment-card">
                                <div class="appointment-header">
                                    <div class="appointment-date">
                                        <?php echo date('l, F j, Y', strtotime($row['scheduledate'])); ?>
                                        <span class="appointment-time">
                                            <?php echo date('g:i A', strtotime($row['scheduletime'])); ?>
                                        </span>
                                    </div>
                                    <div class="appointment-number">
                                        #<?php echo $row['apponum']; ?>
                                    </div>
                                </div>
                                <div class="appointment-doctor">
                                    <i class="fas fa-user-md"></i>
                                    Dr. <?php echo $row['docname']; ?>
                                    <span class="appointment-specialty">(<?php echo $row['specialties']; ?>)</span>
                                </div>
                                <div class="appointment-title">
                                    <?php echo $row['title']; ?>
                                </div>
                                <div class="appointment-actions">
                                    <a href="?action=view&id=<?php echo $row['appoid']; ?>" class="action-btn view-btn">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            
            // Show selected tab content
            document.getElementById(tabName).style.display = 'block';
            
            // Update tab buttons
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        function confirmCancel(appointmentId) {
            if(confirm('Are you sure you want to cancel this appointment?')) {
                window.location.href = `?action=cancel&id=${appointmentId}`;
            }
        }
    </script>

    <?php
    if(isset($_GET['action']) && isset($_GET['id'])) {
        $action = $_GET['action'];
        $appoid = $_GET['id'];
        
        if($action == 'cancel') {
            // Add appointment cancellation logic here
            $database->query("DELETE FROM appointment WHERE appoid=$appoid AND pid=$userid");
            echo "<script>
                alert('Appointment cancelled successfully.');
                window.location.href='appointments.php';
            </script>";
        }
        
        if($action == 'view') {
            $appointment = $database->query("
                SELECT a.*, s.*, d.docname, d.specialties,
                    (SELECT GROUP_CONCAT(CONCAT(disease, ':', diagnosis, ':', treatment, ':', prescription) SEPARATOR '||')
                     FROM patient_reports 
                     WHERE appointment_id=a.appoid) as report_data
                FROM appointment a 
                JOIN schedule s ON a.scheduleid = s.scheduleid 
                JOIN doctor d ON s.docid = d.docid 
                WHERE a.appoid=$appoid AND a.pid=$userid
            ")->fetch_assoc();
            
            if($appointment) {
                echo '<div class="popup-overlay">
                        <div class="popup-content">
                            <h2>Appointment Details</h2>
                            <div class="appointment-details">
                                <p><strong>Appointment Number:</strong> '.$appointment["apponum"].'</p>
                                <p><strong>Session Title:</strong> '.$appointment["title"].'</p>
                                <p><strong>Doctor:</strong> Dr. '.$appointment["docname"].'</p>
                                <p><strong>Specialty:</strong> '.$appointment["specialties"].'</p>
                                <p><strong>Date:</strong> '.date("F j, Y", strtotime($appointment["scheduledate"])).'</p>
                                <p><strong>Time:</strong> '.date("g:i A", strtotime($appointment["scheduletime"])).'</p>';
                
                if($appointment["report_data"]) {
                    $reports = explode("||", $appointment["report_data"]);
                    foreach($reports as $report) {
                        list($disease, $diagnosis, $treatment, $prescription) = explode(":", $report);
                        echo '<div class="report-section">
                                <h3>Medical Report</h3>
                                <p><strong>Disease/Condition:</strong> '.$disease.'</p>
                                <p><strong>Diagnosis:</strong> '.$diagnosis.'</p>
                                <p><strong>Treatment Plan:</strong> '.$treatment.'</p>
                                <p><strong>Prescription:</strong> '.$prescription.'</p>
                            </div>';
                    }
                }
                
                echo '</div>
                        <div class="popup-actions">
                            <a href="appointments.php" class="action-btn">Close</a>
                        </div>
                    </div>
                </div>';
            }
        }
    }
    ?>
</body>
</html> 