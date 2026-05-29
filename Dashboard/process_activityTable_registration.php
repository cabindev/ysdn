<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn/app/auth/auth.php";
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn/vendor/autoload.php";

use App\Model\User;
use App\Model\Activitycms;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_verify();
    if (isset($_POST['action']) && $_POST['action'] === 'updateStatus') {
        $id        = (int) ($_POST['id'] ?? 0);
        $newStatus = in_array($_POST['newStatus'] ?? '', ['อนุมัติ', 'ไม่อนุมัติ', 'รอดำเนินการ'])
                     ? $_POST['newStatus'] : '';

        if (!$id || $newStatus === '') {
            http_response_code(400);
            exit('invalid input');
        }

        $activityModel = new Activitycms();
        $result = $activityModel->updateActivityStatusregistration($id, $newStatus);

        if ($result) {
            echo "success"; // สำเร็จ
        } else {
            echo "error"; // ไม่สำเร็จ
        }
    }
}
?>
