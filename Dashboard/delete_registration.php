<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/vendor/autoload.php";

use App\Model\Activitycms;

$activityModel = new Activitycms();

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
    $registrationId = $_GET['id'];
    $registrationData = $activityModel->getRegisteredUserById($registrationId);

    if ($registrationData) {
        ?>
        <p>Are you sure you want to delete this registration?</p>
        <p>ID: <?= $registrationData['id'] ?></p>
        <form action="process_delete_registration.php" method="POST">
            <input type="hidden" name="id" value="<?= $registrationId ?>">
            <input type="submit" value="Delete">
        </form>
        <?php
    } else {
        echo "Registration not found.";
    }
} else {
    echo "Invalid request.";
}
?>
