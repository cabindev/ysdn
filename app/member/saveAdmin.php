<?php
require __DIR__ . "../../app/auth/auth.php";
require __DIR__ . "../../vendor/autoload.php";

use App\Model\Person;
use App\Helper\ImageHelper;
use App\Helper\Logger;

csrf_verify();

$personObj = new Person();
$avatar = null;

if (!empty($_FILES['upload']['tmp_name'])) {
    try {
        $mime   = ImageHelper::validateUpload($_FILES['upload']);
        $avatar = "/ysdn/app/member/avatars/" . ImageHelper::uniqueFilename($mime);
        ImageHelper::resize($_FILES['upload']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $avatar, $mime, 800, 800);
    } catch (\RuntimeException $e) {
        Logger::error('Admin avatar upload failed', ['error' => $e->getMessage()]);
        exit($e->getMessage());
    }
}

if ($_REQUEST['action'] == 'delete') {
    $person = $personObj->getPersonById($_REQUEST['id']);
    if ($person['avatar']) {
        unlink($_SERVER['DOCUMENT_ROOT'] . $person['avatar']);
    }
    $personObj->deletePerson($_REQUEST['id']);

} elseif ($_REQUEST['action'] == 'edit') {
    $person = $_REQUEST;
    unset($person['action']);

    if ($avatar) {
        if ($person['avatar']) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $person['avatar']);
        }
        $person['avatar'] = $avatar;
    }

    $personObj->updatePerson($person);

} elseif ($_REQUEST['action'] == 'add') {
    $person = $_REQUEST;
    unset($person['action'], $person['id']);
    $person['avatar'] = $avatar;
    $personObj->addPerson($person);
}

header("location: index.php");
