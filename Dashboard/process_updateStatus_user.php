<?php
require __DIR__ . "/../app/auth/auth.php";
require __DIR__ . "/../vendor/autoload.php";

use App\Model\User;
use App\Model\Activitycms;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_verify();
    if (isset($_POST['action']) && $_POST['action'] === 'updateStatus') {
        $id        = (int) ($_POST['id'] ?? 0);
        $newStatus = in_array($_POST['newStatus'] ?? '', ['member', 'admin']) ? $_POST['newStatus'] : '';

        if (!$id || !$newStatus) {
            http_response_code(400);
            exit('invalid input');
        }

        $userModel = new User();
        $result = $userModel->updateUserStatus($id, $newStatus);

        if ($result) {
            echo "success"; // สำเร็จ
        } else {
            echo "error"; // ไม่สำเร็จ
        }
    }
}
?>
