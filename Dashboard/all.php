<?php
require __DIR__ . "/../app/auth/auth.php";
require_once __DIR__ . "/../vendor/autoload.php";

use App\Model\User;
use App\Model\Activitycms;
use App\Model\Ref;

$user = new User();
$activityModel = new Activitycms();

// สร้าง function ในคลาส Activitycms ด้วย
$allActivities = $activityModel->getAllActivities();

if (isset($_POST['selected_activity'])) {
    $selectedActivity = $_POST['selected_activity'];
    $registrants = $activityModel->viewRegistrantsForActivity($selectedActivity);
}


?>

<!DOCTYPE html>
<html>

<head>
    <title>รายละเอียดผู้สมัครกิจกรรม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

    <form method="post">
        <select name="selected_activity" onchange="this.form.submit()">
            <option>เลือกกิจกรรม</option>
            <?php
            foreach ($allActivities as $activity) {
                echo "<option value='" . $activity['id'] . "'>" . $activity['name'] . "</option>";
            }
            ?>
        </select>
    </form>
    <?php
    if (isset($registrants)) {
        echo "<h2 class='mt-4'>Registrants for selected activity</h2>";
        echo "<table class='table table-striped'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th scope='col'>รหัสสมาชิก</th>";
        echo "<th scope='col'>ชื่อ</th>";
        echo "<th scope='col'>นามสกุล</th>";
        echo "<th scope='col'>ชื่อเล่น</th>";
        echo "<th scope='col'>อีเมล</th>";
        echo "<th scope='col'>ประเภท</th>";
        echo "<th scope='col'>ชื่อกิจกรรม</th>";
        echo "<th scope='col'>แพ้อาหาร</th>";
        echo "<th scope='col'>แพ้ยา</th>";
        echo "<th scope='col'>ยาที่ใช้รักษา</th>";
        echo "<th scope='col'>โรคประจำตัว</th>";
        echo "<th scope='col'>เบอร์โทรผู้ปกครอง</th>";
        // echo "<th scope='col'>สถานะ</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        foreach ($registrants as $person) {
            echo "<tr>";
            echo "<td>" . (isset($person['member_code']) ? $person['member_code'] : "") . "</td>";
            echo "<td>" . (isset($person['lastname']) ? $person['lastname'] : "") . "</td>";
            echo "<td>" . (isset($person['firstname']) ? $person['firstname'] : "") . "</td>";
            echo "<td>" . (isset($person['nickname']) ? $person['nickname'] : "") . "</td>";
            echo "<td>" . (isset($person['email']) ? $person['email'] : "") . "</td>";
            echo "<td>" . (isset($person['type']) ? $person['type'] : "") . "</td>";
            echo "<td>" . (isset($person['activity_name']) ? $person['activity_name'] : "") . "</td>";
            echo "<td>" . (isset($person['food_preference']) ? $person['food_preference'] : "") . "</td>";
            echo "<td>" . (isset($person['medication']) ? $person['medication'] : "") . "</td>";
            echo "<td>" . (isset($person['medication_type']) ? $person['medication_type'] : "") . "</td>";
            echo "<td>" . (isset($person['medical_condition']) ? $person['medical_condition'] : "") . "</td>";
            echo "<td>" . (isset($person['guardian_phone']) ? $person['guardian_phone'] : "") . "</td>";
            // echo "<td>" . (isset($person['status']) ? $person['status'] : "") . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>

</script>
</body>

</html>