<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . "/auth/csrf.php";
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use App\Model\Activitycms;
use App\Helper\Input;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit;
}

csrf_verify();

$data = Input::sanitizePost([
    'user_id'               => 'int',
    'activity_list'         => 'int',
    'food_preference'       => 'text',
    'food_type'             => 'text',
    'medication_type'       => 'text',
    'medical_condition'     => 'text',
    'guardian_fullname'     => 'text',
    'guardian_relationship' => 'text',
    'guardian_phone'        => 'phone',
]);

if (empty($data['user_id'])) {
    header("Location: /ysdn/auth/login.php");
    exit;
}

$activity_obj = new Activitycms();
$result = $activity_obj->registerUserForActivity(
    $data['user_id'],
    $data['activity_list'],
    [
        'food_preference'       => $data['food_preference'],
        'food_type'             => $data['food_type'],
        'medication_type'       => $data['medication_type'],
        'medical_condition'     => $data['medical_condition'],
        'guardian_fullname'     => $data['guardian_fullname'],
        'guardian_relationship' => $data['guardian_relationship'],
        'guardian_phone'        => $data['guardian_phone'],
    ]
);

if ($result) {
    echo "สมัครกิจกรรมสำเร็จ!";
} else {
    echo "คุณเคยสมัครกิจกรรมนี้แล้ว";
}
