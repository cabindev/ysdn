<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use App\Model\Chart;
use App\Model\User;

// Instantiate the User class
$userModel = new Chart();
$userData = $userModel->getAllType();
$genderData = $userModel->getAllGender();
$levelData = $userModel->getLevelsStatistics();
$religionData = $userModel->getReligionStatistics();
$bloodtypeData = $userModel->getBloodTypeStatistics();

function generateRandomColor()
{
  $red = rand(0, 255);
  $green = rand(0, 255);
  $blue = rand(0, 255);
  return "rgb($red, $green, $blue)";
}
$donutChartData = [];
$total = 0;
$data = [];
$colors = [];

foreach ($userData as $row) {
  $type = $row['type'];
  $count = (int) $row['count'];

  if (isset($data[$type])) {
    $data[$type] += $count;
  } else {
    $data[$type] = $count;
  }

  $total += $count;
  $color = generateRandomColor();
  $donutChartData['backgroundColor'][] = $color;
  $colors[$type] = $color;  // Assign generated color to its type
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>YSDN | ChartJS</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Include Highcharts library -->
  <script src="https://code.highcharts.com/highcharts.js"></script>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="style.css">
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="index3.html" class="nav-link">Home</a>
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
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="../Dashboard/index.php" class="brand-link">
        <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Back</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel ">
          <div class="image">
            <?php if (!empty($avatar)) { ?>
              <img src="<?php echo $avatar; ?>" id="avatar" class="rounded-circle" alt="Avatar">
            <?php } ?>
          </div>
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
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-header">YSDN Charts</li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-chart-pie"></i>
                <p>
                  Charts
                  <i class="right fas fa-angle-left"></i>
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
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">ChartYSDN</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">ChartYSDN</li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- Main content -->
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-6">
              <div class="card card-success">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">จำนวนสมาชิก ระดับภูมิภาค</h3>
                    <a id="downloadDonutChart" class="btn btn-primary" download="donut_chart.png">Download</a>
                    <!-- <a href="javascript:void(0);">View Report</a> -->
                  </div>
                </div>
                <div class="card-body">
                  <div class="d-flex">
                    <div class="position-relative mb-4">
                      <canvas id="donutChart" height="200"></canvas>
                    </div>
                  </div>
                  <!-- /.d-flex -->
                  <div class="d-flex flex-row justify-content-end">
                    <?php foreach ($data as $type => $count) : ?>
                      <span class="mr-2">
                        <i class="fas fa-circle" style="color: <?php echo $colors[$type]; ?>"></i>
                        <?php echo $type; ?> (<?php echo $count; ?>)
                      </span>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="card card-success">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title"> ประเภท เพศสมาชิก</h3>
                    <a id="downloadGenderChart" class="btn btn-primary" download="gender_chart.png">Download</a>
                    <!-- <a href="javascript:void(0);">View Report</a> -->
                  </div>
                </div>
                <div class="card-body">
                  <div class="d-flex">
                    <div class="position-relative mb-4">
                      <canvas id="genderChart" height="200"></canvas>
                    </div>
                  </div>
                  <!-- /.d-flex -->
                  <div class="d-flex flex-row justify-content-end">
                    <?php foreach ($genderData as $gender) : ?>
                      <span class="mr-2">
                        <i class="fas fa-circle" style="color: <?php echo generateRandomColor(); ?>"></i>
                        <?php echo $gender['gender_id']; ?> (<?php echo $gender['count']; ?>)
                      </span>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- /.col-lg-6 -->
            <div class="col-lg-6">
              <div class="card card-success">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">ระดับสมาชิก</h3>
                    <a id="downloadLevelChart" class="btn btn-primary" download="level_chart.png">Download</a>
                    <!-- <a href="javascript:void(0);">View Report</a> -->
                  </div>
                </div>
                <div class="card-body">
                  <div class="d-flex">
                    <div class="position-relative mb-4">
                      <canvas id="levelChart" height="200"></canvas>
                    </div>
                  </div>
                  <div class="d-flex flex-row justify-content-end">
                    <!-- PHP loop to display level data -->
                    <?php foreach ($levelData as $level) : ?>
                      <span class="mr-2">
                        <i class="fas fa-circle" style="color: <?php echo generateRandomColor(); ?>"></i>
                        <?php echo $level['level']; ?> (<?php echo $level['count']; ?>)
                      </span>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Main content -->
            <div class="content">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-lg-6">
                    <!-- BAR CHART -->
                    <div class="card card-success">
                      <div class="card-header">
                        <div class="d-flex justify-content-between">
                          <h3 class="card-title">Bar Chart - Religion Statistics</h3>
                          <a id="downloadReligionChart" class="btn btn-primary" download="religion_chart.png">Download</a>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="chart">
                          <canvas id="religionChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-lg-6">
                    <!-- BAR CHART -->
                    <div class="card card-success">
                      <div class="card-header">
                        <div class="d-flex justify-content-between">
                          <h3 class="card-title">Bar Chart - Blood Type Statistics</h3>
                          <a id="downloadBloodTypeChart" class="btn btn-primary" download="blood_type_chart.png">Download</a>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="chart">
                          <canvas id="bloodTypeChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div><!-- /.container-fluid -->
            </div>
            <!-- /.row -->
          </div><!-- /.container-fluid -->
        </div>
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->

      <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">
          Anything you want
        </div>
        <strong>Powered by <a href="https://adminlte.io">AdminLTE</a>.</strong> All rights reserved.
      </footer>

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
    <!-- ChartJS -->
    <script src="plugins/chart.js/Chart.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>

    <!-- Page specific script -->
    <script>
      $(function() {
        // Donut Chart
        const donutChartCanvas = $('#donutChart').get(0).getContext('2d');
        const donutData = {
          labels: <?php echo json_encode(array_keys($data)); ?>,
          datasets: [{
            data: <?php echo json_encode(array_values($data)); ?>,
            backgroundColor: <?php echo json_encode(array_map('generateRandomColor', array_keys($data))); ?>,
          }]
        };
        const donutOptions = {
          maintainAspectRatio: false,
          responsive: true,
          legend: {
            position: 'right',
            align: 'end',
          }
        };
        // Create donut chart
        const donutChart = new Chart(donutChartCanvas, {
          type: 'doughnut',
          data: donutData,
          options: donutOptions
        });

        // Gender Chart
        const genderChartCanvas = $('#genderChart').get(0).getContext('2d');
        const genderData = {
          labels: <?php echo json_encode(array_column($genderData, 'gender_id')); ?>,
          datasets: [{
            data: <?php echo json_encode(array_column($genderData, 'count')); ?>,
            backgroundColor: <?php echo json_encode(array_map('generateRandomColor', array_column($genderData, 'gender_id'))); ?>,
          }]
        };
        const genderOptions = {
          maintainAspectRatio: false,
          responsive: true,
          legend: {
            position: 'right',
            align: 'end',
          }
        };
        // Create gender chart
        const genderChart = new Chart(genderChartCanvas, {
          type: 'pie',
          data: genderData,
          options: genderOptions
        });

        // Level Chart
        const levelChartCanvas = $('#levelChart').get(0).getContext('2d');
        const levelData = {
          labels: <?php echo json_encode(array_column($levelData, 'level')); ?>,
          datasets: [{
            label: 'Number of Users',
            data: <?php echo json_encode(array_column($levelData, 'count')); ?>,
            backgroundColor: <?php echo json_encode(array_map('generateRandomColor', array_column($levelData, 'level'))); ?>,
          }]
        };
        const levelOptions = {
          maintainAspectRatio: false,
          responsive: true,
          legend: {
            display: false,
          },
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
              },
            }],
          },
        };
        // Create level chart
        const levelChart = new Chart(levelChartCanvas, {
          type: 'bar',
          data: levelData,
          options: levelOptions
        });

        // Religion Chart
        const religionChartCanvas = $('#religionChart').get(0).getContext('2d');
        const religionData = {
          labels: <?php echo json_encode(array_column($religionData, 'religion')); ?>,
          datasets: [{
            label: 'Number of Members',
            data: <?php echo json_encode(array_column($religionData, 'count')); ?>,
            backgroundColor: <?php echo json_encode(array_map('generateRandomColor', array_column($religionData, 'religion'))); ?>,
          }]
        };
        const religionOptions = {
          maintainAspectRatio: false,
          responsive: true,
          legend: {
            display: false,
          }
        };
        // Create religion chart
        const religionChart = new Chart(religionChartCanvas, {
          type: 'pie',
          data: religionData,
          options: religionOptions
        });

        // Blood Type Chart
        const bloodTypeChartCanvas = $('#bloodTypeChart').get(0).getContext('2d');
        const bloodTypeData = {
          labels: <?php echo json_encode(array_column($bloodtypeData, 'blood_type')); ?>,
          datasets: [{
            label: 'Number of Members',
            data: <?php echo json_encode(array_column($bloodtypeData, 'count')); ?>,
            backgroundColor: <?php echo json_encode(array_map('generateRandomColor', array_column($bloodtypeData, 'blood_type'))); ?>,
          }]
        };
        const bloodTypeOptions = {
          maintainAspectRatio: false,
          responsive: true,
          legend: {
            display: false,
          }
        };
        // Create blood type chart
        const bloodTypeChart = new Chart(bloodTypeChartCanvas, {
          type: 'pie',
          data: bloodTypeData,
          options: bloodTypeOptions
        });

        // Function to download canvas as an image
        function downloadCanvas(canvasId, downloadId) {
          const canvas = document.getElementById(canvasId);
          const downloadLink = document.getElementById(downloadId);

          downloadLink.addEventListener('click', function() {
            const image = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
            downloadLink.setAttribute("href", image);
          });
        }
        // Call the function for each chart
        downloadCanvas('donutChart', 'downloadDonutChart');
        downloadCanvas('genderChart', 'downloadGenderChart');
        downloadCanvas('levelChart', 'downloadLevelChart');
        downloadCanvas('religionChart', 'downloadReligionChart');
        downloadCanvas('bloodTypeChart', 'downloadBloodTypeChart');
      });
    </script>
</body>

</html>