<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <html lang="th">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YSDN Thailand | DataTables</title>
    <link rel="stylesheet" href="style.css">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Your custom JavaScript -->
    <script>
        $(document).ready(function() {
            // เมื่อคลิกที่ลิงก์ของ Table Activity
            $(".nav-link.fa-running").click(function() {
                // กำหนดสถานะ "active" ให้กับ <li> ที่มีคลาส "menu-open"
                $(".nav-item.menu-open").removeClass("active");
                // กำหนดสถานะ "active" ให้กับ <li> ที่คลิก
                $(this).closest(".nav-item.menu-open").addClass("active");
            });
        });
    </script>
</head>

<?php
require $_SERVER['DOCUMENT_ROOT'] . "/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use App\Model\User;
use App\Model\Activitycms;
use App\Model\Ref;

// Instantiate the User class
$user = new User();
$activityModel = new Activitycms();
$activityData = new Activitycms();
$activityId = $_GET['activityId'] ?? null; // รับค่า $activityId จาก URL
$registeredUsers = $activityModel->getRegisteredUsersForActivity($activityId); // เรียกใช้งานฟังก์ชันเพื่อดึงข้อมูลสมาชิกที่ลงทะเบียนในกิจกรรม

// สร้าง function ในคลาส Activitycms ด้วย
$allActivities = $activityModel->getAllActivities();


// สร้างตัวแปร $selectedActivity และกำหนดค่าเริ่มต้นเป็นค่าว่าง
$selectedActivity = '';

// ตรวจสอบว่ามีการส่งค่า selected_activity ผ่าน POST หรือไม่
if (isset($_POST['selected_activity'])) {
    // ถ้ามีการส่งค่า selected_activity ให้กำหนดค่าให้กับตัวแปร $selectedActivity
    $selectedActivity = $_POST['selected_activity'];
}

// ตรวจสอบว่า $selectedActivity ไม่เป็นค่าว่าง
if (!empty($selectedActivity)) {
    // ถ้ามีการเลือกกิจกรรมให้แสดงผลตามที่เลือก
    $registrants = $activityModel->viewRegistrantsForActivity($selectedActivity);
} else {
    // ถ้าไม่มีการเลือกกิจกรรม ให้แสดงข้อมูลทั้งหมด
    $registrants = $activityModel->viewRegistrantsForActivity(''); // ให้ค่าว่างหรือค่าที่เหมาะสม
}


// Get the user data from the last inserted ID
$userData = $user->getUserById($_SESSION['id'] ?? null);

// Extract user data
$name = $userData['name'] ?? '';
$firstname = $userData['firstname'] ?? '';
$lastname = $userData['lastname'] ?? '';
$email = $userData['email'] ?? '';
$memberCode = $userData['member_code'] ?? '';
$avatar = $userData['avatar'] ?? '';
$role = $userData['role'] ?? '';


?>

<style>
    body {
        font-family: 'Your Thai Font', sans-serif;
    }
</style>

