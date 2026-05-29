<?php
require __DIR__ . "../../app/auth/auth.php";
require_once __DIR__ . "../../vendor/autoload.php";

use App\Model\User;
use App\Helper\Input;

$user     = new User();
$userData = $user->getUserById($_SESSION['id'] ?? '');

$firstname  = Input::e($userData['firstname']   ?? '');
$lastname   = Input::e($userData['lastname']    ?? '');
$memberCode = Input::e($userData['member_code'] ?? '');
$avatar     = $userData['avatar'] ?? '';
$role       = $userData['role'] ?? '';
$editLink   = $role === 'admin' ? 'edit-form-Admin.php' : 'edit-form-User.php';
$editUrl    = $editLink . '?id=' . (int)($userData['id'] ?? 0) . '&action=edit';

function row(string $label, $val): string {
    $v = htmlspecialchars((string)($val ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    return "<div class=\"info-row\"><span class=\"info-label\">{$label}</span><span class=\"info-value\">" . ($v !== '' ? $v : '<span style=\"color:#ddd\">—</span>') . "</span></div>";
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ตั้งค่าบัญชี — YSDN</title>
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

    /* ── Profile header ──────────────────── */
    .profile-header {
      display: flex; align-items: center; gap: 20px;
      padding-bottom: 24px; border-bottom: 1px solid #f0f0f0; margin-bottom: 28px;
    }
    .profile-avatar {
      width: 68px; height: 68px; border-radius: 50%; object-fit: cover;
      border: 1.5px solid #ebebeb; flex-shrink: 0;
    }
    .profile-avatar-placeholder {
      width: 68px; height: 68px; border-radius: 50%; background: #f5f5f5;
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .profile-hname { font-size: 16px; font-weight: 500; color: #1a1a1a; }
    .profile-hcode { font-size: 12px; color: #bbb; margin-top: 3px; }

    .btn-edit {
      margin-left: auto; display: inline-flex; align-items: center; gap: 7px;
      font-size: 12px; background: #f58220; color: #fff;
      border: none; border-radius: 8px; padding: 8px 18px;
      text-decoration: none; cursor: pointer; transition: opacity .15s;
    }
    .btn-edit:hover { opacity: .88; color: #fff; }

    /* ── Info sections ───────────────────── */
    .section-title {
      font-size: 9.5px; font-weight: 500; color: #ccc;
      letter-spacing: .1em; text-transform: uppercase;
      margin: 0 0 12px;
    }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; margin-bottom: 28px; }
    .info-row {
      display: flex; flex-direction: column;
      padding: 12px 0; border-bottom: 1px solid #f5f5f5;
    }
    .info-label { font-size: 10.5px; color: #bbb; margin-bottom: 3px; }
    .info-value { font-size: 13px; color: #333; }

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
          <li class="nav-item"><a href="editProfile.php" class="nav-link active"><i class="fas fa-sliders-h nav-icon"></i><p>ตั้งค่าบัญชี</p></a></li>
          <li class="nav-item"><a href="profile_activity.php" class="nav-link"><i class="fas fa-calendar-check nav-icon"></i><p>กิจกรรมของฉัน</p></a></li>
        </ul>
      </nav>
      <div class="sb-footer">
        <a href="/ysdn/app/auth/logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
      </div>
    </div>
  </aside>

  <!-- Content -->
  <div class="content-wrapper">
    <section class="content" style="padding:24px;max-width:760px">

      <!-- Header -->
      <div class="profile-header">
        <?php if ($avatar): ?>
          <img src="<?= Input::e($avatar) ?>" class="profile-avatar" alt="">
        <?php else: ?>
          <div class="profile-avatar-placeholder">
            <i class="fas fa-user" style="font-size:26px;color:#ddd"></i>
          </div>
        <?php endif ?>
        <div>
          <div class="profile-hname"><?= $firstname ?> <?= $lastname ?></div>
          <div class="profile-hcode"><?= $memberCode ?></div>
        </div>
        <a href="<?= Input::e($editUrl) ?>" class="btn-edit">
          <i class="fas fa-pen"></i> แก้ไขข้อมูล
        </a>
      </div>

      <!-- Personal info -->
      <div class="section-title">ข้อมูลส่วนตัว</div>
      <div class="info-grid">
        <?= row('ชื่อ', $userData['firstname'] ?? '') ?>
        <?= row('นามสกุล', $userData['lastname'] ?? '') ?>
        <?= row('ชื่อเล่น', $userData['nickname'] ?? '') ?>
        <?= row('วันเกิด', $userData['dob'] ?? '') ?>
        <?= row('เพศ', $userData['gender_id'] ?? '') ?>
        <?= row('ศาสนา', $userData['religion'] ?? '') ?>
        <?= row('กรุ๊ปเลือด', $userData['blood_type'] ?? '') ?>
        <?= row('ระดับ', $userData['level'] ?? '') ?>
      </div>

      <!-- Contact -->
      <div class="section-title">ข้อมูลติดต่อ</div>
      <div class="info-grid">
        <?= row('Email', $userData['email'] ?? '') ?>
        <?= row('เบอร์โทร', $userData['phone'] ?? '') ?>
        <?= row('เลขบัตรประชาชน', $userData['citizen_id'] ?? '') ?>
        <?= row('รหัสสมาชิก', $userData['member_code'] ?? '') ?>
      </div>

      <!-- Address -->
      <div class="section-title">ที่อยู่</div>
      <div class="info-grid">
        <?= row('ที่อยู่', $userData['address'] ?? '') ?>
        <?= row('ตำบล', $userData['district'] ?? '') ?>
        <?= row('อำเภอ', $userData['amphoe'] ?? '') ?>
        <?= row('จังหวัด', $userData['province'] ?? '') ?>
        <?= row('รหัสไปรษณีย์', $userData['zipcode'] ?? '') ?>
        <?= row('ภาค', $userData['type'] ?? '') ?>
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
</body>
</html>
