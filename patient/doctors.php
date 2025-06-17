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
$userrow = $database->query("select * from patient where pemail='$useremail'");
$userfetch=$userrow->fetch_assoc();
$userid= $userfetch["pid"];
$username=$userfetch["pname"];

// Get all specialties for filter
$specialties = $database->query("SELECT DISTINCT specialties, COUNT(*) as count FROM doctor GROUP BY specialties");
$specialties_data = [];
while($row = $specialties->fetch_assoc()) {
    $specialties_data[] = $row;
}

// Get doctors with their session counts and ratings
$sqlmain = "SELECT d.*, 
    (SELECT COUNT(*) FROM schedule WHERE docid=d.docid AND scheduledate >= CURDATE()) as upcoming_sessions,
    (SELECT COUNT(*) FROM appointment a JOIN schedule s ON a.scheduleid=s.scheduleid WHERE s.docid=d.docid) as total_appointments
FROM doctor d";

// Check if doctor_ratings table exists
$table_exists = $database->query("SHOW TABLES LIKE 'doctor_ratings'")->num_rows > 0;

if($table_exists) {
    // Add ratings column if table exists
    $sqlmain = "SELECT d.*, 
        (SELECT COUNT(*) FROM schedule WHERE docid=d.docid AND scheduledate >= CURDATE()) as upcoming_sessions,
        (SELECT COUNT(*) FROM appointment a JOIN schedule s ON a.scheduleid=s.scheduleid WHERE s.docid=d.docid) as total_appointments,
        COALESCE((SELECT AVG(rating) FROM doctor_ratings WHERE docid=d.docid), 0) as avg_rating
    FROM doctor d";
}

$filter_conditions = [];
if(isset($_POST['search'])) {
    $keyword = $_POST['search'];
    $filter_conditions[] = "(docname LIKE '%$keyword%' OR docemail LIKE '%$keyword%' OR specialties LIKE '%$keyword%')";
}
if(isset($_POST['specialty']) && !empty($_POST['specialty'])) {
    $specialty = $_POST['specialty'];
    $filter_conditions[] = "specialties = '$specialty'";
}
if($table_exists && isset($_POST['rating']) && !empty($_POST['rating'])) {
    $min_rating = $_POST['rating'];
    $filter_conditions[] = "(SELECT AVG(rating) FROM doctor_ratings WHERE docid=d.docid) >= $min_rating";
}
if(isset($_POST['availability']) && $_POST['availability'] == 'available') {
    $filter_conditions[] = "(SELECT COUNT(*) FROM schedule WHERE docid=d.docid AND scheduledate >= CURDATE()) > 0";
}

if(!empty($filter_conditions)) {
    $sqlmain .= " WHERE " . implode(" AND ", $filter_conditions);
}

