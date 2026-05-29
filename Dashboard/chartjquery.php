<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn/app/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/ysdn/vendor/autoload.php";

use App\Model\Chart;
use App\Model\User;
use App\Helper\Input;

$userModel    = new User();
$userData     = $userModel->getUserById($_SESSION['id'] ?? '');
$firstname    = Input::e($userData['firstname']   ?? '');
$lastname     = Input::e($userData['lastname']    ?? '');
$memberCode   = Input::e($userData['member_code'] ?? '');
$avatar       = $userData['avatar'] ?? '';

$chartModel   = new Chart();
$genderData   = $chartModel->getAllGender();
$typeData     = $chartModel->getAllType();
$religionData = $chartModel->getReligionStatistics();
$levelData    = $chartModel->getLevelsStatistics();
$bloodData    = $chartModel->getBloodTypeStatistics();

// Build gender aggregate
$genderAgg = [];
foreach ($genderData as $row) {
    $g = $row['gender_id'];
    $genderAgg[$g] = ($genderAgg[$g] ?? 0) + (int)$row['count'];
}

// Fixed palette (no random colors)
$palette = ['#f58220','#2563eb','#16a34a','#7c3aed','#0d9488','#db2777','#ca8a04','#64748b'];
function palette(int $i): string {
    global $palette;
    return $palette[$i % count($palette)];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>รายงาน — YSDN</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&family=Noto+Sans+Thai:wght@300;400;500&display=swap">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon.png">
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://code.highcharts.com/modules/accessibility.js"></script>
  <style>
    /* ── Sidebar ─────────────────────────── */
    .main-sidebar { background: #1c1c1e !important; border-right: none; }
    .sidebar { display: flex; flex-direction: column; height: 100%; }
    .brand-link { border-bottom: 1px solid rgba(255,255,255,.05) !important; padding: 16px !important; }
    .brand-text  { font-size: 13px !important; font-weight: 400 !important; color: rgba(255,255,255,.8) !important; }
    .sb-user { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,.05); }
    .sb-user-name { font-size: 12px; color: rgba(255,255,255,.75); }
    .sb-user-code { font-size: 11px; color: rgba(255,255,255,.3); margin-top: 2px; }
    .nav-sidebar .nav-item { margin: 0; }
    .nav-sidebar .nav-link {
      font-size: 13px !important; font-weight: 300 !important;
      color: rgba(255,255,255,.45) !important;
      border-radius: 0 !important; padding: 9px 16px !important;
      transition: color .15s; display: flex !important; align-items: center;
    }
    .nav-sidebar .nav-link i.nav-icon { width: 18px; font-size: 12px; text-align: center; margin-right: 10px; flex-shrink: 0; }
    .nav-sidebar .nav-link p { margin: 0; }
    .nav-sidebar .nav-link:hover  { background: rgba(255,255,255,.04) !important; color: rgba(255,255,255,.8) !important; }
    .nav-sidebar .nav-link.active { background: rgba(255,255,255,.07) !important; color: #fff !important;
                                    border-left: 2px solid #f58220; padding-left: 14px !important; }
    .nav-header { font-size: 9px !important; letter-spacing: .12em; text-transform: uppercase;
                  color: rgba(255,255,255,.18) !important; padding: 16px 16px 5px !important; margin: 0; }
    .sb-footer { margin-top: auto; border-top: 1px solid rgba(255,255,255,.05); padding: 8px 0; }
    .sb-footer a { display: flex; align-items: center; gap: 10px; font-size: 12.5px;
                   color: rgba(255,255,255,.3); padding: 9px 16px; text-decoration: none; transition: color .15s; }
    .sb-footer a:hover { color: #f87171; }
    .sb-footer a i { font-size: 12px; width: 18px; text-align: center; }

    /* ── Topbar ──────────────────────────── */
    .main-header.navbar { background: #fff !important; border-bottom: 1px solid #f0f0f0 !important;
                          box-shadow: none !important; min-height: 52px !important; }
    .topbar-user-btn { display: flex; align-items: center; gap: 8px; background: none; border: none;
                       cursor: pointer; font-size: 12px; color: #555; padding: 4px 12px; }
    .topbar-user-btn img { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; }

    /* ── Content ─────────────────────────── */
    .content-wrapper { background: #fff !important; }
    .page-title { font-size: 18px; font-weight: 500; color: #1a1a1a; margin: 0; }
    .page-sub   { font-size: 12px; color: #bbb; margin-top: 2px; }

    /* ── Chart grid ──────────────────────── */
    .chart-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #f0f0f0; }
    .chart-cell { background: #fff; padding: 28px 24px; }
    .chart-cell.full { grid-column: 1 / -1; }
    .chart-title { font-size: 11px; font-weight: 500; color: #bbb; letter-spacing: .08em;
                   text-transform: uppercase; margin-bottom: 20px; }

    /* Highcharts overrides */
    .highcharts-background { fill: transparent; }
    .highcharts-title { font-family: inherit !important; font-size: 13px !important; font-weight: 400 !important; fill: #222 !important; }
    .highcharts-axis-labels text { font-size: 11px !important; fill: #aaa !important; }
    .highcharts-credits { display: none; }

    /* ── Footer ──────────────────────────── */
    .main-footer { background: #fff !important; border-top: 1px solid #f0f0f0 !important;
                   font-size: 11px !important; color: #bbb !important; padding: 12px 20px !important; }
    .main-footer a { color: #f58220 !important; }

    @media (max-width: 768px) {
      .chart-grid { grid-template-columns: 1fr; }
      .chart-cell.full { grid-column: 1; }
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
          <i class="fas fa-bars" style="font-size:13px;color:#999"></i>
        </a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto align-items-center">
      <li class="nav-item dropdown">
        <button class="topbar-user-btn dropdown-toggle" data-toggle="dropdown">
          <?php if ($avatar): ?><img src="<?= Input::e($avatar) ?>" alt="avatar">
          <?php else: ?><i class="fas fa-user-circle" style="font-size:22px;color:#ccc"></i><?php endif ?>
          <span><?= $firstname ?></span>
        </button>
        <div class="dropdown-menu dropdown-menu-right border-0" style="border-radius:10px;min-width:160px;font-size:12px;padding:6px;box-shadow:0 4px 20px rgba(0,0,0,.08)">
          <a class="dropdown-item" href="../activity/index.php" style="border-radius:6px;padding:8px 12px"><i class="fas fa-home fa-fw" style="color:#ccc;margin-right:8px"></i> หน้าหลัก</a>
          <a class="dropdown-item" href="../app/auth/profile.php" style="border-radius:6px;padding:8px 12px"><i class="fas fa-user fa-fw" style="color:#ccc;margin-right:8px"></i> โปรไฟล์</a>
        </div>
      </li>
    </ul>
  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar elevation-0">
    <a href="index.php" class="brand-link"><span class="brand-text">YSDN Thailand</span></a>
    <div class="sidebar">
      <div class="sb-user">
        <div class="sb-user-name"><?= $firstname ?> <?= $lastname ?></div>
        <div class="sb-user-code"><?= $memberCode ?></div>
      </div>
      <nav class="mt-1 pb-2" style="flex:1;overflow-y:auto">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-header">หลัก</li>
          <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-home nav-icon"></i><p>ภาพรวม</p></a></li>
          <li class="nav-item"><a href="data.php" class="nav-link"><i class="fas fa-id-card nav-icon"></i><p>ทะเบียนสมาชิก</p></a></li>
          <li class="nav-item"><a href="allUser.php" class="nav-link"><i class="fas fa-users nav-icon"></i><p>จัดการผู้ใช้</p></a></li>
          <li class="nav-item"><a href="chartjquery.php" class="nav-link active"><i class="fas fa-chart-bar nav-icon"></i><p>รายงาน</p></a></li>
          <li class="nav-header">กิจกรรม</li>
          <li class="nav-item"><a href="../activity/all_activity.php" class="nav-link"><i class="fas fa-calendar-alt nav-icon"></i><p>จัดการกิจกรรม</p></a></li>
          <li class="nav-item"><a href="approove_user_activity.php" class="nav-link"><i class="fas fa-clipboard-check nav-icon"></i><p>อนุมัติการลงทะเบียน</p></a></li>
          <li class="nav-header">ระบบ</li>
          <li class="nav-item"><a href="../app/auth/profile.php" class="nav-link"><i class="fas fa-user-circle nav-icon"></i><p>โปรไฟล์ของฉัน</p></a></li>
          <li class="nav-item"><a href="../app/auth/editProfile.php" class="nav-link"><i class="fas fa-sliders-h nav-icon"></i><p>ตั้งค่าบัญชี</p></a></li>
        </ul>
      </nav>
      <div class="sb-footer">
        <a href="/ysdn/app/auth/logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
      </div>
    </div>
  </aside>

  <!-- Content -->
  <div class="content-wrapper">
    <section class="content" style="padding:24px 24px 0">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
          <div class="page-title">รายงาน</div>
          <div class="page-sub">สถิติสมาชิก YSDN</div>
        </div>
      </div>
    </section>

    <div class="chart-grid">

      <!-- Gender -->
      <div class="chart-cell">
        <div class="chart-title">เพศ</div>
        <div id="chart-gender"></div>
      </div>

      <!-- Region -->
      <div class="chart-cell">
        <div class="chart-title">ภูมิภาค</div>
        <div id="chart-type"></div>
      </div>

      <!-- Religion -->
      <div class="chart-cell">
        <div class="chart-title">ศาสนา</div>
        <div id="chart-religion"></div>
      </div>

      <!-- Blood type -->
      <div class="chart-cell">
        <div class="chart-title">กรุ๊ปเลือด</div>
        <div id="chart-blood"></div>
      </div>

      <!-- Level (full width) -->
      <div class="chart-cell full">
        <div class="chart-title">ระดับสมาชิก</div>
        <div style="font-size:11px;color:#bbb;margin-bottom:16px;line-height:1.9">
          LV1 อาสาสมัคร &nbsp;·&nbsp; LV2 แกนนำเยาวชน &nbsp;·&nbsp;
          LV3 พี่เลี้ยงจังหวัด &nbsp;·&nbsp; LV4 นักรณรงค์
        </div>
        <div id="chart-level"></div>
      </div>

    </div>
  </div>

  <footer class="main-footer">
    <span>YSDN Thailand &copy; <?= date('Y') ?></span>
    <span class="float-right">v2.0</span>
  </footer>

  <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.js"></script>
<script>
const commonOpts = {
  chart:   { backgroundColor: 'transparent', style: { fontFamily: 'Inter, Noto Sans Thai, sans-serif' } },
  credits: { enabled: false },
  title:   { text: null },
  exporting:{ enabled: false }
};

// Gender — pie
Highcharts.chart('chart-gender', Object.assign({}, commonOpts, {
  chart: Object.assign({}, commonOpts.chart, { type: 'pie', height: 280 }),
  tooltip: { pointFormat: '<b>{point.y} คน ({point.percentage:.1f}%)</b>' },
  plotOptions: { pie: { allowPointSelect: true, cursor: 'pointer',
    dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.y}', style: { fontSize: '11px', fontWeight: '400' } }
  }},
  series: [{ name: 'สมาชิก', colorByPoint: true, data: [
    <?php $i=0; foreach ($genderAgg as $g => $c): ?>
      { name: '<?= addslashes($g) ?>', y: <?= $c ?>, color: '<?= palette($i++) ?>' },
    <?php endforeach ?>
  ]}]
}));

// Region — bar
Highcharts.chart('chart-type', Object.assign({}, commonOpts, {
  chart: Object.assign({}, commonOpts.chart, { type: 'bar', height: 280 }),
  xAxis: { categories: [<?php foreach ($typeData as $r): ?>'<?= addslashes($r['type']) ?>',<?php endforeach ?>],
           labels: { style: { fontSize: '11px', color: '#aaa' } }, lineColor: '#f0f0f0', tickColor: 'transparent' },
  yAxis: { title: { text: null }, gridLineColor: '#f5f5f5', labels: { style: { color: '#bbb' } } },
  plotOptions: { bar: { dataLabels: { enabled: true, format: '{y}', style: { fontSize: '11px', fontWeight: '300', color: '#888' } } } },
  series: [{ name: 'สมาชิก', data: [
    <?php $i=0; foreach ($typeData as $r): ?>
      { y: <?= (int)$r['count'] ?>, color: '<?= palette($i++) ?>' },
    <?php endforeach ?>
  ], showInLegend: false }]
}));

// Religion — pie
Highcharts.chart('chart-religion', Object.assign({}, commonOpts, {
  chart: Object.assign({}, commonOpts.chart, { type: 'pie', height: 280 }),
  tooltip: { pointFormat: '<b>{point.y} คน ({point.percentage:.1f}%)</b>' },
  plotOptions: { pie: { allowPointSelect: true, cursor: 'pointer',
    dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.y}', style: { fontSize: '11px', fontWeight: '400' } }
  }},
  series: [{ name: 'สมาชิก', colorByPoint: true, data: [
    <?php $i=0; foreach ($religionData as $r): ?>
      { name: '<?= addslashes($r['religion']) ?>', y: <?= (int)$r['count'] ?>, color: '<?= palette($i++) ?>' },
    <?php endforeach ?>
  ]}]
}));

// Blood — column
Highcharts.chart('chart-blood', Object.assign({}, commonOpts, {
  chart: Object.assign({}, commonOpts.chart, { type: 'column', height: 280 }),
  xAxis: { categories: [<?php foreach ($bloodData as $r): ?>'<?= addslashes($r['blood_type']) ?>',<?php endforeach ?>],
           labels: { style: { fontSize: '11px', color: '#aaa' } }, lineColor: '#f0f0f0', tickColor: 'transparent' },
  yAxis: { title: { text: null }, gridLineColor: '#f5f5f5', labels: { style: { color: '#bbb' } } },
  plotOptions: { column: { borderRadius: 3, dataLabels: { enabled: true, format: '{y}', style: { fontSize: '11px', fontWeight: '300', color: '#888' } } } },
  series: [{ name: 'สมาชิก', data: [
    <?php $i=0; foreach ($bloodData as $r): ?>
      { y: <?= (int)$r['count'] ?>, color: '<?= palette($i++) ?>' },
    <?php endforeach ?>
  ], showInLegend: false }]
}));

// Level — column
Highcharts.chart('chart-level', Object.assign({}, commonOpts, {
  chart: Object.assign({}, commonOpts.chart, { type: 'column', height: 260 }),
  xAxis: { categories: [<?php foreach ($levelData as $r): ?>'<?= addslashes($r['level']) ?>',<?php endforeach ?>],
           labels: { style: { fontSize: '11px', color: '#aaa' } }, lineColor: '#f0f0f0', tickColor: 'transparent' },
  yAxis: { title: { text: null }, gridLineColor: '#f5f5f5', labels: { style: { color: '#bbb' } } },
  plotOptions: { column: { borderRadius: 3, dataLabels: { enabled: true, format: '{y}', style: { fontSize: '11px', fontWeight: '300', color: '#888' } } } },
  series: [{ name: 'สมาชิก', data: [
    <?php $i=0; foreach ($levelData as $r): ?>
      { y: <?= (int)$r['count'] ?>, color: '<?= palette($i++) ?>' },
    <?php endforeach ?>
  ], showInLegend: false }]
}));
</script>
</body>
</html>
