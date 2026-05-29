<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn/app/auth/csrf.php";
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn/vendor/autoload.php";

csrf_verify();

use App\Model\User;
use App\Helper\ImageHelper;
use App\Helper\Logger;
use App\Helper\Input;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Invalid request method.');
}

// Disposable/spam email domain blocklist
$blockedDomains = ['mailbox.in.ua', 'mailnull.com', 'guerrillamail.com', 'tempmail.com',
                   'throwam.com', 'yopmail.com', 'sharklasers.com', 'trashmail.com',
                   'maildrop.cc', 'fakeinbox.com', '10minutemail.com', 'dispostable.com'];

$name             = Input::text($_POST['name']             ?? '');
$email            = Input::email($_POST['email']           ?? '');
$password         = $_POST['password']         ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$firstname        = $_POST['firstname']        ?? '';
$lastname         = $_POST['lastname']         ?? '';
$nickname         = $_POST['nickname']         ?? '';
$password         = $_POST['password']         ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$firstname        = Input::text($_POST['firstname']     ?? '');
$lastname         = Input::text($_POST['lastname']      ?? '');
$nickname         = Input::text($_POST['nickname']      ?? '');
$dob              = $_POST['dob']              ?? '';
$gender_id        = (int) ($_POST['gender_id']          ?? 0);
$address          = Input::text($_POST['address']       ?? '');
$district         = Input::text($_POST['district']      ?? '');
$amphoe           = Input::text($_POST['amphoe']        ?? '');
$province         = Input::text($_POST['province']      ?? '');
$province_code    = Input::digits($_POST['province_code'] ?? '');
$type             = Input::text($_POST['type']          ?? '');
$zipcode          = Input::digits($_POST['zipcode']     ?? '');
$phone            = Input::phone($_POST['phone']        ?? '');
$citizenId        = Input::digits($_POST['citizen_id']  ?? '');
$level            = Input::text($_POST['level']         ?? '');
$religion         = Input::text($_POST['religion']      ?? '');
$blood_type       = Input::text($_POST['blood_type']    ?? '');

// Block disposable email domains
if ($email) {
    $emailDomain = strtolower(substr(strrchr($email, '@'), 1));
    if (in_array($emailDomain, $blockedDomains)) {
        exit('ไม่อนุญาตให้ใช้ email ชั่วคราว กรุณาใช้ email จริง');
    }
}

if (empty($name) || empty($email) || empty($password) || empty($confirm_password)
    || empty($firstname) || empty($lastname) || empty($nickname) || empty($dob)
    || empty($citizenId) || empty($gender_id) || empty($address) || empty($district)
    || empty($amphoe) || empty($province) || empty($zipcode) || empty($province_code)
    || empty($type) || empty($phone) || empty($level) || empty($religion) || empty($blood_type)) {
    exit('กรุณากรอกข้อมูลให้ครบถ้วน');
}

if ($password !== $confirm_password) {
    exit('รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน');
}

$user = new User();

if ($user->checkUserByEmail($email)) {
    exit('มีบัญชีผู้ใช้ด้วย email นี้อยู่แล้ว');
}

if ($user->checkUserByCitizenId($citizenId)) {
    exit('มีบัญชีผู้ใช้ด้วยเลขบัตรประชาชนนี้อยู่แล้ว');
}

// validate + process image
if (empty($_FILES['upload']['tmp_name'])) {
    exit('กรุณาอัปโหลดรูปภาพของคุณ');
}

try {
    $mime       = ImageHelper::validateUpload($_FILES['upload']);
    $avatarPath = "/ysdn/app/auth/avatars/" . ImageHelper::uniqueFilename($mime);
    $dest       = $_SERVER['DOCUMENT_ROOT'] . $avatarPath;

    ImageHelper::resize($_FILES['upload']['tmp_name'], $dest, $mime, 800, 800);

    $quality = 75;
    ImageHelper::compress($dest, $dest, $mime, $quality);
    while (filesize($dest) > 500 * 1024 && $quality > 10) {
        $quality -= 10;
        ImageHelper::compress($dest, $dest, $mime, $quality);
    }
} catch (\RuntimeException $e) {
    Logger::error('Register avatar upload failed', ['error' => $e->getMessage()]);
    exit($e->getMessage());
}

$userData = [
    'name'          => $name,
    'email'         => $email,
    'password'      => $password,
    'firstname'     => $firstname,
    'lastname'      => $lastname,
    'nickname'      => $nickname,
    'dob'           => $dob,
    'gender_id'     => $gender_id,
    'address'       => $address,
    'district'      => $district,
    'amphoe'        => $amphoe,
    'province'      => $province,
    'province_code' => $province_code,
    'type'          => $type,
    'zipcode'       => $zipcode,
    'phone'         => $phone,
    'citizen_id'    => $citizenId,
    'level'         => $level,
    'religion'      => $religion,
    'blood_type'    => $blood_type,
    'avatar'        => $avatarPath,
];

try {
    $user->createUser($userData);
    header("location: /ysdn/app/auth/profile.php");
    exit;
} catch (\Exception $e) {
    Logger::error('User registration failed', ['email' => $email, 'error' => $e->getMessage()]);
    exit('เกิดข้อผิดพลาดในการสมัครสมาชิก');
}
