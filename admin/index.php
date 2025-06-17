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

    date_default_timezone_set('Asia/Kolkata');
    $today = date('Y-m-d');

    $patientrow = $database->query("select * from patient;");
    $doctorrow = $database->query("select * from doctor;");
    $appointmentrow = $database->query("select * from appointment where appodate>='$today';");
    $schedulerow = $database->query("select * from schedule where scheduledate='$today';");
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
        
    <title>Admin Dashboard</title>
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
            overflow-x: hidden; /* Prevent horizontal scroll */
            width: 100%;
            min-height: 100vh;
        }

        .container {
            display: flex;
            min-height: 100vh;
            position: relative;
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .menu {
            width: 280px;
            background: white;
            box-shadow: var(--shadow);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
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
            max-width: calc(100% - 280px); /* Ensure content doesn't overflow */
            overflow-x: hidden;
        }

        /* Search Bar Styling */
        .search-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-bottom: 25px;
            transition: var(--transition);
            width: 100%;
            max-width: 100%;
        }

        .search-container:hover {
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }

        .search-container form {
            display: flex;
            gap: 15px;
            width: 100%;
        }

        .search-container input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #eee;
            border-radius: 10px;
            font-size: 15px;
            transition: var(--transition);
            min-width: 0;
        }

        .search-container input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,131,176,0.1);
        }

        /* Stats Cards */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
            width: 100%;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            min-width: 0; /* Prevent grid item overflow */
            width: 100%;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1));
            pointer-events: none;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .stat-card .icon {
            font-size: 2.5em;
            color: var(--primary-color);
            background: var(--bg-light);
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            margin-bottom: 15px;
            transition: var(--transition);
        }

        .stat-card:hover .icon {
            transform: scale(1.1) rotate(10deg);
            color: var(--secondary-color);
        }

        .stat-info {
            position: relative;
            z-index: 1;
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: var(--text-dark);
            margin: 0;
            line-height: 1.2;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 1.1em;
            margin-top: 5px;
        }

        /* Chart Container */
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            max-height: 400px;
            height: 350px;
            width: 100%;
            min-width: 0;
            max-width: 100%;
            overflow: hidden;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
            width: 100%;
        }

        .chart-grid .chart-container {
            height: 250px;
        }

        .chart-title {
            font-size: 1.2em;
            color: var(--text-dark);
            margin-bottom: 20px;
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
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px; /* Minimum width to ensure readability */
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

        .table-actions {
            padding: 20px;
            text-align: right;
            border-top: 1px solid #eee;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .menu {
                width: 240px;
            }
            .dash-body {
                margin-left: 240px;
                max-width: calc(100% - 240px);
            }
            .chart-container {
                height: 300px;
            }
            .chart-grid {
                grid-template-columns: 1fr;
            }
            .chart-grid .chart-container {
                height: 250px;
            }
        }

        @media (max-width: 768px) {
            .menu {
                width: 100%;
                height: auto;
                position: relative;
                margin-bottom: 20px;
            }
            .dash-body {
                margin-left: 0;
                max-width: 100%;
                padding: 15px;
            }
            .container {
                flex-direction: column;
            }
            .dashboard-stats {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            .search-container form {
                flex-direction: column;
            }
            .btn-primary {
                width: 100%;
                justify-content: center;
            }
            .chart-container {
                height: 250px;
            }
            .modern-table {
                min-width: 500px;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        .slide-in {
            animation: slideIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-light);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }

        /* Fix for chart containers */
        canvas {
            max-width: 100%;
            height: auto !important;
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

            <a href="index.php" class="menu-btn menu-active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="doctors.php" class="menu-btn">
                <i class="fas fa-user-md"></i>
                <span>Doctors</span>
            </a>
            <a href="schedule.php" class="menu-btn">
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
            <div class="search-container">
                <form>
                    <input type="text" placeholder="Search for doctors, patients, or appointments..." />
                    <button type="submit" class="btn-primary" style="padding: 12px 25px; border: none; border-radius: 10px; background: var(--primary-color); color: white; cursor: pointer;">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-user-injured"></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-number"><?php echo $patientrow->num_rows ?></h3>
                        <p class="stat-label">Total Patients</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-number"><?php echo $doctorrow->num_rows ?></h3>
                        <p class="stat-label">Total Doctors</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-number"><?php echo $appointmentrow->num_rows ?></h3>
                        <p class="stat-label">New Appointments</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-number"><?php echo $schedulerow->num_rows ?></h3>
                        <p class="stat-label">Today's Sessions</p>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <h3 class="chart-title">Appointments Overview</h3>
                <canvas id="appointmentsChart"></canvas>
            </div>

            <div class="chart-grid">
                <div class="chart-container">
                    <h3 class="chart-title">Doctor Specialties Distribution</h3>
                    <canvas id="specialtiesChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3 class="chart-title">Patient Age Distribution</h3>
                    <canvas id="ageChart"></canvas>
                </div>
            </div>

            <div class="data-table fade-in">
                <h3>
                    <i class="fas fa-calendar"></i>
                    Upcoming Appointments until Next <?php echo date("l",strtotime("+1 week")); ?>
                </h3>
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-user"></i> Patient</th>
                                <th><i class="fas fa-user-md"></i> Doctor</th>
                                <th><i class="fas fa-calendar-day"></i> Session</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $nextweek=date("Y-m-d",strtotime("+1 week"));
                            $sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid  where schedule.scheduledate>='$today' and schedule.scheduledate<='$nextweek' order by schedule.scheduledate desc";
                            $result= $database->query($sqlmain);
                            if($result->num_rows==0){
                                echo '<tr><td colspan="4" style="text-align: center;">No appointments scheduled</td></tr>';
                            }else{
                                for($x=0; $x<$result->num_rows; $x++){
                                    $row=$result->fetch_assoc();
                                    $appoid=$row["appoid"];
                                    $scheduleid=$row["scheduleid"];
                                    $title=$row["title"];
                                    $docname=$row["docname"];
                                    $scheduledate=$row["scheduledate"];
                                    $scheduletime=$row["scheduletime"];
                                    $pname=$row["pname"];
                                    $apponum=$row["apponum"];
                                    $appodate=$row["appodate"];
                                    echo '<tr>
                                        <td>#'.$apponum.'</td>
                                        <td>'.substr($pname,0,25).'</td>
                                        <td>'.substr($docname,0,25).'</td>
                                        <td>'.substr($title,0,25).'</td>
                                    </tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="table-actions">
                    <a href="appointment.php" class="btn-primary">
                        <i class="fas fa-list"></i> View All Appointments
                    </a>
                </div>
            </div>

            <div class="data-table fade-in">
                <h3>
                    <i class="fas fa-calendar-alt"></i>
                    Upcoming Sessions until Next <?php echo date("l",strtotime("+1 week")); ?>
                </h3>
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-bookmark"></i> Title</th>
                                <th><i class="fas fa-user-md"></i> Doctor</th>
                                <th><i class="fas fa-clock"></i> Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $nextweek=date("Y-m-d",strtotime("+1 week"));
                            $sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid  where schedule.scheduledate>='$today' and schedule.scheduledate<='$nextweek' order by schedule.scheduledate desc";
                            $result= $database->query($sqlmain);
                            if($result->num_rows==0){
                                echo '<tr><td colspan="3" style="text-align: center;">No sessions scheduled</td></tr>';
                            }else{
                                for($x=0; $x<$result->num_rows; $x++){
                                    $row=$result->fetch_assoc();
                                    $scheduleid=$row["scheduleid"];
                                    $title=$row["title"];
                                    $docname=$row["docname"];
                                    $scheduledate=$row["scheduledate"];
                                    $scheduletime=$row["scheduletime"];
                                    $nop=$row["nop"];
                                    echo '<tr>
                                        <td>'.substr($title,0,30).'</td>
                                        <td>'.substr($docname,0,20).'</td>
                                        <td>'.substr($scheduledate,0,10).' '.substr($scheduletime,0,5).'</td>
                                    </tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="table-actions">
                    <a href="schedule.php" class="btn-primary">
                        <i class="fas fa-list"></i> View All Sessions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sample data for charts
        const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
        new Chart(appointmentsCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Appointments',
                    data: [65, 59, 80, 81, 56, 55],
                    borderColor: '#0083b0',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(0, 131, 176, 0.1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        const specialtiesCtx = document.getElementById('specialtiesChart').getContext('2d');
        new Chart(specialtiesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Cardiology', 'Neurology', 'Pediatrics', 'Orthopedics', 'Others'],
                datasets: [{
                    data: [30, 20, 25, 15, 10],
                    backgroundColor: [
                        '#0083b0',
                        '#00b4db',
                        '#38ef7d',
                        '#11998e',
                        '#4CAF50'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });

        const ageCtx = document.getElementById('ageChart').getContext('2d');
        new Chart(ageCtx, {
            type: 'bar',
            data: {
                labels: ['0-18', '19-30', '31-50', '51-70', '70+'],
                datasets: [{
                    label: 'Patients',
                    data: [65, 59, 80, 81, 56],
                    backgroundColor: '#0083b0'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    </script>
</body>
</html>