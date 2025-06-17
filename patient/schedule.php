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

// Initialize variables
$insertkey = "";
$searchtype = "All";
$q = "'";

if($_POST){
    if(!empty($_POST["search"])){
        $insertkey = $_POST["search"];
        $searchtype = "Search Result : ";
    }
}

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
    <title>Sessions</title>
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

        .profile-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1));
            pointer-events: none;
        }

        .profile-container img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            transition: var(--transition);
            margin-bottom: 15px;
        }

        .profile-container img:hover {
            transform: scale(1.1);
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

        /* Menu Items Styling */
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

        /* Main Content Area */
        .dash-body {
            flex: 1;
            margin-left: 280px;
            padding: 25px;
            transition: var(--transition);
        }

        /* Search Bar Styling */
        .search-container {
            background: white;
            padding: 25px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            margin-bottom: 25px;
            transition: var(--transition);
        }

        .search-container:hover {
            box-shadow: var(--shadow-lg);
        }

        .search-container form {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-container input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #eee;
            border-radius: var(--radius);
            font-size: 15px;
            transition: var(--transition);
        }

        .search-container input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,131,176,0.1);
        }

        /* Table Styling */
        .data-table {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            margin-bottom: 25px;
            overflow: hidden;
            transition: var(--transition);
        }

        .data-table:hover {
            box-shadow: var(--shadow-lg);
        }

        .table-header {
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-header h3 {
            margin: 0;
            color: var(--text-dark);
            font-size: 1.2em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-header h3 i {
            color: var(--primary-color);
        }

        .table-responsive {
            padding: 20px;
            overflow-x: auto;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-table th {
            background: var(--bg-light);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.95em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #eee;
        }

        .modern-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: var(--text-light);
            font-size: 0.95em;
            transition: var(--transition);
        }

        .modern-table tr:hover td {
            background: var(--bg-light);
            color: var(--text-dark);
        }

        /* Button Styling */
        .btn-primary {
            background: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: var(--radius);
            font-size: 0.95em;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,131,176,0.3);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .menu {
                width: 240px;
            }
            .dash-body {
                margin-left: 240px;
            }
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
            .search-container form {
                flex-direction: column;
            }
            .btn-primary {
                width: 100%;
                justify-content: center;
            }
            .table-responsive {
                padding: 10px;
            }
            .modern-table th,
            .modern-table td {
                padding: 10px;
            }
        }

        /* Calendar Container */
        .calendar-container {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .calendar-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 25px;
            color: white;
        }

        .calendar-header h2 {
            margin: 0;
            font-size: 1.5em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .calendar-nav {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 15px;
        }

        .date-picker {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: var(--radius);
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 1em;
        }

        .date-picker:focus {
            outline: none;
            border-color: white;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
            color: white;
        }

        .upcoming-icon { background: var(--primary-color); }
        .completed-icon { background: var(--success); }
        .cancelled-icon { background: var(--danger); }

        .stat-info h3 {
            margin: 0;
            font-size: 0.9em;
            color: var(--text-light);
        }

        .stat-info p {
            margin: 5px 0 0;
            font-size: 1.8em;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Sessions Grid */
        .sessions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            padding: 25px;
        }

        .session-card {
            background: white;
            border-radius: var(--radius);
            overflow: hidden;
            transition: var(--transition);
            border: 1px solid #eee;
        }

        .session-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .session-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 15px;
            color: white;
        }

        .session-date {
            font-size: 1.2em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .session-time {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 0.9em;
        }

        .session-body {
            padding: 20px;
        }

        .doctor-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .doctor-info img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .doctor-details h4 {
            margin: 0;
            color: var(--text-dark);
        }

        .doctor-details p {
            margin: 5px 0 0;
            color: var(--text-light);
            font-size: 0.9em;
        }

        .session-info {
            display: grid;
            gap: 10px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
        }

        .session-footer {
            padding: 15px 20px;
            background: var(--bg-light);
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 15px;
            border-radius: var(--radius);
            font-size: 0.9em;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: var(--transition);
            flex: 1;
            justify-content: center;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            grid-column: 1 / -1;
        }

        .empty-state img {
            width: 200px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            margin: 0 0 10px;
            color: var(--text-dark);
        }

        .empty-state p {
            margin: 0 0 20px;
            color: var(--text-light);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: 1fr;
            }

            .sessions-grid {
                grid-template-columns: 1fr;
            }

            .session-footer {
                flex-direction: column;
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
                <a href="../logout.php" class="btn-primary" style="background: rgba(255,255,255,0.2);">
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
            <div class="calendar-container">
                <div class="calendar-header">
                    <h2>
                        <i class="fas fa-calendar-alt"></i>
                        Available Sessions
                    </h2>
                    <div class="calendar-nav">
                        <input type="text" id="date-picker" class="date-picker" placeholder="Select Date">
                        <button class="btn btn-primary" onclick="filterByDate()">
                            <i class="fas fa-filter"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="stats-container">
                <?php
                    // Get upcoming sessions count
                    $upcoming = $database->query("SELECT COUNT(*) as count FROM schedule WHERE scheduledate >= CURRENT_DATE()");
                    $upcoming_count = $upcoming->fetch_assoc()['count'];

                    // Get completed sessions count
                    $completed = $database->query("SELECT COUNT(*) as count FROM schedule WHERE scheduledate < CURRENT_DATE()");
                    $completed_count = $completed->fetch_assoc()['count'];

                    // Get total doctors
                    $doctors = $database->query("SELECT COUNT(DISTINCT docid) as count FROM schedule");
                    $doctors_count = $doctors->fetch_assoc()['count'];
                ?>
                <div class="stat-card">
                    <div class="stat-icon upcoming-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Upcoming Sessions</h3>
                        <p><?php echo $upcoming_count; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon completed-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Completed Sessions</h3>
                        <p><?php echo $completed_count; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Available Doctors</h3>
                        <p><?php echo $doctors_count; ?></p>
                    </div>
                </div>
            </div>

            <div class="sessions-grid">
                <?php
                    $sqlmain = "SELECT schedule.*, doctor.docname, doctor.docid, doctor.specialties, specialties.sname 
                               FROM schedule 
                               INNER JOIN doctor ON schedule.docid = doctor.docid 
                               LEFT JOIN specialties ON doctor.specialties = specialties.id
                               WHERE scheduledate >= CURRENT_DATE()
                               ORDER BY scheduledate ASC, scheduletime ASC";
                    
                    $result = $database->query($sqlmain);

                    if($result->num_rows == 0) {
                        echo '<div class="empty-state">
                            <img src="../img/empty-calendar.svg" alt="No sessions">
                            <h3>No Sessions Available</h3>
                            <p>There are no upcoming sessions scheduled at the moment.</p>
                            <a href="doctors.php" class="btn btn-primary">
                                <i class="fas fa-user-md"></i>
                                Find a Doctor
                            </a>
                        </div>';
                    } else {
                        while($row = $result->fetch_assoc()) {
                            $scheduleid = $row["scheduleid"];
                            $title = $row["title"];
                            $docname = $row["docname"];
                            $scheduledate = $row["scheduledate"];
                            $scheduletime = $row["scheduletime"];
                            $specialties = $row["sname"] ?? "General";
                            
                            // Format date and time
                            $date = new DateTime($scheduledate);
                            $time = new DateTime($scheduletime);
                            
                            echo '<div class="session-card">
                                <div class="session-header">
                                    <h3 class="session-date">
                                        <i class="fas fa-calendar"></i>
                                        '.$date->format('l, M j, Y').'
                                    </h3>
                                    <p class="session-time">
                                        <i class="fas fa-clock"></i>
                                        '.$time->format('g:i A').'
                                    </p>
                                </div>
                                <div class="session-body">
                                    <div class="doctor-info">
                                        <img src="../img/doctor.png" alt="Doctor">
                                        <div class="doctor-details">
                                            <h4>Dr. '.$docname.'</h4>
                                            <p>'.$specialties.'</p>
                                        </div>
                                    </div>
                                    <div class="session-info">
                                        <div class="info-item">
                                            <i class="fas fa-tag"></i>
                                            '.$title.'
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-user-clock"></i>
                                            30 Minutes Duration
                                        </div>
                                    </div>
                                </div>
                                <div class="session-footer">
                                    <a href="booking.php?scheduleid='.$scheduleid.'" class="btn btn-primary">
                                        <i class="fas fa-calendar-check"></i>
                                        Book Appointment
                                    </a>
                                </div>
                            </div>';
                        }
                    }
                ?>
            </div>
        </div>
    </div>

    <script>
        // Initialize date picker
        flatpickr("#date-picker", {
            dateFormat: "Y-m-d",
            minDate: "today",
            maxDate: new Date().fp_incr(30), // Allow booking up to 30 days in advance
        });

        function filterByDate() {
            const date = document.getElementById('date-picker').value;
            if(date) {
                window.location.href = `schedule.php?date=${date}`;
            }
        }
    </script>
</body>
</html>
