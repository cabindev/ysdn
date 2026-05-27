<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . "/auth/csrf.php";
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

csrf_verify();

use App\Model\User;
use App\Helper\Logger;
use App\Helper\RateLimiter;

$emailOrName = trim($_POST['email_or_name'] ?? '');
$password    = $_POST['password'] ?? '';

if (empty($emailOrName) || empty($password)) {
    header("Location: /ysdn/auth/login.php?msg=missing_fields");
    exit;
}

// Rate limit by email/username + IP combined
$identifier = $emailOrName . '|' . ($_SERVER['REMOTE_ADDR'] ?? '');

if (RateLimiter::isBlocked($identifier)) {
    $minutes = RateLimiter::retryAfterMinutes($identifier);
    header("Location: /ysdn/auth/login.php?msg=rate_limited&wait={$minutes}");
    exit;
}

$user_obj = new User();

if (!$user_obj->checkEmailOrNameExists($emailOrName)) {
    RateLimiter::recordFailure($identifier);
    header("Location: /ysdn/auth/login.php?msg=not_registered");
    exit;
}

if ($user_obj->checkUserByEmailOrName($emailOrName, $password)) {
    RateLimiter::clear($identifier);
    Logger::info('User login', ['user' => $emailOrName]);
    header("Location: /ysdn/auth/profile.php");
    exit;
}

RateLimiter::recordFailure($identifier);
Logger::info('Failed login attempt', ['user' => $emailOrName]);
header("Location: /ysdn/auth/login.php?msg=incorrect_password");
exit;
