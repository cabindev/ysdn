<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style_activity.css">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-icon/favicon.png">
    <title>YSDN Activities</title>
</head>

<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/vendor/autoload.php";

use App\Model\User;
use App\Model\Activitycms;
use App\Model\CategoryActivity;

$user_obj = new User();
$activityModel = new Activitycms();


$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 6;

$activities = $activityModel->getAllActivities($currentPage, $itemsPerPage);

?>

<body>

    <div class="navbar navbar-expand-lg  p-3 border-bottom">
        <a class="navbar-brand" href="#"></a>
         <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbarLight" aria-controls="offcanvasNavbarLight" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbarLight" aria-labelledby="offcanvasNavbarLightLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasNavbarLightLabel"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body"class="navbar-nav justify-content-end flex-grow-1 pe-3">
            <ul class="navbar-nav ml-auto"> <!-- เพิ่ม class ml-auto ที่นี่ -->
                <?php if (isset($_SESSION['id'])) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/ysdn_thailand/ysdn/auth/logout.php">Logout</a>
                    </li>
                <?php else : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../ysdn/auth/login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
            <a class="navbar-brand d-none d-sm-block ms-5" href="#">
                <?php
                if (isset($_SESSION['name'])) {
                    echo "<span class='text-secondary'>สวัสดี</span> ";
                    echo "<span style='color: #f58220;'>" . $_SESSION['name'] . "</span>";
                }
                ?>
            </a>
            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                    <li><a href="../Dashboard/index.php" class="nav-link px-2 link-dark">Dashboard</a></li>
                <?php } ?>
                <li><a href="../activity/activity.php" class="nav-link px-2 link-dark">Activity</a></li>
                <li><a href="../ysdn/auth/profile.php" class="nav-link px-2 link-dark">Profile</a></li>
            </ul>
        </div>
        </div>
    </div>

    <div>
        <img src="../ysdn/auth/img/Artboard 1.jpg" class="img-fluid" alt="Banner Image">
    </div>
    <div class="container text-center">
        <h1>YSDN Activities</h1>
        <?php if (isset($_SESSION['nickname'])) : ?>
            <h6>เรามีกิจกรรมใหม่ให้คุณนะ</h6>
            <div class="name">
                <h4><?php echo $_SESSION['nickname']; ?></h4>
            </div>
        <?php else : ?>
            <!-- ใส่โค้ดที่คุณต้องการแสดงถ้าไม่มีค่า 'nickname' ใน $_SESSION -->
        <?php endif; ?>
    </div>
    <div class="container">
        <div class="row">
            <?php
            foreach ($activities as $activity) {
            ?>
                <div class="col-md-4">
                    <div class="card mt-5">
                        <img src="images/<?php echo $activity['coverimage']; ?>" class="card-img-top" alt="Cover Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $activity['name']; ?></h5>
                            <p class="card-text">
                                <i class="fas fa-calendar-alt"></i> Date: <span style="color: your_color;"><?php echo $activity['date']; ?></span><br>
                                <i class="fa-solid fa-pen-to-square"></i> Description: <span style="color: your_color;"><?php echo $activity['description']; ?></span><br>
                                <i class="fas fa-user"></i> By: <span style="color: your_color;"><?php echo $activity['byname']; ?></span><br>
                                <i class="fas fa-tags"></i> Category: <span style="color: your_color;"><?php echo $activity['category_name']; ?></span>
                            </p>
                            <?php if (isset($_SESSION['id'])) : ?>
                                <a href="activity_form/ysdn_form.php?activity_id=<?php echo $activity['id']; ?>" class="icon-button">
                                    <i class="fas fa-sign-in-alt"></i> สมัคร
                                </a>
                            <?php else : ?>
                                <a id="register-button" href="../ysdn/auth/login.php" class="icon-button">
                                    <i class="fas fa-sign-in-alt"></i> สมัครกิจกรรม
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
    </div>
    <nav aria-label="Page navigation" class="pagination-container mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo max(1, $currentPage - 1); ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item <?php echo ($currentPage == 1) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=1">1</a>
            </li>
            <li class="page-item <?php echo ($currentPage == 2) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=2">2</a>
            </li>
            <!-- ... -->
            <li class="page-item <?php echo ($currentPage == 3) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=3">3</a>
            </li>
            <!-- ... -->
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>