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
    <title>Doctors Management</title>
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

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
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

        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            height: 400px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filter-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .filter-select {
            padding: 8px 15px;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 14px;
        }

        .doctor-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .doctor-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .doctor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }

        .doctor-info {
            flex: 1;
        }

        .doctor-name {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .doctor-specialty {
            color: #666;
            margin: 5px 0;
        }

        .doctor-stats {
            display: flex;
            gap: 20px;
            margin-top: 10px;
            font-size: 14px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }

        .btn-view {
            background: #17a2b8;
            color: white;
        }

        .btn-edit {
            background: #ffc107;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .search-box {
            position: relative;
            flex: 1;
        }

        .search-box input {
            width: 100%;
            padding: 12px 20px;
            padding-left: 45px;
            border: 2px solid #eee;
            border-radius: var(--radius);
            font-size: 15px;
            transition: var(--transition);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .quick-actions {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <?php
        // Keep existing PHP session and database connection code
        
        // Get statistics
        $total_doctors = $database->query("SELECT COUNT(*) as count FROM doctor")->fetch_assoc()['count'];
        $total_specialties = $database->query("SELECT COUNT(DISTINCT specialties) as count FROM doctor")->fetch_assoc()['count'];
        $total_appointments = $database->query("SELECT COUNT(*) as count FROM appointment")->fetch_assoc()['count'];
        
        // Get specialty distribution
        $specialty_dist = $database->query("
            SELECT s.sname, COUNT(d.docid) as count 
            FROM specialties s 
            LEFT JOIN doctor d ON s.id = d.specialties 
            GROUP BY s.id
        ");
        
        $specialty_labels = [];
        $specialty_data = [];
        while($row = $specialty_dist->fetch_assoc()) {
            $specialty_labels[] = $row['sname'];
            $specialty_data[] = $row['count'];
        }
    ?>
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
            <a href="doctors.php" class="menu-btn menu-active">
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
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #0083b0">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $total_doctors; ?></h4>
                        <p>Total Doctors</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #28a745">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $total_specialties; ?></h4>
                        <p>Specialties</p>
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

            <div class="chart-container">
                <div class="chart-header">
                    <h3>Specialty Distribution</h3>
                </div>
                <canvas id="specialtyChart"></canvas>
            </div>

            <div class="search-container">
                <form action="" method="post" class="header-search">
                    <div class="filter-container">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="search" name="search" class="input-text" placeholder="Search doctor name or email" list="doctors">
                        </div>
                        
                        <select class="filter-select" name="specialty">
                            <option value="">All Specialties</option>
                            <?php
                                $specialties = $database->query("SELECT * FROM specialties ORDER BY sname");
                                while($specialty = $specialties->fetch_assoc()) {
                                    echo "<option value='".$specialty['id']."'>".$specialty['sname']."</option>";
                                }
                            ?>
                        </select>
                        
                        <div class="quick-actions">
                            <a href="?action=add&id=none&error=0" class="btn-primary">
                                <i class="fas fa-plus"></i>
                                Add New Doctor
                            </a>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-filter"></i>
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="doctors-grid">
                <?php
                    if($_POST){
                        $keyword = $_POST["search"];
                        $specialty = $_POST["specialty"];
                        
                        $sqlmain = "SELECT d.*, s.sname, 
                            (SELECT COUNT(*) FROM appointment a 
                             INNER JOIN schedule sch ON a.scheduleid = sch.scheduleid 
                             WHERE sch.docid = d.docid) as total_appointments
                            FROM doctor d 
                            LEFT JOIN specialties s ON d.specialties = s.id 
                            WHERE (d.docemail LIKE '%$keyword%' OR d.docname LIKE '%$keyword%')";
                        
                        if(!empty($specialty)) {
                            $sqlmain .= " AND d.specialties = '$specialty'";
                        }
                        
                        $sqlmain .= " ORDER BY d.docid DESC";
                    } else {
                        $sqlmain = "SELECT d.*, s.sname,
                            (SELECT COUNT(*) FROM appointment a 
                             INNER JOIN schedule sch ON a.scheduleid = sch.scheduleid 
                             WHERE sch.docid = d.docid) as total_appointments
                            FROM doctor d 
                            LEFT JOIN specialties s ON d.specialties = s.id 
                            ORDER BY d.docid DESC";
                    }
                    
                    $result = $database->query($sqlmain);
                    
                    if($result->num_rows==0){
                        echo '<div class="empty-state" style="text-align:center;padding:40px;">
                            <img src="../img/notfound.svg" alt="No doctors found" style="width:200px;margin-bottom:20px;">
                            <p style="color:var(--text-light);font-size:1.1em;margin:0;">No doctors found matching your criteria</p>
                            <a href="doctors.php" class="btn-primary" style="margin-top:15px;">
                                <i class="fas fa-sync"></i>
                                Show all Doctors
                            </a>
                        </div>';
                    } else {
                        while($row = $result->fetch_assoc()) {
                            echo '<div class="doctor-card">
                                <img src="../img/user.png" alt="'.$row["docname"].'" class="doctor-avatar">
                                <div class="doctor-info">
                                    <h3 class="doctor-name">'.$row["docname"].'</h3>
                                    <p class="doctor-specialty">'.$row["sname"].'</p>
                                    <div class="doctor-stats">
                                        <div class="stat-item">
                                            <i class="fas fa-envelope"></i>
                                            '.$row["docemail"].'
                                        </div>
                                        <div class="stat-item">
                                            <i class="fas fa-phone"></i>
                                            '.$row["doctel"].'
                                        </div>
                                        <div class="stat-item">
                                            <i class="fas fa-calendar-check"></i>
                                            '.$row["total_appointments"].' appointments
                                        </div>
                                    </div>
                                </div>
                                <div class="action-buttons">
                                    <a href="?action=view&id='.$row["docid"].'" class="btn-action btn-view">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                    <a href="?action=edit&id='.$row["docid"].'&error=0" class="btn-action btn-edit">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                    <a href="?action=drop&id='.$row["docid"].'&name='.$row["docname"].'" class="btn-action btn-delete">
                                        <i class="fas fa-trash"></i>
                                        Remove
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
        // Initialize specialty distribution chart
        const ctx = document.getElementById('specialtyChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($specialty_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($specialty_data); ?>,
                    backgroundColor: [
                        '#0083b0',
                        '#00b4db',
                        '#38ef7d',
                        '#11998e',
                        '#40E0D0',
                        '#FF8C00',
                        '#FF0080'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 15,
                            padding: 10,
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 10,
                        bottom: 10
                    }
                }
            }
        });
    </script>

    <?php 
        if($_GET){
            $id=$_GET["id"];
            $action=$_GET["action"];
            $error = isset($_GET["error"]) ? $_GET["error"] : '0';
            
            if($action=='add'){
                $error_1=$_GET["error"];
                $errorlist= array(
                    '1'=>'<p style="color:rgb(255, 62, 62);margin:0;">Email address already registered.</p>',
                    '2'=>'<p style="color:rgb(255, 62, 62);margin:0;">Password confirmation failed.</p>',
                    '3'=>'<p style="color:rgb(255, 62, 62);margin:0;">Error while adding the doctor.</p>',
                    '4'=>'<p style="color:rgb(37, 184, 37);margin:0;">Doctor added successfully.</p>',
                    '0'=>'<p style="color:rgb(0, 0, 0);margin:0;"></p>',
                );

                echo '
                <div class="overlay">
                    <div class="popup">
                        <h2>Add New Doctor</h2>
                        <a class="close" href="doctors.php">&times;</a>
                        '.$errorlist[$error_1].'
                        <div style="display: flex;justify-content: center;">
                            <form action="add-new.php" method="POST" class="add-new-form">
                                <div style="display:flex;gap:15px;flex-direction:column;margin-top:20px;">
                                    <div>
                                        <label for="name" class="form-label">Doctor Name:</label>
                                        <input type="text" name="name" class="input-text" placeholder="Doctor Name" required>
                                    </div>

                                    <div>
                                        <label for="nic" class="form-label">NIC:</label>
                                        <input type="text" name="nic" class="input-text" placeholder="NIC" required>
                                    </div>

                                    <div>
                                        <label for="spec" class="form-label">Specialties:</label>
                                        <select name="spec" class="input-text" required>
                                            <option value="" disabled selected>Select Specialty</option>';
                                            $list11 = $database->query("select * from specialties order by sname asc;");
                                            for ($y=0;$y<$list11->num_rows;$y++){
                                                $row00=$list11->fetch_assoc();
                                                $sn=$row00["sname"];
                                                $id00=$row00["id"];
                                                echo "<option value=".$id00.">$sn</option>";
                                            }
                                    echo '</select>
                                    </div>

                                    <div>
                                        <label for="email" class="form-label">Email:</label>
                                        <input type="email" name="email" class="input-text" placeholder="Email Address" required>
                                    </div>

                                    <div>
                                        <label for="Tele" class="form-label">Telephone:</label>
                                        <input type="tel" name="Tele" class="input-text" placeholder="Telephone Number" required>
                                    </div>

                                    <div>
                                        <label for="password" class="form-label">Password:</label>
                                        <input type="password" name="password" class="input-text" placeholder="Password" required>
                                    </div>

                                    <div>
                                        <label for="cpassword" class="form-label">Confirm Password:</label>
                                        <input type="password" name="cpassword" class="input-text" placeholder="Confirm Password" required>
                                    </div>

                                    <div style="display:flex;gap:10px;margin-top:10px;">
                                        <button type="submit" class="btn-primary">
                                            <i class="fas fa-plus"></i>
                                            Add Doctor
                                        </button>
                                        <a href="doctors.php" class="btn-primary" style="background:var(--bg-dark)">
                                            <i class="fas fa-times"></i>
                                            Cancel
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>';
            }
        }
    ?>
</body>
</html>