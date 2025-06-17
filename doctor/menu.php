<?php
    if(!isset($useremail)){
        $useremail=$_SESSION["user"];
    }
    $sqlmain= "select * from doctor where docemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s",$useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["docid"];
    $username=$userfetch["docname"];
?>

<div class="menu">
    <div class="menu-header">
        <div class="profile-pic">
            <img src="../img/user.png" alt="Doctor Profile" width="100" height="100">
        </div>
        <h2><?php echo substr($username,0,20) ?></h2>
        <p class="email"><?php echo substr($useremail,0,22) ?></p>
    </div>
    
    <div class="menu-items">
        <a href="index.php" class="menu-item <?php if($page=='dashboard'){echo 'menu-active';} ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="appointment.php" class="menu-item <?php if($page=='appointment'){echo 'menu-active';} ?>">
            <i class="fas fa-calendar-check"></i>
            <span>My Appointments</span>
            <?php
            $pending_query = "SELECT COUNT(*) as count 
                            FROM appointment a 
                            JOIN schedule s ON a.scheduleid = s.scheduleid 
                            WHERE s.docid = '$userid'";
            $pending_appointments = $database->query($pending_query)->fetch_assoc();
            if($pending_appointments['count'] > 0) {
                echo '<span class="badge">'.$pending_appointments['count'].'</span>';
            }
            ?>
        </a>
        
        <a href="schedule.php" class="menu-item <?php if($page=='schedule'){echo 'menu-active';} ?>">
            <i class="fas fa-clock"></i>
            <span>My Sessions</span>
        </a>
        
        <a href="patient.php" class="menu-item <?php if($page=='patient'){echo 'menu-active';} ?>">
            <i class="fas fa-user-injured"></i>
            <span>My Patients</span>
        </a>
        
        <a href="settings.php" class="menu-item <?php if($page=='settings'){echo 'menu-active';} ?>">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
        
        <a href="../logout.php" class="menu-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<style>
.menu {
    width: 280px;
    background: white;
    min-height: 100vh;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    padding: 20px 0;
    position: fixed;
    left: 0;
    top: 0;
}

.menu-header {
    text-align: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
    margin-bottom: 20px;
}

.profile-pic {
    width: 100px;
    height: 100px;
    margin: 0 auto 15px;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.profile-pic img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.menu-header h2 {
    color: #333;
    font-size: 1.2em;
    margin: 10px 0 5px;
}

.menu-header .email {
    color: #666;
    font-size: 0.9em;
    margin: 0;
}

.menu-items {
    padding: 0 15px;
}

.menu-item {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #555;
    text-decoration: none;
    border-radius: 8px;
    margin-bottom: 5px;
    transition: all 0.3s;
    position: relative;
}

.menu-item i {
    font-size: 1.2em;
    margin-right: 12px;
    width: 20px;
    text-align: center;
}

.menu-item span {
    font-weight: 500;
}

.menu-item:hover {
    background: #f8f9fa;
    color: #0083b0;
    transform: translateX(5px);
}

.menu-active {
    background: #0083b0 !important;
    color: white !important;
}

.menu-active:hover {
    background: #00b4db !important;
    color: white !important;
}

.badge {
    position: absolute;
    right: 15px;
    background: #dc3545;
    color: white;
    font-size: 0.8em;
    padding: 2px 8px;
    border-radius: 10px;
}

.logout {
    margin-top: 30px;
    color: #dc3545;
}

.logout:hover {
    background: #dc3545;
    color: white;
}

@media (max-width: 768px) {
    .menu {
        width: 100%;
        position: relative;
        min-height: auto;
        padding: 10px;
    }
    
    .menu-header {
        padding: 10px;
    }
    
    .profile-pic {
        width: 60px;
        height: 60px;
    }
}
</style> 