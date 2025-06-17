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
            margin-top: 25px;
        }

        .settings-card {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            transition: var(--transition);
            overflow: hidden;
            text-decoration: none;
            color: var(--text-dark);
        }

        .settings-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .settings-card-content {
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .settings-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .settings-danger {
            background: linear-gradient(135deg, var(--danger), #ff8080);
        }

        .settings-text h3 {
            margin: 0 0 10px;
            font-size: 1.2em;
            font-weight: 600;
        }

        .settings-text p {
            margin: 0;
            color: var(--text-light);
            font-size: 0.9em;
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
            <a href="schedule.php" class="menu-btn">
                <i class="fas fa-calendar-alt"></i>
                <span>Scheduled Sessions</span>
            </a>
            <a href="appointment.php" class="menu-btn">
                <i class="fas fa-calendar-check"></i>
                <span>My Bookings</span>
            </a>
            <a href="settings.php" class="menu-btn menu-active">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
        
        <div class="dash-body">
            <div class="settings-grid">
                <a href="?action=edit&id=<?php echo $userid ?>&error=0" class="settings-card">
                    <div class="settings-card-content">
                        <div class="settings-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="settings-text">
                            <h3>Account Settings</h3>
                            <p>Edit your account details & change password</p>
                        </div>
                    </div>
                </a>

                <a href="?action=view&id=<?php echo $userid ?>" class="settings-card">
                    <div class="settings-card-content">
                        <div class="settings-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="settings-text">
                            <h3>View Account Details</h3>
                            <p>View personal information about your account</p>
                        </div>
                    </div>
                </a>

                <a href="?action=drop&id=<?php echo $userid.'&name='.$username ?>" class="settings-card">
                    <div class="settings-card-content">
                        <div class="settings-icon settings-danger">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <div class="settings-text">
                            <h3 style="color: var(--danger)">Delete Account</h3>
                            <p>Permanently remove your account</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <?php 
    if($_GET){
        $id=$_GET["id"];
        $action=$_GET["action"];
        if($action=='drop'){
            $nameget=$_GET["name"];
            echo '
            <div class="overlay" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(5px);display:flex;align-items:center;justify-content:center;z-index:1000;">
                <div class="popup" style="background:white;border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);padding:30px;max-width:500px;width:90%;position:relative;animation:fadeIn 0.3s ease;">
                    <h2 style="margin:0 0 20px;color:var(--danger);">Delete Account?</h2>
                    <p style="margin:0 0 20px;color:var(--text-light);">Are you sure you want to delete your account? This action cannot be undone.</p>
                    <div style="display:flex;gap:15px;justify-content:flex-end;">
                        <a href="delete-account.php?id='.$id.'" class="btn-primary" style="background:var(--danger)">
                            <i class="fas fa-check"></i>
                            Yes
                        </a>
                        <a href="settings.php" class="btn-primary" style="background:var(--bg-dark)">
                            <i class="fas fa-times"></i>
                            No
                        </a>
                    </div>
                </div>
            </div>';
        }elseif($action=='view'){
            $sqlmain= "select * from patient where pid=?";
            $stmt = $database->prepare($sqlmain);
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row=$result->fetch_assoc();
            $name=$row["pname"];
            $email=$row["pemail"];
            $address=$row["paddress"];
            $dob=$row["pdob"];
            $tel=$row["ptel"];
            
            echo '
            <div class="overlay" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(5px);display:flex;align-items:center;justify-content:center;z-index:1000;">
                <div class="popup" style="background:white;border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);padding:30px;max-width:500px;width:90%;position:relative;animation:fadeIn 0.3s ease;">
                    <h2 style="margin:0 0 20px;color:var(--text-dark);">Account Details</h2>
                    <div class="content" style="margin-bottom:25px;">
                        <p><strong>Name:</strong> '.$name.'</p>
                        <p><strong>Email:</strong> '.$email.'</p>
                        <p><strong>Address:</strong> '.$address.'</p>
                        <p><strong>Date of Birth:</strong> '.$dob.'</p>
                        <p><strong>Telephone:</strong> '.$tel.'</p>
                    </div>
                    <div style="display:flex;justify-content:center;">
                        <a href="settings.php" class="btn-primary">
                            <i class="fas fa-check"></i>
                            OK
                        </a>
                    </div>
                </div>
            </div>';
        }elseif($action=='edit'){
            $sqlmain= "select * from patient where pid=?";
            $stmt = $database->prepare($sqlmain);
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row=$result->fetch_assoc();
            $name=$row["pname"];
            $email=$row["pemail"];
            $address=$row["paddress"];
            $dob=$row["pdob"];
            $tel=$row["ptel"];
            
            $error_1=$_GET["error"];
            $errorlist= array(
                '1'=>'<p style="color:var(--danger);margin:0 0 10px;">Email already exists.</p>',
                '2'=>'<p style="color:var(--danger);margin:0 0 10px;">Password confirmation error! Please try again.</p>',
                '3'=>'<p style="color:var(--danger);margin:0 0 10px;">Password length must be between 8 and 16 characters.</p>',
                '4'=>"",
                '0'=>'',
            );

            if($error_1!='4'){
                echo '
                <div class="overlay" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(5px);display:flex;align-items:center;justify-content:center;z-index:1000;">
                    <div class="popup" style="background:white;border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);padding:30px;max-width:600px;width:90%;position:relative;animation:fadeIn 0.3s ease;">
                        <h2 style="margin:0 0 20px;color:var(--text-dark);">Edit Account Details</h2>
                        '.$errorlist[$error_1].'
                        <form action="edit-user.php" method="POST">
                            <input type="hidden" name="id" value="'.$id.'">
                            <div style="margin-bottom:15px;">
                                <label style="display:block;margin-bottom:5px;color:var(--text-dark);">Name</label>
                                <input type="text" name="name" value="'.$name.'" required style="width:100%;padding:10px;border:2px solid #eee;border-radius:var(--radius);font-size:15px;">
                            </div>
                            <div style="margin-bottom:15px;">
                                <label style="display:block;margin-bottom:5px;color:var(--text-dark);">Email</label>
                                <input type="email" name="email" value="'.$email.'" required style="width:100%;padding:10px;border:2px solid #eee;border-radius:var(--radius);font-size:15px;">
                            </div>
                            <div style="margin-bottom:15px;">
                                <label style="display:block;margin-bottom:5px;color:var(--text-dark);">Address</label>
                                <input type="text" name="address" value="'.$address.'" required style="width:100%;padding:10px;border:2px solid #eee;border-radius:var(--radius);font-size:15px;">
                            </div>
                            <div style="margin-bottom:15px;">
                                <label style="display:block;margin-bottom:5px;color:var(--text-dark);">Date of Birth</label>
                                <input type="date" name="dob" value="'.$dob.'" required style="width:100%;padding:10px;border:2px solid #eee;border-radius:var(--radius);font-size:15px;">
                            </div>
                            <div style="margin-bottom:15px;">
                                <label style="display:block;margin-bottom:5px;color:var(--text-dark);">Telephone</label>
                                <input type="tel" name="tel" value="'.$tel.'" required style="width:100%;padding:10px;border:2px solid #eee;border-radius:var(--radius);font-size:15px;">
                            </div>
                            <div style="margin-bottom:15px;">
                                <label style="display:block;margin-bottom:5px;color:var(--text-dark);">Password</label>
                                <input type="password" name="password" required style="width:100%;padding:10px;border:2px solid #eee;border-radius:var(--radius);font-size:15px;">
                            </div>
                            <div style="margin-bottom:25px;">
                                <label style="display:block;margin-bottom:5px;color:var(--text-dark);">Confirm Password</label>
                                <input type="password" name="cpassword" required style="width:100%;padding:10px;border:2px solid #eee;border-radius:var(--radius);font-size:15px;">
                            </div>
                            <div style="display:flex;gap:15px;justify-content:flex-end;">
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-save"></i>
                                    Save Changes
                                </button>
                                <a href="settings.php" class="btn-primary" style="background:var(--bg-dark)">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>';
            }
        }
    }
    ?>
</body>
</html>
