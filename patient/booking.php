<?php
session_start();

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
        header("location: ../login.php");
    }else{
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
}

//import database
include("../connection.php");

$sqlmain= "select * from patient where pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s",$useremail);
$stmt->execute();
$result = $stmt->get_result();
$userfetch=$result->fetch_assoc();
$userid= $userfetch["pid"];
$username=$userfetch["pname"];

date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');

if(!isset($_GET["scheduleid"])){
    header("location: schedule.php");
    exit();
}

$scheduleid = $_GET["scheduleid"];
$sqlmain = "SELECT schedule.*, doctor.*, specialties.sname 
           FROM schedule 
           INNER JOIN doctor ON schedule.docid=doctor.docid 
           LEFT JOIN specialties ON doctor.specialties=specialties.id
           WHERE schedule.scheduleid=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("i", $scheduleid);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    header("location: schedule.php");
    exit();
}

$row = $result->fetch_assoc();
$docname = $row["docname"];
$docemail = $row["docemail"];
$specialties = $row["sname"] ?? "General";
$scheduledate = $row["scheduledate"];
$scheduletime = $row["scheduletime"];
$title = $row["title"];

// Get appointment number
$sql2 = "SELECT COUNT(*) as count FROM appointment WHERE scheduleid=?";
$stmt = $database->prepare($sql2);
$stmt->bind_param("i", $scheduleid);
$stmt->execute();
$result2 = $stmt->get_result();
$apponum = $result2->fetch_assoc()['count'] + 1;

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
        
    <title>Book Appointment</title>
    <style>
        :root {
            --primary-color: #0083b0;
            --secondary-color: #00b4db;
            --accent-color: #38ef7d;
            --text-dark: #333;
            --text-light: #666;
            --bg-light: #f8f9fa;
            --bg-dark: #2c3e50;
            --danger: #dc3545;
            --success: #28a745;
            --warning: #ffc107;
            --info: #17a2b8;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-lg: 0 5px 25px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
            --radius: 10px;
            --radius-lg: 15px;
        }

        body {
            background-color: var(--bg-light);
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .menu {
            width: 280px;
            background: white;
            box-shadow: var(--shadow);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: var(--transition);
            z-index: 1000;
        }

        .menu::-webkit-scrollbar {
            width: 6px;
        }

        .menu::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: var(--radius);
        }

        .profile-container {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 25px;
            border-radius: var(--radius-lg);
            margin: 15px;
            position: relative;
            overflow: hidden;
            color: white;
            text-align: center;
        }

        .profile-container img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            margin-bottom: 15px;
        }

        .profile-title {
            font-size: 1.4em;
            margin: 10px 0 5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .profile-subtitle {
            color: rgba(255,255,255,0.9);
            font-size: 1em;
            margin: 0 0 15px;
        }

        /* Menu Items */
        .menu-btn {
            padding: 15px 25px;
            margin: 5px 15px;
            border-radius: var(--radius);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
        }

        .menu-btn i {
            font-size: 1.2em;
            width: 25px;
            text-align: center;
            color: var(--primary-color);
        }

        .menu-btn:hover {
            background: var(--bg-light);
            transform: translateX(5px);
        }

        .menu-active {
            background: var(--primary-color) !important;
            color: white !important;
        }

        .menu-active i {
            color: white !important;
        }

        /* Main Content */
        .dash-body {
            flex: 1;
            margin-left: 280px;
            padding: 25px;
        }

        .booking-container {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-top: 20px;
        }

        .booking-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 25px;
            color: white;
        }

        .booking-header h2 {
            margin: 0;
            font-size: 1.5em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .booking-body {
            padding: 25px;
        }

        .doctor-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid #eee;
        }

        .doctor-info img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }

        .doctor-details h3 {
            margin: 0 0 5px;
            color: var(--text-dark);
        }

        .doctor-details p {
            margin: 0;
            color: var(--text-light);
        }

        .session-details {
            display: grid;
            gap: 15px;
            margin-bottom: 25px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-dark);
        }

        .detail-item i {
            width: 25px;
            color: var(--primary-color);
        }

        .booking-footer {
            padding: 25px;
            background: var(--bg-light);
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: var(--radius);
            font-size: 1em;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-secondary {
            background: var(--text-light);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        @media (max-width: 768px) {
            .menu {
                width: 100%;
                height: auto;
                position: relative;
            }
            .dash-body {
                margin-left: 0;
                padding: 15px;
            }
            .container {
                flex-direction: column;
            }
            .booking-footer {
                flex-direction: column;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="menu">
            <div class="profile-container">
                <img src="../img/user.png" alt="Patient Profile">
                <h2 class="profile-title"><?php echo substr($username,0,13) ?></h2>
                <p class="profile-subtitle"><?php echo substr($useremail,0,22) ?></p>
                <a href="../logout.php" class="btn btn-primary" style="background: rgba(255,255,255,0.2);">
                    <i class="fas fa-sign-out-alt"></i>
                    Log out
                </a>
            </div>

            <a href="index.php" class="menu-btn">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="doctors.php" class="menu-btn">
                <i class="fas fa-user-md"></i>
                <span>All Doctors</span>
            </a>
            <a href="schedule.php" class="menu-btn menu-active">
                <i class="fas fa-calendar-alt"></i>
                <span>Scheduled Sessions</span>
            </a>
            <a href="appointment.php" class="menu-btn">
                <i class="fas fa-calendar-check"></i>
                <span>My Bookings</span>
            </a>
            <a href="settings.php" class="menu-btn">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>

        <div class="dash-body">
            <div class="booking-container">
                <div class="booking-header">
                    <h2>
                        <i class="fas fa-calendar-check"></i>
                        Book Appointment
                    </h2>
                </div>

                <div class="booking-body">
                    <div class="doctor-info">
                        <img src="../img/doctor.png" alt="Doctor">
                        <div class="doctor-details">
                            <h3>Dr. <?php echo $docname; ?></h3>
                            <p><?php echo $specialties; ?></p>
                            <p><?php echo $docemail; ?></p>
                        </div>
                    </div>

                    <div class="session-details">
                        <div class="detail-item">
                            <i class="fas fa-calendar"></i>
                            <span>Date: <?php echo date('l, F j, Y', strtotime($scheduledate)); ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-clock"></i>
                            <span>Time: <?php echo date('g:i A', strtotime($scheduletime)); ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-tag"></i>
                            <span>Session: <?php echo $title; ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-user-clock"></i>
                            <span>Appointment Number: <?php echo $apponum; ?></span>
                        </div>
                    </div>

                    <form action="booking-complete.php" method="post">
                        <input type="hidden" name="scheduleid" value="<?php echo $scheduleid; ?>">
                        <input type="hidden" name="apponum" value="<?php echo $apponum; ?>">
                        <input type="hidden" name="date" value="<?php echo $today; ?>">
                        <input type="hidden" name="booknow" value="true">
                        
                        <div class="booking-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle"></i>
                                Confirm Booking
                            </button>
                            <a href="schedule.php" class="btn btn-secondary">
                                <i class="fas fa-times-circle"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>