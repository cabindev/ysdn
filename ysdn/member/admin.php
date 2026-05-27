<?php
require $_SERVER['DOCUMENT_ROOT'] . "/auth/auth.php";
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
use App\Model\Person;
use App\Model\Ref;
use App\Model\Geo;

$itemsPerPage = 10; // Number of items to display per page

// Get the current page number
$page = isset($_GET['page']) ? $_GET['page'] : 1;

{ ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require $_SERVER['DOCUMENT_ROOT'] . "/inc/components/nav.php"; ?>

</head>
<body class="font-mali">

<div class="container-fluid">
    <div class="row mt-5">
        <div class="col">
            <div class="card mb-3">
                <div class="card-header  text-dark d-flex justify-content-between">
                    <h4>ระบบข้อมูลสมาชิก CRUD </h4>
                    <a href="form.php" class="btn btn-secondary">เพิ่มสมาชิกใหม่</a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <form action="" class="form-inline row" method="GET">
                            <div class="col-md-4 mt-2 mr-2">
                                <input type="text" name="search" id="search" placeholder="ค้นหา" class="form-control"
                                       value="<?php echo isset($_REQUEST['search']); ?>">
                            </div>
                            <div class="col-md-2 mt-2 mr-2">
                                <select name="gender_id" class="form-control">
                                    <option value="">เพศ</option>
                                    <?php
                                    $refObj = new Ref;
                                    $genders = $refObj->getRefsByGroupId(2);
                                    foreach ($genders as $gender) {
                                        $selected = ($gender['ref_id'] == $_REQUEST['gender_id']) ? "selected" : "";
                                        echo "
													<option value='{$gender['ref_id']}' {$selected} >{$gender['ref_title']}</option>
												";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2 mt-2 mr-2">
                                <select name="club_id" class="form-control">
                                    <option value="">ภูมิภาค</option>
                                    <?php
                                    $geogObj = new Geo;
                                    $geos = $geogObj->getAllGeo();
                                    foreach ($geos as $geo) {
                                        $selected = ($geo['id'] == $_REQUEST['geo']) ? "selected" : "";
                                        echo "
													<option value='{$geo['id']}' {$selected} >{$geo['name']}</option>
												";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 mt-2 mr-2"><button type="submit" class="btn btn-primary">ตกลง</button>
                            </div>
                        </form>
                    </div>
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
                        $personObj = new Person();
                        $filters = array_intersect_key($_REQUEST, array_flip(['search', 'gender_id', 'geo']));
                        //สร้างตัวแปร $fillter ดึงเฉพาะ Key ที่ต้องการ
                        $persons = $personObj->getAllPersons($filters);

                        $totalItems = count($persons);
                        $totalPages = ceil($totalItems / $itemsPerPage);

                        // Adjust current page if it's out of range
                        if ($page < 1) {
                            $page = 1;
                        } elseif ($page > $totalPages) {
                            $page = $totalPages;
                        }

                        // Calculate the offset for the query
                        $offset = ($page - 1) * $itemsPerPage;

                        // Get the items for the current page
                        $currentPageItems = array_slice($persons, $offset, $itemsPerPage);

                        $n = ($page - 1) * $itemsPerPage; // Start index for numbering

                        foreach ($currentPageItems as $person) {
                            $n++;
                            echo "
											<tr>
												<td>{$n}</td>
												<td><img src='{$person['avatar']}' class='avatar'></td>
												<td>{$person['firstname']}</td>
												<td>{$person['lastname']}</td>
												<td>{$person['nickname']}</td>
												<td>{$person['dob']}</td>
												<td>{$person['gender']}</td>
												<td>{$person['phone']}</td>
												<td>{$person['address']}</td>
												<td>{$person['district']}</td>
												<td>{$person['amphoe']}</td>
												<td>{$person['province']}</td>
												<td>{$person['zipcode']}</td>
												<td>
													<a href='form.php?id={$person['id']}&action=edit' class='btn btn-info  btn-sm'>แก้ไข</a>
													<a href='save.php?id={$person['id']}&action=delete' class='btn btn-danger  btn-sm'>ลบ</a>
												</td>
											</tr>
										";
                        }
                        ?>
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
        integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
        crossorigin="anonymous"></script>


</body>
</html>
<?php }; ?>
