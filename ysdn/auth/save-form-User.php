<?php
require $_SERVER['DOCUMENT_ROOT'] . "/auth/auth.php";
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use App\Model\User;
use App\Helper\ImageHelper;
use App\Helper\Logger;

csrf_verify();

$userObj = new User();
$avatar = null;

if (!empty($_FILES['upload']['tmp_name'])) {
    try {
        $mime   = ImageHelper::validateUpload($_FILES['upload']);
        $avatar = "/auth/avatars/" . ImageHelper::uniqueFilename($mime);
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

if ($_REQUEST['action'] == 'delete') {
    $userData = $userObj->getUserById($_REQUEST['id']);
    if ($userData['avatar']) {
        unlink($_SERVER['DOCUMENT_ROOT'] . $userData['avatar']);
    }
    $userObj->deleteUser($_REQUEST['id']);
} elseif ($_REQUEST['action'] == 'edit') {
    $userData = $userObj->getUserById($_REQUEST['id']);
    $userData['firstname']     = $_REQUEST['firstname'];
    $userData['lastname']      = $_REQUEST['lastname'];
    $userData['nickname']      = $_REQUEST['nickname'];
    $userData['citizen_id']    = $_REQUEST['citizen_id'];
    $userData['dob']           = $_REQUEST['dob'];
    $userData['gender_id']     = $_REQUEST['gender_id'];
    $userData['type']          = $_REQUEST['type'];
    $userData['address']       = $_REQUEST['address'];
    $userData['district']      = $_REQUEST['district'];
    $userData['amphoe']        = $_REQUEST['amphoe'];
    $userData['province']      = $_REQUEST['province'];
    $userData['zipcode']       = $_REQUEST['zipcode'];
    $userData['province_code'] = $_REQUEST['province_code'];
    $userData['member_code']   = $_REQUEST['member_code'];
    $userData['phone']         = $_REQUEST['phone'];
    $userData['level']         = $_REQUEST['level'];
    $userData['religion']      = $_REQUEST['religion'];
    $userData['blood_type']    = $_REQUEST['blood_type'];

    if ($avatar) {
        if ($userData['avatar']) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $userData['avatar']);
        }
        $userData['avatar'] = $avatar;
    }

    $userObj->updateUser($userData);
} elseif ($_REQUEST['action'] == 'add') {
    $userData = $_REQUEST;
    unset($userData['action'], $userData['id']);
    $userData['avatar'] = $avatar;
    $userObj->createUser($userData);
}

header("location: ../auth/editProfile.php");
