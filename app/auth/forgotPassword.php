<?php
session_start();
require_once __DIR__ . "../../app/auth/csrf.php";
require __DIR__ . "../../vendor/autoload.php";
use App\Model\User;

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    // Get the email entered by the user
    $email = $_POST['email'];

    // Create a new User instance
    $user = new User();

    // Check if the user exists
    if ($user->checkUserByEmail($email)) {
        // Send the password reset link
        $user->sendPasswordResetLink($email);
        $msg = '<h4 class="text-success">Password reset link has been sent to your email.</h4>';
    } else {
        $msg = 'User not found.';
    }
}
?>

<html>
<head>
    <link rel="stylesheet" href="/ysdn/theme/css/bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <title>Forgot Password</title>
</head>
<body>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
        <div class="box-with-shadow">
            <div class="card-header">
                <div class="card-body">
                    <div class="form-group">
                        <h3 class="text-center">Forgot Password</h1>
                        <?php if ($msg !== ''): ?>
                            <?php echo $msg; ?>
                        <?php else: ?>
                            <form method="post" action="" class="text-center">
                                <?= csrf_field() ?>
                                <label>Email:</label><br>
                                <input type="email" name="email" class="form-control" required><br>
                                <input type="submit" value="Reset Password"class="btn-reset">
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
