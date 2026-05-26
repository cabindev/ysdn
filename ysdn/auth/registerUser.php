<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/ysdn/auth/csrf.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>YSDN Register</title>
    <link rel="stylesheet" href="styleUi.css">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
    <link rel="stylesheet" href="/ysdn_thailand/theme/css/bootstrap.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        function validateFileSize(input) {
            const maxSize = 5 * 1024 * 1024; // 5 MB
            const file = input.files[0];

            if (file.size > maxSize) {
                document.getElementById('image-size-error').innerText = 'ขนาดของไฟล์ใหญ่เกิน 5 MB กรุณาแก้ไขขนาดไฟล์ก่อนอัปโหลดอีกครั้ง.';
                input.value = ''; // Clear the input
            } else {
                document.getElementById('image-size-error').innerText = '';
            }
        }
    </script>
</head>

<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/vendor/autoload.php";

use App\Model\Person;
use App\Model\Ref;

$dob = $dob ?? '';

?>

<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-9 col-md-7 col-lg-6 col-xl-5 text-center p-0 mt-3 mb-2">
                <div class="card px-0 pt-4 pb-0 mt-3 mb-3 mx-2">
                    <h5 id="heading">Sign Up YSDNTHAILAND Account</h5>
                    <p>Fill all form fields to go to the next step</p>

                    <form id="msform" action="saveRegister.php" class="mb-3 mx-3" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <?= csrf_field() ?>
                        <!-- progressbar -->
                        <div class="progressbar">
                            <ul id="progressbar">
                                <li class="active" id="account">Account</li>
                                <li id="personal">Personal</li>
                                <li id="address">Address</li>
                                <li id="image">Image</li>
                                <li id="confirm">Finish</li>
                            </ul>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <br>
                        <!-- fieldsets -->
                        <fieldset>
                            <div class="form-card" id="step-1">
                                <div class="row">
                                    <div class="col-7">
                                        <h5 class="fs-title">Account Information:</h5>
                                    </div>
                                    <div class="col-5">
                                        <p></p><small>Step 1 - 5</small></p>
                                    </div>
                                </div>
                                <label class="form-label" for="name">Name:Account *</label>
                                <input type="text" id="name" name="name" placeholder="Name" />
                                <label class="form-label" for="email">Email: *</label>
                                <input type="email" id="email" name="email" placeholder="Email" />
                                <label class="form-label" for="password">Password: *</label>
                                <input type="password" id="password" name="password" placeholder="Password" />
                                <label class="form-label" for="confirm_password">Confirm Password: *</label>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" />
                            </div>
                            <input type="button" name="next-1" class="next action-button" value="Next" />
                        </fieldset>
                        <fieldset>
                            <div class="form-card" id="step-2">
                                <div class="row">
                                    <div class="col-7">
                                        <h5 class="fs-title">Personal Information:</h5>
                                    </div>
                                    <div class="col-5">
                                        <p></p><small>Step 2 - 5</small></p>
                                    </div>
                                </div>
                                <label class="form-label" for="firstname">ชื่อจริง</label>
                                <input type="text" name="firstname" class="form-control" id="firstname" placeholder="Firstname" required />
                                <label class="form-label" for="lastname">นามสกุล</label>
                                <input type="text" name="lastname" class="form-control" id="lastname" placeholder="Lastname" required />
                                <label class="form-label" for="nickname">ชื่อเล่น</label>
                                <input type="text" name="nickname" class="form-control" id="nickname" placeholder="Nickname" required />
                                <label class="form-label" for="citizen_id">เลขบัตรประชาชน</label>
                                <input type="text" id="citizen_id" name="citizen_id" class="form-control" maxlength="17" onkeyup="autoTab(this)" placeholder="_-____-_____-_-__" required>
                                <label class="form-label" for="dob">วันเกิด</label>
                                <input input type="text" id="datepicker" name="dob" class="form-control" id="dob" placeholder="Date of birthday" value="<?php echo $dob; ?>" required />
                                <div class="row mt-4">
                                    <div class="col-md">
                                        <label class="form-label" for="gender_id">เลือกเพศ(Gender)</label>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="gender_lgbt" name="gender_id" value="LGBT" required>
                                            <label class="form-check-label" for="gender_lgbt">LGBT</label>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="gender_male" name="gender_id" value="ชาย" required>
                                            <label class="form-check-label" for="gender_male">ชาย</label>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="gender_female" name="gender_id" value="หญิง" required>
                                            <label class="form-check-label" for="gender_female">หญิง</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="button" name="next-2" class="next action-button" value="Next" />
                            <input type="button" name="previous-1" class="previous action-button-previous" value="Previous" />
                        </fieldset>
                        <fieldset>
                            <div class="form-card" id="step-3">
                                <div class="row">
                                    <div class="col-7">
                                        <h5 class="fs-title">Address:</h5>
                                    </div>
                                    <div class="col-5">
                                        <p></p><small>Step 3 - 5</small></p>
                                    </div>
                                </div>
                                <label class="form-label">ที่อยู่ตามบัตรประชาชน-สามารถจัดส่งของให้ได้</label>
                                <input type="text" name="address" class="form-control" id="address" placeholder="Address/เลขที่ ตามด้วยชื่อ/อาคาร/หมู่บ้าน/" required />
                                <div class="col-md">
                                    <label for="district" class="form-label">ตำบล/แขวง</label>
                                    <input type="text" name="district" class="form-control" id="district" autocomplete="off" placeholder="District" required />
                                </div>
                                <div class="col-md">
                                    <label for="amphoe" class="form-label">อำเภอ/เขต</label>
                                    <input type="text" name="amphoe" id="amphoe" class="form-control" autocomplete="off" placeholder="Amphoe" required />
                                </div>
                                <label for="province" class="form-label">จังหวัด</label>
                                <input type="text" name="province" id="province" class="form-control" autocomplete="off" placeholder="Province" required />
                                <label for="zipcode" class="form-label">รหัสไปรษณีย์</label>
                                <input type="text" name="zipcode" class="form-control" id="zipcode" autocomplete="off" placeholder="Zipcode" required />
                                <input type="text" name="province_code" class="form-control" id="province_code" autocomplete="off" placeholder="Province_code" required />
                                <input type="text" name="type" class="form-control" id="type" autocomplete="off" placeholder="type" required />
                                <label for="phone" class="form-label">เบอร์มือถือ ( format: xxxxxxxxxx )</label>
                                <input type="number" name="phone" id="phone" class="form-control" placeholder="xxxxxxxxxx" required />
                            </div>
                            <input type="button" name="next-3" class="next action-button" value="Next" />
                            <input type="button" name="previous-2" class="previous action-button-previous" value="Previous" />
                        </fieldset>
                        <fieldset>
                            <div class="form-card" id="step-4">
                                <div class="row">
                                    <div class="col-7">
                                        <h5 class="fs-title">Image Upload:</h5>
                                    </div>
                                    <div class="col-5">
                                        <p></p><small>Step 4 - 5</small></p>
                                    </div>
                                </div>
                                <label for="upload" class="form-label">รูปภาพ : ระบบจะบีบอัดไม่เกิน 500 KB</label>
                                <input type="file" name="upload" id="upload" class="form-control" accept="image/*" onchange="validateFileSize(this)">
                                <input type="hidden" name="avatar" id="avatar" class="form-control" required />
                                <div id="image-size-error" style="color: red;"></div>
                            </div>
                            <input type="button" name="next-4" class="next action-button" value="Next" />
                            <input type="button" name="previous-3" class="previous action-button-previous" value="Previous" />
                        </fieldset>
                        <fieldset>
                            <div class="form-card" id="step-5">
                                <div class="row">
                                    <div class="col-7">
                                        <h6 class="fs-title">Confirm:</h6>
                                    </div>
                                    <div class="col-5">
                                        <p></p><small>Step 5 - 5</small></p>
                                    </div>
                                </div>
                                <!-- Confirmation content -->
                                <h5 class="form-label">Select Level:สถานะสมาชิก</h5>
                                <div>
                                    <span class="small"><b>LV1</b></span>
                                    <span class="small">อาสาสมัคร : สนใจเข้าร่วมกิจกรรมเป็นครั้งคราว</span>
                                </div>
                                <div>
                                    <span class="small"><b>LV2</b></span>
                                    <span class="small">แกนนำเยาวชน : เคยผ่านกิจกรรมค่ายพัฒนาศักยภาพระดับจังหวัด/ภูมิภาค/ประเทศ</span>
                                </div>
                                <div>
                                    <span class="small"><b>LV3</b></span>
                                    <span class="small">พี่เลี้ยงจังหวัด : ผ่านกระบวนการพัฒนาศักยภาพมาอย่างต่อเนื่องจนเป็นต้นแบบไม่สูบ-ไม่ดื่ม</span>
                                </div>
                                <div>
                                    <span class="small"><b>LV4</b></span>
                                    <span class="small">นักรณรงค์ : ผ่านการพัฒนาศักยภาพจนสามารถสื่อสารให้ความรู้และเชื่อมประสานการทำงานได้</span>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md text-center">
                                        <input class="form-check-input" type="radio" id="lv1" name="level" value="LV1" required>
                                        <label for="lv1">LV1</label>
                                    </div>
                                    <div class="col-md text-center">
                                        <input class="form-check-input" type="radio" id="lv2" name="level" value="LV2" required>
                                        <label for="lv2">LV2</label>
                                    </div>
                                    <div class="col-md text-center">
                                        <input class="form-check-input" type="radio" id="lv3" name="level" value="LV3" required>
                                        <label for="lv3">LV3</label>
                                    </div>
                                    <div class="col-md text-center">
                                        <input class="form-check-input" type="radio" id="lv4" name="level" value="LV4" required>
                                        <label for="lv4">LV4</label>
                                    </div>
                                </div>
                                <!-- Religion Selection -->
                                <div class="row mt-4">
                                    <div class="col-md">
                                        <h5>Religion:ศาสนา</h5>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="buddhism" name="religion" value="ศาสนาพุทธ" required>
                                            <label class="form-check-label" for="buddhism">ศาสนาพุทธ</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="islam" name="religion" value="ศาสนาอิสลาม" required>
                                            <label class="form-check-label" for="islam">ศาสนาอิสลาม</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="christianity" name="religion" value="ศาสนาคริสต์" required>
                                            <label class="form-check-label" for="christianity">ศาสนาคริสต์</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="other_religion" name="religion" value="ศาสนาอื่น ๆ" required>
                                            <label class="form-check-label" for="other_religion">ศาสนาอื่น ๆ</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Blood Type Selection -->
                                <div class="row mt-4">
                                    <div class="col-md ">
                                        <h5>Blood Type:กรุ๊ปเลือด</h5>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="blood_a" name="blood_type" value="A" required>
                                            <label class="form-check-label" for="blood_a">A</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="blood_b" name="blood_type" value="B" required>
                                            <label class="form-check-label" for="blood_b">B</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="blood_ab" name="blood_type" value="AB" required>
                                            <label class="form-check-label" for="blood_ab">AB</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="blood_o" name="blood_type" value="O" required>
                                            <label class="form-check-label" for="blood_o">O</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="submit" name="submit" class="next action-button" value="Submit" />
                            <input type="button" name="previous-4" class="previous action-button-previous" value="Previous" />
                        </fieldset>
                    </form>
                    <a id="btn-login" href="login.php">Login</a>
                </div>
            </div>
        </div>
        <script src="script.js"></script>
        <script>
            $(function() {
                $("#datepicker").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "c-100:c+0", // ปี ค.ศ. จากปีปัจจุบันถอยหลัง 100 ปี ถึงปีปัจจุบัน
                    dateFormat: "yy-mm-dd" // รูปแบบวันที่เป็น ค.ศ.
                });
            });
        </script>
        <!-- เพิ่มการเรียกใช้ไลบรารี Iconify -->
        <script src="https://code.iconify.design/2/2.0.4/iconify.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/JQL.min.js"></script>
        <script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/typeahead.bundle.js"></script>
        <script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.js"></script>
    </body>

</html>
