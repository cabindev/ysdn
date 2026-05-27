<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style_activity.css">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-icon/favicon.png">
    <title>YSDN Activities</title>
</head>

<?php
require $_SERVER['DOCUMENT_ROOT'] . "/auth/auth.php";
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use App\Model\User;
use App\Model\Activitycms;
use App\Model\CategoryActivity;

$user_obj = new User();
$activityModel = new Activitycms();

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 6;

$activities = $activityModel->getAllActivities($currentPage, $itemsPerPage);

// ดึงข้อมูลผู้ใช้ที่ล็อกอินออกมา
$userData = $user_obj->getUserById($_SESSION['id']);
?>

<body>
    <nav class="navbar navbar-expand-lg  p-3 mb-3 border-bottom">
        <!-- Navbar Brand (Logo + Text) -->
        <a class="navbar-brand d-none d-sm-block" href="#">

        </a>
        <a class="navbar-brand d-none d-sm-block" href="#">
            <?php
            if (isset($_SESSION['name'])) {
                echo "<span class='text-secondary'>สวัสดี</span> ";
                echo "<span style='color: #f58220;'>" . $_SESSION['name'] . "</span>";
            }
            ?>
        </a>
        <!-- Hamburger menu button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible content -->
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../activity/index.php" class="nav-link px-2 link-dark">Home</a>
                </li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                    <li><a href="../Dashboard/index.php" class="nav-link px-2 link-dark">Dashboard</a></li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link" href="../ysdn/auth/profile.php" class="nav-link px-2 link-dark">Profile</a>
                </li>
            </ul>
        </div>

        <!-- Profile Dropdown -->
        <div class="dropdown text-end">
            <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown">
                <?php if (!empty($avatar)) { ?>
                    <img src="<?php echo $avatar; ?>" class="rounded-circle" alt="Avatar">
                <?php } ?>
            </a>
            <div class="dropdown-menu dropdown-menu-end text-small" aria-labelledby="profileDropdown">
                <a class="dropdown-item" href="#">
                    Status :
                    <span style="color: #f58220;">
                        <?php echo ($_SESSION['role']); ?>
                    </span>
                </a>
                <a class="dropdown-item" href="../ysdn/auth/profile.php">Profile</a>
                <a class="dropdown-item" href="../ysdn/auth/editProfile.php">Edit Profile</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/auth/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <body>
        <h1 class="text-center">YSDN Activities</h1>
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

                                <a href="activity_form/ysdn_form.php?activity_id=<?php echo $activity['id']; ?>" class="icon-button">
                                    <i class="fas fa-sign-in-alt"></i> สมัคร
                                </a>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>

</html>