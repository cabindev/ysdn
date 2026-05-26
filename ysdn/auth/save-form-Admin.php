<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/ysdn/auth/auth.php";
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/vendor/autoload.php";

use App\Model\User;
use App\Helper\ImageHelper;
use App\Helper\Logger;

csrf_verify();

$userObj = new User();
$avatar = null;

if (!empty($_FILES['upload']['tmp_name'])) {
    try {
        $mime   = ImageHelper::validateUpload($_FILES['upload']);
        $avatar = "/ysdn_thailand/ysdn/auth/avatars/" . ImageHelper::uniqueFilename($mime);
        $dest   = $_SERVER['DOCUMENT_ROOT'] . $avatar;

        ImageHelper::resize($_FILES['upload']['tmp_name'], $dest, $mime, 800, 800);

        $quality = 75;
        ImageHelper::compress($dest, $dest, $mime, $quality);
        while (filesize($dest) > 500 * 1024 && $quality > 10) {
            $quality -= 10;
            ImageHelper::compress($dest, $dest, $mime, $quality);
        }
    } catch (\RuntimeException $e) {
        Logger::error('Avatar upload failed', ['error' => $e->getMessage()]);
        echo $e->getMessage();
        exit;
    }
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id     = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

if ($action === 'delete') {
    if (!$id) { http_response_code(400); exit; }
    $existing = $userObj->getUserById($id);
    if (!empty($existing['avatar'])) {
        $avatarPath = $_SERVER['DOCUMENT_ROOT'] . $existing['avatar'];
        if (file_exists($avatarPath)) {
            unlink($avatarPath);
        }
    }
    $userObj->deleteUser($id);
    header("location: ../../Dashboard/data.php");
    exit;
} elseif ($action === 'edit') {
    $userData = $userObj->getUserById($id);
    $userData['firstname']     = $_POST['firstname']     ?? $userData['firstname'];
    $userData['lastname']      = $_POST['lastname']      ?? $userData['lastname'];
    $userData['nickname']      = $_POST['nickname']      ?? $userData['nickname'];
    $userData['citizen_id']    = $_POST['citizen_id']    ?? $userData['citizen_id'];
    $userData['dob']           = $_POST['dob']           ?? $userData['dob'];
    $userData['gender_id']     = $_POST['gender_id']     ?? $userData['gender_id'];
    $userData['type']          = $_POST['type']          ?? $userData['type'];
    $userData['address']       = $_POST['address']       ?? $userData['address'];
    $userData['district']      = $_POST['district']      ?? $userData['district'];
    $userData['amphoe']        = $_POST['amphoe']        ?? $userData['amphoe'];
    $userData['province']      = $_POST['province']      ?? $userData['province'];
    $userData['zipcode']       = $_POST['zipcode']       ?? $userData['zipcode'];
    $userData['province_code'] = $_POST['province_code'] ?? $userData['province_code'];
    $userData['member_code']   = $_POST['member_code']   ?? $userData['member_code'];
    $userData['phone']         = $_POST['phone']         ?? $userData['phone'];
    $userData['level']         = $_POST['level']         ?? $userData['level'];
    $userData['religion']      = $_POST['religion']      ?? $userData['religion'];
    $userData['blood_type']    = $_POST['blood_type']    ?? $userData['blood_type'];

    if ($avatar) {
        if (!empty($userData['avatar'])) {
            $old = $_SERVER['DOCUMENT_ROOT'] . $userData['avatar'];
            if (file_exists($old)) unlink($old);
        }
        $userData['avatar'] = $avatar;
    }

    $userObj->updateUser($userData);
    header("location: ../auth/editProfile.php");
    exit;
} elseif ($action === 'add') {
    $data = $_POST;
    unset($data['action'], $data['id'], $data['csrf_token']);
    $data['avatar'] = $avatar;
    $userObj->createUser($data);
    header("location: ../auth/editProfile.php");
    exit;
}
