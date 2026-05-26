<?php
require $_SERVER['DOCUMENT_ROOT']."/ysdn_thailand/ysdn/auth/auth.php";
require $_SERVER['DOCUMENT_ROOT']."/ysdn_thailand/vendor/autoload.php";
use App\Model\Person;
use App\Model\User;
use App\Model\Ref;
use App\Model\Geo;
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<?php require $_SERVER['DOCUMENT_ROOT']."/ysdn_thailand/ysdn/inc/components/nav.php";?>
</head>
<body class="font-mali">
	<div class="container-fluid">
		<div class="row mt-5">
			<div class="col">
				<div class="card mb-3">
					<div class="card-header text-dark d-flex justify-content-between">
						<h4>สมาชิก YSDN Thailand</h4>
						<!-- <a href="formMember.php" class="btn btn-secondary" id="btnMember">เพิ่มข้อมูลส่วนตัว</a> -->
					</div>
					<div class="card-body">
						<table class="table">
							<thead>
								<tr>
									<th>#</th>
									<th>Avatar</th>
									<th>Firstname</th>
									<th>Lastname</th>
									<th>Nickname</th>
									<th>DOB</th>
									<th>Gender</th>
									<th>Phone</th>
									<th>Address</th>
									<th>District</th>
									<th>Amphoe</th>
									<th>Province</th>
									<th>Zipcode</th>
									<th>จัดการ</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$userObj = new User();
								$filters = []; // Define the filters array here or provide appropriate filter values

								$userProfiles = $userObj->getAllUser($filters);

								if ($userProfiles) {
									$n = 0;
									foreach ($userProfiles as $userProfile) {
										$n++;
										echo "
											<tr>
												<td>{$n}</td>
												<td><img src='{$userProfile['avatar']}' class='avatar'></td>
												<td>{$userProfile['firstname']}</td>
												<td>{$userProfile['lastname']}</td>
												<td>{$userProfile['nickname']}</td>
												<td>{$userProfile['dob']}</td>
												<td>{$userProfile['gender_id']}</td>
												<td>{$userProfile['phone']}</td>
												<td>{$userProfile['address']}</td>
												<td>{$userProfile['district']}</td>
												<td>{$userProfile['amphoe']}</td>
												<td>{$userProfile['province']}</td>
												<td>{$userProfile['zipcode']}</td>
												<td>
													
												</td>
											</tr>
										";
									}
								} else {
									echo "No user profiles found.";
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>
</html>
