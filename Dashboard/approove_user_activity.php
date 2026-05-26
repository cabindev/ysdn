<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/ysdn/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/vendor/autoload.php";

use App\Model\User;
use App\Model\Activitycms;
use App\Helper\Input;

$user      = new User();
$userData  = $user->getUserById($_SESSION['id'] ?? '');

$firstname  = Input::e($userData['firstname']   ?? '');
$lastname   = Input::e($userData['lastname']    ?? '');
$memberCode = Input::e($userData['member_code'] ?? '');
$avatar     = $userData['avatar'] ?? '';

$activityModel   = new Activitycms();
$activityId      = isset($_GET['activityId']) ? (int)$_GET['activityId'] : null;
$allActivities   = $activityModel->getAllActivities();
$registeredUsers = $activityModel->getRegisteredUsersForActivity($activityId);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>อนุมัติการลงทะเบียน — YSDN</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&family=Noto+Sans+Thai:wght@300;400;500&display=swap">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
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

    /* ── Filter ──────────────────────────── */
    .activity-select {
      font-size: 12px; border: 1px solid #ebebeb; border-radius: 7px;
      padding: 7px 32px 7px 12px; color: #444; background: #fff;
      appearance: none; -webkit-appearance: none; min-width: 220px; cursor: pointer;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23bbb'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 10px center;
    }
    .activity-select:focus { outline: none; border-color: #f58220; }

    /* ── Toast ───────────────────────────── */
    #toast { position: fixed; bottom: 24px; right: 24px; z-index: 9999;
             background: #1c1c1e; color: #fff; font-size: 12px;
             padding: 10px 18px; border-radius: 8px;
             opacity: 0; transition: opacity .2s; pointer-events: none; }
    #toast.show { opacity: 1; }

    /* ── Table ───────────────────────────── */
    #regTable { border-collapse: collapse; width: 100%; }
    #regTable thead th {
      font-size: 10px !important; font-weight: 500 !important;
      text-transform: uppercase; letter-spacing: .08em;
      color: #c0c0c0 !important; padding: 0 14px 10px !important;
      border: none !important; border-bottom: 1px solid #f0f0f0 !important;
      background: none !important; white-space: nowrap;
    }
    #regTable tbody tr { transition: background .1s; }
    #regTable tbody tr:hover td { background: #fafafa !important; }
    #regTable td { padding: 11px 14px !important; font-size: 12px !important;
                   border: none !important; border-bottom: 1px solid #f5f5f5 !important;
                   vertical-align: middle !important; color: #333; }
    #regTable tbody tr:last-child td { border-bottom: none !important; }

    .badge-status { font-size: 10.5px; padding: 3px 10px; border-radius: 99px; display: inline-block; }
    .badge-approved { background: rgba(22,163,74,.08);  color: #15803d; }
    .badge-pending  { background: rgba(245,130,32,.09); color: #c2621a; }
    .badge-rejected { background: rgba(220,38,38,.08);  color: #dc2626; }

    .status-select {
      font-size: 11.5px; border: 1px solid #ebebeb; border-radius: 6px;
      padding: 4px 8px; color: #444; background: #fff;
      appearance: none; -webkit-appearance: none; cursor: pointer;
    }
    .status-select:focus { outline: none; border-color: #f58220; }

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
          <?php if ($avatar): ?><img src="<?= Input::e($avatar) ?>" alt="avatar">
          <?php else: ?><i class="fas fa-user-circle" style="font-size:22px;color:#ccc"></i><?php endif ?>
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
          <li class="nav-item"><a href="chartjquery.php" class="nav-link"><i class="fas fa-chart-bar nav-icon"></i><p>รายงาน</p></a></li>
          <li class="nav-header">กิจกรรม</li>
          <li class="nav-item"><a href="../activity/all_activity.php" class="nav-link"><i class="fas fa-calendar-alt nav-icon"></i><p>จัดการกิจกรรม</p></a></li>
          <li class="nav-item"><a href="approove_user_activity.php" class="nav-link active"><i class="fas fa-clipboard-check nav-icon"></i><p>อนุมัติการลงทะเบียน</p></a></li>
          <li class="nav-header">ระบบ</li>
          <li class="nav-item"><a href="../ysdn/auth/profile.php" class="nav-link"><i class="fas fa-user-circle nav-icon"></i><p>โปรไฟล์ของฉัน</p></a></li>
          <li class="nav-item"><a href="../ysdn/auth/editProfile.php" class="nav-link"><i class="fas fa-sliders-h nav-icon"></i><p>ตั้งค่าบัญชี</p></a></li>
        </ul>
      </nav>
      <div class="sb-footer">
        <a href="/ysdn_thailand/ysdn/auth/logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
      </div>
    </div>
  </aside>

  <!-- Content -->
  <div class="content-wrapper">
    <section class="content" style="padding:24px">

      <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
          <div class="page-title">อนุมัติการลงทะเบียน</div>
          <div class="page-sub"><?= count($registeredUsers) ?> รายการ<?= $activityId ? ' ในกิจกรรมนี้' : ' ทั้งหมด' ?></div>
        </div>
        <!-- Activity filter -->
        <form method="GET">
          <select name="activityId" class="activity-select" onchange="this.form.submit()">
            <option value="">ทุกกิจกรรม</option>
            <?php foreach ($allActivities as $act): ?>
              <option value="<?= (int)$act['id'] ?>" <?= (int)$act['id'] === $activityId ? 'selected' : '' ?>>
                <?= Input::e($act['name']) ?>
              </option>
            <?php endforeach ?>
          </select>
        </form>
      </div>

      <table id="regTable" class="table">
        <thead>
          <tr>
            <th>ชื่อ-นามสกุล</th>
            <th>อีเมล</th>
            <th>แพ้อาหาร</th>
            <th>ประเภทอาหาร</th>
            <th>ยาที่ใช้</th>
            <th>โรคประจำตัว</th>
            <th>ผู้ปกครอง</th>
            <th>เบอร์ผู้ปกครอง</th>
            <th>สถานะ</th>
            <th>เปลี่ยนสถานะ</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($registeredUsers as $reg):
            $status = $reg['status'] ?? '';
            $badgeCls = match($status) {
              'อนุมัติ'    => 'badge-approved',
              'ไม่อนุมัติ' => 'badge-rejected',
              default      => 'badge-pending',
            };
          ?>
          <tr>
            <td><?= Input::e(($reg['firstname'] ?? '') . ' ' . ($reg['lastname'] ?? '')) ?></td>
            <td style="color:#888"><?= Input::e($reg['email'] ?? '-') ?></td>
            <td><?= Input::e($reg['food_preference'] ?? '-') ?></td>
            <td><?= Input::e($reg['food_type'] ?? '-') ?></td>
            <td style="color:#888"><?= Input::e($reg['medication_type'] ?? '-') ?></td>
            <td style="color:#888"><?= Input::e($reg['medical_condition'] ?? '-') ?></td>
            <td><?= Input::e($reg['guardian_fullname'] ?? '-') ?></td>
            <td><?= Input::e($reg['guardian_phone'] ?? '-') ?></td>
            <td>
              <span class="badge-status <?= $badgeCls ?> status-badge">
                <?= Input::e($status ?: 'รอดำเนินการ') ?>
              </span>
            </td>
            <td>
              <select class="status-select" data-reg-id="<?= (int)$reg['id'] ?>">
                <option value="รอดำเนินการ" <?= ($status === '' || $status === 'รอดำเนินการ') ? 'selected' : '' ?>>รอดำเนินการ</option>
                <option value="อนุมัติ"     <?= $status === 'อนุมัติ'     ? 'selected' : '' ?>>อนุมัติ</option>
                <option value="ไม่อนุมัติ"  <?= $status === 'ไม่อนุมัติ'  ? 'selected' : '' ?>>ไม่อนุมัติ</option>
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
<script src="dist/js/adminlte.js"></script>
<script>
$(function () {
  $("#regTable").DataTable({
    responsive: true,
    autoWidth: false,
    language: {
      search: "ค้นหา:",
      lengthMenu: "แสดง _MENU_ รายการ",
      info: "แสดง _START_–_END_ จาก _TOTAL_ รายการ",
      paginate: { previous: "‹", next: "›" },
      zeroRecords: "ไม่พบข้อมูล",
      emptyTable: "ยังไม่มีข้อมูลการลงทะเบียน"
    },
    columnDefs: [{ orderable: false, targets: [8, 9] }]
  });

  function showToast(msg) {
    const t = $("#toast").text(msg).addClass("show");
    setTimeout(() => t.removeClass("show"), 2000);
  }

  $(document).on("change", ".status-select", function () {
    const regId    = $(this).data("reg-id");
    const newStatus = $(this).val();
    const badge    = $(this).closest("tr").find(".status-badge");

    $.post("process_activityTable_registration.php", {
      action: "updateStatus",
      id: regId,
      newStatus: newStatus,
      csrf_token: $('meta[name="csrf-token"]').attr('content')
    }, function () {
      const cls = newStatus === 'อนุมัติ' ? 'badge-approved' : newStatus === 'ไม่อนุมัติ' ? 'badge-rejected' : 'badge-pending';
      badge.text(newStatus || 'รอดำเนินการ')
           .removeClass('badge-approved badge-pending badge-rejected')
           .addClass(cls);
      showToast("อัปเดตสถานะเป็น "" + newStatus + "" แล้ว");
    }).fail(function () {
      showToast("เกิดข้อผิดพลาด ลองใหม่อีกครั้ง");
    });
  });
});
</script>
</body>
</html>
