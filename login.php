<?php
session_start();
include 'dbconnect.php';

$alert = '';

if(isset($_SESSION['role'])){
    $role = $_SESSION['role'];

    if($role == 'Admin'){
        header("location:admin");
        exit();
    } else {
        header("location:user");
        exit();
    }
}

// Proses login
if(isset($_POST['btn-login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Cek apakah user adalah admin
    $cariadmin = mysqli_query($conn, "SELECT * FROM admin WHERE adminemail='$email'");
    $cariuser = mysqli_query($conn, "SELECT * FROM user WHERE useremail='$email'");

    $cekadmin = mysqli_num_rows($cariadmin);
    $cekuser = mysqli_num_rows($cariuser);

    if($cekadmin > 0){
        $data = mysqli_fetch_assoc($cariadmin);
        if ($password == $data['adminpassword']) { // Cek password tanpa hash
            $_SESSION['email'] = $data['adminemail'];
            $_SESSION['role'] = "Admin";
            header("location:admin");
            exit();
        }
    } elseif ($cekuser > 0){
        $data = mysqli_fetch_assoc($cariuser);
        if ($password == $data['userpassword']) { // Cek password tanpa hash
            $_SESSION['email'] = $data['useremail'];
            $_SESSION['userid'] = $data['userid'];
            $_SESSION['role'] = "User";
            header("location:user");
            exit();
        }
    }

    // Jika gagal login
    $alert = "<div class='alert alert-warning'>Email atau Password salah.</div>";
}

// Proses Sign Up
if(isset($_POST['btn-signup'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); // Tidak di-hash

    // Cek apakah email sudah ada
    $cekEmail = mysqli_query($conn, "SELECT * FROM user WHERE useremail='$email'");
    if(mysqli_num_rows($cekEmail) > 0) {
        $alert = "<div class='alert alert-danger'>Email sudah digunakan!</div>";
    } else {
        // Masukkan ke database
        $insert = mysqli_query($conn, "INSERT INTO user (useremail, userpassword) VALUES ('$email', '$password')");

        if($insert) {
            $alert = "<div class='alert alert-success'>Pendaftaran berhasil, silakan login.</div>";
        } else {
            $alert = "<div class='alert alert-danger'>Gagal mendaftar. Silakan coba lagi.</div>";
        }
    }
}
?>


<!-- HTML untuk login dan signup -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup</title>
    <link rel="icon" type="image/png" href="assets/img/tut.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <section class="forms-section">
        <h1 class="section-title">Login & Signup</h1>
        <?= $alert; ?> <!-- Menampilkan pesan alert -->
        <div class="forms">
            <!-- Login Form -->
            <div class="form-wrapper is-active" id="login-form">
                <button type="button" class="switcher switcher-login">
                    Login
                    <span class="underline"></span>
                </button>
                <form class="form form-login" method="post">
                    <fieldset>
                        <legend>Please enter your email and password.</legend>
                        <div class="input-block">
                            <label for="login-email">E-mail</label>
                            <input id="login-email" type="email" name="email" required>
                        </div>
                        <div class="input-block">
                            <label for="login-password">Password</label>
                            <input id="login-password" type="password" name="password" required>
                        </div>
                    </fieldset>
                    <button type="submit" class="btn-login" name="btn-login">Login</button>
                </form>
            </div>

            
            <!-- Sign Up Form -->
            <div class="form-wrapper" id="signup-form">
                <button type="button" class="switcher switcher-signup">
                    Sign Up
                    <span class="underline"></span>
                </button>
                <form class="form form-signup" method="post">
                    <fieldset>
                        <legend>Please enter your email and password for sign up.</legend>
                        <div class="input-block">
                            <label for="signup-email">E-mail</label>
                            <input id="signup-email" type="email" name="email" required>
                        </div>
                        <div class="input-block">
                            <label for="signup-password">Password</label>
                            <input id="signup-password" type="password" name="password" required>
                        </div>
                    </fieldset>
                    <button type="submit" class="btn-signup" name="btn-signup">Sign Up</button>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const loginForm = document.getElementById("login-form");
            const signupForm = document.getElementById("signup-form");
            const switchToLogin = document.querySelector(".switcher-login");
            const switchToSignup = document.querySelector(".switcher-signup");

            switchToSignup.addEventListener("click", function() {
                loginForm.classList.remove("is-active");
                signupForm.classList.add("is-active");
            });

            switchToLogin.addEventListener("click", function() {
                signupForm.classList.remove("is-active");
                loginForm.classList.add("is-active");
            });
        });
    </script>
</body>
</html>
