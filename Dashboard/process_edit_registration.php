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

$registrationId = (int) ($_POST['id'] ?? 0);

if (!$registrationId) {
    exit("Registration not found.");
}

$activityModel    = new Activitycms();
$registrationData = $activityModel->getRegisteredUserById($registrationId);

if (!$registrationData) {
    exit("Registration not found.");
}

$updatedData = Input::sanitizePost([
    'firstname' => 'text',
    'lastname'  => 'text',
]);

if ($activityModel->updateRegistration($registrationId, $updatedData)) {
    echo "Registration updated successfully.";
} else {
    echo "Failed to update registration.";
}
