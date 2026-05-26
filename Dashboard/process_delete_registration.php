<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/ysdn/auth/auth.php";
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/vendor/autoload.php";

use App\Model\Activitycms;

$activityModel = new Activitycms();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_verify();
    $registrationId = (int) ($_POST['id'] ?? 0);
    if (!$registrationId) { http_response_code(400); exit('invalid id'); }

    // Attempt to delete the registration
    $deleteResult = $activityModel->deleteRegistration($registrationId);

    if ($deleteResult) {
        echo "Registration deleted successfully.";
    } else {
        echo "Failed to delete registration.";
    }
} else {
    echo "Invalid request.";
}
?>
