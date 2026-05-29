<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn/app/auth/auth.php";
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn/vendor/autoload.php";

use App\Model\User;
use App\Model\Activitycms;
use App\Model\CategoryActivity;
// สร้างอ็อบเจกต์ของคลาส User
$user_obj = new User();
$activityModel = new Activitycms();
$categoryModel = new CategoryActivity();
// ดึงข้อมูลผู้ใช้ที่ล็อกอินออกมา
$userData = $user_obj->getUserById($_SESSION['id']);
?>
<nav class="p-3 mb-3 border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="#" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap">
                    <use xlink:href="#bootstrap" />
                </svg>
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                    <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap">
                        <use xlink:href="#bootstrap" />
                    </svg>
                    <a class="navbar-brand d-none d-sm-block" href="#">
                        <?php
                        if (isset($_SESSION['name'])) {
                            echo "<span class='text-secondary'>สวัสดี</span> ";
                            echo "<span style='color: #f58220;'>" . $_SESSION['name'] . "</span>";
                        }
                        ?>
                    </a>
                </a>
            </a>
            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                    <li><a href="../Dashboard/index.php" class="nav-link px-2 link-dark">Dashboard</a></li>
                    <li><a href="../activity/activity.php" class="nav-link px-2 link-dark">Activity</a></li>
                <?php } ?>
                <!-- <li><a href="#" class="nav-link px-2 link-dark">Inventory</a></li>
        <li><a href="#" class="nav-link px-2 link-dark">Customers</a></li>
        <li><a href="#" class="nav-link px-2 link-dark">Products</a></li> -->
            </ul>

        
        </div>
    </div>
</nav>