<body class="hold-transition sidebar-mini">

    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Navbar Search -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                        <i class="fas fa-search"></i>
                    </a>
                    <div class="navbar-search-block">
                        <form class="form-inline">
                            <div class="input-group input-group-sm">
                                <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-navbar" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>
                <li><a class="dropdown-item" href="#"><?php echo ($_SESSION['role']); ?></a></li>
                <li><a class="dropdown-item" href="../ysdn/auth/profile.php">Profile</a></li>
                <li><a class="dropdown-item" href="../ysdn/auth/editProfile.php">Edit</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="/auth/logout.php">Logout</a></li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="index.php" class="brand-link">
                <svg class="space-t m-3" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                    <style>
                        svg {
                            fill: #f7f7f8
                        }
                    </style>
                    <path d="M512 256A256 256 0 1 0 0 256a256 256 0 1 0 512 0zM271 135c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-87 87 87 87c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0L167 273c-9.4-9.4-9.4-24.6 0-33.9L271 135z" />
                </svg>
                <span class="brand-text font-weight-light"> Dashboard</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user (optional) -->
                <div class="user-panel">
                    <div class="image">
                        <?php if (!empty($avatar)) { ?>
                            <img src="<?php echo $avatar; ?>" id="avatar" class="rounded-circle" alt="Avatar">
                        <?php } ?>
                    </div>
                </div>
                <div class="user-detial">
                    <span class="firstname text-white"><?php echo $firstname . '  ' . $lastname; ?></span>
                </div>
                <!-- SidebarSearch Form -->
                <div class="form-inline">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar">
                                <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul id="sidebarMenu" class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item menu-open active">
                            <a href="data.php" class="nav-link">
                                <i class="nav-icon fas fa-table"></i>
                                <p>
                                    Database Users
                                </p>
                            </a>
                        </li>
                        <li class="nav-item menu-open active">
                            <a href="allUser.php" class="nav-link">
                                <i class="nav-icon fas fa-user" ></i>
                                <p>
                                    Admin | Member
                                </p>
                            </a>
                        </li>

                        <li class="nav-item menu-open">
                            <a href="data_activity.php" class="nav-link">
                                <i class="nav-icon fas fa-running"style="color: #f58220;"></i>
                                <p>
                                    Tables | Actitvity
                                </p>
                            </a>
                        </li>
                        <li class="nav-item menu-open">
                            <a href="approove_user_activity.php" class="nav-link">
                                <i class="nav-icon fas fa-list"></i>
                                <p>
                                    Approve User | Activity Registrations
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="col-md-6">

            </div>
            <!-- ส่วนของตารางเพื่อแสดงข้อมูลสมาชิกที่ลงทะเบียนในกิจกรรม -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4>เรียกดูแต่ละกิจกรรม</h4>
                            <form method="post">
                                <select name="selected_activity" onchange="this.form.submit()">
                                    <option value="">แสดงทั้งหมด</option>
                                    <?php
                                    foreach ($allActivities as $activity) {
                                        $selected = ($activity['id'] == $selectedActivity) ? 'selected' : '';
                                        echo "<option value='" . $activity['id'] . "' $selected>" . $activity['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </form>
                            <?php
                            if (isset($registrants)) {
                                echo "<h2 class='mt-4'>Registrants for selected activity</h2>";
                                echo "<table id='registeredUsersTable' class='table table-bordered table-striped'>";
                                echo "<thead>";
                                echo "<tr>";
                                echo "<th scope='col'>รหัสสมาชิก</th>";
                                echo "<th scope='col'>ชื่อ</th>";
                                echo "<th scope='col'>นามสกุล</th>";
                                echo "<th scope='col'>ชื่อเล่น</th>";
                                echo "<th scope='col'>อีเมล</th>";
                                echo "<th scope='col'>ภาค</th>";
                                echo "<th scope='col'>ชื่อกิจกรรม</th>";
                                echo "<th scope='col'>แพ้อาหาร</th>";
                                echo "<th scope='col'>ประเภทอาหาร</th>";
                                echo "<th scope='col'>ยาที่ใช้รักษา</th>";
                                echo "<th scope='col'>โรคประจำตัว</th>";
                                echo "<th scope='col'>เบอร์โทรผู้ปกครอง</th>";
                                echo "<th scope='col'>สถานะ</th>";
                                echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";

                                foreach ($registrants as $person) {
                                    echo "<tr>";
                                    echo "<td>" . (isset($person['member_code']) ? $person['member_code'] : "") . "</td>";
                                    echo "<td>" . (isset($person['lastname']) ? $person['lastname'] : "") . "</td>";
                                    echo "<td>" . (isset($person['firstname']) ? $person['firstname'] : "") . "</td>";
                                    echo "<td>" . (isset($person['nickname']) ? $person['nickname'] : "") . "</td>";
                                    echo "<td>" . (isset($person['email']) ? $person['email'] : "") . "</td>";
                                    echo "<td>" . (isset($person['type']) ? $person['type'] : "") . "</td>";
                                    echo "<td>" . (isset($person['activity_name']) ? $person['activity_name'] : "") . "</td>";
                                    echo "<td>" . (isset($person['food_preference']) ? $person['food_preference'] : "") . "</td>";
                                    echo "<td>" . (isset($person['food_type']) ? $person['food_type'] : "") . "</td>";
                                    echo "<td>" . (isset($person['medication_type']) ? $person['medication_type'] : "") . "</td>";
                                    echo "<td>" . (isset($person['medical_condition']) ? $person['medical_condition'] : "") . "</td>";
                                    echo "<td>" . (isset($person['guardian_phone']) ? $person['guardian_phone'] : "") . "</td>";
                                    echo "<td>" . (isset($person['status']) ? $person['status'] : "") . "</td>";

                                    echo "</tr>";
                                }
                                echo "</tbody>";
                                echo "</table>";
                            }
                            ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->


        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
    </div>
    <!-- /.col -->
    </div>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2023 <a href="#">YSDN</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 3.2.0
        </div>
    </footer>
    </div>
    <!-- /.content-wrapper -->


    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>


    <!-- Page specific script -->
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel","print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // เรียกใช้งาน DataTables สำหรับตาราง registeredUsersTable
            $('#registeredUsersTable').DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#registeredUsersTable_wrapper .col-md-6:eq(0)');

        });
    </script>

</body>

</html>