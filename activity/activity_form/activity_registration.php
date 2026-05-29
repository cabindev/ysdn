<?php
session_start();
require __DIR__ . "/../../vendor/autoload.php";

use App\Model\Activitycms;

$activity_obj = new Activitycms();
$loggedIn = false; // เริ่มต้นที่ยังไม่ได้เข้าสู่ระบบ


if(isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
    // ทำอะไรต่อ...
} else {
    echo "กรุณาล็อกอินก่อนทำการสมัครกิจกรรม";
    // หรือ redirect ไปยังหน้าล็อกอิน
    header("Location: /ysdn/app/auth/login.php");
    exit;
}
// รับค่า activity_id จาก URL parameter
$activity_id = $_GET['activity_id'];

// ดึงข้อมูลกิจกรรมตาม activity_id
$activity = $activity_obj->getActivityById($activity_id);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ข้ามส่วน head เดิมไว้เหมือนเดิม -->
</head>

<body>
    
    <div class="container">
    <?php if (isset($_SESSION['id'])) : ?>
            <div>
                ยินดีต้อนรับ, <?php echo $_SESSION['name']; ?>
            </div>
        <?php endif; ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-5">
                    <img src="../images/<?php echo $activity['coverimage']; ?>" class="card-img-top" alt="Cover Image">
                    <div class="card-body">
                        <h2 class="card-title"><?php echo $activity['name']; ?></h2>
                        <p class="card-text"><?php echo $activity['description']; ?></p>
                        <?php if ($loggedIn) { ?>
                            <!-- แสดงฟอร์มสมัครกิจกรรม -->
                            <form method="POST" action="process_activity_registration.php" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                                <!-- ส่วนฟอร์มเหมือนเดิม -->
                            </form>
                        <?php } else { ?>
                            <!-- แสดงข้อความเข้าสู่ระบบหรือสมัครสมาชิก -->
                            <p>กรุณาเข้าสู่ระบบหรือสมัครสมาชิกก่อนทำการสมัครกิจกรรม</p>
                            <!-- ตรวจสอบหากมีการส่งค่า user_id ใน URL ให้แสดงลิงก์ไปยังหน้าเข้าสู่ระบบ -->
                            <?php if (isset($_GET['user_id'])) { ?>
                                <a href="login.php">เข้าสู่ระบบ</a>
                            <?php } else { ?>
                                <!-- หากไม่มีค่า user_id ใน URL ให้แสดงลิงก์ไปยังหน้าสมัครสมาชิก -->
                                <a href="register.php">สมัครสมาชิก</a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ข้ามส่วน JavaScript เดิมไว้เหมือนเดิม -->
</body>

</html>
