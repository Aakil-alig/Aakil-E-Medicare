<?php
session_start();
if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
        header("location: ../login.php");
    }
}else{
    header("location: ../login.php");
}
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
    <title>Patient Management</title>
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

        .btn-danger {
            background: var(--danger);
        }

        .btn-danger:hover {
            background: #c82333;
        }

        /* Popup Styling */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .popup {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            padding: 30px;
            max-width: 500px;
            width: 90%;
            position: relative;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .popup h2 {
            margin: 0 0 20px;
            color: var(--text-dark);
        }

        .popup .close {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 24px;
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
        }

        .popup .close:hover {
            color: var(--danger);
        }

        .popup .content {
            margin-bottom: 25px;
            color: var(--text-light);
        }

        .popup .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
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

        /* Empty State Styling */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state img {
            width: 200px;
            max-width: 100%;
            margin-bottom: 20px;
        }

        .empty-state p {
            color: var(--text-light);
            font-size: 1.1em;
            margin: 0;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            transition: var(--transition);
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
            color: var(--text-light);
        }

        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .chart-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: var(--shadow);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .patient-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .patient-card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .patient-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .patient-header {
            padding: 15px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .patient-name {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .patient-id {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .patient-body {
            padding: 20px;
        }

        .patient-info {
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: var(--text-light);
        }

        .info-item i {
            color: var(--primary-color);
            width: 20px;
        }

        .patient-actions {
            display: flex;
            gap: 10px;
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
            transition: var(--transition);
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
            box-shadow: var(--shadow);
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
            color: var(--text-light);
            font-size: 14px;
        }

        .filter-input {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 14px;
            transition: var(--transition);
        }

        .filter-input:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        @media (max-width: 768px) {
            .stats-container,
            .charts-container {
                grid-template-columns: 1fr;
            }
            
            .filter-form {
                flex-direction: column;
            }
            
            .patient-grid {
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
            <a href="patient.php" class="menu-btn menu-active">
                <i class="fas fa-user-injured"></i>
                <span>Patients</span>
            </a>
        </div>
        
        <div class="dash-body">
            <div class="stats-container">
                <?php
                    $total_patients = $database->query("SELECT COUNT(*) as count FROM patient")->fetch_assoc()['count'];
                    $new_patients = $database->query("SELECT COUNT(*) as count FROM patient WHERE joindate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetch_assoc()['count'];
                    $total_appointments = $database->query("SELECT COUNT(*) as count FROM appointment")->fetch_assoc()['count'];
                ?>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #0083b0">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $total_patients; ?></h4>
                        <p>Total Patients</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #28a745">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $new_patients; ?></h4>
                        <p>New Patients (30 days)</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #17a2b8">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $total_appointments; ?></h4>
                        <p>Total Appointments</p>
                    </div>
                </div>
            </div>

            <div class="charts-container">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Patient Registration Trend</h3>
                    </div>
                    <canvas id="registrationChart"></canvas>
                </div>
                
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Age Distribution</h3>
                    </div>
                    <canvas id="ageChart"></canvas>
                </div>
            </div>

            <div class="filter-container">
                <form action="" method="post" class="filter-form">
                    <div class="filter-group">
                        <label class="filter-label">Search</label>
                        <input type="search" name="search" class="filter-input" placeholder="Search by name, email or phone" 
                            value="<?php echo isset($_POST['search']) ? $_POST['search'] : ''; ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Registration Date</label>
                        <input type="date" name="joindate" class="filter-input" 
                            value="<?php echo isset($_POST['joindate']) ? $_POST['joindate'] : ''; ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Sort By</label>
                        <select name="sort" class="filter-input">
                            <option value="newest" <?php echo (isset($_POST['sort']) && $_POST['sort'] == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                            <option value="oldest" <?php echo (isset($_POST['sort']) && $_POST['sort'] == 'oldest') ? 'selected' : ''; ?>>Oldest First</option>
                            <option value="name" <?php echo (isset($_POST['sort']) && $_POST['sort'] == 'name') ? 'selected' : ''; ?>>Name (A-Z)</option>
                        </select>
                    </div>
                    
                    <div class="filter-group" style="display: flex; align-items: flex-end;">
                        <button type="submit" class="btn-action btn-view" style="width: 100%;">
                            <i class="fas fa-filter"></i>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <div class="patient-grid">
                <?php
                    $sqlmain = "SELECT * FROM patient";
                    $conditions = [];
                    $where = "";
                    
                    if($_POST) {
                        if(!empty($_POST["search"])){
                            $keyword = $_POST["search"];
                            $conditions[] = "(pname LIKE '%$keyword%' OR pemail LIKE '%$keyword%' OR ptel LIKE '%$keyword%')";
                        }
                        if(!empty($_POST["joindate"])){
                            $joindate = $_POST["joindate"];
                            $conditions[] = "joindate = '$joindate'";
                        }
                    }
                    
                    if(!empty($conditions)) {
                        $where = " WHERE " . implode(" AND ", $conditions);
                    }
                    
                    $sort = isset($_POST['sort']) ? $_POST['sort'] : 'newest';
                    $order_by = "ORDER BY ";
                    switch($sort) {
                        case 'oldest':
                            $order_by .= "joindate ASC";
                            break;
                        case 'name':
                            $order_by .= "pname ASC";
                            break;
                        default:
                            $order_by .= "joindate DESC";
                    }
                    
                    $sqlmain .= $where . " " . $order_by;
                    
                    $result = $database->query($sqlmain);
                    
                    if($result->num_rows==0) {
                        echo '<div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                            <img src="../img/notfound.svg" alt="No patients found" style="width:200px;margin-bottom:20px;">
                            <p style="color:var(--text-light);font-size:16px;margin:0;">No patients found matching your criteria</p>
                            <a href="patient.php" class="btn-action btn-view" style="display:inline-flex;margin-top:15px;">
                                <i class="fas fa-sync"></i>
                                Show All Patients
                            </a>
                        </div>';
                    } else {
                        while($row = $result->fetch_assoc()) {
                            $pid = $row["pid"];
                            $name = $row["pname"];
                            $email = $row["pemail"];
                            $dob = $row["pdob"];
                            $tel = $row["ptel"];
                            $address = $row["paddress"];
                            $joindate = $row["joindate"];
                            
                            // Calculate age
                            $today = new DateTime();
                            $birthdate = new DateTime($dob);
                            $age = $birthdate->diff($today)->y;
                            
                            // Get appointment count
                            $appointment_count = $database->query("SELECT COUNT(*) as count FROM appointment WHERE pid='$pid'")->fetch_assoc()['count'];
                            
                            echo '<div class="patient-card">
                                <div class="patient-header">
                                    <h3 class="patient-name">'.$name.'</h3>
                                    <p class="patient-id">Patient ID: '.$pid.'</p>
                                </div>
                                <div class="patient-body">
                                    <div class="patient-info">
                                        <div class="info-item">
                                            <i class="fas fa-envelope"></i>
                                            <span>'.$email.'</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-phone"></i>
                                            <span>'.$tel.'</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-birthday-cake"></i>
                                            <span>'.$age.' years old</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>Registered: '.date('d M Y', strtotime($joindate)).'</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-calendar-check"></i>
                                            <span>'.$appointment_count.' appointments</span>
                                        </div>
                                    </div>
                                    <div class="patient-actions">
                                        <a href="?action=view&id='.$pid.'" class="btn-action btn-view">
                                            <i class="fas fa-eye"></i>
                                            View
                                        </a>
                                        <a href="?action=edit&id='.$pid.'" class="btn-action btn-edit">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </a>
                                        <a href="?action=drop&id='.$pid.'&name='.$name.'" class="btn-action btn-delete">
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

    <script>
        // Get registration trend data
        <?php
            $reg_data = $database->query("
                SELECT DATE_FORMAT(joindate, '%Y-%m') as month, COUNT(*) as count 
                FROM patient 
                GROUP BY DATE_FORMAT(joindate, '%Y-%m') 
                ORDER BY month DESC 
                LIMIT 6
            ");
            
            $months = [];
            $counts = [];
            while($row = $reg_data->fetch_assoc()) {
                $months[] = date('M Y', strtotime($row['month'].'-01'));
                $counts[] = $row['count'];
            }
            $months = array_reverse($months);
            $counts = array_reverse($counts);
        ?>

        // Registration trend chart
        new Chart(document.getElementById('registrationChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'New Registrations',
                    data: <?php echo json_encode($counts); ?>,
                    borderColor: '#0083b0',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(0,131,176,0.1)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Get age distribution data
        <?php
            $age_ranges = [
                '0-18' => 0,
                '19-30' => 0,
                '31-50' => 0,
                '51+' => 0
            ];
            
            $patients = $database->query("SELECT pdob FROM patient");
            while($row = $patients->fetch_assoc()) {
                $birthdate = new DateTime($row['pdob']);
                $age = $birthdate->diff(new DateTime())->y;
                
                if($age <= 18) $age_ranges['0-18']++;
                elseif($age <= 30) $age_ranges['19-30']++;
                elseif($age <= 50) $age_ranges['31-50']++;
                else $age_ranges['51+']++;
            }
        ?>

        // Age distribution chart
        new Chart(document.getElementById('ageChart'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_keys($age_ranges)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($age_ranges)); ?>,
                    backgroundColor: ['#0083b0', '#00b4db', '#38ef7d', '#17a2b8']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    </script>

    <?php 
    if($_GET){
        $id=$_GET["id"];
        $action=$_GET["action"];
        if($action=='drop'){
            $nameget=$_GET["name"];
            echo '
            <div class="overlay">
                <div class="popup">
                    <h2>Are you sure?</h2>
                    <a class="close" href="patient.php">&times;</a>
                    <div class="content">
                        You want to delete this record<br>('.substr($nameget,0,40).').
                    </div>
                    <div class="actions">
                        <a href="delete-patient.php?id='.$id.'" class="btn-primary">
                            <i class="fas fa-check"></i>
                            Yes
                        </a>
                        <a href="patient.php" class="btn-primary btn-danger">
                            <i class="fas fa-times"></i>
                            No
                        </a>
                    </div>
                </div>
            </div>';
        }
    }
    ?>
</body>
</html>