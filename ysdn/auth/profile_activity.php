<?php
require $_SERVER['DOCUMENT_ROOT'] . "/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use App\Model\User;
use App\Model\Activitycms;
use App\Helper\Input;

$user     = new User();
$userData = $user->getUserById($_SESSION['id'] ?? '');

$firstname  = Input::e($userData['firstname']   ?? '');
$lastname   = Input::e($userData['lastname']    ?? '');
$memberCode = Input::e($userData['member_code'] ?? '');
$avatar     = $userData['avatar'] ?? '';
$role       = $userData['role'] ?? '';

$activityModel  = new Activitycms();
$userId         = $_SESSION['id'];
$activities     = $activityModel->getActivitiesByUserId($userId);
$approvalStatus = $activityModel->getUserApprovalStatus($userId);

$now  = new DateTime();
$past = $upcoming = [];
foreach ((array)$activities as $act) {
    try {
        $d = DateTime::createFromFormat('Y-m-d', $act['date'] ?? '');
        if ($d && $d < $now) $past[] = $act;
        else $upcoming[] = $act;
    } catch (Exception $e) {
        $past[] = $act;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>กิจกรรมของฉัน — YSDN</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&family=Noto+Sans+Thai:wght@300;400;500&display=swap">
  <link rel="stylesheet" href="../../Dashboard/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../../Dashboard/dist/css/adminlte.min.css">
  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
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

    /* ── Tabs ────────────────────────────── */
    .tab-bar { display: flex; gap: 0; border-bottom: 1px solid #f0f0f0; margin-bottom: 24px; }
    .tab-btn {
      font-size: 12.5px; color: #bbb; background: none; border: none;
      padding: 10px 20px; cursor: pointer; border-bottom: 2px solid transparent;
      margin-bottom: -1px; transition: color .15s;
    }
    .tab-btn:hover { color: #555; }
    .tab-btn.active { color: #1a1a1a; border-bottom-color: #f58220; }
    .tab-count { font-size: 10px; background: #f0f0f0; color: #888;
                 border-radius: 99px; padding: 1px 7px; margin-left: 6px; }
    .tab-btn.active .tab-count { background: rgba(245,130,32,.12); color: #f58220; }

    /* ── Activity list ───────────────────── */
    .act-list { display: none; }
    .act-list.active { display: block; }

    .act-row {
      display: flex; align-items: center; gap: 16px;
      padding: 14px 0; border-bottom: 1px solid #f5f5f5;
    }
    .act-row:last-child { border-bottom: none; }
    .act-icon {
      width: 38px; height: 38px; border-radius: 9px; flex-shrink: 0;
      background: #f5f5f5; display: flex; align-items: center; justify-content: center;
    }
    .act-icon i { font-size: 14px; color: #ccc; }
    .act-name { font-size: 13px; color: #222; font-weight: 400; }
    .act-date { font-size: 11px; color: #bbb; margin-top: 2px; }
    .act-status { margin-left: auto; flex-shrink: 0; }

    .badge-status { font-size: 10.5px; padding: 3px 10px; border-radius: 99px; display: inline-block; }
    .badge-approved { background: rgba(22,163,74,.08);  color: #15803d; }
    .badge-pending  { background: rgba(245,130,32,.09); color: #c2621a; }
    .badge-rejected { background: rgba(220,38,38,.08);  color: #dc2626; }

    .empty-state { text-align: center; padding: 48px 0; color: #ccc; }
    .empty-state i { font-size: 28px; margin-bottom: 12px; display: block; }
    .empty-state p { font-size: 12.5px; margin: 0; }

    /* ── Footer ──────────────────────────── */
    .main-footer { background: #fff !important; border-top: 1px solid #f0f0f0 !important;
                   font-size: 11px !important; color: #bbb !important; padding: 12px 20px !important; }
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
    <ul class="navbar-nav ml-auto align-items-center">
      <li class="nav-item dropdown">
        <button class="topbar-user-btn dropdown-toggle" data-toggle="dropdown">
          <?php if ($avatar): ?><img src="<?= Input::e($avatar) ?>" alt="">
          <?php else: ?><i class="fas fa-user-circle" style="font-size:22px;color:#ccc"></i><?php endif ?>
          <span><?= $firstname ?></span>
        </button>
        <div class="dropdown-menu dropdown-menu-right border-0" style="border-radius:10px;min-width:160px;font-size:12px;padding:6px;box-shadow:0 4px 20px rgba(0,0,0,.08)">
          <a class="dropdown-item" href="../../activity/index.php" style="border-radius:6px;padding:8px 12px"><i class="fas fa-home fa-fw" style="color:#ccc;margin-right:8px"></i> หน้าหลัก</a>
          <a class="dropdown-item" href="profile.php" style="border-radius:6px;padding:8px 12px"><i class="fas fa-user fa-fw" style="color:#ccc;margin-right:8px"></i> โปรไฟล์</a>
        </div>
      </li>
    </ul>
  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar elevation-0">
    <a href="../../Dashboard/index.php" class="brand-link"><span class="brand-text">YSDN Thailand</span></a>
    <div class="sidebar">
      <div class="sb-user">
        <div class="sb-user-name"><?= $firstname ?> <?= $lastname ?></div>
        <div class="sb-user-code"><?= $memberCode ?></div>
      </div>
      <nav class="mt-1 pb-2" style="flex:1;overflow-y:auto">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <?php if ($role === 'admin'): ?>
          <li class="nav-header">Dashboard</li>
          <li class="nav-item"><a href="../../Dashboard/index.php" class="nav-link"><i class="fas fa-home nav-icon"></i><p>ภาพรวม</p></a></li>
          <li class="nav-item"><a href="../../Dashboard/data.php" class="nav-link"><i class="fas fa-id-card nav-icon"></i><p>ทะเบียนสมาชิก</p></a></li>
          <li class="nav-item"><a href="../../Dashboard/allUser.php" class="nav-link"><i class="fas fa-users nav-icon"></i><p>จัดการผู้ใช้</p></a></li>
          <li class="nav-item"><a href="../../Dashboard/chartjquery.php" class="nav-link"><i class="fas fa-chart-bar nav-icon"></i><p>รายงาน</p></a></li>
          <li class="nav-header">กิจกรรม</li>
          <li class="nav-item"><a href="../../activity/all_activity.php" class="nav-link"><i class="fas fa-calendar-alt nav-icon"></i><p>จัดการกิจกรรม</p></a></li>
          <li class="nav-item"><a href="../../Dashboard/approove_user_activity.php" class="nav-link"><i class="fas fa-clipboard-check nav-icon"></i><p>อนุมัติการลงทะเบียน</p></a></li>
          <?php endif ?>
          <li class="nav-header">บัญชีของฉัน</li>
          <li class="nav-item"><a href="profile.php" class="nav-link"><i class="fas fa-user-circle nav-icon"></i><p>โปรไฟล์ของฉัน</p></a></li>
          <li class="nav-item"><a href="editProfile.php" class="nav-link"><i class="fas fa-sliders-h nav-icon"></i><p>ตั้งค่าบัญชี</p></a></li>
          <li class="nav-item"><a href="profile_activity.php" class="nav-link active"><i class="fas fa-calendar-check nav-icon"></i><p>กิจกรรมของฉัน</p></a></li>
        </ul>
      </nav>
      <div class="sb-footer">
        <a href="/auth/logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
      </div>
    </div>
  </aside>

  <!-- Content -->
  <div class="content-wrapper">
    <section class="content" style="padding:24px;max-width:680px">

      <div class="d-flex align-items-baseline justify-content-between mb-4">
        <div>
          <div class="page-title">กิจกรรมของฉัน</div>
          <div class="page-sub">รวม <?= count((array)$activities) ?> กิจกรรมที่ลงทะเบียน</div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="tab-bar">
        <button class="tab-btn active" data-tab="upcoming">
          กำลังจะมาถึง <span class="tab-count"><?= count($upcoming) ?></span>
        </button>
        <button class="tab-btn" data-tab="past">
          ผ่านมาแล้ว <span class="tab-count"><?= count($past) ?></span>
        </button>
      </div>

      <!-- Upcoming -->
      <div class="act-list active" id="tab-upcoming">
        <?php if (empty($upcoming)): ?>
          <div class="empty-state">
            <i class="fas fa-calendar"></i>
            <p>ยังไม่มีกิจกรรมที่กำลังจะมาถึง</p>
          </div>
        <?php else: ?>
          <?php foreach ($upcoming as $act):
            $status = $approvalStatus[$act['id']] ?? ($act['status'] ?? '');
            $badgeCls = match($status) {
              'อนุมัติ'    => 'badge-approved',
              'ไม่อนุมัติ' => 'badge-rejected',
              default      => 'badge-pending',
            };
          ?>
          <div class="act-row">
            <div class="act-icon"><i class="fas fa-calendar-alt"></i></div>
            <div style="min-width:0">
              <div class="act-name"><?= Input::e($act['name'] ?? '') ?></div>
              <div class="act-date">
                <i class="fas fa-clock" style="font-size:10px;margin-right:4px"></i>
                <?= Input::e($act['date'] ?? '-') ?>
              </div>
            </div>
            <div class="act-status">
              <span class="badge-status <?= $badgeCls ?>">
                <?= Input::e($status ?: 'รอดำเนินการ') ?>
              </span>
            </div>
          </div>
          <?php endforeach ?>
        <?php endif ?>
      </div>

      <!-- Past -->
      <div class="act-list" id="tab-past">
        <?php if (empty($past)): ?>
          <div class="empty-state">
            <i class="fas fa-history"></i>
            <p>ยังไม่มีกิจกรรมที่ผ่านมา</p>
          </div>
        <?php else: ?>
          <?php foreach (array_reverse($past) as $act):
            $status = $approvalStatus[$act['id']] ?? ($act['status'] ?? '');
            $badgeCls = match($status) {
              'อนุมัติ'    => 'badge-approved',
              'ไม่อนุมัติ' => 'badge-rejected',
              default      => 'badge-pending',
            };
          ?>
          <div class="act-row">
            <div class="act-icon" style="background:#fafafa"><i class="fas fa-check" style="color:#ddd"></i></div>
            <div style="min-width:0">
              <div class="act-name" style="color:#888"><?= Input::e($act['name'] ?? '') ?></div>
              <div class="act-date"><?= Input::e($act['date'] ?? '-') ?></div>
            </div>
            <div class="act-status">
              <span class="badge-status <?= $badgeCls ?>">
                <?= Input::e($status ?: 'รอดำเนินการ') ?>
              </span>
            </div>
          </div>
          <?php endforeach ?>
        <?php endif ?>
      </div>

    </section>
  </div>

  <footer class="main-footer">
    <span>YSDN Thailand &copy; <?= date('Y') ?></span>
    <span class="float-right">v2.0</span>
  </footer>

  <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<script src="../../Dashboard/plugins/jquery/jquery.min.js"></script>
<script src="../../Dashboard/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../Dashboard/dist/js/adminlte.js"></script>
<script>
document.querySelectorAll(".tab-btn").forEach(function (btn) {
  btn.addEventListener("click", function () {
    document.querySelectorAll(".tab-btn").forEach(b => b.classList.remove("active"));
    document.querySelectorAll(".act-list").forEach(l => l.classList.remove("active"));
    btn.classList.add("active");
    document.getElementById("tab-" + btn.dataset.tab).classList.add("active");
  });
});
</script>
</body>
</html>
