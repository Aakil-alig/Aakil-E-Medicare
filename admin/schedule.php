<?php
session_start();

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
        header("location: ../login.php");
    }
}else{
    header("location: ../login.php");
}

//import database
include("../connection.php");

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Schedule Management</title>
    <style>
        :root {
            --primary-color: #0083b0;
            --secondary-color: #00b4db;
            --accent-color: #38ef7d;
            --text-dark: #333;
            --text-light: #666;
            --bg-light: #f8f9fa;
            --bg-dark: #2c3e50;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--bg-light);
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            border-radius: 10px;
        }

        .profile-container {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 20px;
            border-radius: 15px;
            margin: 15px;
            position: relative;
            overflow: hidden;
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
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            transition: var(--transition);
        }

        .profile-container img:hover {
            transform: scale(1.1);
        }

        .profile-title {
            font-size: 1.4em;
            color: white;
            margin: 10px 0 5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .profile-subtitle {
            color: rgba(255,255,255,0.9);
            font-size: 1em;
            margin: 0;
        }

        /* Menu Items Styling */
        .menu-btn {
            padding: 15px 25px;
            margin: 5px 15px;
            border-radius: 10px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--text-dark);
            text-decoration: none;
        }

        .menu-btn i {
            font-size: 1.2em;
            width: 25px;
            text-align: center;
        }

        .menu-btn:hover {
            background: var(--bg-light);
            transform: translateX(5px);
        }

        .menu-active {
            background: var(--primary-color) !important;
            color: white !important;
        }

        /* Main Content Area */
        .dash-body {
            flex: 1;
            margin-left: 280px;
            padding: 20px;
            transition: var(--transition);
        }

        /* Search Bar Styling */
        .search-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-bottom: 25px;
            transition: var(--transition);
        }

        .search-container:hover {
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }

        .search-container form {
            display: flex;
            gap: 15px;
        }

        .search-container input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #eee;
            border-radius: 10px;
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
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-bottom: 25px;
            overflow: hidden;
            transition: var(--transition);
        }

        .data-table:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .data-table h3 {
            padding: 20px 25px;
            margin: 0;
            color: var(--text-dark);
            font-size: 1.2em;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .data-table h3 i {
            color: var(--primary-color);
        }

        .table-responsive {
            padding: 20px;
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
            border-radius: 8px;
            font-size: 0.95em;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,131,176,0.3);
        }

        .popup {
            animation: transitionIn-Y-bottom 0.5s;
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            padding: 25px;
        }

        .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
        }

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
        }

        .schedule-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-info h4 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .stat-info p {
            margin: 5px 0 0;
            color: #666;
        }

        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .schedule-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .schedule-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .schedule-header {
            padding: 15px;
            background: linear-gradient(135deg, #0083b0, #00b4db);
            color: white;
        }

        .schedule-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .schedule-doctor {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .schedule-body {
            padding: 20px;
        }

        .schedule-info {
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: #666;
        }

        .info-item i {
            color: #0083b0;
            width: 20px;
        }

        .schedule-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-action {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
        }

        .btn-view {
            background: #17a2b8;
        }

        .btn-edit {
            background: #ffc107;
        }

        .btn-delete {
            background: #dc3545;
        }

        .btn-action:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .filter-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }

        .filter-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-label {
            display: block;
            margin-bottom: 8px;
            color: #666;
            font-size: 14px;
        }

        .filter-input {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .filter-input:focus {
            border-color: #0083b0;
            outline: none;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-active {
            background: #e8f7f2;
            color: #28a745;
        }

        .status-completed {
            background: #f8f9fa;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .schedule-stats {
                grid-template-columns: 1fr;
            }
            
            .filter-form {
                flex-direction: column;
            }
            
            .schedule-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="menu">
            <div class="profile-container">
                <img src="../img/user.png" alt="Admin Profile">
                <h2 class="profile-title">Administrator</h2>
                <p class="profile-subtitle">admin@edoc.com</p>
                <a href="../logout.php" class="btn-primary" style="display: inline-block; padding: 10px 20px; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 8px; margin-top: 15px;">
                    <i class="fas fa-sign-out-alt"></i> Log out
                </a>
            </div>

            <a href="index.php" class="menu-btn">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="doctors.php" class="menu-btn">
                <i class="fas fa-user-md"></i>
                <span>Doctors</span>
            </a>
            <a href="schedule.php" class="menu-btn menu-active">
                <i class="fas fa-calendar-alt"></i>
                <span>Schedule</span>
            </a>
            <a href="appointment.php" class="menu-btn">
                <i class="fas fa-calendar-check"></i>
                <span>Appointments</span>
            </a>
            <a href="patient.php" class="menu-btn">
                <i class="fas fa-user-injured"></i>
                <span>Patients</span>
            </a>
        </div>
        <div class="dash-body">
            <div class="schedule-stats">
                <?php
                    $total_sessions = $database->query("SELECT COUNT(*) as count FROM schedule")->fetch_assoc()['count'];
                    $today_sessions = $database->query("SELECT COUNT(*) as count FROM schedule WHERE scheduledate = '$today'")->fetch_assoc()['count'];
                    $upcoming_sessions = $database->query("SELECT COUNT(*) as count FROM schedule WHERE scheduledate > '$today'")->fetch_assoc()['count'];
                ?>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #0083b0">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $total_sessions; ?></h4>
                        <p>Total Sessions</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #28a745">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $today_sessions; ?></h4>
                        <p>Today's Sessions</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #17a2b8">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $upcoming_sessions; ?></h4>
                        <p>Upcoming Sessions</p>
                    </div>
                </div>
            </div>

            <div class="filter-container">
                <form action="" method="post" class="filter-form">
                    <div class="filter-group">
                        <label class="filter-label">Search</label>
                        <input type="search" name="search" class="filter-input" placeholder="Search by title or doctor name" 
                            value="<?php echo isset($_POST['search']) ? $_POST['search'] : ''; ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Doctor</label>
                        <select name="doctor" class="filter-input">
                            <option value="">All Doctors</option>
                            <?php
                                $doctors = $database->query("SELECT * FROM doctor ORDER BY docname");
                                while($doctor = $doctors->fetch_assoc()) {
                                    $selected = (isset($_POST['doctor']) && $_POST['doctor'] == $doctor['docid']) ? 'selected' : '';
                                    echo "<option value='".$doctor['docid']."' ".$selected.">".$doctor['docname']."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Date Range</label>
                        <input type="date" name="date" class="filter-input" 
                            value="<?php echo isset($_POST['date']) ? $_POST['date'] : ''; ?>">
                    </div>
                    
                    <div class="filter-group" style="display: flex; align-items: flex-end;">
                        <button type="submit" class="btn-action btn-view" style="width: 100%;">
                            <i class="fas fa-filter"></i>
                            Apply Filters
                        </button>
                    </div>
                    
                    <div class="filter-group" style="display: flex; align-items: flex-end;">
                        <a href="?action=add-session" class="btn-action" style="width: 100%; background: #0083b0;">
                            <i class="fas fa-plus"></i>
                            Add New Session
                        </a>
                    </div>
                </form>
            </div>

            <div class="schedule-grid">
                <?php
                    $sqlmain = "SELECT schedule.*, doctor.docname, doctor.docid,
                        (SELECT COUNT(*) FROM appointment WHERE appointment.scheduleid = schedule.scheduleid) as total_appointments
                        FROM schedule 
                        INNER JOIN doctor ON schedule.docid = doctor.docid";
                    
                    $conditions = [];
                    $where = "";
                    
                    if($_POST) {
                        if(!empty($_POST["search"])){
                            $keyword = $_POST["search"];
                            $conditions[] = "(doctor.docname LIKE '%$keyword%' OR schedule.title LIKE '%$keyword%')";
                        }
                        if(!empty($_POST["doctor"])){
                            $doctor = $_POST["doctor"];
                            $conditions[] = "doctor.docid = '$doctor'";
                        }
                        if(!empty($_POST["date"])){
                            $date = $_POST["date"];
                            $conditions[] = "schedule.scheduledate = '$date'";
                        }
                    }
                    
                    if(!empty($conditions)) {
                        $where = " WHERE " . implode(" AND ", $conditions);
                    }
                    
                    $sqlmain .= $where . " ORDER BY schedule.scheduledate DESC";
                    
                    $result = $database->query($sqlmain);
                    
                    if($result->num_rows==0) {
                        echo '<div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                            <img src="../img/notfound.svg" alt="No sessions found" style="width:200px;margin-bottom:20px;">
                            <p style="color:#666;font-size:16px;margin:0;">No sessions found matching your criteria</p>
                            <a href="schedule.php" class="btn-action btn-view" style="display:inline-flex;margin-top:15px;">
                                <i class="fas fa-sync"></i>
                                Show All Sessions
                            </a>
                        </div>';
                    } else {
                        while($row = $result->fetch_assoc()) {
                            $scheduleid = $row["scheduleid"];
                            $title = $row["title"];
                            $docname = $row["docname"];
                            $scheduledate = $row["scheduledate"];
                            $scheduletime = $row["scheduletime"];
                            $nop = $row["nop"];
                            $total_appointments = $row["total_appointments"];
                            $status = ($scheduledate < $today) ? 'completed' : 'active';
                            
                            echo '<div class="schedule-card">
                                <div class="schedule-header">
                                    <h3 class="schedule-title">'.substr($title, 0, 30).'</h3>
                                    <p class="schedule-doctor">Dr. '.substr($docname, 0, 20).'</p>
                                </div>
                                <div class="schedule-body">
                                    <div class="schedule-info">
                                        <div class="info-item">
                                            <i class="fas fa-calendar"></i>
                                            <span>'.$scheduledate.'</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-clock"></i>
                                            <span>'.$scheduletime.'</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-users"></i>
                                            <span>'.$total_appointments.' / '.$nop.' Appointments</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="status-badge status-'.$status.'">
                                                '.ucfirst($status).'
                                            </span>
                                        </div>
                                    </div>
                                    <div class="schedule-actions">
                                        <a href="?action=view&id='.$scheduleid.'" class="btn-action btn-view">
                                            <i class="fas fa-eye"></i>
                                            View
                                        </a>
                                        <a href="?action=edit&id='.$scheduleid.'" class="btn-action btn-edit">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </a>
                                        <a href="?action=drop&id='.$scheduleid.'&name='.$title.'" class="btn-action btn-delete">
                                            <i class="fas fa-trash"></i>
                                            Delete
                                        </a>
                                    </div>
                                </div>
                            </div>';
                        }
                    }
                ?>
            </div>
        </div>
    </div>

    <?php
    
    if($_GET){
        $id=$_GET["id"];
        $action=$_GET["action"];
        if($action=='add-session'){
            $error_1 = isset($_GET["error"]) ? $_GET["error"] : '0';
            $errorlist= array(
                '1'=>'<p style="color:var(--danger);margin:0 0 10px;">Failed to add session. Please try again.</p>',
                '2'=>'<p style="color:var(--danger);margin:0 0 10px;">Session date must be in the future.</p>',
                '3'=>'<p style="color:var(--danger);margin:0 0 10px;">Maximum bookings must be greater than 0.</p>',
                '0'=>'',
            );

            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <a class="close" href="schedule.php">&times;</a> 
                        <div style="display: flex;justify-content: center;">
                            <div class="abc">
                                <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                    <tr>
                                        <td class="label-td" colspan="2">'.$errorlist[$error_1].'</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Add New Session.</p><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <form action="add-session.php" method="POST" class="add-new-form">
                                                <label for="title" class="form-label">Session Title : </label>
                                                <input type="text" name="title" class="input-text" placeholder="Name of this Session" required><br>

                                                <label for="docid" class="form-label">Select Doctor: </label>
                                                <select name="docid" class="input-text" required>
                                                    <option value="" disabled selected>Choose Doctor Name from the list</option>';
                                                    $list11 = $database->query("select * from doctor order by docname asc;");
                                                    for ($y=0;$y<$list11->num_rows;$y++){
                                                        $row00=$list11->fetch_assoc();
                                                        $sn=$row00["docname"];
                                                        $id00=$row00["docid"];
                                                        echo "<option value=".$id00.">$sn</option>";
                                                    }
                                                echo '</select><br>
                                                
                                                <label for="nop" class="form-label">Number of Patients/Appointment Numbers : </label>
                                                <input type="number" name="nop" class="input-text" min="1" placeholder="The final appointment number for this session depends on this number" required><br>
                                                
                                                <label for="date" class="form-label">Session Date: </label>
                                                <input type="date" name="date" class="input-text" min="'.date('Y-m-d').'" required><br>
                                                
                                                <label for="time" class="form-label">Schedule Time: </label>
                                                <input type="time" name="time" class="input-text" placeholder="Time" required><br>
                                                
                                                <br>
                                                <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type="submit" value="Add Session" class="login-btn btn-primary btn">
                                            </form>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </center>
                    <br><br>
                </div>
            </div>';
        }elseif($action=='session-added'){
            $titleget=$_GET["title"];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                    <br><br>
                        <h2>Session Placed.</h2>
                        <a class="close" href="schedule.php">&times;</a>
                        <div class="content">
                        '.substr($titleget,0,40).' was scheduled.<br><br>
                            
                        </div>
                        <div style="display: flex;justify-content: center;">
                        
                        <a href="schedule.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>
                        <br><br><br><br>
                        </div>
                    </center>
            </div>
            </div>
            ';
        }elseif($action=='drop'){
            $nameget=$_GET["name"];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2>Are you sure?</h2>
                        <a class="close" href="schedule.php">&times;</a>
                        <div class="content">
                            You want to delete this session<br>('.substr($nameget,0,40).').
                            
                        </div>
                        <div style="display: flex;justify-content: center;gap:20px;margin-top:20px;">
                            <a href="delete-session.php?id='.$id.'" class="btn-primary">
                                <i class="fas fa-check"></i>
                                Yes
                            </a>
                            <a href="schedule.php" class="btn-primary" style="background:var(--bg-dark)">
                                <i class="fas fa-times"></i>
                                No
                            </a>
                        </div>
                    </center>
            </div>
            </div>
            '; 
        }elseif($action=='view'){
            $sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop,schedule.docid from schedule inner join doctor on schedule.docid=doctor.docid where schedule.scheduleid=$id";
            $result= $database->query($sqlmain);
            $row=$result->fetch_assoc();
            $docname=$row["docname"];
            $scheduleid=$row["scheduleid"];
            $title=$row["title"];
            $scheduledate=$row["scheduledate"];
            $scheduletime=$row["scheduletime"];
            $nop=$row["nop"];
            $docid=$row["docid"];

            $sqlmain12= "select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.scheduleid=$id;";
            $result12= $database->query($sqlmain12);
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup" style="width: 70%;">
                    <center>
                        <h2></h2>
                        <a class="close" href="schedule.php">&times;</a>
                        <div class="content">
                            
                            
                        </div>
                        <div class="abc scroll" style="display: flex;justify-content: center;">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        
                            <tr>
                                <td>
                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details.</p><br><br>
                                </td>
                            </tr>
                            
                            <tr>
                                
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Session Title: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    '.$title.'<br><br>
                                </td>
                                
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Email" class="form-label">Doctor of this session: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$docname.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="nic" class="form-label">Scheduled Date: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$scheduledate.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Tele" class="form-label">Scheduled Time: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$scheduletime.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="spec" class="form-label"><b>Patients that Already registerd for this session:</b> ('.$result12->num_rows."/".$nop.')</label>
                                    <br><br>
                                </td>
                            </tr>

                            
                            <tr>
                            <td colspan="4">
                                <center>
                                 <div class="abc scroll">
                                 <table width="100%" class="sub-table scrolldown" border="0">
                                 <thead>
                                 <tr>   
                                        <th class="table-headin">
                                             Patient ID
                                         </th>
                                         <th class="table-headin">
                                             Patient name
                                         </th>
                                         <th class="table-headin">
                                             
                                             Appointment number
                                             
                                         </th>
                                        
                                         
                                         <th class="table-headin">
                                             Patient Telephone
                                         </th>
                                         
                                 </thead>
                                 <tbody>';
                                 
                
                
                                         
                                         $result= $database->query($sqlmain12);
                
                                         if($result->num_rows==0){
                                             echo '<tr>
                                             <td colspan="7">
                                             <br><br><br><br>
                                             <center>
                                             <img src="../img/notfound.svg" width="25%">
                                             
                                             <br>
                                             <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We  couldnt find anything related to your keywords !</p>
                                             <a class="non-style-link" href="appointment.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Appointments &nbsp;</font></button>
                                             </a>
                                             </center>
                                             <br><br><br><br>
                                             </td>
                                             </tr>';
                                             
                                         }
                                         else{
                                         for ( $x=0; $x<$result->num_rows;$x++){
                                             $row=$result->fetch_assoc();
                                             $apponum=$row["apponum"];
                                             $pid=$row["pid"];
                                             $pname=$row["pname"];
                                             $ptel=$row["ptel"];
                                             
                                             echo '<tr style="text-align:center;">
                                                <td>
                                                '.substr($pid,0,15).'
                                                </td>
                                                 <td style="font-weight:600;padding:25px">'.
                                                 
                                                 substr($pname,0,25)
                                                 .'</td >
                                                 <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">
                                                 '.$apponum.'
                                                 
                                                 </td>
                                                 <td>
                                                 '.substr($ptel,0,25).'
                                                 </td>
                                                 
                                                 
                
                                                 
                                             </tr>';
                                             
                                         }
                                     }
                                          
                                     
                
                                    echo '</tbody>
                
                                 </table>
                                 </div>
                                 </center>
                            </td> 
                         </tr>

                        </table>
                        </div>
                    </center>
                    <br><br>
            </div>
            </div>
            ';  
        }elseif($action=='edit'){
            $sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop,schedule.docid from schedule inner join doctor on schedule.docid=doctor.docid where schedule.scheduleid=$id";
            $result= $database->query($sqlmain);
            $row=$result->fetch_assoc();
            $docname=$row["docname"];
            $scheduleid=$row["scheduleid"];
            $title=$row["title"];
            $scheduledate=$row["scheduledate"];
            $scheduletime=$row["scheduletime"];
            $nop=$row["nop"];
            $docid=$row["docid"];
            
            $error_1 = isset($_GET["error"]) ? $_GET["error"] : '';
            $errorlist= array(
                '1'=>'<p style="color:var(--danger);margin:0 0 10px;">Failed to update session.</p>',
                '2'=>'<p style="color:var(--danger);margin:0 0 10px;">Session date must be in the future.</p>',
                '3'=>'<p style="color:var(--danger);margin:0 0 10px;">Maximum bookings must be greater than 0.</p>',
                '4'=>'<p style="color:var(--success);margin:0 0 10px;">Session updated successfully.</p>',
                '0'=>'',
            );

            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <a class="close" href="schedule.php">&times;</a> 
                        <div style="display: flex;justify-content: center;">
                            <div class="abc">
                                <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                    <tr>
                                        <td class="label-td" colspan="2">'.$errorlist[$error_1].'</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Edit Session Details.</p><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <form action="edit-session.php" method="POST" class="add-new-form">
                                                <input type="hidden" name="scheduleid" value="'.$scheduleid.'">
                                                <input type="hidden" name="docid" value="'.$docid.'">
                                                <label for="title" class="form-label">Session Title : </label>
                                                <input type="text" name="title" class="input-text" value="'.$title.'" placeholder="Name of this Session" required><br>
                                                
                                                <label for="nop" class="form-label">Number of Patients/Appointment Numbers : </label>
                                                <input type="number" name="nop" class="input-text" min="0" value="'.$nop.'" placeholder="The final appointment number for this session depends on this number" required><br>
                                                
                                                <label for="date" class="form-label">Session Date: </label>
                                                <input type="date" name="date" class="input-text" value="'.$scheduledate.'" min="'.date('Y-m-d').'" required><br>
                                                
                                                <label for="time" class="form-label">Schedule Time: </label>
                                                <input type="time" name="time" class="input-text" value="'.$scheduletime.'" placeholder="Time" required><br>
                                                
                                                <br>
                                                <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type="submit" value="Save Changes" class="login-btn btn-primary btn">
                                            </form>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </center>
                    <br><br>
                </div>
            </div>';
        }
    }
        
    ?>
    </div>

</body>
</html>