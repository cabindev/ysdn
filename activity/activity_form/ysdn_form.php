<?php
session_start();
require __DIR__ . "../../vendor/autoload.php";
if (!isset($_SESSION['login'])) {
    header("Location: ../../app/auth/login.php");
    exit;
}
use App\Model\User;
use App\Model\Activitycms;

$activity_obj = new Activitycms();

$user_obj = new User();

$activity_id = isset($_GET['activity_id']) ? $_GET['activity_id'] : null;


$activity = $activity_obj->getActivityById($activity_id);

$userData = $user_obj->getUserById($_SESSION['id']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครเข้าร่วมกิจกรรมใหม่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style_activity.css">
</head>
<style>
       
        .card {
          
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-success {
            background-color: #f58220;
            border: none;
        }
        .btn-success:hover {
            background-color: #A2C579;
        }
    </style>

<body>

    <a href="../activity.php" class="text-success mt-3">
        <i class="fas fa-arrow-left"></i> ย้อนกลับ
    </a>
    <div class="container">
        <div class="card-name text-center">
        <?php if (isset($_SESSION['id'])) : ?>
            <div>
                Activity for you, <?php echo $_SESSION['name']; ?>
            </div>
        <?php endif; ?>
        </div>
        <div class="container">
            <img src="../images/<?php echo $activity['coverimage']; ?>" class="card-img-top" alt="Cover Image">
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-body">
                        <h2 class="card-title text-center"><?php echo $activity['name']; ?></h2>
                        <p class="card-text"><?php echo $activity['description']; ?></p>
                        <!-- ประกาศปิดการสมัครถ้าเป็น 2 ปรับที่ dadatase -->
                        <?php
                        if ($activity['is_registration_open'] == 2) {
                            echo '<p style="color: red;">กิจกรรมนี้ปิดรับสมัครแล้ว</p>';
                        } else {
                        ?>
                            <form method="POST" action="process_activity_registration.php" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                                <div class="form-group">
                                    <label for="activity_list">กิจกรรมที่เลือก:</label>
                                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['id']; ?>">
                                    <select name="activity_list" id="activity_list" class="form-control">
                                        <?php
                                        $activity_id = isset($_GET['activity_id']) ? $_GET['activity_id'] : null;
                                        $activities = $activity_obj->getAllActivities();
                                        if ($activities) {
                                            foreach ($activities as $activity) {
                                                $isSelected = ($activity_id == $activity['id']) ? 'selected' : '';
                                                echo '<option value="' . $activity['id'] . '" ' . $isSelected . '>' . $activity['name'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="food_preference">แพ้อาหาร:</label>
                                    <input type="text" name="food_preference" id="food_preference" placeholder="ไม่แพ้ - อาหารที่แพ้คือ?"class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="food_type">ประเภทอาหาร:</label>
                                    <input type="text" name="food_type" id="food_type" placeholder="อิสลาม,มังสวิรัติ,ทั่วไป"class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="medication_type">ประเภทยา: (กรณีฉุกเฉิน)</label>
                                    <input type="text" name="medication_type" id="medication_type" placeholder="ไม่มี - ระบุยาหรือรายละเอียดในการรักษา"class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="medical_condition">โรคประจำตัว:</label>
                                    <input type="text" name="medical_condition" id="medical_condition"placeholder="ไม่มี - มี โปรดระบุ"class="form-control"requierd>
                                </div>
                                <div class="form-group">
                                    <label for="guardian_fullname">ชื่อ-สกุลผู้ปกครอง:</label>
                                    <input type="text" name="guardian_fullname" id="guardian_fullname" placeholder="ชื่อ- นามสกุล"class="form-control"requierd>
                                </div>
                                <div class="form-group">
                                    <label for="guardian_relationship">ความสัมพันธ์:</label>
                                    <input type="text" name="guardian_relationship" id="guardian_relationship" placeholder="เกี่ยวข้องเป็น"class="form-control"requierd>
                                </div>
                                <div class="form-group">
                                    <label for="guardian_phone">หมายเลขโทรศัพท์ผู้ปกครอง: ( format: xxxxxxxxxx )</label>
                                    <input type="number" name="guardian_phone" id="guardian_phone" placeholder="xxxxxxxxxx"class="form-control"required>
                                </div>
                        
                                <div class="form-group text-center mt-3">
                                <button type="submit" id="act-register-btn" class="btn btn-success">
                                    <i class="fas fa-sign-in-alt"></i> สมัครกิจกรรม
                                </button>
                            </div>
                            </form>
                        <?php
                        }
                        ?>
                   
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
