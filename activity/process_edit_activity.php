<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn/auth/auth.php";
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use App\Model\Activitycms;
use App\Helper\ImageHelper;
use App\Helper\Logger;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit('Invalid request method.');
}

csrf_verify();

$activityModel = new Activitycms();

if (isset($_POST['action'])) {
    if ($_POST['action'] === 'updateStatus') {
        $id        = $_POST['id'];
        $newStatus = $_POST['newStatus'];
        echo $activityModel->updateActivityStatus($id, $newStatus) ? 'success' : 'error';

    } elseif ($_POST['action'] === 'delete') {
        $result = $activityModel->deleteActivity($_POST['id']);
        echo $result ? 'ลบกิจกรรมสำเร็จ!' : 'ลบกิจกรรมไม่สำเร็จ';
    }

} elseif (isset($_POST['activity_id'])) {
    $activityId           = $_POST['activity_id'];
    $existingActivityData = $activityModel->getActivityById($activityId);

    if (!$existingActivityData) {
        exit('Activity not found.');
    }

    $activityData = [
        "name"              => $_POST["activity_name"]        ?? null,
        "date"              => $_POST["activity_date"]         ?? null,
        "description"       => $_POST["activity_description"]  ?? null,
        "byname"            => $_POST["activity_byname"]       ?? null,
        "category_activity" => $_POST["activity_category"]     ?? null,
        "coverimage"        => $existingActivityData["coverimage"],
    ];

    if (!empty($_FILES["activity_cover_image"]["size"])) {
        try {
            $mime     = ImageHelper::validateUpload($_FILES["activity_cover_image"]);
            $filename = ImageHelper::uniqueFilename($mime);
            $dir      = $_SERVER['DOCUMENT_ROOT'] . "/activity/images/";
            ImageHelper::resize($_FILES["activity_cover_image"]["tmp_name"], $dir . $filename, $mime, 1200, 800);
            $activityData["coverimage"] = $filename;
        } catch (\RuntimeException $e) {
            Logger::error('Activity cover update failed', ['error' => $e->getMessage()]);
            exit($e->getMessage());
        }
    }

    $activityModel->updateActivity($activityId, $activityData);
    echo 'Activity updated successfully!';

} else {
    http_response_code(400);
    exit('Invalid request.');
}
