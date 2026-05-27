<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn/auth/auth.php";
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use App\Model\User;
use App\Model\Activitycms;
use App\Helper\Input;

$user          = new User();
$userData      = $user->getUserById($_SESSION['id'] ?? '');
$firstname     = Input::e($userData['firstname']   ?? '');
$lastname      = Input::e($userData['lastname']    ?? '');
$memberCode    = Input::e($userData['member_code'] ?? '');
$avatar        = $userData['avatar'] ?? '';

$activityModel = new Activitycms();
$currentPage   = max(1, (int)($_GET['page'] ?? 1));
$itemsPerPage  = 9;
$activities    = $activityModel->getAllActivities($currentPage, $itemsPerPage);
$totalCount    = $activityModel->getAllActivitiesCount();
$totalPages    = max(1, (int)ceil($totalCount / $itemsPerPage));
$regCounts     = $activityModel->getRegisteredUsersCountForAllActivities();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>จัดการกิจกรรม — YSDN</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&family=Noto+Sans+Thai:wght@300;400;500&display=swap">
  <link rel="stylesheet" href="../Dashboard/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../Dashboard/dist/css/adminlte.min.css">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon-icon/favicon.png">
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

    /* ── Activity cards ──────────────────── */
    .act-card {
      border: 1px solid #f0f0f0; border-radius: 10px;
      overflow: hidden; transition: border-color .2s;
      height: 100%;
    }
    .act-card:hover { border-color: #ddd; }
    .act-card-img {
      width: 100%; height: 160px; object-fit: cover;
      background: #f5f5f5; display: block;
    }
    .act-card-img-placeholder {
      width: 100%; height: 160px; background: #f5f5f5;
      display: flex; align-items: center; justify-content: center;
    }
    .act-card-body { padding: 16px; }
    .act-card-title { font-size: 13px; font-weight: 500; color: #1a1a1a; margin-bottom: 10px;
                      white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .act-meta { font-size: 11.5px; color: #999; line-height: 1.9; }
    .act-meta i { width: 14px; color: #ccc; }
    .act-reg-badge {
      display: inline-flex; align-items: center; gap: 5px;
      font-size: 11px; color: #f58220; margin-top: 10px;
    }

    /* ── Pagination ──────────────────────── */
    .pager { display: flex; align-items: center; gap: 4px; margin-top: 32px; justify-content: center; }
    .pager a, .pager span {
      font-size: 12px; padding: 6px 12px; border-radius: 6px;
      text-decoration: none; color: #888; border: 1px solid #f0f0f0;
      transition: all .15s;
    }
    .pager a:hover   { border-color: #ddd; color: #333; }
    .pager span.cur  { background: #f58220; color: #fff; border-color: #f58220; }
    .pager span.dots { border: none; color: #ccc; }

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
          <a class="dropdown-item" href="index.php" style="border-radius:6px;padding:8px 12px"><i class="fas fa-home fa-fw" style="color:#ccc;margin-right:8px"></i> หน้าหลัก</a>
          <a class="dropdown-item" href="../ysdn/auth/profile.php" style="border-radius:6px;padding:8px 12px"><i class="fas fa-user fa-fw" style="color:#ccc;margin-right:8px"></i> โปรไฟล์</a>
        </div>
      </li>
    </ul>
  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar elevation-0">
    <a href="../Dashboard/index.php" class="brand-link"><span class="brand-text">YSDN Thailand</span></a>
    <div class="sidebar">
      <div class="sb-user">
        <div class="sb-user-name"><?= $firstname ?> <?= $lastname ?></div>
        <div class="sb-user-code"><?= $memberCode ?></div>
      </div>
      <nav class="mt-1 pb-2" style="flex:1;overflow-y:auto">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-header">หลัก</li>
          <li class="nav-item"><a href="../Dashboard/index.php" class="nav-link"><i class="fas fa-home nav-icon"></i><p>ภาพรวม</p></a></li>
          <li class="nav-item"><a href="../Dashboard/data.php" class="nav-link"><i class="fas fa-id-card nav-icon"></i><p>ทะเบียนสมาชิก</p></a></li>
          <li class="nav-item"><a href="../Dashboard/allUser.php" class="nav-link"><i class="fas fa-users nav-icon"></i><p>จัดการผู้ใช้</p></a></li>
          <li class="nav-item"><a href="../Dashboard/chartjquery.php" class="nav-link"><i class="fas fa-chart-bar nav-icon"></i><p>รายงาน</p></a></li>
          <li class="nav-header">กิจกรรม</li>
          <li class="nav-item"><a href="all_activity.php" class="nav-link active"><i class="fas fa-calendar-alt nav-icon"></i><p>จัดการกิจกรรม</p></a></li>
          <li class="nav-item"><a href="../Dashboard/approove_user_activity.php" class="nav-link"><i class="fas fa-clipboard-check nav-icon"></i><p>อนุมัติการลงทะเบียน</p></a></li>
          <li class="nav-header">ระบบ</li>
          <li class="nav-item"><a href="../ysdn/auth/profile.php" class="nav-link"><i class="fas fa-user-circle nav-icon"></i><p>โปรไฟล์ของฉัน</p></a></li>
          <li class="nav-item"><a href="../ysdn/auth/editProfile.php" class="nav-link"><i class="fas fa-sliders-h nav-icon"></i><p>ตั้งค่าบัญชี</p></a></li>
        </ul>
      </nav>
      <div class="sb-footer">
        <a href="/ysdn/auth/logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
      </div>
    </div>
  </aside>

  <!-- Content -->
  <div class="content-wrapper">
    <section class="content" style="padding:24px">

      <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
          <div class="page-title">จัดการกิจกรรม</div>
          <div class="page-sub">ทั้งหมด <?= number_format($totalCount) ?> กิจกรรม</div>
        </div>
        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
        <a href="create_activity.php" class="btn btn-sm" style="background:#f58220;color:#fff;border-radius:8px;font-size:12px;padding:7px 16px;border:none">
          <i class="fas fa-plus" style="margin-right:6px"></i> เพิ่มกิจกรรม
        </a>
        <?php endif ?>
      </div>

      <!-- Cards -->
      <div class="row">
        <?php foreach ($activities as $act):
          $aid   = $act['id'];
          $regs  = $regCounts[$aid] ?? 0;
          $img   = !empty($act['coverimage']) ? 'images/' . $act['coverimage'] : null;
        ?>
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="act-card">
            <?php if ($img): ?>
              <img src="<?= Input::e($img) ?>" class="act-card-img" alt="">
            <?php else: ?>
              <div class="act-card-img-placeholder">
                <i class="fas fa-image" style="font-size:28px;color:#ddd"></i>
              </div>
            <?php endif ?>
            <div class="act-card-body">
              <div class="act-card-title"><?= Input::e($act['name']) ?></div>
              <div class="act-meta">
                <div><i class="fas fa-calendar-alt"></i> <?= Input::e($act['date'] ?? '-') ?></div>
                <div><i class="fas fa-tags"></i> <?= Input::e($act['category_name'] ?? '-') ?></div>
                <div><i class="fas fa-user-edit"></i> <?= Input::e($act['byname'] ?? '-') ?></div>
              </div>
              <div class="act-reg-badge">
                <i class="fas fa-users"></i> <?= number_format($regs) ?> คนลงทะเบียน
              </div>
              <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
              <div class="mt-3 d-flex gap-2" style="gap:8px">
                <a href="edit_activity.php?id=<?= (int)$aid ?>" style="font-size:11px;color:#888;text-decoration:none">
                  <i class="fas fa-pen" style="margin-right:4px"></i>แก้ไข
                </a>
              </div>
              <?php endif ?>
            </div>
          </div>
        </div>
        <?php endforeach ?>
      </div>

      <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
      <div class="pager">
        <?php if ($currentPage > 1): ?>
          <a href="?page=<?= $currentPage - 1 ?>">‹</a>
        <?php endif ?>
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
          <?php if ($p === $currentPage): ?>
            <span class="cur"><?= $p ?></span>
          <?php elseif ($p <= 2 || $p >= $totalPages - 1 || abs($p - $currentPage) <= 1): ?>
            <a href="?page=<?= $p ?>"><?= $p ?></a>
          <?php elseif (abs($p - $currentPage) === 2): ?>
            <span class="dots">…</span>
          <?php endif ?>
        <?php endfor ?>
        <?php if ($currentPage < $totalPages): ?>
          <a href="?page=<?= $currentPage + 1 ?>">›</a>
        <?php endif ?>
      </div>
      <?php endif ?>

    </section>
  </div>

  <footer class="main-footer">
    <span>YSDN Thailand &copy; <?= date('Y') ?></span>
    <span class="float-right">v2.0</span>
  </footer>

  <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<script src="../Dashboard/plugins/jquery/jquery.min.js"></script>
<script src="../Dashboard/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../Dashboard/dist/js/adminlte.js"></script>
</body>
</html>
