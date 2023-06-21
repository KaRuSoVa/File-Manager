<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include "database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // reCAPTCHA verification
    $recaptcha = $_POST['g-recaptcha-response'];

    if (!empty($recaptcha)) {
        $googleSecretKey = "6LdgnrcmAAAAAFcGYhzAsMNz4bqwPmEu0HF1SnMd";
        
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $googleSecretKey . "&response=" . $_POST['g-recaptcha-response']);

        $responseKeys = json_decode($response, true);

        if (!$responseKeys["success"]) {
            $_SESSION["error"] = "reCAPTCHA doğrulaması başarısız oldu. Lütfen tekrar deneyin.";
        }
    } else {
        $_SESSION["error"] = "Lütfen reCAPTCHA doğrulamasını tamamlayın.";
    }

    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $username = validate($_POST["username"]);
    $email = validate($_POST["email"]);
    $password = validate($_POST["password"]);

    if (empty($username)) {
        $_SESSION["error"] = "Username cannot be left blank!Please try again";
    } else if (empty($email)) {
        $_SESSION["error"] = "E-mail cannot be left blank!Please try again";
    } else if (empty($password)) {
        $_SESSION["error"] = "Password cannot be left blank!Please try again";
    } else {
        $password = md5($password); // Password hash

        // Check if the email already exists
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $_SESSION["error"] = "This e-mail is already exist.Please try again";
        } else {
            // Add user to database
            $query = "INSERT INTO users (username, email, password,role,quota) VALUES ('$username', '$email', '$password','normal','100')";
            $result = mysqli_query($conn, $query);

            if ($result) {
                // Get the id of the user
                $user_id = mysqli_insert_id($conn);

                // Create a directory for the user
                $user_dir = "upload_directory/" . $user_id;
                if (!file_exists($user_dir)) {
                    mkdir($user_dir, 0777, true);
                }

                header("Location: login.php?register=success");
                exit();
            } else {
                $_SESSION["error"] = "Kayıt oluşturulamadı, lütfen tekrar deneyin.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--=============== REMIXICONS ===============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700&display=swap" rel="stylesheet">
<script src="https://www.google.com/recaptcha/api.js"></script>

    <title>Digital Chateaux File Manager </title>
    <style>
        input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
    transition: background-color 5000s ease-in-out 0s;
}
    </style>
</head>
<body>
    
<div class="login">
    <img src="img/bg.png" alt="register image" class="login__img">
    <form action="" method="POST" id="register" class="login__form">
    <?php if(isset($_SESSION['error'])): ?>
            <p><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
      
    <center><img src="img/logochateaux.png" height="100px"/></center>

        <div class="login__content">
            <div class="login__box">
                <i class="ri-user-3-line login__icon"></i>

                <div class="login__box-input">
                    <input type="text" required class="login__input" name="username" placeholder=" ">
                    <label for="" class="login__label">Username</label>
                </div>
            </div>

            <div class="login__box">
                <i class="ri-mail-line login__icon"></i>

                <div class="login__box-input">
                    <input type="email" required class="login__input" name="email" placeholder=" ">
                    <label for="" class="login__label">Email</label>
                </div>
            </div>

            <div class="login__box">
                <i class="ri-lock-2-line login__icon"></i>

                <div class="login__box-input">
                    <input type="password" required class="login__input" name="password" id="register-pass" placeholder=" ">
                    <label for="" class="login__label">Password</label>
                    <i class="ri-eye-off-line login__eye" id="register-eye"></i>
                </div>
            </div>

            <div class="login__box">
                <i class="ri-lock-2-line login__icon"></i>

                <div class="login__box-input">
                    <input type="password" required class="login__input" name="confirm_password" id="confirm-pass" placeholder=" ">
                    <label for="" class="login__label">Confirm Password</label>
                    <i class="ri-eye-off-line login__eye" id="confirm-eye"></i>
                </div>
            </div>
        </div>

        <div class="login__check">
            <div class="g-recaptcha" data-sitekey="6LdgnrcmAAAAAFcGYhzAsMNz4bqwPmEu0HF1SnMd"></div>
             <!-- Add this right after the reCAPTCHA div -->
   

            <div class="login__check-group">
          
            </div>
            <button type="submit" class="login__button">Register</button>

    </form>
</div>
<script>
    function onSubmit(token) {
      document.getElementById("register").submit();
    }
  </script>
<!--=============== MAIN JS ===============-->
<script src="assets/js/main2.js"></script> 

</body>
</html>
