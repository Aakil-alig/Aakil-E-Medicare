<?php
// Start session at the very beginning
session_start();

// Check if user is logged in and is a patient
if(!isset($_SESSION["user"]) || $_SESSION["user"]=="" || $_SESSION['usertype']!='p'){
    header("location: ../login.php");
    exit(); // Add exit after redirect
}

// Include database connection
include("../connection.php");

try {
    // Get patient details
    $useremail = $_SESSION["user"];
    $stmt = $database->prepare("SELECT * FROM patient WHERE pemail=?");
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $result = $stmt->get_result();
    $userfetch = $result->fetch_assoc();
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];

    // Get counts and statistics
    $stmt = $database->prepare("SELECT COUNT(*) as count FROM doctor");
    $stmt->execute();
    $result = $stmt->get_result();
    $doctorrow = $result->fetch_assoc();
    $doctor_count = $doctorrow['count'];

    $stmt = $database->prepare("SELECT COUNT(*) as count FROM patient");
    $stmt->execute();
    $result = $stmt->get_result();
    $patientrow = $result->fetch_assoc();
    $patient_count = $patientrow['count'];

    $stmt = $database->prepare("SELECT COUNT(*) as count FROM appointment WHERE pid=?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookingrow = $result->fetch_assoc();
    $booking_count = $bookingrow['count'];

    // Get reports count
    $stmt = $database->prepare("SELECT COUNT(*) as count FROM patient_reports WHERE patient_id=?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $reportsrow = $result->fetch_assoc();
    $reports_count = $reportsrow['count'];

    $today = date('Y-m-d');
    
    // Get today's session count
    $stmt = $database->prepare("SELECT COUNT(*) as count FROM schedule WHERE scheduledate = ?");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $today_count = $result->fetch_assoc()['count'];
    
    // Get upcoming appointments
    $stmt = $database->prepare("
        SELECT a.*, s.title, s.scheduledate, s.scheduletime, d.docname, d.docid, d.specialties 
        FROM appointment a 
        JOIN schedule s ON a.scheduleid = s.scheduleid 
        JOIN doctor d ON s.docid = d.docid 
        WHERE a.pid = ? AND s.scheduledate >= ? 
        ORDER BY s.scheduledate ASC 
        LIMIT 5
    ");
    $stmt->bind_param("is", $userid, $today);
    $stmt->execute();
    $upcoming_appointments = $stmt->get_result();

    // Get appointment history for chart
    $stmt = $database->prepare("
        SELECT DATE_FORMAT(s.scheduledate, '%Y-%m') as month, COUNT(*) as count 
        FROM appointment a 
        JOIN schedule s ON a.scheduleid = s.scheduleid 
        WHERE a.pid = ? 
        GROUP BY DATE_FORMAT(s.scheduledate, '%Y-%m') 
        ORDER BY month DESC 
        LIMIT 6
    ");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $appointment_history = $stmt->get_result();
    
    $months = [];
    $counts = [];
    while($row = $appointment_history->fetch_assoc()) {
        $months[] = date('M Y', strtotime($row['month'].'-01'));
        $counts[] = $row['count'];
    }
    $months = array_reverse($months);
    $counts = array_reverse($counts);

} catch(Exception $e) {
    // Log error and show user-friendly message
    error_log($e->getMessage());
    $error_message = "An error occurred while loading the dashboard. Please try again later.";
}
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
    <title>Patient Dashboard</title>
    <style>
        :root {
            --primary-color: #2193b0;
            --secondary-color: #6dd5ed;
            --accent-color: #FF6B6B;
            --text-dark: #333;
            --text-light: #666;
            --bg-light: #f8f9fa;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --radius: 15px;
            --shadow: 0 2px 15px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Modern Layout */
        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Left Sidebar */
        .sidebar {
            width: 250px;
            background: var(--white);
            padding: 20px;
            border-right: 1px solid #ddd;
        }

        .profile {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }

        .profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .profile h3 {
            margin: 10px 0 5px;
            color: var(--text-dark);
        }

        .profile p {
            color: var(--text-light);
            font-size: 14px;
        }

        .nav-menu {
            margin-top: 20px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(33, 150, 243, 0.1);
            color: var(--primary-color);
        }

        .nav-link i {
            margin-right: 10px;
            width: 20px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 24px;
            color: var(--text-dark);
        }

        .date {
            color: var(--text-light);
        }

        .welcome-section {
            background: var(--white);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .welcome-section h2 {
            margin-bottom: 10px;
            color: var(--text-dark);
        }

        .welcome-section p {
            color: var(--text-light);
            line-height: 1.5;
        }

        .search-section {
            margin-bottom: 30px;
        }

        .search-box {
            display: flex;
            gap: 10px;
        }

        .search-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .search-button {
            padding: 10px 20px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Status Cards */
        .status-section {
            margin-bottom: 30px;
        }

        .status-section h3 {
            margin-bottom: 20px;
            color: var(--text-dark);
            font-size: 1.5em;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }

        .status-card {
            background: white;
            padding: 20px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: var(--transition);
        }

        .status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .status-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .status-info {
            flex: 1;
        }

        .status-info h3 {
            margin: 0 0 5px;
            font-size: 28px;
            font-weight: 600;
        }

        .status-info p {
            margin: 0;
            color: var(--text-light);
            font-size: 14px;
        }

        /* Bookings Table */
        .bookings-section {
            background: var(--white);
            padding: 20px;
            border-radius: 10px;
            box-shadow: var(--shadow-sm);
        }

        .bookings-title {
            margin-bottom: 20px;
            color: var(--text-dark);
        }

        .bookings-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bookings-table th,
        .bookings-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .bookings-table th {
            background: #f5f5f5;
            font-weight: 600;
            color: var(--text-dark);
        }

        .bookings-table tr:hover {
            background: #f8f9fa;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #ddd;
            }
            .status-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }
    </style>
</head>
<body>
    <?php if(isset($error_message)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div class="container">
        <?php 
        $page = 'dashboard';
        include("menu.php"); 
        ?>
        <div class="dash-body" style="margin-top: 15px">
            <div class="dash-header">
                <h1>Welcome, <?php echo $username; ?></h1>
                <p>Here's your health overview</p>
            </div>

            <div class="dash-stats">
                <div class="stat-card" onclick="window.location.href='appointments.php'" style="cursor:pointer">
                    <div class="stat-icon" style="background: #0083b0">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $booking_count; ?></h4>
                        <p>My Appointments</p>
                    </div>
                </div>

                <div class="stat-card" onclick="window.location.href='reports.php'" style="cursor:pointer">
                    <div class="stat-icon" style="background: #00b4db">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $reports_count; ?></h4>
                        <p>Medical Reports</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #38ef7d">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="stat-info">
                        <h4><?php echo $doctor_count; ?></h4>
                        <p>Available Doctors</p>
                    </div>
                </div>
            </div>

            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-grid">
                    <a href="appointments.php" class="action-card">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Book Appointment</span>
                    </a>
                    <a href="reports.php" class="action-card">
                        <i class="fas fa-file-medical"></i>
                        <span>View Reports</span>
                    </a>
                    <a href="doctors.php" class="action-card">
                        <i class="fas fa-user-md"></i>
                        <span>Find Doctors</span>
                    </a>
                </div>
            </div>

            <style>
                .dash-header {
                    margin-bottom: 30px;
                    padding: 0 20px;
                }
                .dash-header h1 {
                    margin: 0;
                    color: #333;
                    font-size: 24px;
                }
                .dash-header p {
                    margin: 5px 0 0;
                    color: #666;
                }
                .dash-stats {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 20px;
                    padding: 20px;
                }
                .stat-card {
                    background: white;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    transition: all 0.3s ease;
                }
                .stat-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
                }
                .stat-icon {
                    width: 50px;
                    height: 50px;
                    border-radius: 10px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 24px;
                    color: white;
                }
                .stat-info h4 {
                    margin: 0;
                    font-size: 24px;
                    color: #333;
                }
                .stat-info p {
                    margin: 5px 0 0;
                    color: #666;
                }
                .quick-actions {
                    padding: 20px;
                }
                .quick-actions h2 {
                    margin: 0 0 20px;
                    color: #333;
                    font-size: 20px;
                }
                .action-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 20px;
                }
                .action-card {
                    background: white;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    text-decoration: none;
                    color: #333;
                    text-align: center;
                    transition: all 0.3s ease;
                }
                .action-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
                }
                .action-card i {
                    font-size: 30px;
                    color: #0083b0;
                    margin-bottom: 10px;
                }
                .action-card span {
                    display: block;
                    font-weight: 500;
                }
            </style>
        </div>
    </div>

    <script>
        // Initialize Health Metrics Chart
        function initHealthMetricsChart() {
            const ctx = document.getElementById('healthChart').getContext('2d');
            const healthChart = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['General Health', 'Mental Health', 'Physical Activity', 'Sleep', 'Diet', 'Stress Level'],
                    datasets: [{
                        label: 'Current',
                        data: [80, 75, 70, 65, 85, 70],
                        fill: true,
                        backgroundColor: 'rgba(33, 147, 176, 0.2)',
                        borderColor: '#2193b0',
                        pointBackgroundColor: '#2193b0',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#2193b0'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        r: {
                            angleLines: {
                                display: true
                            },
                            suggestedMin: 0,
                            suggestedMax: 100
                        }
                    }
                }
            });
            return healthChart;
        }

        // Handle Virtual Consultation
        function showVirtualConsult() {
            Swal.fire({
                title: 'Start Virtual Consultation',
                html: `
                    <div class="virtual-consult-setup">
                        <div class="device-check">
                            <p><i class="fas fa-video"></i> Checking camera...</p>
                            <p><i class="fas fa-microphone"></i> Checking microphone...</p>
                        </div>
                        <select class="form-control" id="doctorSelect">
                            <option value="">Select Doctor</option>
                            <?php 
                                $doctors = $database->query("SELECT docid, docname, specialties FROM doctor");
                                while($doc = $doctors->fetch_assoc()) {
                                    echo "<option value='".$doc['docid']."'>".$doc['docname']." (".$doc['specialties'].")</option>";
                                }
                            ?>
                        </select>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Start Consultation',
                confirmButtonColor: '#2196F3',
                cancelButtonColor: '#666',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve) => {
                        setTimeout(resolve, 1500);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'virtual-consultation.php';
                }
            });
        }

        // Initialize components
        document.addEventListener('DOMContentLoaded', function() {
            initHealthMetricsChart();
        });
    </script>
</body>
</html>