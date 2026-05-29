<?php
session_start();
require_once __DIR__ . '/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>login</title>
    <link rel="stylesheet" href="/ysdn/theme/css/minimal.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
</head>
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background-image: url('./img/coverLogin-01.webp');
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }

    .card-header {
        color: #f58220;
    }

    .btn-orange {
        background-color: #f58220;
        /* สีพื้นหลังของปุ่ม */
        color: #fff;
        /* สีข้อความของปุ่ม */
    }

    .btn-orange:hover {
        background-color: #A2C579;
        /* สีพื้นหลังของปุ่มเมื่อ hover */
        color: #fff;
        /* สีข้อความของปุ่มเมื่อ hover */
    }

    .card-login {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .box-with-shadow {
        box-shadow: 0 4px 4px 4px rgba(0, 0, 0, 0.1);

        /* เงาของกล่อง */
        padding: 20px;
        border-radius: 10px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    /* CSS สำหรับลิงก์ Register และ Forgot Password */
    .btn-register {
        color: #f58220;
        /* สีข้อความเริ่มต้น (สีส้ม) */
    }

    .btn-register:hover {
        color: #7C81AD;
        /* สีข้อความเมื่อโฮเวอร์ (สีดำ) */
    }

    .btn-forgot-password {
        color: #f58220;
        /* สีข้อความเริ่มต้น (สีส้ม) */
    }

    .btn-forgot-password:hover {
        color: #000;
        /* สีข้อความเมื่อโฮเวอร์ (สีดำ) */
    }

    a {
        color: #f58220;
        /* สีของลิงก์ */
        transition: color 0.3s;
        /* ให้เปลี่ยนสีด้วย animation 0.3 วินาที */
    }

    .btn-link-orange {
        color: #f58220;
        /* สีข้อความของลิงค์ */
    }

    .btn-link-orange:hover {
        color: #A2C579;
        /* สีข้อความของลิงค์เมื่อ hover */
    }
</style>


<body>
    <div class="card-login">
        <div class="box-with-shadow">
            <div class="card-header text-center">
                <h4>Login</h4>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <?php
                    // เช็คหากมีข้อความ "msg" ที่ส่งมาจากหน้า checkLogin.php
                    if (isset($_GET['msg'])) {
                        $message = $_GET['msg'];
                        if ($message === 'not_registered') {
                            echo '<p class="text-danger small">ไม่พบบัญชี โปรดลงทะเบียนก่อน</p>';
                        } elseif ($message === 'incorrect_password') {
                            echo '<p class="text-danger small">รหัสผ่านไม่ถูกต้อง</p>';
                        } elseif ($message === 'missing_fields') {
                            echo '<p class="text-danger small">กรุณากรอกข้อมูลให้ครบ</p>';
                        } elseif ($message === 'rate_limited') {
                            $wait = intval($_GET['wait'] ?? 15);
                            echo "<p class=\"text-danger small\">พยายาม login หลายครั้งเกินไป กรุณารอ {$wait} นาทีแล้วลองใหม่</p>";
                        }
                    }
                    ?>
                </div>
                <form action="checkLogin.php" class="mb-3" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="email_or_name">Email / Name</label>
                        <input type="text" name="email_or_name" id="email_or_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="remember_me" id="remember_me">
                        <label class="form-check-label" for="remember_me">Remember Me</label>
                    </div>
                    <button type="submit" class="btn btn-orange btn-block mt-3">Login</button>
                </form>
                <a href="registerUser.php" class="btn btn-link btn-block btn-link-orange">Register</a>
                <a href="forgotPassword.php" class="btn btn-link btn-block btn-link-orange">Forgot Password?</a>
            </div>
        </div>
    </div>
</body>

</html>