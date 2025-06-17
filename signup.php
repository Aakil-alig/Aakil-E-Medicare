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

if($_POST){
    $_SESSION["personal"]=array(
        'fname'=>$_POST['fname'],
        'lname'=>$_POST['lname'],
        'address'=>$_POST['address'],
        'nic'=>$_POST['nic'],
        'dob'=>$_POST['dob']
    );
    print_r($_SESSION["personal"]);
    header("location: create-account.php");
    exit(); // Add exit after redirect
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
    <link rel="stylesheet" href="css/signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Sign Up - eMediCare</title>
    <style>
        :root {
            --primary-color: #0083b0;
            --secondary-color: #00b4db;
            --background-color: #f8f9fa;
            --text-color: #333;
            --error-color: #ff4444;
            --success-color: #00C851;
        }

        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .header-text {
            color: var(--primary-color);
            font-size: 2.5em;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }

        .sub-text {
            color: #666;
            font-size: 1.1em;
            margin: 10px 0 30px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: 500;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }

        .input-text {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: white;
        }

        .input-text:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 131, 176, 0.1);
            outline: none;
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 40px;
            color: #999;
            transition: all 0.3s ease;
        }

        .input-text:focus + .input-icon {
            color: var(--primary-color);
        }

        .name-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 10px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 131, 176, 0.3);
        }

        .btn-primary-soft {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-primary-soft:hover {
            background: rgba(0, 131, 176, 0.1);
        }

        .button-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }

        .hover-link1 {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .hover-link1:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        /* Progress Steps */
        .progress-steps {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            position: relative;
            margin: 0 20px;
        }

        .step.active {
            background: var(--primary-color);
        }

        .step::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 2px;
            background: #e0e0e0;
            right: -40px;
            top: 50%;
            transform: translateY(-50%);
        }

        .step:last-child::after {
            display: none;
        }

        .step.active::after {
            background: var(--primary-color);
        }

        /* Floating Labels */
        .form-floating {
            position: relative;
        }

        .form-floating label {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            padding: 0 5px;
            color: #999;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .form-floating input:focus ~ label,
        .form-floating input:not(:placeholder-shown) ~ label {
            top: 0;
            font-size: 0.85em;
            color: var(--primary-color);
        }

        /* Animation for inputs */
        @keyframes inputHighlight {
            from { transform: translateY(10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .form-group {
            animation: inputHighlight 0.5s ease-out forwards;
            opacity: 0;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }

    </style>
</head>
<body>
    <center>
        <div class="container">
            <div class="progress-steps">
                <div class="step active">1</div>
                <div class="step">2</div>
                <div class="step">3</div>
            </div>

            <h1 class="header-text">Let's Get Started</h1>
            <p class="sub-text">Add Your Personal Details to Continue</p>

            <form action="" method="POST">
                <div class="name-group">
                    <div class="form-group">
                        <div class="form-floating">
                            <input type="text" name="fname" class="input-text" placeholder=" " required>
                            <label for="fname">First Name</label>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-floating">
                            <input type="text" name="lname" class="input-text" placeholder=" " required>
                            <label for="lname">Last Name</label>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-floating">
                        <input type="text" name="address" class="input-text" placeholder=" " required>
                        <label for="address">Address</label>
                        <i class="fas fa-home input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-floating">
                        <input type="text" name="nic" class="input-text" placeholder=" " required>
                        <label for="nic">NIC Number</label>
                        <i class="fas fa-id-card input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-floating">
                        <input type="date" name="dob" class="input-text" required>
                        <label for="dob">Date of Birth</label>
                        <i class="fas fa-calendar input-icon"></i>
                    </div>
                </div>

                <div class="button-group">
                    <input type="reset" value="Reset" class="btn btn-primary-soft">
                    <input type="submit" value="Next" class="btn btn-primary">
                </div>

                <div class="login-link">
                    <span class="sub-text">Already have an account? </span>
                    <a href="login.php" class="hover-link1">Login</a>
                </div>
            </form>
        </div>
    </center>

    <script>
        // Add animation to form groups
        document.querySelectorAll('.form-group').forEach((group, index) => {
            group.style.animationDelay = `${index * 0.1}s`;
        });

        // Enhance date input
        const dateInput = document.querySelector('input[type="date"]');
        dateInput.addEventListener('focus', function() {
            this.style.borderColor = 'var(--primary-color)';
        });
        dateInput.addEventListener('blur', function() {
            this.style.borderColor = '#e0e0e0';
        });

        // Form validation feedback
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const inputs = form.querySelectorAll('input[required]');
            inputs.forEach(input => {
                if (!input.value) {
                    input.style.borderColor = 'var(--error-color)';
                    e.preventDefault();
                }
            });
        });

        // Input validation feedback
        const inputs = document.querySelectorAll('.input-text');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = this.value ? 'var(--success-color)' : '#e0e0e0';
            });
        });
    </script>
</body>
</html>