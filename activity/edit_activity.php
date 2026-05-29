<?php
require __DIR__ . "/../vendor/autoload.php";

use App\Model\User;
use App\Model\Activitycms;
use App\Model\CategoryActivity;



$activityModel = new Activitycms();
$categoryModel = new CategoryActivity();


$activityId = $_REQUEST['id'];
$activityData = $activityModel->getActivityById($activityId);

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create YSDN Activity</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_activity.css">
   
</head>
<?php include 'nav_activity.php';?>
<body>
<div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <?php include 'sidebar.php'; ?>
            </div>
            <div class="col-md-9">
<div id="root">
        <h1 class="header">Edit YSDN Activity</h1>
        <form action="process_edit_activity.php" method="post" enctype="multipart/form-data" class="form-container">
        <?= csrf_field() ?>
            <!-- เพิ่มฟิลด์ที่แสดงข้อมูลปัจจุบัน -->
            <input type="hidden" name="activity_id" value="<?php echo isset($activityData['id']) ? $activityData['id'] : ''; ?>">

            <div class="form-group">
                <label for="activity_name">Activity Name : ชื่อกิจกรรม</label>
                <input type="text" name="activity_name" id="activity_name" value="<?php echo $activityData['name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="activity_date">Activity Date : วันที่เริ่มกิจกรรม ปี ค.ศ</label>
                <input type="date" name="activity_date" id="activity_date" value="<?php echo $activityData['date']; ?>" required>
            </div>

            <div class="form-group">
                <label for="activity_description">Activity Description : รายละเอียดกิจกรรม</label>
                <textarea name="activity_description" id="activity_description" required><?php echo $activityData['description']; ?></textarea>
            </div>

            <div class="form-group">
                <label for="activity_byname">Activity By Name : จัดโดย</label>
                <input type="text" name="activity_byname" id="activity_byname" value="<?php echo $activityData['byname']; ?>" required>
            </div>

            <div class="form-group">
                <label for="activity_category">Activity Category : หมวดหมู่กิจกรรม</label>
                <select name="activity_category" id="activity_category" required>
                    <?php
                    $categories = $categoryModel->getAllCategories();
                    foreach ($categories as $category) {
                        $selected = $activityData['category_activity'] == $category['id'] ? "selected" : "";
                        echo "<option value=\"{$category['id']}\" $selected>{$category['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="activity_cover_image">Activity Cover Image : ภาพปกกิจกรรม</label>
                <input type="file" name="activity_cover_image" id="activity_cover_image" accept="image/*">
                <small>Current image: <?php echo $activityData['coverimage']; ?></small>
            </div>

            <button type="submit" class="submit-btn">Update Activity</button>
        </form>
    </div>
</body>

</html>