<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/ysdn/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/vendor/autoload.php";

use App\Model\User;
use App\Helper\Input;

$user     = new User();
$userData = $user->getUserById($_SESSION['id'] ?? '');

$firstname  = Input::e($userData['firstname']   ?? '');
$lastname   = Input::e($userData['lastname']    ?? '');
$memberCode = Input::e($userData['member_code'] ?? '');
$avatar     = $userData['avatar'] ?? '';

$userDatas = $user->getAllUser();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>จัดการผู้ใช้ — YSDN</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&family=Noto+Sans+Thai:wght@300;400;500&display=swap">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon.png">
  <meta name="csrf-token" content="<?= csrf_token() ?>">
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
    .nav-header {
      font-size: 9px !important; letter-spacing: .12em; text-transform: uppercase;
      color: rgba(255,255,255,.18) !important; padding: 16px 16px 5px !important; margin: 0;
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
      background: #fff !important; border-bottom: 1px solid #f0f0f0 !important;
      box-shadow: none !important; min-height: 52px !important;
    }
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

    /* ── Toast ───────────────────────────── */
    #toast {
      position: fixed; bottom: 24px; right: 24px; z-index: 9999;
      background: #1c1c1e; color: #fff; font-size: 12px;
      padding: 10px 18px; border-radius: 8px;
      opacity: 0; transition: opacity .2s; pointer-events: none;
    }
    #toast.show { opacity: 1; }

    /* ── Table ───────────────────────────── */
    #userTable { border-collapse: collapse; width: 100%; }
    #userTable thead th {
      font-size: 10px !important; font-weight: 500 !important;
      text-transform: uppercase; letter-spacing: .08em;
      color: #c0c0c0 !important; padding: 0 14px 10px !important;
      border: none !important; border-bottom: 1px solid #f0f0f0 !important;
      background: none !important; white-space: nowrap;
    }
    #userTable tbody tr { transition: background .1s; }
    #userTable tbody tr:hover td { background: #fafafa !important; }
    #userTable td {
      padding: 11px 14px !important; font-size: 12px !important;
      border: none !important; border-bottom: 1px solid #f5f5f5 !important;
      vertical-align: middle !important; color: #333;
    }
    #userTable tbody tr:last-child td { border-bottom: none !important; }

    .member-avatar   { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; }
    .member-initial  {
      width: 30px; height: 30px; border-radius: 50%;
      background: #f0f0f0; display: inline-flex; align-items: center;
      justify-content: center; font-size: 11px; color: #aaa; text-transform: uppercase;
    }

    .badge-role {
      font-size: 10.5px; padding: 3px 10px; border-radius: 99px; display: inline-block;
    }
    .badge-admin  { background: rgba(245,130,32,.1); color: #c2621a; }
    .badge-member { background: #f5f5f5; color: #888; }

    .role-select {
      font-size: 11.5px; border: 1px solid #ebebeb; border-radius: 6px;
      padding: 4px 8px; color: #444; background: #fff;
      appearance: none; -webkit-appearance: none; cursor: pointer;
    }
    .role-select:focus { outline: none; border-color: #f58220; }

    /* DataTables overrides */
    .dataTables_wrapper .dataTables_filter input {
      font-size: 12px; border: 1px solid #ebebeb; border-radius: 7px; padding: 5px 10px; outline: none;
    }
    .dataTables_wrapper .dataTables_length select {
      font-size: 12px; border: 1px solid #ebebeb; border-radius: 7px; padding: 4px 8px;
    }
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate { font-size: 12px; color: #bbb; }
    .dataTables_wrapper .paginate_button { border-radius: 6px !important; font-size: 12px !important; }
    .dataTables_wrapper .paginate_button.current { background: #f58220 !important; border-color: #f58220 !important; color: #fff !important; }

    /* ── Footer ──────────────────────────── */
    .main-footer {
      background: #fff !important; border-top: 1px solid #f0f0f0 !important;
      font-size: 11px !important; color: #bbb !important; padding: 12px 20px !important;
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
    <ul class="navbar-nav ml-auto align-items-center">
      <li class="nav-item dropdown">
        <button class="topbar-user-btn dropdown-toggle" id="userDropdown" data-toggle="dropdown" aria-expanded="false">
          <?php if ($avatar): ?>
            <img src="<?= Input::e($avatar) ?>" alt="avatar">
          <?php else: ?>
            <i class="fas fa-user-circle" style="font-size:22px;color:#ccc"></i>
          <?php endif ?>
          <span><?= $firstname ?></span>
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
      <div class="sb-user">
        <div class="sb-user-name"><?= $firstname ?> <?= $lastname ?></div>
        <div class="sb-user-code"><?= $memberCode ?></div>
      </div>
      <nav class="mt-1 pb-2" style="flex:1;overflow-y:auto">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-header">หลัก</li>
          <li class="nav-item">
            <a href="index.php" class="nav-link">
              <i class="fas fa-home nav-icon"></i><p>ภาพรวม</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="data.php" class="nav-link">
              <i class="fas fa-id-card nav-icon"></i><p>ทะเบียนสมาชิก</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="allUser.php" class="nav-link active">
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
      <div class="sb-footer">
        <a href="/ysdn_thailand/ysdn/auth/logout.php">
          <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
        </a>
      </div>
    </div>
  </aside>

  <!-- Content -->
  <div class="content-wrapper">
    <section class="content" style="padding:24px">

      <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
          <div class="page-title">จัดการผู้ใช้</div>
          <div class="page-sub">ผู้ใช้ทั้งหมด <?= count($userDatas) ?> คน</div>
        </div>
      </div>

      <table id="userTable" class="table">
        <thead>
          <tr>
            <th></th>
            <th>ชื่อ-นามสกุล</th>
            <th>รหัสสมาชิก</th>
            <th>Email</th>
            <th>เบอร์โทร</th>
            <th>ภาค</th>
            <th>จังหวัด</th>
            <th>Role</th>
            <th>เปลี่ยน Role</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($userDatas as $row):
            $fn      = $row['firstname'] ?? '';
            $initial = mb_strtoupper(mb_substr($fn, 0, 1, 'UTF-8'), 'UTF-8');
            $userRole = $row['role'] ?? 'member';
          ?>
          <tr>
            <td style="width:46px">
              <?php if (!empty($row['avatar'])): ?>
                <img src="<?= Input::e($row['avatar']) ?>" class="member-avatar" alt="">
              <?php else: ?>
                <span class="member-initial"><?= $initial ?: '?' ?></span>
              <?php endif ?>
            </td>
            <td><?= Input::e($fn . ' ' . ($row['lastname'] ?? '')) ?></td>
            <td style="color:#888;font-size:11px"><?= Input::e($row['member_code'] ?? '-') ?></td>
            <td style="color:#888"><?= Input::e($row['email'] ?? '-') ?></td>
            <td><?= Input::e($row['phone'] ?? '-') ?></td>
            <td><?= Input::e($row['type'] ?? '-') ?></td>
            <td><?= Input::e($row['province'] ?? '-') ?></td>
            <td>
              <span class="badge-role <?= $userRole === 'admin' ? 'badge-admin' : 'badge-member' ?>">
                <?= Input::e($userRole) ?>
              </span>
            </td>
            <td>
              <select class="role-select" data-user-id="<?= (int)$row['id'] ?>">
                <option value="member" <?= $userRole === 'member' ? 'selected' : '' ?>>Member</option>
                <option value="admin"  <?= $userRole === 'admin'  ? 'selected' : '' ?>>Admin</option>
              </select>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>

    </section>
  </div>

  <footer class="main-footer">
    <span>YSDN Thailand &copy; <?= date('Y') ?></span>
    <span class="float-right">v2.0</span>
  </footer>

  <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<div id="toast"></div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
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
<script src="dist/js/adminlte.js"></script>
<script>
$(function () {
  $("#userTable").DataTable({
    responsive: true,
    autoWidth: false,
    language: {
      search: "ค้นหา:",
      lengthMenu: "แสดง _MENU_ รายการ",
      info: "แสดง _START_–_END_ จาก _TOTAL_ รายการ",
      paginate: { previous: "‹", next: "›" },
      zeroRecords: "ไม่พบข้อมูล",
      emptyTable: "ไม่มีข้อมูล"
    },
    columnDefs: [{ orderable: false, targets: [0, 7, 8] }]
  });

  function showToast(msg) {
    const t = $("#toast").text(msg).addClass("show");
    setTimeout(() => t.removeClass("show"), 2000);
  }

  $(document).on("change", ".role-select", function () {
    const userId   = $(this).data("user-id");
    const newRole  = $(this).val();
    const badgeEl  = $(this).closest("tr").find(".badge-role");

    $.post("process_updateStatus_user.php", {
      action: "updateStatus",
      id: userId,
      newStatus: newRole,
      csrf_token: $('meta[name="csrf-token"]').attr('content')
    }, function () {
      badgeEl
        .text(newRole)
        .removeClass("badge-admin badge-member")
        .addClass(newRole === "admin" ? "badge-admin" : "badge-member");
      showToast("เปลี่ยน role เป็น " + newRole + " แล้ว");
    }).fail(function () {
      showToast("เกิดข้อผิดพลาด ลองใหม่อีกครั้ง");
    });
  });
});
</script>
</body>
</html>
