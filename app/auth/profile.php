<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn/app/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/ysdn/vendor/autoload.php";

use App\Model\User;
use App\Helper\Input;

$user     = new User();
$userData = $user->getUserById($_SESSION['id'] ?? '');

$firstname  = Input::e($userData['firstname']   ?? '');
$lastname   = Input::e($userData['lastname']    ?? '');
$nickname   = Input::e($userData['nickname']    ?? '');
$email      = Input::e($userData['email']       ?? '');
$memberCode = Input::e($userData['member_code'] ?? '');
$avatar     = $userData['avatar'] ?? '';
$role       = Input::e($userData['role']        ?? '');
$type       = Input::e($userData['type']        ?? '');
$level      = Input::e($userData['level']       ?? '');

$age = '-';
if (!empty($userData['dob'])) {
    try {
        $dob     = new DateTime($userData['dob']);
        $age     = $dob->diff(new DateTime())->y . ' ปี';
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>โปรไฟล์ — YSDN</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&family=Noto+Sans+Thai:wght@300;400;500&display=swap">
  <link rel="stylesheet" href="../../Dashboard/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../../Dashboard/dist/css/adminlte.min.css">
  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon.png">
  <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
  <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
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

    /* ── Member card ─────────────────────── */
    .member-card {
      max-width: 360px;
      border: 1px solid #f0f0f0; border-radius: 14px; overflow: hidden;
      padding: 32px 24px 24px;
      text-align: center;
    }
    .member-avatar-wrap { position: relative; display: inline-block; margin-bottom: 16px; }
    .member-avatar {
      width: 88px; height: 88px; border-radius: 50%; object-fit: cover;
      border: 2px solid #f0f0f0;
    }
    .member-role-dot {
      position: absolute; bottom: 4px; right: 4px;
      width: 14px; height: 14px; border-radius: 50%;
      background: #f58220; border: 2px solid #fff;
    }
    .member-name { font-size: 16px; font-weight: 500; color: #1a1a1a; }
    .member-nick { font-size: 12px; color: #bbb; margin-top: 2px; }
    .member-code { font-size: 11px; color: #ccc; margin-top: 6px; letter-spacing: .04em; }

    .member-stats {
      display: flex; justify-content: center; gap: 28px;
      margin: 20px 0; padding: 16px 0;
      border-top: 1px solid #f5f5f5; border-bottom: 1px solid #f5f5f5;
    }
    .member-stat-val { font-size: 15px; font-weight: 400; color: #333; }
    .member-stat-lbl { font-size: 10px; color: #ccc; margin-top: 2px; letter-spacing: .04em; }

    #qrcode img { margin: 0 auto; }

    .btn-download {
      display: inline-flex; align-items: center; gap: 7px;
      margin-top: 16px; font-size: 12px; color: #bbb;
      background: none; border: 1px solid #ebebeb;
      border-radius: 8px; padding: 7px 16px; cursor: pointer;
      transition: all .15s; text-decoration: none;
    }
    .btn-download:hover { border-color: #ccc; color: #555; }

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
          <a class="dropdown-item" href="editProfile.php" style="border-radius:6px;padding:8px 12px"><i class="fas fa-pen fa-fw" style="color:#ccc;margin-right:8px"></i> แก้ไขข้อมูล</a>
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
          <li class="nav-item"><a href="profile.php" class="nav-link active"><i class="fas fa-user-circle nav-icon"></i><p>โปรไฟล์ของฉัน</p></a></li>
          <li class="nav-item"><a href="editProfile.php" class="nav-link"><i class="fas fa-sliders-h nav-icon"></i><p>ตั้งค่าบัญชี</p></a></li>
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
    <section class="content" style="padding:24px">

      <div class="mb-4">
        <div class="page-title">โปรไฟล์ของฉัน</div>
      </div>

      <div id="member-card-wrap">
        <div class="member-card">
          <div class="member-avatar-wrap">
            <?php if ($avatar): ?>
              <img src="<?= Input::e($avatar) ?>" class="member-avatar" alt="avatar">
            <?php else: ?>
              <div class="member-avatar" style="background:#f5f5f5;display:flex;align-items:center;justify-content:center">
                <i class="fas fa-user" style="font-size:32px;color:#ddd"></i>
              </div>
            <?php endif ?>
            <div class="member-role-dot" title="<?= $role ?>"></div>
          </div>

          <div class="member-name"><?= $firstname ?> <?= $lastname ?></div>
          <?php if ($nickname): ?>
            <div class="member-nick"><?= $nickname ?></div>
          <?php endif ?>
          <div class="member-code"><?= $memberCode ?></div>

          <div class="member-stats">
            <div>
              <div class="member-stat-val"><?= $age ?></div>
              <div class="member-stat-lbl">อายุ</div>
            </div>
            <div>
              <div class="member-stat-val"><?= $level ?: '-' ?></div>
              <div class="member-stat-lbl">ระดับ</div>
            </div>
            <div>
              <div class="member-stat-val" style="font-size:12px"><?= $type ?: '-' ?></div>
              <div class="member-stat-lbl">ภาค</div>
            </div>
          </div>

          <div id="qrcode" style="margin-bottom:4px"></div>
          <div style="font-size:10px;color:#ccc;margin-bottom:8px"><?= $memberCode ?></div>

          <button class="btn-download" id="btn-download">
            <i class="fas fa-download"></i> บันทึกบัตรสมาชิก
          </button>
        </div>
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
new QRCode(document.getElementById("qrcode"), {
  text: "<?= addslashes($userData['member_code'] ?? '') ?>",
  width: 120, height: 120, colorDark: "#1a1a1a", colorLight: "#ffffff"
});

document.getElementById("btn-download").addEventListener("click", function () {
  html2canvas(document.querySelector(".member-card"), { scale: 3, backgroundColor: "#fff" }).then(function (canvas) {
    const link = document.createElement("a");
    link.download = "ysdn-member-<?= addslashes($userData['member_code'] ?? 'card') ?>.png";
    link.href = canvas.toDataURL("image/png");
    link.click();
  });
});
</script>
</body>
</html>
