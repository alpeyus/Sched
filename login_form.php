<?php

session_start();

if (isset($_POST['submit'])) {
    @include 'admin_dashboard/template/config.php';

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = md5($_POST['password']);
    $cpass = md5($_POST['cpassword']);
    $user_type = $_POST['user_type'];

    // Non-image CAPTCHA verification
    $captcha = $_POST['captcha'];
    $expected_captcha = $_SESSION['captcha'];

    if ($captcha !== $expected_captcha) {
        $error[] = 'Invalid CAPTCHA.';
    } else {
        $select = "SELECT * FROM user_formm WHERE email = '$email' && password = '$pass' ";
        $result = mysqli_query($conn, $select);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);

            if ($row['user_type'] == 'admin') {
                $_SESSION['admin_name'] = $row['name'];
                header('location:/login_system/admin_dashboard/template/index.php');
            } elseif ($row['user_type'] == 'user') {
                $_SESSION['user_name'] = $row['name'];
                header('location:/login_system/admin_dashboard/template/studentIndex.php');
            } elseif ($row['user_type'] == 'teacher') {
                $_SESSION['teacher_name'] = $row['name'];
                header('location:/login_system/admin_dashboard/template/teacherIndex.php');
            }
        } else {
            $error[] = 'Incorrect email or password!';
        }
    }
}

// Generate a random CAPTCHA code
function generateCaptcha()
{
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $captcha = '';
    for ($i = 0; $i < 6; $i++) {
        $captcha .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $captcha;
}

// Store the CAPTCHA code in a session
$_SESSION['captcha'] = generateCaptcha();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="styleee.css">
    <style>
        body {
            background-color: #f7f7f7;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #fff;
            padding: 20px;
            text-align: center;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            text-decoration: none;
            color: #1a1a1a;
        }

        .form-container {
            margin: 200px auto;
            max-width: 400px;
            padding: 30px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.3);
        }

        h3 {
            margin-top: 0px;
        }

        .error-msg {
            color: red;
            display: block;
            margin-top: 10px;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"],
        input[type="submit"] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #154c79;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        input[type="submit"]:hover {
            background-color: #176184;
        }

        p {
            margin-top: 20px;
            font-size: 14px;
            text-align: center;
        }

        a {
            color: blue;
            text-decoration: none;
            transition: color 0.2s;
        }

        a:hover {
            color: #444;
        }

        /* Styling for the CAPTCHA input field */
        label[for="captcha"] {
            color: #000;
            font-weight: bold;
            font-size: 24px;
        }
    </style>
</head>

<body>
    <header>
        <a href="index.php" class="logo"><img src="STIII.jpg"></a>
    </header>
    <div class="custom-loader"></div>
    <div class="form-container">
        <form action="" method="post">
            <h3>Login Now</h3>
            <?php
            if (isset($error)) {
                foreach ($error as $error) {
                    echo '<span class="error-msg">' . $error . '</span>';
                };
            }
            ?>
            <input type="email" name="email" required placeholder="Enter your email">
            <input type="password" name="password" required placeholder="Enter your password">

           <span style="color: #000; font-weight: bold; font-size: 24px;"><?php echo $_SESSION['captcha']; ?></span></label>
            <input type="text" name="captcha" required autocomplete="off" placeholder="Enter the CAPTCHA code:">

            <input type="submit" name="submit" value="Login Now" class="form-btn">
            <p>Don't have an account? Ask the registrar</p>
        </form>
    </div>
</body>

</html>