<?php
    //learn from w3schools.com
    //Unset all the server side variables
    session_start();
    $_SESSION["user"]="";
    $_SESSION["usertype"]="";
    
    // Set the new timezone
    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d');
    $_SESSION["date"]=$date;
    
    //import database
    include("connection.php");

    if($_POST){
        $email=$_POST['useremail'];
        $password=$_POST['userpassword'];
        $usertype = isset($_POST['usertype']) ? $_POST['usertype'] : 'patient';
        
        $error='<label for="promter" class="form-label"></label>';

        $result= $database->query("select * from webuser where email='$email'");
        if($result->num_rows==1){
            $row = $result->fetch_assoc();
            $utype = $row['usertype'];
            
            // Map form usertype to database usertype
            $expected_type = '';
            if($usertype == 'patient') $expected_type = 'p';
            else if($usertype == 'doctor') $expected_type = 'd';
            else if($usertype == 'admin') $expected_type = 'a';
            
            // Check if user is trying to login with correct role
            if($utype != $expected_type){
                if($utype == 'p') 
                    $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">This email is registered as a Patient. Please use Patient login.</label>';
                else if($utype == 'd')
                    $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">This email is registered as a Doctor. Please use Doctor login.</label>';
                else if($utype == 'a')
                    $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">This email is registered as an Admin. Please use Admin login.</label>';
            } else {
                // User type matches, now verify password
                if($utype == 'p'){
                    $checker = $database->query("select * from patient where pemail='$email' and ppassword='$password'");
                    if ($checker->num_rows==1){
                        $_SESSION['user']=$email;
                        $_SESSION['usertype']='p';
                        header('location: patient/index.php');
                        exit();
                    } else {
                        $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
                    }
                } else if($utype == 'd'){
                    $checker = $database->query("select * from doctor where docemail='$email' and docpassword='$password'");
                    if ($checker->num_rows==1){
                        $_SESSION['user']=$email;
                        $_SESSION['usertype']='d';
                        header('location: doctor/index.php');
                        exit();
                    } else {
                        $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
                    }
                } else if($utype == 'a'){
                    $checker = $database->query("select * from admin where aemail='$email' and apassword='$password'");
                    if ($checker->num_rows==1){
                        $_SESSION['user']=$email;
                        $_SESSION['usertype']='a';
                        header('location: admin/index.php');
                        exit();
                    } else {
                        $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
                    }
                }
            }
        } else {
            $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">No account found for this email address.</label>';
        }
    }else{
        $error='<label for="promter" class="form-label">&nbsp;</label>';
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
        
    <title>Login</title>

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
            --input-text-color: #000000;
        }

        body {
            background-color: var(--bg-light);
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
        }

        .header-text {
            font-size: 2em;
            color: var(--text-dark);
            margin: 0 0 10px;
            text-align: center;
        }

        .sub-text {
            color: var(--text-light);
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-floating {
            position: relative;
            background: #ffffff;
            border-radius: 10px;
        }

        .input-text {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            transition: var(--transition);
            color: var(--input-text-color);
            background-color: #ffffff;
            -webkit-text-fill-color: var(--input-text-color);
            opacity: 1;
        }

        .input-text::placeholder {
            color: #999;
            opacity: 1;
        }

        .input-text:-webkit-autofill,
        .input-text:-webkit-autofill:hover,
        .input-text:-webkit-autofill:focus {
            -webkit-text-fill-color: var(--input-text-color);
            -webkit-box-shadow: 0 0 0px 1000px #ffffff inset;
            transition: background-color 5000s ease-in-out 0s;
        }

        .input-text:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,131,176,0.1);
        }

        .form-label {
            position: absolute;
            left: 45px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            transition: all 0.2s ease-in-out;
            pointer-events: none;
            font-size: 14px;
            font-weight: 500;
            padding: 0 5px;
            background-color: #ffffff;
        }

        .input-text:focus ~ .form-label,
        .input-text:not(:placeholder-shown) ~ .form-label {
            top: -10px;
            left: 15px;
            font-size: 12px;
            color: var(--primary-color);
            font-weight: 600;
            background-color: #ffffff;
            z-index: 1;
        }

        .error-message {
            color: #ff3e3e;
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            background-color: rgba(255, 62, 62, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 20px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 131, 176, 0.3);
        }

        .signup-link {
            text-align: center;
            margin-top: 25px;
        }

        .hover-link1 {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .hover-link1:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .error-shake {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .user-type-selector {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .user-type-btn {
            padding: 10px 20px;
            border: 2px solid var(--primary-color);
            border-radius: 20px;
            background: transparent;
            color: var(--primary-color);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
        }

        .user-type-btn.active {
            background: var(--primary-color);
            color: white;
        }

        .forgot-password {
            text-align: right;
            margin-top: 10px;
        }

        .forgot-password a {
            color: #666;
            text-decoration: none;
            font-size: 0.9em;
            transition: var(--transition);
        }

        .forgot-password a:hover {
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="header-text">Welcome Back!</h1>
        <p class="sub-text">Login with your details to continue</p>

        <form action="" method="POST" id="loginForm">
            <div class="user-type-selector">
                <button type="button" class="user-type-btn active" data-type="patient">Patient</button>
                <button type="button" class="user-type-btn" data-type="doctor">Doctor</button>
                <button type="button" class="user-type-btn" data-type="admin">Admin</button>
            </div>

            <input type="hidden" name="usertype" id="usertype" value="patient">

            <div class="form-group">
                <div class="form-floating">
                    <input type="email" name="useremail" class="input-text" placeholder=" " required>
                    <i class="fas fa-envelope input-icon"></i>
                    <label class="form-label">Email Address</label>
                </div>
            </div>

            <div class="form-group">
                <div class="form-floating">
                    <input type="password" name="userpassword" class="input-text" placeholder=" " required>
                    <i class="fas fa-lock input-icon"></i>
                    <label class="form-label">Password</label>
                </div>
            </div>

            <div class="forgot-password">
                <a href="forgot-password.php">Forgot Password?</a>
            </div>

            <?php 
            if(isset($_GET['action']) && $_GET['action'] == 'reset_success') {
                echo '<div class="success-message">Password has been reset successfully!</div>';
            }
            
            if($error != '<label for="promter" class="form-label"></label>' && $error != '<label for="promter" class="form-label">&nbsp;</label>') {
                echo '<div class="error-message">' . strip_tags($error, '<strong>') . '</div>';
            }
            ?>

            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>

            <div class="signup-link">
                <span class="sub-text">Don't have an account? </span>
                <a href="signup.php" class="hover-link1">Sign Up</a>
            </div>
        </form>
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

        // Password visibility toggle
        const passwordInput = document.querySelector('input[type="password"]');
        const togglePassword = document.createElement('i');
        togglePassword.className = 'fas fa-eye input-icon';
        togglePassword.style.right = '15px';
        togglePassword.style.left = 'auto';
        togglePassword.style.cursor = 'pointer';
        passwordInput.parentElement.appendChild(togglePassword);

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.className = `fas fa-eye${type === 'password' ? '' : '-slash'} input-icon`;
        });

        // Form validation and error handling
        const form = document.getElementById('loginForm');
        const inputs = form.querySelectorAll('.input-text');

        inputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.value) {
                    this.style.borderColor = 'var(--success-color)';
                } else {
                    this.style.borderColor = '#e0e0e0';
                }
            });
        });

        // Error animation
        if (document.querySelector('.form-label[style*="color:rgb(255, 62, 62)"]')) {
            form.classList.add('error-shake');
            setTimeout(() => form.classList.remove('error-shake'), 500);
        }
    </script>
</body>
</html>