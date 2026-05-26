<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/ysdn/auth/auth.php";
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/vendor/autoload.php";

use App\Model\User;
use App\Model\Ref;

if (isset($_REQUEST['action']) == 'edit') {
    $userObj = new User;
    $userData = $userObj->getUserById($_REQUEST['id']);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/ysdn/inc/components/nav.php"; ?>
</head>

<body class="container">
    <div class="row mt-5">
        <div class="col">
            <div class="card m-3">
                <div id="card-header" class="card-header d-flex justify-content-between m-3">
                    <h4>แบบฟอร์ม<?php echo (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') ? "แก้ไขข้อมูลสมาชิก" : "เพิ่มสมาชิกใหม่"; ?></h4>
                    <a class="navbar-brand mx-3" href="#" style="color: white;"> <!-- เพิ่ม style="color: white;" -->
                        <?php if (isset($_SESSION['name'])) {
                            echo $_SESSION['name'] . ' ' . $_SESSION['role'];
                        } ?>
                    </a>
                    <a href="../auth/editProfile.php" class="btn btn-light">ย้อนกลับ
                    </a>
                </div>
                <div class="container">
                    <form class="row g-3" action="save-form-User.php" method="POST" id="myForm" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                        <?php if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') : ?>
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo isset($userData['id']) ? $userData['id'] : ''; ?>">
                        <?php else : ?>
                            <input type="hidden" name="action" value="add">
                        <?php endif; ?>
                        <div class="col-md-12">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <?php if (isset($userData['avatar']) && !empty($userData['avatar'])) : ?>
                                        <img id="avatar-preview" src="<?php echo $userData['avatar']; ?>" alt="Avatar Preview" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                                    <?php else : ?>
                                        <div style="width: 100px; height: 100px; border-radius: 50%; background-color: #f1f1f1;">
                                            <i class="fas fa-user-circle" style="font-size: 100px; color: #bbb;"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <label class="icon" style="cursor: pointer;">
                                        <i class="fa-solid fa-cloud-arrow-up"></i> <!-- Icon from Font Awesome -->
                                        <input type="file" name="upload" id="upload" class="form-control d-none" accept="image/*" onchange="validateFileSize(this)">
                                    </label>
                                    <input type="hidden" name="avatar" id="avatar" class="form-control" required value="<?php echo isset($userData['avatar']) ? $userData['avatar'] : ''; ?>">
                                    <div id="file-size-error" style="color: red; display: none;">ขนาดของไฟล์ใหญ่เกิน 5 MB กรุณาแก้ไขขนาดไฟล์ก่อนอัปโหลดอีกครั้ง.</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="name" class="form-label">ชื่อ Account</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="name" required value="<?php echo isset($userData['name']) ? $userData['name'] : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" class="form-control" id="email" placeholder="Email" required value="<?php echo isset($userData['email']) ? $userData['email'] : ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <!-- <label for="password" class="form-label">Password</label> -->
                            <input type="hidden" name="password" class="form-control" id="password" placeholder="password" required value="<?php echo isset($userData['password']) ? $userData['password'] : ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <!-- <label for="confirm_password" class="form-label">Confirm-password</label> -->
                            <input type="hidden" name="confirm_password" class="form-control" id="confirm_password" placeholder="confirm_password" value="<?php echo isset($userData['confirm_password']) ? $userData['confirm_password'] : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="firstname" class="form-label">ชื่อจริง</label>
                            <input type="text" name="firstname" class="form-control" id="firstname" placeholder="Firstname" required value="<?php echo isset($userData['firstname']) ? $userData['firstname'] : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="lastname" class="form-label">นามสกุล</label>
                            <input type="text" name="lastname" class="form-control" id="lastname" placeholder="Lastname" required value="<?php echo isset($userData['lastname']) ? $userData['lastname'] : ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="nickname" class="form-label">ชื่อเล่น</label>
                            <input type="text" name="nickname" class="form-control" id="nickname" placeholder="Nickname" required value="<?php echo isset($userData['nickname']) ? $userData['nickname'] : ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="citizen_id" class="form-label">เลขบัตรประชาชน</label>
                            <input type="text" id="citizen_id" name="citizen_id" class="form-control" maxlength="17" onkeyup="autoTab(this)" placeholder="_-____-_____-_-__" required <?php echo isset($userData) ? 'readonly' : ''; ?>>
                        </div>
                        <div class="col-md-4">
                            <label for="dob" class="form-label">วันเกิด</label>
                            <input type="date" name="dob" class="form-control" id="dob" placeholder="Nickname" required value="<?php echo isset($userData['dob']) ? $userData['dob'] : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="gender_id">เพศ (Gender)</label>
                            <select class="form-control" id="gender_id" name="gender_id">
                                <option value="LGBT" <?php echo ($userData['gender_id'] === 'LGBT') ? 'selected' : ''; ?>>LGBT</option>
                                <option value="ชาย" <?php echo ($userData['gender_id'] === 'ชาย') ? 'selected' : ''; ?>>ชาย</option>
                                <option value="หญิง" <?php echo ($userData['gender_id'] === 'หญิง') ? 'selected' : ''; ?>>หญิง</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="type" class="form-label">ภาค</label>
                            <input type="text" name="type" class="form-control" id="type" placeholder="Type" value="<?php echo isset($_SESSION['type']) ? $_SESSION['type'] : (isset($userData['type']) ? $userData['type'] : ''); ?>">
                        </div>
                        <div class="col-6">
                            <label for="address" class="form-label">ที่อยู่สามารถจัดส่งของให้ได้</label>
                            <input type="text" name="address" class="form-control" id="address" placeholder="Address" required value="<?php echo isset($userData['address']) ? $userData['address'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="district" class="form-label">ตำบล/แขวง</label>
                            <input type="text" name="district" class="form-control" id="district" autocomplete="off" placeholder="District" required value="<?php echo isset($userData['district']) ? $userData['district'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="amphoe" class="form-label">อำเภอ/เขต</label>
                            <input type="text" name="amphoe" id="amphoe" class="form-control" autocomplete="off" placeholder="Amphoe" value="<?php echo isset($userData['amphoe']) ? $userData['amphoe'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="province" class="form-label">จังหวัด</label>
                            <input type="text" name="province" id="province" class="form-control" autocomplete="off" placeholder="Province" value="<?php echo isset($userData['province']) ? $userData['province'] : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="zipcode" class="form-label">รหัสไปรษณีย์</label>
                            <input type="text" name="zipcode" class="form-control" id="zipcode" autocomplete="off" placeholder="Zipcode" value="<?php echo isset($userData['zipcode']) ? $userData['zipcode'] : ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="province_code" class="form-label">รหัสจังหวัด</label>
                            <input type="text" name="province_code" class="form-control" id="province_code" autocomplete="off" placeholder="Province_code" value="<?php echo isset($userData['province_code']) ? $userData['province_code'] : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="member_code" class="form-label">รหัสสมาชิก</label>
                            <input type="text" name="member_code" class="form-control" id="member_code" placeholder="Member Code" value="<?php echo isset($userData['member_code']) ? $userData['member_code'] : ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="phone" class="form-label">เบอร์มือถือ</label>
                            <input type="tel" name="phone" id="phone" class="form-control" placeholder="xxxxxxxxxx" required value="<?php echo isset($userData['phone']) ? $userData['phone'] : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="level">Level</label>
                            <select class="form-control" id="level" name="level">
                                <option value="LV1" <?php echo ($userData['level'] === 'LV1') ? 'selected' : ''; ?>>LV1 อาสาสมัคร</option>
                                <option value="LV2" <?php echo ($userData['level'] === 'LV2') ? 'selected' : ''; ?>>LV2 แกนนำเยาวชน</option>
                                <option value="LV3" <?php echo ($userData['level'] === 'LV3') ? 'selected' : ''; ?>>LV3 พี่เลี้ยงจังหวัด</option>
                                <option value="LV4" <?php echo ($userData['level'] === 'LV4') ? 'selected' : ''; ?>>LV4 นักรณรงค์</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="religion">Religion</label>
                            <select class="form-control" id="religion" name="religion">
                                <option value="ศาสนาพุทธ" <?php echo ($userData['religion'] === 'ศาสนาพุทธ') ? 'selected' : ''; ?>>ศาสนาพุทธ</option>
                                <option value="ศาสนาอิสลาม" <?php echo ($userData['religion'] === 'ศาสนาอิสลาม') ? 'selected' : ''; ?>>ศาสนาอิสลาม</option>
                                <option value="ศาสนาคริสต์" <?php echo ($userData['religion'] === 'ศาสนาคริสต์') ? 'selected' : ''; ?>>ศาสนาคริสต์</option>
                                <option value="ศาสนาอื่น ๆ" <?php echo ($userData['religion'] === 'ศาสนาอื่น ๆ') ? 'selected' : ''; ?>>ศาสนาอื่น ๆ</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="blood_type">Blood Type</label>
                            <select class="form-control" id="blood_type" name="blood_type">
                                <option value="A" <?php echo ($userData['blood_type'] === 'A') ? 'selected' : ''; ?>>A</option>
                                <option value="B" <?php echo ($userData['blood_type'] === 'B') ? 'selected' : ''; ?>>B</option>
                                <option value="AB" <?php echo ($userData['blood_type'] === 'AB') ? 'selected' : ''; ?>>AB</option>
                                <option value="O" <?php echo ($userData['blood_type'] === 'O') ? 'selected' : ''; ?>>O</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button id="btn-submit" type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- เพิ่มส่วนของ JavaScript ด้านล่างในส่วน <script> -->
    <script type="text/javascript">
        function autoTab(obj) {
            var pattern = new String("_-____-_____-__-_"); // กำหนดรูปแบบในนี้
            var pattern_ex = new String("-"); // กำหนดสัญลักษณ์หรือเครื่องหมายที่ใช้แบ่งในนี้
            var returnText = new String("");
            var obj_l = obj.value.length;
            var obj_l2 = obj_l - 1;
            for (i = 0; i < pattern.length; i++) {
                if (obj_l2 == i && pattern.charAt(i + 1) == pattern_ex) {
                    returnText += obj.value + pattern_ex;
                    obj.value = returnText;
                }
            }
            if (obj_l >= pattern.length) {
                obj.value = obj.value.substr(0, pattern.length);
            }
        }
        //แสดงรูปก่อนแก้ไข
        document.getElementById("upload").addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function() {
                    document.getElementById("avatar-preview").src = reader.result;
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById("avatar-preview").src = "<?php echo isset($userData['avatar']) ? $userData['avatar'] : ''; ?>";
            }
        });

        // เพิ่มการตรวจสอบขนาดไฟล์และการแจ้งเตือน
        function validateFileSize(input) {
            const file = input.files[0];
            const maxSize = 5 * 1024 * 1024; // 5 MB

            if (file.size > maxSize) {
                document.getElementById('file-size-error').style.display = 'block';
                input.value = ''; // Reset the file input
            } else {
                document.getElementById('file-size-error').style.display = 'none';
            }
        }
    </script>
    <script src="script.js"></script>
</body>

</html>
