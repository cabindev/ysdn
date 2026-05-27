<?php
require $_SERVER['DOCUMENT_ROOT']."/auth/auth.php";
require $_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php";

use App\Model\Person;
use App\Model\Ref;

if (isset($_REQUEST['action'])=='edit') {
	$personObj = new Person;
	$person = $personObj->getPersonById($_REQUEST['id']);
}
?>
<?php
// Check if the $person variable is defined
if (isset($person['dob'])) {
    // Format the date to "yyyy-MM-dd" if needed
    $dob = date('Y-m-d', strtotime($person['dob']));
} else {
    // Set a default value or an empty string if the $person variable is not defined
    $dob = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php require $_SERVER['DOCUMENT_ROOT']."/inc/components/nav.php";?>
</head>
<body class="container">
	<div class="row mt-5">
		<div class="col">
			<div class="card m-3">
				<div class="card-header bg-primary text-white d-flex justify-content-between m-3">
					<h4>แบบฟอร์ม<?php echo (isset($_REQUEST['action'])=='edit') ? "แก้ไขข้อมูลสมาชิก" : "เพิ่มสมาชิกใหม่";?></h4>
					<a href="../../Dashboard/data.php" class="btn btn-light">ย้อนกลับ</a>
				</div>
				
				<div class="container">
					<form class="row g-3" action="saveAdmin.php" method="POST" id="myForm" enctype="multipart/form-data">
					<?= csrf_field() ?>
						<input type="hidden" name="action" value="<?php echo (isset($_REQUEST['action'])=='edit') ? "edit" : "add";?>">
						<input type="hidden" name="id" value="<?php echo $person['id']; ?>">
						<div class="col-md-4">
							<label for="firstname" class="form-label">ชื่อจริง</label>
							<input type="text" name="firstname"class="form-control" id="firstname"placeholder="Firstname"required
							value="<?php echo isset($person['firstname']) ? $person['firstname'] : ''; ?>">
						</div>
						<div class="col-md-4">
							<label for="lastname" class="form-label">นามสกุล</label>
							<input type="text" name="lastname"class="form-control" id="lastname"placeholder="Lastname"required
							value="<?php echo isset($person['lastname']) ? $person['lastname'] : ''; ?>">
						</div>
						<div class="col-md-2">
							<label for="nickname" class="form-label">ชื่อเล่น</label>
							<input type="text" name="nickname"class="form-control" id="nickname"placeholder="Nickname"required
							value="<?php echo isset($person['nickname']) ? $person['nickname'] : ''; ?>">
						</div>
						<div class="col-md-4">
							<label for="dob" class="form-label">วันเกิด</label>
							<input type="date" name="dob"class="form-control" id="dob"placeholder="Nickname"value="<?php echo $dob; ?>"required
							value="<?php echo isset($person['dob']) ? $person['dob'] : ''; ?>">
						</div>
						<div class="col-md-2">
								<label for="gender_id"class="form-label">เพศ</label>
								<select type="text"name="gender_id" id="gender_id"class="form-select"required>
								<option value="">Gender</option>
									<?php
										$refObj = new Ref;
										$genders = $refObj->getRefsByGroupId(2);
										
										foreach($genders as $gender) {
											$selected = ($gender['ref_id'] == $person['gender_id']) ? "selected" : "";
											echo "
												<option value='{$gender['ref_id']}' {$selected} >{$gender['ref_title']}</option>
												
											";
										}
									?>
								</select>
						</div>
						<div class="col-12">
							<label for="address" class="form-label">ที่อยู่สามารถจัดส่งของให้ได้</label>
							<input type="text" name="address"class="form-control" id="address" placeholder="Address"required
							value="<?php echo isset($person['address']) ? $person['address'] : ''; ?>">
						</div>
						<div class="col-md-6">
							<label for="district" class="form-label">ตำบล/แขวง</label>
							<input type="text" name="district"class="form-control" id="district"autocomplete="off"placeholder="District"required
							value="<?php echo isset($person['district']) ? $person['district'] : ''; ?>">
						</div>
						<div class="col-md-4">
							<label for="amphoe" class="form-label">อำเภอ/เขต</label>
							<input type="text"name="amphoe"id="amphoe" class="form-control"autocomplete="off"placeholder="Amphoe"
							value="<?php echo isset($person['amphoe']) ? $person['amphoe'] : ''; ?>">
						</div>
						<div class="col-md-6">
							<label for="province" class="form-label">จังหวัด</label>
							<input type="text"name="province"id="province" class="form-control"autocomplete="off"placeholder="Province"
							value="<?php echo isset($person['province']) ? $person['province'] : ''; ?>">
						</div>
						<div class="col-md-2">
							<label for="zipcode" class="form-label">รหัสไปรษณีย์</label>
							<input type="text" name="zipcode"class="form-control"id="zipcode"autocomplete="off"placeholder="Zipcode"
							value="<?php echo isset($person['zipcode']) ? $person['zipcode'] : ''; ?>">
						</div>
						<div class="col-md-2">
							<!-- <label for="province_code" class="form-label">รหัสจังหวัด</label> -->
							<input type="text" name="province_code"class="form-control"id="province_code"autocomplete="off"placeholder="Province_code"
							value="<?php echo isset($person['province_code']) ? $person['province_code'] : ''; ?>">
						</div>
						<div class="col-md-2"> 
							<!-- <label for="member_code" class="form-label">รหัสสมาชิก</label> -->
							<input type="text" name="member_code"class="form-control"id="member_code"placeholder="member_code"
							value= <?php echo $_SESSION['member_code'];?> 
							value="<?php echo isset($person['member_code']) ? $person['member_code'] : ''; ?>">
						</div>
						<div class="col-md-6">
							<label for="phone" class="form-label">เบอร์มือถือ ( format: xxx-xxx-xxxx )</label>
							<input type="tel" name="phone" id="phone"pattern="^\d{3}-\d{3}-\d{4}$"class="form-control"placeholder="xxx-xxx-xxxx"required
							value="<?php echo isset($person['phone']) ? $person['phone'] : ''; ?>">
						</div>
						<div class="col-md-6">
							<label for="upload"class="form-label">รูปภาพ</label>
							<input type="file" name="upload" id="upload" class="form-control"accept="image/*">
							<input type="hidden" name="avatar" id="avatar" class="form-control"required 
							value="<?php echo isset($person['avatar']) ? $person['avatar'] : ''; ?>">
						</div>
						<button class="btn btn-primary text-white m-3" type="submit">บันทึก</button>
					</form>

	<script src="script.js"></script>				

</body>
</html>
