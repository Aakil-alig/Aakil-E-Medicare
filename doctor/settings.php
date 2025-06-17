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
        
    <title>Settings</title>
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

        /* Settings Cards */
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .settings-card {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 25px;
            transition: var(--transition);
        }

        .settings-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .settings-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }

        .settings-icon {
            width: 45px;
            height: 45px;
            background: var(--bg-light);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.2em;
        }

        .settings-title {
            margin: 0;
            font-size: 1.2em;
            color: var(--text-dark);
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #eee;
            border-radius: var(--radius);
            font-size: 0.95em;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,131,176,0.1);
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
            .settings-grid {
                grid-template-columns: 1fr;
            }
            .btn-primary {
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
            <a href="patient.php" class="menu-btn">
                <i class="fas fa-user-injured"></i>
                <span>My Patients</span>
            </a>
            <a href="settings.php" class="menu-btn menu-active">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
        
        <div class="dash-body">
            <div class="settings-grid">
                <div class="settings-card">
                    <div class="settings-header">
                        <div class="settings-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="settings-title">Personal Information</h3>
                    </div>
                    <form action="update-personal.php" method="post">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $userfetch['docname'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $userfetch['docemail'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="tel" class="form-control" value="<?php echo $userfetch['doctel'] ?>" required>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i>
                            Save Changes
                        </button>
                    </form>
                </div>

                <div class="settings-card">
                    <div class="settings-header">
                        <div class="settings-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3 class="settings-title">Change Password</h3>
                    </div>
                    <form action="update-password.php" method="post">
                        <div class="form-group">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="currentpassword" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="newpassword" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirmpassword" class="form-control" required>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-key"></i>
                            Update Password
                        </button>
                    </form>
                </div>

                <div class="settings-card">
                    <div class="settings-header">
                        <div class="settings-icon">
                            <i class="fas fa-hospital"></i>
                        </div>
                        <h3 class="settings-title">Professional Details</h3>
                    </div>
                    <form action="update-professional.php" method="post">
                        <div class="form-group">
                            <label class="form-label">Specialties</label>
                            <select name="specialties" class="form-control" required>
                                <?php
                                    $current_specialties = $userfetch['specialties'];
                                    $specialties_result = $database->query("select * from specialties");
                                    while($row = $specialties_result->fetch_assoc()) {
                                        $selected = ($current_specialties == $row['id']) ? 'selected' : '';
                                        echo "<option value='".$row['id']."' ".$selected.">".$row['sname']."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i>
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php 
    if(isset($_GET['action'])){
        $action = $_GET['action'];
        if($action == 'success'){
            echo '
            <div class="overlay" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(5px);display:flex;align-items:center;justify-content:center;z-index:1000;">
                <div class="popup" style="background:white;border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);padding:30px;max-width:400px;width:90%;position:relative;animation:fadeIn 0.3s ease;text-align:center;">
                    <div style="color:var(--success);font-size:3em;margin-bottom:20px;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 style="margin:0 0 10px;color:var(--text-dark);">Success!</h2>
                    <p style="margin:0 0 20px;color:var(--text-light);">Your changes have been saved successfully.</p>
                    <a href="settings.php" class="btn-primary">
                        <i class="fas fa-check"></i>
                        OK
                    </a>
                </div>
            </div>';
        }elseif($action == 'error'){
            echo '
            <div class="overlay" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(5px);display:flex;align-items:center;justify-content:center;z-index:1000;">
                <div class="popup" style="background:white;border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);padding:30px;max-width:400px;width:90%;position:relative;animation:fadeIn 0.3s ease;text-align:center;">
                    <div style="color:var(--danger);font-size:3em;margin-bottom:20px;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h2 style="margin:0 0 10px;color:var(--text-dark);">Error!</h2>
                    <p style="margin:0 0 20px;color:var(--text-light);">An error occurred while saving your changes.</p>
                    <a href="settings.php" class="btn-primary" style="background:var(--danger)">
                        <i class="fas fa-times"></i>
                        Close
                    </a>
                </div>
            </div>';
        }
    }
    ?>
</body>
</html>