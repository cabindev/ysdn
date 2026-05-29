<?php

require_once __DIR__ . "../vendor/autoload.php";

use App\Database\Db;
use App\Model\User;
use App\Model\Activitycms;
use App\Model\CategoryActivity;

$categoryModel = new CategoryActivity();
$activityModel = new Activitycms();

$categories = $categoryModel->getAllCategories();
$totalCategory = count($categories);

$activitiesCount = $activityModel->getAllActivitiesCount();

    // รับ URL ปัจจุบัน
    $current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="list-group">
    <a href="all_activity.php" class="list-group-item list-group-item-action list-group-item-warning <?php echo ($current_page == 'all_activity.php') ? 'active' : ''; ?>">กิจกรรมทั้งหมด</a>
    <a href="create_category.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'create_category.php') ? 'active' : ''; ?>">สร้างหมวดหมู่ (<?php echo $totalCategory; ?>)</a>
    <a href="edit_category.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'edit_category.php') ? 'active' : ''; ?>">แก้ไขหมวดหมู่</a>
    <a href="create_activity.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'create_activity.php') ? 'active' : ''; ?>">สร้างกิจกรรม (<?php echo $activitiesCount; ?>)</a>
    <a href="all_edit_activity.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'all_edit_activity.php') ? 'active' : ''; ?>">แก้ไขกิจกรรม</a>
</div>
