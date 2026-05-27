<?php
require $_SERVER['DOCUMENT_ROOT'] . "/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use App\Model\User;
use App\Model\Activitycms;
use App\Helper\Input;

$user     = new User();
$userData = $user->getUserById($_SESSION['id'] ?? '');

$firstname  = Input::e($userData['firstname']    ?? '');
$lastname   = Input::e($userData['lastname']     ?? '');
$email      = Input::e($userData['email']        ?? '');
$memberCode = Input::e($userData['member_code']  ?? '');
$avatar     = $userData['avatar'] ?? '';
$role       = Input::e($userData['role']         ?? '');
$nickname   = Input::e($userData['nickname']     ?? '');

// Stats
$userCount        = $user->getUserCount();
$activityModel    = new Activitycms();
$activitiesCount  = $activityModel->getAllActivitiesCount();
$registrationCount = array_sum($activityModel->getRegisteredUsersCountForAllActivities());
$admins           = $user->getAllAdmins();

// Greeting
$hour = (int) date('H');
$greeting = match(true) {
    $hour < 12 => 'อรุณสวัสดิ์',
    $hour < 17 => 'สวัสดีตอนบ่าย',
    default    => 'สวัสดีตอนเย็น',
};
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard — YSDN</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&family=Noto+Sans+Thai:wght@300;400;500&display=swap">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon.png">
  <style>
    /* ── Sidebar ─────────────────────────── */
    .main-sidebar { background: #1c1c1e !important; border-right: none; }
    .sidebar { display: flex; flex-direction: column; height: 100%; }

    .brand-link { border-bottom: 1px solid rgba(255,255,255,.05) !important; padding: 16px !important; }
    .brand-text  { font-size: 13px !important; font-weight: 400 !important; color: rgba(255,255,255,.8) !important; }

    .sb-user {
      padding: 14px 16px;
      border-bottom: 1px solid rgba(255,255,255,.05);
    }
    .sb-user-name { font-size: 12px; color: rgba(255,255,255,.75); }
    .sb-user-code { font-size: 11px; color: rgba(255,255,255,.3); margin-top: 2px; }

    .nav-sidebar .nav-item { margin: 0; }
    .nav-sidebar .nav-link {
      font-size: 13px !important; font-weight: 300 !important;
      color: rgba(255,255,255,.45) !important;
      border-radius: 0 !important;
      padding: 9px 16px !important;
      transition: color .15s;
      display: flex !important; align-items: center;
    }
    .nav-sidebar .nav-link i.nav-icon {
      width: 18px; font-size: 12px; text-align: center;
      margin-right: 10px; flex-shrink: 0;
    }
    .nav-sidebar .nav-link p { margin: 0; }
    .nav-sidebar .nav-link:hover  { background: rgba(255,255,255,.04) !important; color: rgba(255,255,255,.8) !important; }
    .nav-sidebar .nav-link.active { background: rgba(255,255,255,.07) !important; color: #fff !important;
                                    border-left: 2px solid #f58220; padding-left: 14px !important; }

    .nav-header {
      font-size: 9px !important; letter-spacing: .12em; text-transform: uppercase;
      color: rgba(255,255,255,.18) !important;
      padding: 16px 16px 5px !important; margin: 0;
    }

    .sb-footer { margin-top: auto; border-top: 1px solid rgba(255,255,255,.05); padding: 8px 0; }
    .sb-footer a {
      display: flex; align-items: center; gap: 10px;
      font-size: 12.5px; color: rgba(255,255,255,.3);
      padding: 9px 16px; text-decoration: none; transition: color .15s;
    }
    .sb-footer a:hover { color: #f87171; }
    .sb-footer a i { font-size: 12px; width: 18px; text-align: center; }

    /* ── Topbar ──────────────────────────── */
    .main-header.navbar {
      background: #fff !important;
      border-bottom: 1px solid #f0f0f0 !important;
      box-shadow: none !important; min-height: 52px !important;
    }
    .topbar-greeting { font-size: 12px; color: #888; margin-left: 12px; }
    .topbar-user-btn {
      display: flex; align-items: center; gap: 8px;
      background: none; border: none; cursor: pointer;
      font-size: 12px; color: #555; padding: 4px 12px;
    }
    .topbar-user-btn img { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; }

    /* ── Content ─────────────────────────── */
    .content-wrapper { background: #fff !important; }

    .page-title { font-size: 18px; font-weight: 500; color: #1a1a1a; margin: 0; }
    .page-sub   { font-size: 12px; color: #bbb; margin-top: 2px; }

    /* ── Stat Cards ──────────────────────── */
    .stat-card {
      background: none;
      border: none;
      border-bottom: 1px solid #f0f0f0;
      border-radius: 0;
      padding: 18px 0;
      display: flex; align-items: center; gap: 16px;
    }
    .stat-icon {
      width: 36px; height: 36px; border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      font-size: 15px; flex-shrink: 0; background: none;
    }
    .stat-icon.orange { color: #f58220; }
    .stat-icon.green  { color: #16a34a; }
    .stat-icon.blue   { color: #2563eb; }
    .stat-icon.purple { color: #7c3aed; }
    .stat-value { font-size: 28px; font-weight: 300; color: #1a1a1a; line-height: 1; }
    .stat-label { font-size: 11px; color: #bbb; margin-top: 4px; letter-spacing: .02em; }
    .stat-link  { font-size: 11px; color: #f58220; text-decoration: none; margin-top: 6px; display: inline-block; }
    .stat-link:hover { opacity: .7; }

    /* ── Section heading ─────────────────── */
    .section-heading {
      font-size: 10px; font-weight: 500; color: #ccc;
      letter-spacing: .08em; text-transform: uppercase;
      margin-bottom: 14px;
    }

    /* ── Admin Chips ─────────────────────── */
    .admin-chip {
      display: flex; flex-direction: column; align-items: center; gap: 6px;
      padding: 0 8px 0 0;
      background: none; border: none; min-width: 60px;
    }
    .admin-chip img {
      width: 38px; height: 38px; border-radius: 50%; object-fit: cover;
      border: 1.5px solid #eee;
    }
    .admin-chip span { font-size: 11px; color: #999; text-align: center; }

    /* ── Activity table ──────────────────── */
    .recent-table { background: none; }
    .recent-table table { margin: 0; border-collapse: collapse; width: 100%; }
    .recent-table thead th {
      font-size: 10px !important; font-weight: 500 !important;
      text-transform: uppercase; letter-spacing: .08em;
      color: #c8c8c8 !important;
      padding: 0 16px 10px !important;
      border: none !important;
      border-bottom: 1px solid #f0f0f0 !important;
      background: none !important;
      white-space: nowrap;
    }
    .recent-table tbody tr { transition: background .1s ease; }
    .recent-table tbody tr:hover td { background: #fafafa !important; }
    .recent-table td {
      padding: 13px 16px !important; font-size: 12.5px !important;
      border: none !important; border-bottom: 1px solid #f5f5f5 !important;
      vertical-align: middle !important; color: #333;
    }
    .recent-table tbody tr:last-child td { border-bottom: none !important; }

    .user-cell { display: flex; align-items: center; gap: 10px; }
    .user-initial {
      width: 30px; height: 30px; border-radius: 50%;
      background: #f0f0f0; display: flex; align-items: center;
      justify-content: center; font-size: 11px; font-weight: 500;
      color: #888; flex-shrink: 0; text-transform: uppercase;
    }
    .user-initial.has-img { background: none; overflow: hidden; }
    .user-initial img { width: 30px; height: 30px; object-fit: cover; }
    .user-fullname { font-size: 12.5px; color: #222; font-weight: 400; }
    .user-subtext  { font-size: 11px; color: #bbb; }

    .badge-status {
      font-size: 10.5px; padding: 3px 10px; border-radius: 99px;
      font-weight: 400; display: inline-block; letter-spacing: .01em;
    }
    .badge-approved { background: rgba(22,163,74,.08);  color: #15803d; }
    .badge-pending  { background: rgba(245,130,32,.09); color: #c2621a; }
    .badge-rejected { background: rgba(220,38,38,.08);  color: #dc2626; }

    .table-footer-link {
      display: flex; align-items: center; justify-content: center;
      padding: 12px; border-top: 1px solid #f5f5f5;
      font-size: 11.5px; color: #bbb; text-decoration: none;
      transition: color .15s;
    }
    .table-footer-link:hover { color: #f58220; }

    /* ── Footer ──────────────────────────── */
    .main-footer {
      background: #fff !important;
      border-top: 1px solid #f0f0f0 !important;
      font-size: 11px !important; color: #bbb !important;
      padding: 12px 20px !important;
    }
    .main-footer a { color: #f58220 !important; }
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
    <span class="topbar-greeting"><?= $greeting ?>, <?= $nickname ?: $firstname ?></span>

    <ul class="navbar-nav ml-auto align-items-center">
      <li class="nav-item dropdown">
        <button class="topbar-user-btn dropdown-toggle" id="userDropdown" data-toggle="dropdown" aria-expanded="false">
          <?php if ($avatar): ?>
            <img src="<?= Input::e($avatar) ?>" alt="avatar">
          <?php else: ?>
            <i class="fas fa-user-circle" style="font-size:22px;color:#ccc"></i>
          <?php endif ?>
          <span><?= $firstname ?></span>
          <span class="badge badge-secondary" style="font-size:9px;background:#f0f0f0;color:#888;border-radius:4px;padding:2px 6px"><?= $role ?></span>
        </button>
        <div class="dropdown-menu dropdown-menu-right border-0" style="border-radius:10px;min-width:160px;font-size:12px;padding:6px;box-shadow:0 4px 20px rgba(0,0,0,.08)">
          <a class="dropdown-item" href="../activity/index.php" style="border-radius:6px;padding:8px 12px"><i class="fas fa-home fa-fw" style="color:#ccc;margin-right:8px"></i> หน้าหลัก</a>
          <a class="dropdown-item" href="../ysdn/auth/profile.php" style="border-radius:6px;padding:8px 12px"><i class="fas fa-user fa-fw" style="color:#ccc;margin-right:8px"></i> โปรไฟล์</a>
        </div>
      </li>
    </ul>
  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar elevation-0">
    <a href="index.php" class="brand-link">
      <span class="brand-text">YSDN Thailand</span>
    </a>
    <div class="sidebar">

      <!-- User panel -->
      <div class="sb-user">
        <div class="sb-user-name"><?= $firstname ?> <?= $lastname ?></div>
        <div class="sb-user-code"><?= $memberCode ?></div>
      </div>

      <!-- Nav -->
      <nav class="mt-1 pb-2" style="flex:1;overflow-y:auto">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

          <li class="nav-header">หลัก</li>

          <li class="nav-item">
            <a href="index.php" class="nav-link active">
              <i class="fas fa-home nav-icon"></i><p>ภาพรวม</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="data.php" class="nav-link">
              <i class="fas fa-id-card nav-icon"></i><p>ทะเบียนสมาชิก</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="allUser.php" class="nav-link">
              <i class="fas fa-users nav-icon"></i><p>จัดการผู้ใช้</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="chartjquery.php" class="nav-link">
              <i class="fas fa-chart-bar nav-icon"></i><p>รายงาน</p>
            </a>
          </li>

          <li class="nav-header">กิจกรรม</li>

          <li class="nav-item">
            <a href="../activity/all_activity.php" class="nav-link">
              <i class="fas fa-calendar-alt nav-icon"></i><p>จัดการกิจกรรม</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="approove_user_activity.php" class="nav-link">
              <i class="fas fa-clipboard-check nav-icon"></i><p>อนุมัติการลงทะเบียน</p>
            </a>
          </li>

          <li class="nav-header">ระบบ</li>

          <li class="nav-item">
            <a href="../ysdn/auth/profile.php" class="nav-link">
              <i class="fas fa-user-circle nav-icon"></i><p>โปรไฟล์ของฉัน</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../ysdn/auth/editProfile.php" class="nav-link">
              <i class="fas fa-sliders-h nav-icon"></i><p>ตั้งค่าบัญชี</p>
            </a>
          </li>

        </ul>
      </nav>

      <!-- Logout -->
      <div class="sb-footer">
        <a href="/auth/logout.php">
          <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
        </a>
      </div>

    </div>
  </aside>

  <!-- Content -->
  <div class="content-wrapper">
    <section class="content" style="padding: 24px">

      <!-- Page title -->
      <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
          <div class="page-title">Dashboard</div>
          <div class="page-sub"><?= date('l, j F Y') ?></div>
        </div>
        <a href="../activity/all_activity.php" class="btn btn-sm" style="background:#f58220;color:#fff;border-radius:8px;font-size:12px;padding:7px 16px;border:none">
          <i class="fas fa-plus me-1"></i> เพิ่มกิจกรรม
        </a>
      </div>

      <!-- Stat cards -->
      <div class="row g-3 mb-4">
        <div class="col-lg-3 col-sm-6 mb-3">
          <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-users"></i></div>
            <div>
              <div class="stat-value"><?= number_format($userCount) ?></div>
              <div class="stat-label">สมาชิกทั้งหมด</div>
              <a href="data.php" class="stat-link">ดูรายชื่อ →</a>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-3">
          <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
            <div>
              <div class="stat-value"><?= number_format($activitiesCount) ?></div>
              <div class="stat-label">กิจกรรมทั้งหมด</div>
              <a href="../activity/all_activity.php" class="stat-link">จัดการ →</a>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-3">
          <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-clipboard-list"></i></div>
            <div>
              <div class="stat-value"><?= number_format($registrationCount) ?></div>
              <div class="stat-label">การลงทะเบียนทั้งหมด</div>
              <a href="approove_user_activity.php" class="stat-link">ดูรายการ →</a>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-3">
          <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-chart-line"></i></div>
            <div>
              <div class="stat-value"><?= count($admins) ?></div>
              <div class="stat-label">ผู้ดูแลระบบ</div>
              <a href="allUser.php" class="stat-link">จัดการ →</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Admin section -->
      <?php if (!empty($admins)): ?>
      <div class="mb-4">
        <div class="section-heading">ผู้ดูแลระบบ</div>
        <div class="d-flex flex-wrap gap-2">
          <?php foreach ($admins as $admin): ?>
            <div class="admin-chip">
              <?php if (!empty($admin['avatar'])): ?>
                <img src="<?= Input::e($admin['avatar']) ?>" alt="admin">
              <?php else: ?>
                <div style="width:40px;height:40px;border-radius:50%;background:#f0f0f0;display:flex;align-items:center;justify-content:center">
                  <i class="fas fa-user" style="color:#ccc;font-size:16px"></i>
                </div>
              <?php endif ?>
              <span><?= Input::e($admin['nickname'] ?: ($admin['firstname'] ?? '')) ?></span>
            </div>
          <?php endforeach ?>
        </div>
      </div>
      <?php endif ?>

      <!-- Recent registrations -->
      <?php
        $recentRegs = $activityModel->getRegisteredUsersAllActivity();
        $recentRegs = array_slice((array) $recentRegs, 0, 10);
      ?>
      <div class="mb-2">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="section-heading mb-0">การลงทะเบียนกิจกรรมล่าสุด</div>
          <a href="approove_user_activity.php" style="font-size:11px;color:#f58220;text-decoration:none">ดูทั้งหมด →</a>
        </div>
        <div class="recent-table">
          <table>
            <thead>
              <tr>
                <th style="width:36%">สมาชิก</th>
                <th>กิจกรรม</th>
                <th>อาหาร</th>
                <th>สถานะ</th>
                <th style="text-align:right">วันที่</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($recentRegs)): ?>
                <tr>
                  <td colspan="5" style="text-align:center;padding:32px 0 !important;color:#ccc;font-size:12px">
                    ยังไม่มีข้อมูลการลงทะเบียน
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($recentRegs as $reg):
                  $fn      = $reg['firstname'] ?? '';
                  $ln      = $reg['lastname']  ?? '';
                  $initial = mb_strtoupper(mb_substr($fn, 0, 1, 'UTF-8'), 'UTF-8');
                  $status  = $reg['status'] ?? '';
                  $badgeCls = match($status) {
                    'อนุมัติ'     => 'badge-approved',
                    'ไม่อนุมัติ'  => 'badge-rejected',
                    default       => 'badge-pending',
                  };
                  $statusLabel = $status ?: 'รอดำเนินการ';
                  $hasAvatar   = !empty($reg['avatar']);
                  $dateStr     = '-';
                  if (!empty($reg['created_at'])) {
                    $ts = strtotime($reg['created_at']);
                    $diff = time() - $ts;
                    $dateStr = $diff < 86400
                      ? ($diff < 3600 ? ceil($diff/60) . ' นาทีที่แล้ว' : ceil($diff/3600) . ' ชม.ที่แล้ว')
                      : date('d M Y', $ts);
                  }
                ?>
                <tr>
                  <td>
                    <div class="user-cell">
                      <div class="user-initial <?= $hasAvatar ? 'has-img' : '' ?>">
                        <?php if ($hasAvatar): ?>
                          <img src="<?= Input::e($reg['avatar']) ?>" alt="">
                        <?php else: ?>
                          <?= $initial ?: '?' ?>
                        <?php endif ?>
                      </div>
                      <div>
                        <div class="user-fullname"><?= Input::e("$fn $ln") ?></div>
                        <?php if (!empty($reg['member_code'])): ?>
                          <div class="user-subtext"><?= Input::e($reg['member_code']) ?></div>
                        <?php endif ?>
                      </div>
                    </div>
                  </td>
                  <td style="color:#555;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    <?= Input::e($reg['activity_name'] ?? $reg['activity_id'] ?? '-') ?>
                  </td>
                  <td style="color:#888"><?= Input::e($reg['food_preference'] ?? '-') ?></td>
                  <td><span class="badge-status <?= $badgeCls ?>"><?= Input::e($statusLabel) ?></span></td>
                  <td style="text-align:right;color:#bbb;white-space:nowrap"><?= $dateStr ?></td>
                </tr>
                <?php endforeach ?>
              <?php endif ?>
            </tbody>
          </table>
          <?php if (!empty($recentRegs)): ?>
            <a href="approove_user_activity.php" class="table-footer-link">ดูรายการทั้งหมด</a>
          <?php endif ?>
        </div>
      </div>

    </section>
  </div>

  <!-- Footer -->
  <footer class="main-footer">
    <span>YSDN Thailand &copy; <?= date('Y') ?></span>
    <span class="float-right">v2.0</span>
  </footer>

  <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="dist/js/adminlte.js"></script>
</body>
</html>