$sqlmain .= " ORDER BY upcoming_sessions DESC";
$result = $database->query($sqlmain);
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
    <title>Doctors</title>
    <style>
        :root {
            --primary-color: #2193b0;
            --secondary-color: #6dd5ed;
            --accent-color: #FF6B6B;
            --text-dark: #333;
            --text-light: #666;
            --bg-light: #f8f9fa;
            --bg-dark: #2c3e50;
            --danger: #dc3545;
            --success: #28a745;
            --warning: #ffc107;
            --info: #17a2b8;
            --shadow: 0 2px 15px rgba(0,0,0,0.1);
            --shadow-lg: 0 5px 25px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
            --radius: 15px;
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

        /* Filters and Stats Container */
        .filters-stats-container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .filters-section, .stats-section {
            background: white;
            border-radius: var(--radius-lg);
            padding: 25px;
            box-shadow: var(--shadow);
        }

        .filters-section h3 {
            margin: 0 0 20px;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filters-form {
            display: grid;
            gap: 15px;
        }

        .filter-group {
            display: grid;
            gap: 8px;
        }

        .filter-group label {
            color: var(--text-dark);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-group select {
            padding: 10px;
            border: 2px solid #eee;
            border-radius: var(--radius);
            font-size: 14px;
            width: 100%;
            transition: var(--transition);
        }

        .filter-group select:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: var(--transition);
            border: 1px solid #eee;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5em;
        }

        .stat-info h4 {
            margin: 0;
            color: var(--text-light);
            font-size: 0.9em;
        }

        .stat-info p {
            margin: 5px 0 0;
            color: var(--text-dark);
            font-size: 1.5em;
            font-weight: 600;
        }

        /* Doctors Grid */
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            padding: 25px;
        }

        .doctor-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: var(--transition);
            border: 1px solid #eee;
        }

        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .doctor-header {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #eee;
        }

        .doctor-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
        }

        .doctor-info h3 {
            margin: 0;
            color: var(--text-dark);
            font-size: 1.2em;
        }

        .specialty {
            margin: 5px 0 0;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .doctor-body {
            padding: 20px;
        }

        .email {
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 15px;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .stars {
            color: #ffc107;
        }

        .stars i {
            margin-right: 2px;
        }

        .sessions {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
        }

        .doctor-footer {
            padding: 20px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        .doctor-footer .btn-primary {
            flex: 1;
            text-align: center;
            justify-content: center;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px;
            grid-column: 1 / -1;
        }

        .empty-state img {
            width: 200px;
            margin-bottom: 20px;
        }

        .empty-state p {
            color: var(--text-light);
            font-size: 1.1em;
            margin: 0 0 20px;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .menu {
                width: 240px;
            }
            .dash-body {
                margin-left: 240px;
            }
            .filters-stats-container {
                grid-template-columns: 1fr;
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
            .doctors-grid {
                grid-template-columns: 1fr;
                padding: 15px;
            }
            
            .doctor-footer {
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
            <a href="doctors.php" class="menu-btn menu-active">
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
            <a href="settings.php" class="menu-btn">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
        
        <div class="dash-body">
            <div class="search-container">
                <form action="" method="post" class="header-search">
                    <input type="search" name="search" class="input-text" placeholder="Search Doctor name or Email" list="doctors">
                    <?php
                        echo '<datalist id="doctors">';
                        $list11 = $database->query("select docname,docemail from doctor;");
                        for ($y=0;$y<$list11->num_rows;$y++){
                            $row00=$list11->fetch_assoc();
                            $d=$row00["docname"];
                            $c=$row00["docemail"];
                            echo "<option value='$d'><br/>";
                            echo "<option value='$c'><br/>";
                        };
                        echo ' </datalist>';
                    ?>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                </form>
            </div>

            <!-- Advanced Filters and Statistics -->
            <div class="filters-stats-container">
                <div class="filters-section">
                    <h3><i class="fas fa-filter"></i> Advanced Filters</h3>
                    <form action="" method="post" class="filters-form">
                        <div class="filter-group">
                            <label>
                                <i class="fas fa-stethoscope"></i>
                                Specialty
                            </label>
                            <select name="specialty" class="select2">
                                <option value="">All Specialties</option>
                                <?php
                                foreach($specialties_data as $specialty) {
                                    echo "<option value='".$specialty['specialties']."'>".$specialty['specialties']." (".$specialty['count'].")</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <?php if($table_exists): ?>
                        <div class="filter-group">
                            <label>
                                <i class="fas fa-star"></i>
                                Minimum Rating
                            </label>
                            <select name="rating">
                                <option value="">Any Rating</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4+ Stars</option>
                                <option value="3">3+ Stars</option>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="filter-group">
                            <label>
                                <i class="fas fa-calendar-check"></i>
                                Availability
                            </label>
                            <select name="availability">
                                <option value="">All Doctors</option>
                                <option value="available">Available for Booking</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-search"></i>
                            Apply Filters
                        </button>
                    </form>
                </div>

                <div class="stats-section">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div class="stat-info">
                            <h4>Total Doctors</h4>
                            <p><?php echo $list11->num_rows; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <div class="stat-info">
                            <h4>Specialties</h4>
                            <p><?php echo count($specialties_data); ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <canvas id="specialtyChart"></canvas>
                    </div>
                    <?php if($table_exists): ?>
                    <div class="stat-card">
                        <canvas id="ratingsChart"></canvas>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="data-table">
                <div class="table-header">
                    <h3>
                        <i class="fas fa-user-md"></i>
                        All Doctors (<?php echo $list11->num_rows; ?>)
                    </h3>
                </div>

                <div class="doctors-grid">
                    <?php
                        if($_POST){
                            $keyword=$_POST["search"];
                            $sqlmain= "select * from doctor where docemail='$keyword' or docname='$keyword' or docname like '$keyword%' or docname like '%$keyword' or docname like '%$keyword%'";
                        }else{
                            $sqlmain= "select * from doctor order by docid desc";
                        }

                        $result= $database->query($sqlmain);

                        if($result->num_rows==0){
                            echo '<div class="empty-state">
                                <img src="../img/notfound.svg" alt="No doctors found">
                                <p>No doctors found matching your search!</p>
                                <a href="doctors.php" class="btn-primary">
                                    <i class="fas fa-sync"></i>
                                    Show all Doctors
                                </a>
                            </div>';
                        }else{
                            for ($x=0;$x<$result->num_rows;$x++){
                                $row=$result->fetch_assoc();
                                $docid=$row["docid"];
                                $name=$row["docname"];
                                $email=$row["docemail"];
                                $spe=$row["specialties"];
                                $spcil_res= $database->query("select sname from specialties where id='$spe'");
                                $spcil_array= $spcil_res->fetch_assoc();
                                $spcil_name=$spcil_array["sname"];
                                
                                // Get doctor's rating if table exists
                                $rating = 0;
                                $rating_count = 0;
                                if($table_exists) {
                                    $rating_res = $database->query("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM doctor_ratings WHERE docid='$docid'");
                                    $rating_data = $rating_res->fetch_assoc();
                                    $rating = round($rating_data['avg_rating'], 1);
                                    $rating_count = $rating_data['count'];
                                }

                                // Get upcoming sessions count
                                $sessions_res = $database->query("SELECT COUNT(*) as count FROM schedule WHERE docid='$docid' AND scheduledate >= CURDATE()");
                                $sessions_data = $sessions_res->fetch_assoc();
                                $upcoming_sessions = $sessions_data['count'];

                                echo '<div class="doctor-card">
                                    <div class="doctor-header">
                                        <img src="../img/doctor.png" alt="Doctor">
                                        <div class="doctor-info">
                                            <h3>'.substr($name,0,30).'</h3>
                                            <p class="specialty">
                                                <i class="fas fa-stethoscope"></i>
                                                '.substr($spcil_name,0,20).'
                                            </p>
                                        </div>
                                    </div>
                                    <div class="doctor-body">
                                        <p class="email">
                                            <i class="fas fa-envelope"></i>
                                            '.substr($email,0,20).'
                                        </p>';
                                        
                                if($table_exists) {
                                    echo '<div class="rating">
                                        <div class="stars">';
                                        for($i = 1; $i <= 5; $i++) {
                                            if($i <= $rating) {
                                                echo '<i class="fas fa-star"></i>';
                                            } else if($i - 0.5 <= $rating) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        echo '</div>
                                        <span>('.$rating_count.' reviews)</span>
                                    </div>';
                                }

                                echo '<div class="sessions">
                                        <i class="fas fa-calendar-check"></i>
                                        '.$upcoming_sessions.' Upcoming Sessions
                                    </div>
                                    </div>
                                    <div class="doctor-footer">
                                        <a href="?action=view&id='.$docid.'" class="btn-primary">
                                            <i class="fas fa-eye"></i>
                                            View Profile
                                        </a>
                                        <a href="schedule.php?docid='.$docid.'" class="btn-primary">
                                            <i class="fas fa-calendar-plus"></i>
                                            Book Session
                                        </a>
                                    </div>
                                </div>';
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php 
    if($_GET){
        $id=$_GET["id"];
        $action=$_GET["action"];
        if($action=='view'){
            $sqlmain= "select * from doctor where docid='$id'";
            $result= $database->query($sqlmain);
            $row=$result->fetch_assoc();
            $name=$row["docname"];
            $email=$row["docemail"];
            $spe=$row["specialties"];
            
            $spcil_res= $database->query("select sname from specialties where id='$spe'");
            $spcil_array= $spcil_res->fetch_assoc();
            $spcil_name=$spcil_array["sname"];
            $nic=$row['docnic'];
            $tele=$row['doctel'];
            echo '
            <div class="overlay" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(5px);display:flex;align-items:center;justify-content:center;z-index:1000;">
                <div class="popup" style="background:white;border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);padding:30px;max-width:500px;width:90%;position:relative;animation:fadeIn 0.3s ease;">
                    <h2 style="margin:0 0 20px;color:var(--text-dark);">Doctor Details</h2>
                    <a class="close" href="doctors.php" style="position:absolute;top:20px;right:20px;font-size:24px;color:var(--text-light);text-decoration:none;transition:var(--transition);">&times;</a>
                    <div class="content" style="margin-bottom:25px;">
                        <p><strong>Name:</strong> '.$name.'</p>
                        <p><strong>Email:</strong> '.$email.'</p>
                        <p><strong>NIC:</strong> '.$nic.'</p>
                        <p><strong>Telephone:</strong> '.$tele.'</p>
                        <p><strong>Specialties:</strong> '.$spcil_name.'</p>
                    </div>
                    <div style="display:flex;justify-content:center;">
                        <a href="doctors.php" class="btn-primary">
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
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'classic',
                placeholder: 'Select a specialty'
            });

            // Specialty Distribution Chart
            const specialtyCtx = document.getElementById('specialtyChart').getContext('2d');
            new Chart(specialtyCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php 
                        echo json_encode(array_map(function($specialty) {
                            return $specialty['specialties'];
                        }, $specialties_data));
                    ?>,
                    datasets: [{
                        data: <?php 
                            echo json_encode(array_map(function($specialty) {
                                return $specialty['count'];
                            }, $specialties_data));
                        ?>,
                        backgroundColor: [
                            '#2193b0',
                            '#6dd5ed',
                            '#FF6B6B',
                            '#4CAF50',
                            '#9C27B0',
                            '#FF9800'
                        ]
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

            <?php if($table_exists): ?>
            // Ratings Distribution Chart
            const ratingsCtx = document.getElementById('ratingsChart').getContext('2d');
            new Chart(ratingsCtx, {
                type: 'bar',
                data: {
                    labels: ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'],
                    datasets: [{
                        label: 'Number of Doctors',
                        data: [
                            <?php 
                                $ratings_dist = $database->query("
                                    SELECT 
                                        FLOOR(AVG(rating)) as rating_floor,
                                        COUNT(*) as count
                                    FROM doctor_ratings
                                    GROUP BY FLOOR(AVG(rating))
                                    ORDER BY rating_floor DESC
                                ");
                                $ratings_data = array_fill(0, 5, 0);
                                while($row = $ratings_dist->fetch_assoc()) {
                                    $ratings_data[$row['rating_floor']-1] = $row['count'];
                                }
                                echo implode(',', array_reverse($ratings_data));
                            ?>
                        ],
                        backgroundColor: '#2193b0'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
            <?php endif; ?>
        });
    </script>
</body>
</html>