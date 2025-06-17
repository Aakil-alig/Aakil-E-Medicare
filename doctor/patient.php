<?php
session_start();

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
        header("location: ../login.php");
    }else{
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
}

//import database
include("../connection.php");
$userrow = $database->query("select * from doctor where docemail='$useremail'");
$userfetch=$userrow->fetch_assoc();
$userid= $userfetch["docid"];
$username=$userfetch["docname"];
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
        
    <title>My Patients</title>
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

        /* Search Container */
        .search-container {
            background: white;
            padding: 25px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            margin-bottom: 25px;
        }

        .search-form {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-input-container {
            flex: 1;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 12px 12px 12px 45px;
            border: 2px solid #eee;
            border-radius: var(--radius);
            font-size: 0.95em;
            transition: var(--transition);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 1.1em;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,131,176,0.1);
        }

        .search-input::placeholder {
            color: var(--text-light);
        }

        .search-btn {
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
            min-width: 120px;
            justify-content: center;
        }

        .search-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,131,176,0.3);
        }

        /* Data Table */
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

        /* Table Styling */
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
            .search-form {
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

        .action-btns {
            display: flex;
            gap: 10px;
            justify-content: center;
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
        .report-btn {
            background: #00b4db;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .popup-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background: #4CAF50;
            color: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: none;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <?php
    if(isset($_GET['action']) && $_GET['action'] == 'report_added') {
        echo '<div class="popup-message" id="successMessage">
                Report added successfully!
              </div>';
    }
    ?>
    <div class="container">
        <div class="menu">
            <div class="profile-container">
                <img src="../img/user.png" alt="Doctor Profile">
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
            <a href="appointment.php" class="menu-btn">
                <i class="fas fa-calendar-check"></i>
                <span>My Appointments</span>
            </a>
            <a href="schedule.php" class="menu-btn">
                <i class="fas fa-calendar-alt"></i>
                <span>My Sessions</span>
            </a>
            <a href="patient.php" class="menu-btn menu-active">
                <i class="fas fa-user-injured"></i>
                <span>My Patients</span>
            </a>
            <a href="settings.php" class="menu-btn">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
        
        <div class="dash-body">
            <div class="search-container">
                <form action="" method="post" class="search-form">
                    <div class="search-input-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="search" name="search" class="search-input" placeholder="Search patients by name, email or contact number">
                    </div>
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                </form>
            </div>

            <div class="data-table">
                <div class="table-header">
                    <h3>
                        <i class="fas fa-user-injured"></i>
                        My Patients
                    </h3>
                </div>

                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $sqlmain = "select patient.pid,patient.pname,patient.pemail,patient.ptel,patient.paddress 
                            from patient inner join appointment on patient.pid=appointment.pid 
                            inner join schedule on schedule.scheduleid=appointment.scheduleid 
                            where schedule.docid=$userid group by patient.pid order by patient.pid desc";
                            
                            if($_POST){
                                if(!empty($_POST["search"])){
                                    $keyword = $_POST["search"];
                                    $sqlmain .= " having patient.pname like '%$keyword%' or patient.pemail like '%$keyword%' or patient.ptel like '%$keyword%'";
                                }
                            }

                            $result = $database->query($sqlmain);

                            if($result->num_rows==0){
                                echo '<tr>
                                    <td colspan="5">
                                        <div class="empty-state" style="text-align:center;padding:40px;">
                                            <img src="../img/notfound.svg" alt="No patients found" style="width:200px;margin-bottom:20px;">
                                            <p style="color:var(--text-light);font-size:1.1em;margin:0;">No patients found!</p>
                                        </div>
                                    </td>
                                </tr>';
                            }else{
                                while($row=$result->fetch_assoc()){
                                    echo '<tr>
                                        <td>'.substr($row["pname"],0,30).'</td>
                                        <td>'.substr($row["pemail"],0,30).'</td>
                                        <td>'.$row["ptel"].'</td>
                                        <td>'.substr($row["paddress"],0,30).'</td>
                                        <td>
                                            <div class="action-btns">
                                                <a href="?action=view&id='.$row["pid"].'" class="action-btn view-btn">
                                                    <i class="fas fa-eye"></i>
                                                    View
                                                </a>
                                                <a href="add-report.php?pid='.$row["pid"].'" class="action-btn report-btn">
                                                    <i class="fas fa-file-medical"></i>
                                                    Add Report
                                                </a>
                                            </div>
                                        </td>
                                    </tr>';
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php 
    if($_GET){
        $id=$_GET["id"];
        $action=$_GET["action"];
        if($action=='view'){
            $sqlmain = "select * from patient where pid='$id'";
            $result = $database->query($sqlmain);
            $row=$result->fetch_assoc();
            echo '
            <div class="overlay" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(5px);display:flex;align-items:center;justify-content:center;z-index:1000;">
                <div class="popup" style="background:white;border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);padding:30px;max-width:500px;width:90%;position:relative;animation:fadeIn 0.3s ease;">
                    <h2 style="margin:0 0 20px;color:var(--text-dark);">Patient Details</h2>
                    <div class="content" style="margin-bottom:25px;">
                        <p><strong>Name:</strong> '.$row["pname"].'</p>
                        <p><strong>Email:</strong> '.$row["pemail"].'</p>
                        <p><strong>Contact:</strong> '.$row["ptel"].'</p>
                        <p><strong>Address:</strong> '.$row["paddress"].'</p>
                    </div>
                    <div style="display:flex;justify-content:center;">
                        <a href="patient.php" class="btn-primary">
                            <i class="fas fa-check"></i>
                            OK
                        </a>
                    </div>
                </div>
            </div>';
        }
    }
    ?>

    <script>
        // Show and hide success message
        if(document.getElementById('successMessage')) {
            document.getElementById('successMessage').style.display = 'block';
            setTimeout(function() {
                document.getElementById('successMessage').style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>