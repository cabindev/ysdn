<?php
require __DIR__ . "/../app/auth/auth.php";
require __DIR__ . "/../vendor/autoload.php";

use App\Model\Activitycms;
use App\Helper\ImageHelper;
use App\Helper\Logger;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit('Invalid request method.');
}

csrf_verify();

$activity_name        = $_POST["activity_name"]        ?? '';
$activity_date        = $_POST["activity_date"]         ?? '';
$activity_description = $_POST["activity_description"]  ?? '';
$activity_byname      = $_POST["activity_byname"]       ?? '';
$activity_category    = $_POST["activity_category"]     ?? '';

if (empty($activity_name) || empty($activity_date) || empty($activity_description)
    || empty($activity_byname) || empty($activity_category)) {
    exit('กรุณากรอกข้อมูลให้ครบถ้วน');
}

$targetDirectory = __DIR__ . "/../activity/images/";

try {
    $mime     = ImageHelper::validateUpload($_FILES["activity_cover_image"]);
    $filename = ImageHelper::uniqueFilename($mime);
    ImageHelper::resize($_FILES["activity_cover_image"]["tmp_name"], $targetDirectory . $filename, $mime, 1200, 800);
} catch (\RuntimeException $e) {
    Logger::error('Activity cover upload failed', ['error' => $e->getMessage()]);
    exit($e->getMessage());
}

$activityModel = new Activitycms();
$activityModel->createActivity([
    "name"              => $activity_name,
    "date"              => $activity_date,
    "description"       => $activity_description,
    "byname"            => $activity_byname,
    "coverimage"        => $filename,
    "category_activity" => $activity_category,
]);

header("location: all_activity.php");
