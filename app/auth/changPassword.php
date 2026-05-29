<?php
session_start();
require_once __DIR__ . "/../../app/auth/csrf.php";
require __DIR__ . "/../../vendor/autoload.php";

use App\Model\User;
use App\Helper\Logger;

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $token           = $_POST['token']            ?? '';
    $newPassword     = $_POST['new_password']     ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($newPassword !== $confirmPassword) {
        $error = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
    } else {
        $user = new User();
        if ($user->resetPassword($token, $newPassword)) {
            Logger::info('Password reset successful');
            header("Location: login.php");
            exit;
        } else {
            $error = 'Token ไม่ถูกต้องหรือหมดอายุ';
        }
    }
}

$token = $_GET['token'] ?? '';
?>
<html>
<head>
    <link rel="stylesheet" href="/ysdn/theme/css/bootstrap.css">
    <title>Change Password</title>
</head>
<body>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
        <div class="card">
            <div class="card-body">
                <h1>Change Password</h1>
                <?php if ($error): ?>
                    <p class="text-danger"><?= htmlspecialchars($error) ?></p>
                <?php endif ?>
                <form method="POST" action="">
                    <?= csrf_field() ?>
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <div class="form-group mb-3">
                        <label>New Password:</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Confirm Password:</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <input type="submit" class="btn btn-primary" value="Change Password">
                </form>
                <p class="mt-3">Back to <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
