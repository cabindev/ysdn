<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <title>Status_activity</title>


</head>

<?php
session_start();
if (!$_SESSION['login']) {
    header("location:login.php");
    exit;
}
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
require $_SERVER['DOCUMENT_ROOT'] . "/inc/components/nav.php";

use App\Model\User;
use App\Model\Activitycms;
use App\Model\QrCode;

$user = new User();
$activitycms = new Activitycms();

$userData = $user->getUserById($_SESSION['id'] ?? "");
$activities = $activitycms->getActivitiesByUserId($_SESSION['id']);

$activityModel = new Activitycms();

$userId = $_SESSION['id']; // ใช้ $_SESSION['id'] เพื่อกำหนดค่า userId
$approvalStatus = $activityModel->getUserApprovalStatus($userId);

// Extract user data
$firstname = $userData['firstname'] ?? '';
$lastname = $userData['lastname'] ?? '';
$email = $userData['email'] ?? '';
$memberCode = $userData['member_code'] ?? '';
$avatar = $userData['avatar'] ?? '';
$role = $userData['role'] ?? '';
$type = $userData['type'] ?? '';
$status = $userData['status'] ?? '';
$dob = $userData['dob'] ?? "";
if ($dob !== "") {
    $dob = date_create($dob);
    $currentDate = new DateTime();
    $interval = $dob->diff($currentDate);
    $dob = $interval->y;
} else {
    echo "ไม่สามารถคำนวณอายุได้เนื่องจากไม่มีวันเกิดที่ระบุ";
}
// รับ URL ปัจจุบัน
$current_page = basename($_SERVER['PHP_SELF']);
$count = 0;

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
                    <a class="nav-link" href="../../activity/index.php" class="nav-link px-2 link-dark">Home</a>
                </li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../../Dashboard/index.php" class="nav-link px-2 link-dark">Dashboard</a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link" href="../../activity/activity.php" class="nav-link px-2 link-dark">Activity</a>
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
                <a class="dropdown-item" href="profile.php">Profile</a>
                <a class="dropdown-item" href="editProfile.php">Edit Profile</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/auth/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="d-flex justify-content-center mb-2 d-sm-none">
        <div id="sidebar-list" class="container">
            <div class="d-flex align-items-center">
                <span class="activity-icons ">
                    <a href="profile.php" class="activity-icon">
                        <i class="fas fa-home"></i>
                    </a>
                    <a href="status_activity.php" class="activity-icon">
                        <i class="fas fa-chart-bar" style="color:#f58220;"></i>
                    </a>
                    <a href="profile_activity.php" class="activity-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="d-flex">
        <div id="sidebar">
            <h2 class="menu-item d-none d-sm-block">YSDN</h2>
            <div class="list-group d-none d-sm-block">
                <a href="profile.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'profile.php') ? 'list-group-item-warning' : ''; ?>" aria-current="true">
                    ภาพรวม
                </a>
                <a href="status_activity.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'status_activity.php') ? 'list-group-item-warning' : ''; ?>">
                    สถานะ
                </a>
                <a href="profile_activity.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'profile_activity.php') ? 'list-group-item-warning' : ''; ?>">
                    กิจกรรม
                </a>
            </div>
        </div>
        <main id="canvas" class="container">
            <div id="menu-profile">
                <div class="card-list">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="profile-picture-activity d-flex justify-content-center align-items-center mb-5 d-none d-sm-block">
                                    <img src="<?php echo $avatar; ?>" class="avatar" alt="Profile Picture">
                                </div>
                            </div>
                            <div class="col ">
                                <h5 class="text-center">สถานะเข้าร่วมกิจกรรม</h5>
                                <ul class="list-group bg-transparent">
                                    <?php
                                    $maxActivitiesToShow = 3;
                                    $currentTimestamp = time(); // Get the current timestamp

                                    for ($i = count($activities) - 1; $i >= 0 && $maxActivitiesToShow > 0; $i--) {
                                        $activity = $activities[$i];
                                        $activityTimestamp = strtotime($activity['date']); // Convert activity date to a timestamp

                                        // Check if the activity has started
                                        if ($activityTimestamp > $currentTimestamp) {
                                            $activityName = $activity['name'];
                                            $statusMessage = isset($approvalStatus[$activityName]) ? $approvalStatus[$activityName] : 'No Data';

                                            // Display status and date in a nicely formatted card with colors
                                            echo '<li class="list-group-item bg-transparent">';
                                            echo '<div class="row">';
                                            echo '<div class="col-md-8">';
                                            echo '<strong>' . $activityName . '</strong>';
                                            echo '<span class="float-end">';

                                            if ($statusMessage === 'อนุมัติ') {
                                                echo '<span style="color: green;">Approved</span>';
                                            } else if ($statusMessage === 'ไม่อนุมัติ') {
                                                echo '<span style="color: red;">Rejected</span>';
                                            } else {
                                                echo $statusMessage;
                                            }
                                            echo '</span>';
                                            echo '</div>';

                                            echo '<div class="col-md-4">';
                                            echo '<span class="float-end" style="color: blue;">Start Date: ' . $activity['date'] . '</span>';
                                            echo '</div>';

                                            echo '</div>';
                                            echo '</li>';
                                            $maxActivitiesToShow--;
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- <button id="download-button" class="btn btn-save">
                        <i class="fas fa-download" style="color: #f58220;"></i>
                    </button> -->
                </div>
            </div>
        </main>
    </div>

    <script>
        //เปลี่ยน background ตามเวลา กลางวัน กลางคืน
        var date = new Date();
        var hour = date.getHours();

        if (hour >= 6 && hour < 18) { // From 6am to 6pm
            document.body.style.backgroundColor = "#f5f5f5"; // สีที่ใกล้เคียงกับ bg-zinc-100
        } else {
            document.body.style.backgroundColor = "#f5f5f5"; // สีที่ใกล้เคียงกับ bg-zinc-100
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios@0.21.1/dist/axios.min.js"></script>
   

</html>
