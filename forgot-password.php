<?php
session_start();
include("connection.php");

$error = "";
$status = "";

if(isset($_POST['reset_request'])) {
    $email = $_POST['email'];
    $usertype = $_POST['usertype'];
    
    // Check if email exists and get user type
    $webuser_result = $database->query("SELECT * FROM webuser WHERE email='$email'");
    
    if($webuser_result->num_rows == 1) {
        $user_data = $webuser_result->fetch_assoc();
        $db_usertype = $user_data['usertype'];
        
        // Verify if user type matches
        $type_matches = false;
        if($usertype == 'patient' && $db_usertype == 'p') $type_matches = true;
        if($usertype == 'doctor' && $db_usertype == 'd') $type_matches = true;
        if($usertype == 'admin' && $db_usertype == 'a') $type_matches = true;
        
        if($type_matches) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in database
            $database->query("UPDATE webuser SET reset_token='$token', reset_expiry='$expiry' WHERE email='$email'");
            
            // In a real application, you would send an email here
            // For demo purposes, we'll just show the reset form directly
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_token'] = $token;
            $status = "success";
        } else {
            $error = "This email is not registered as a $usertype.";
        }
    } else {
        $error = "No account found with this email address.";
    }
}

if(isset($_POST['reset_password'])) {
    $email = $_SESSION['reset_email'];
    $token = $_SESSION['reset_token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if($new_password == $confirm_password) {
        // Get user type
        $user_result = $database->query("SELECT usertype FROM webuser WHERE email='$email'");
        $user_data = $user_result->fetch_assoc();
        $usertype = $user_data['usertype'];
        
        // Update password based on user type
        if($usertype == 'p') {
            $database->query("UPDATE patient SET ppassword='$new_password' WHERE pemail='$email'");
        } else if($usertype == 'd') {
            $database->query("UPDATE doctor SET docpassword='$new_password' WHERE docemail='$email'");
        } else if($usertype == 'a') {
            $database->query("UPDATE admin SET apassword='$new_password' WHERE aemail='$email'");
        }
        
        // Clear reset token
        $database->query("UPDATE webuser SET reset_token=NULL, reset_expiry=NULL WHERE email='$email'");
        
        // Clear session
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_token']);
        
        header("Location: login.php?action=reset_success");
        exit();
    } else {
        $error = "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
    <title>Reset Password</title>
    
    <style>
        .container {
            animation: transitionIn-Y-bottom 0.5s;
        }
        
        .reset-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            margin: 50px auto;
        }
        
        .header-text {
            font-size: 2em;
            text-align: center;
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 2px solid #eee;
            border-radius: 5px;
            font-size: 1em;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }
        
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
        }
        
        .error-message {
            color: #dc3545;
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            background-color: rgba(220, 53, 69, 0.1);
        }
        
        .success-message {
            color: #28a745;
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            background-color: rgba(40, 167, 69, 0.1);
        }
        
        .user-type-selector {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .user-type-btn {
            padding: 10px 20px;
            border: 2px solid var(--primary-color);
            border-radius: 20px;
            background: transparent;
            color: var(--primary-color);
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .user-type-btn.active {
            background: var(--primary-color);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-container">
            <?php if(!isset($_SESSION['reset_email'])): ?>
                <!-- Email verification form -->
                <h2 class="header-text">Reset Password</h2>
                <p style="text-align: center; margin-bottom: 20px; color: #666;">
                    Enter your email address and we'll help you reset your password.
                </p>
                
                <?php if($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if($status == "success"): ?>
                    <div class="success-message">Verification successful! Please set your new password.</div>
                <?php endif; ?>
                
                <form action="" method="POST">
                    <div class="user-type-selector">
                        <button type="button" class="user-type-btn active" data-type="patient">Patient</button>
                        <button type="button" class="user-type-btn" data-type="doctor">Doctor</button>
                        <button type="button" class="user-type-btn" data-type="admin">Admin</button>
                    </div>
                    
                    <input type="hidden" name="usertype" id="usertype" value="patient">
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <button type="submit" name="reset_request" class="btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Reset Link
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="login.php" style="color: var(--primary-color); text-decoration: none;">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </div>
            <?php else: ?>
                <!-- Password reset form -->
                <h2 class="header-text">Set New Password</h2>
                
                <?php if($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form action="" method="POST">
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    
                    <button type="submit" name="reset_password" class="btn-primary">
                        <i class="fas fa-key"></i> Reset Password
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // User type selector
        document.querySelectorAll('.user-type-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.user-type-btn').forEach(b => b.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Update hidden input
                document.getElementById('usertype').value = this.dataset.type;
            });
        });
    </script>
</body>
</html> 