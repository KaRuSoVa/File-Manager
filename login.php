<?php

session_start();

include "database.php";

$loginSuccessful = false;



if (isset($_SESSION["id"]) && isset($_SESSION["username"]) && isset($_SESSION["email"])) {

    header("Location: index.php");

    exit();

}



if (isset($_POST["email"]) && isset($_POST["password"])) {

    function validate($data)

    {

        $data = trim($data);

        $data = stripslashes($data);

        $data = htmlspecialchars($data);

        return $data;

    }



    $email = validate($_POST["email"]);

    $password = validate($_POST["password"]);



    if (empty($email)) {

        header("Location: login.php?error=email_empty");

        exit();

    } else if (empty($password)) {

        header("Location: login.php?error=password_empty");

        exit();

    } else {

        $password = md5($password); 



        $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";

        $result = mysqli_query($conn, $query);



        if (mysqli_num_rows($result) > 0) {

            $row = mysqli_fetch_assoc($result);

        

            if ($row["email"] == $email && $row["password"] == $password) {

                $_SESSION["id"] = $row["id"];

                $_SESSION["username"] = $row["username"];

                $_SESSION["email"] = $row["email"];

                $_SESSION["role"] = $row["role"];  // role sütunu oturuma eklenir

                $loginSuccessful = true;

            } else {

                header("Location: login.php?error=email_or_password_wrong");

                exit();

            }

        } else {

            header("Location: login.php?error=email_or_password_wrong");

            exit();

        }

        

    }

}

if (isset($_POST['remember'])) {

    // Kullanıcı "Remember me" kutusunu işaretledi

    setcookie('email', $_POST['email'], time() + (86400 * 30)); // 1 ay boyunca çerez saklar

    setcookie('password', $_POST['password'], time() + (86400 * 30)); // 1 ay boyunca çerez saklar

}

if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {

    $_POST['email'] = $_COOKIE['email'];

    $_POST['password'] = $_COOKIE['password'];

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

    <form action="login.php" method="POST" class="login__form">

    <center><img src="img/logochateaux.png" height="100px"/></center>



        <div class="login__content">

            <div class="login__box">

                <i class="ri-user-3-line login__icon"></i>



                <div class="login__box-input">

                    <input type="email" required class="login__input" name="email" placeholder=" ">

                    <label for="" class="login__label">Email</label>

                </div>

            </div>



            <div class="login__box">

                <i class="ri-lock-2-line login__icon"></i>



                <div class="login__box-input">

                    <input type="password" required class="login__input" name="password" id="login-pass" placeholder=" ">

                    <label for="" class="login__label">Password</label>

                    <i class="ri-eye-off-line login__eye" id="login-eye"></i>

                </div>

            </div>

        </div>



        <div class="login__check">

            <div class="login__check-group">

          

            </div>

            <button type="submit" class="login__button">Login</button>

            <button type="button" class="login__button" onclick="window.location.href='register.php'">Register</button>





    </form>

</div>



<!--=============== MAIN JS ===============-->

<script src="assets/js/main.js"></script>

<?php if ($loginSuccessful): ?>

        <script>

            function showConfetti() {

                // canvas-confetti kütüphanesini kullanarak konfeti animasyonu oluşturma

                const duration = 3500;

                const end = Date.now() + duration;



                function frame() {

                    confetti({

                        particleCount: 3,

                        angle: 60,

                        spread: 55,

                        origin: { x: 0 },

                        colors: ['#00539b', '#a5a5a5']

                    });

                    confetti({

                        particleCount: 3,

                        angle: 120,

                        spread: 55,

                        origin: { x: 1 },

                        colors: ['#00539b', '#a5a5a5' ]

                    });



                    if (Date.now() < end) {

                        requestAnimationFrame(frame);

                    }

                }



                frame();

            }



            // Konfeti animasyonunu başlat ve yönlendirmeyi gerçekleştir

            showConfetti();

            setTimeout(() => {

                window.location.href = "index.php";

            }, 3500);

        </script>

    <?php endif; ?>

</body>

</html>

