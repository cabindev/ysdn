<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn/auth/auth.php";
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use App\Model\User;
use App\Model\Activitycms;

$user_obj = new User();
$activityModel = new Activitycms();
$activityId = $_GET['activityId'] ?? null;
// Get the registered users for the specified activity
$registeredUsers = $activityModel->getRegisteredUsersForActivity($activityId);
?>

<!DOCTYPE html>
<html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registered Users for Activity</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>

<body>
<form method="GET" action="">
    <label for="activityId">เลือกกิจกรรม:</label>
    <select name="activityId" id="activityId">
        <option value="">แสดงทุกกิจกรรม</option>
        <?php
        // ดึงรายชื่อกิจกรรมทั้งหมดจากฐานข้อมูล
        $allActivities = $activityModel->getAllActivities();
        foreach ($allActivities as $activity) :
        ?>
            <option value="<?php echo $activity['id']; ?>" <?php echo ($activityId == $activity['id']) ? 'selected' : ''; ?>><?php echo $activity['name']; ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">แสดง</button>
</form>

    <h1>รวมตาราง ลงทะเบียนกิจกรรม</h1>
    <div id="updateStatusAlert" class="alert alert-success" style="display: none;"></div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>Food Preference</th>
                    <th>Medication</th>
                    <th>Medication Type</th>
                    <th>Medical Condition</th>
                    <th>Guardian Fullname</th>
                    <th>Guardian Relationship</th>
                    <th>Guardian Phone</th>
                    <!-- <th>Created At</th>
                <th>Updated At</th> -->
                    <th>Status</th>
                    <!-- <th>Action</th> -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registeredUsers as $registeredUser) : ?>
                    <tr>
                        <td><?php echo $registeredUser['id']; ?></td>
                        <td><?php echo $registeredUser['name']; ?></td>
                        <td><?php echo $registeredUser['email']; ?></td>
                        <td><?php echo $registeredUser['firstname']; ?></td>
                        <td><?php echo $registeredUser['lastname']; ?></td>
                        <td><?php echo $registeredUser['food_preference']; ?></td>
                        <td><?php echo $registeredUser['food_type']; ?></td>
                        <td><?php echo $registeredUser['medication_type']; ?></td>
                        <td><?php echo $registeredUser['medical_condition']; ?></td>
                        <td><?php echo $registeredUser['guardian_fullname']; ?></td>
                        <td><?php echo $registeredUser['guardian_relationship']; ?></td>
                        <td><?php echo $registeredUser['guardian_phone']; ?></td>
                   
                        <td>
                            <select name="act_status[]" class="form-select act-status-select" data-activity-id="<?= $registeredUser['id'] ?>">
                                <option value="อนุมัติ" <?php echo ($registeredUser['status'] == 'อนุมัติ') ? 'selected' : ''; ?>>อนุมัติ</option>
                                <option value="ไม่อนุมัติ" <?php echo ($registeredUser['status'] == 'ไม่อนุมัติ') ? 'selected' : ''; ?>>ไม่อนุมัติ</option>
                            </select>
                        </td>
                        <td>
                            <!-- <td>
                        <a href="edit_registration.php?id=<?= $registeredUser['id'] ?>">แก้ไข</a>
                        <a href="delete_registration.php?id=<?= $registeredUser['id'] ?>">ลบ</a>
                    </td> -->
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
 $(".act-status-select").change(function() {
    const activityId = $(this).data("activity-id");
    const newStatus = $(this).val();
    const statusTd = $(this).closest("tr").find(".act-status");
    const updateStatusAlert = $("#updateStatusAlert");

    $.ajax({
        url: "process_activityTable_registration.php",
        type: "POST",
        data: {
            action: "updateStatus",
            id: activityId,
            newStatus: newStatus
        },
        success: function(responseData) {
            if (responseData === "success") {
                // กระทำเมื่ออัปเดตสถานะสำเร็จ
                console.log("Response Data: " + responseData);

                // แสดงข้อความแจ้งเตือนใน alert
                updateStatusAlert.text("สถานะถูกอัปเดตเรียบร้อย");
                updateStatusAlert.show(); // แสดง alert

                // แสดงสถานะใน <td> และเปลี่ยนสีเขียว
                statusTd.text(newStatus);
                statusTd.addClass("btn btn-success");
                // ซ่อนข้อความแจ้งเตือนหลังจากแสดงเป็นเวลาสั้น ๆ
                setTimeout(function() {
                    updateStatusAlert.hide();
                }, 1000); // หลังจาก 1 วินาที (1000 มิลลิวินาที)
            } else {
                // กระทำเมื่อมีข้อผิดพลาดในการอัปเดต
                console.error("เกิดข้อผิดพลาดในการอัปเดต: " + responseData);
                // แสดงข้อความแจ้งเตือนใน alert หรือทำอะไรตามที่คุณต้องการ
            }
        },
        error: function(xhr, status, error) {
            // กระทำเมื่อเกิดข้อผิดพลาดในการส่งข้อมูล Ajax
            console.error("เกิดข้อผิดพลาดในการส่งข้อมูล Ajax: " + error);
            // แสดงข้อความแจ้งเตือนใน alert หรือทำอะไรตามที่คุณต้องการ
        }
    });
});
</script>


</html>