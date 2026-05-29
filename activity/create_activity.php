<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style_activity.css">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-icon/favicon.png">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <title>Create Activity</title>

</head>
<?php
require __DIR__ . "../vendor/autoload.php";

?>

<?php include "nav_activity.php"; ?>

<body>
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <?php include 'sidebar.php'; ?>
            </div>

            <!-- Content -->
            <div class="col-md-9">
                <div class="smallid" class="root">
                    <h1 class="header">Create YSDN Activity</h1>
                    <form action="process_create_activity.php" method="post" enctype="multipart/form-data" class="form-container">
                    <?= csrf_field() ?>
                        <!-- เพิ่มฟิลด์ที่ต้องการสร้างกิจกรรม -->
                        <div class="form-group">
                            <label for="activity_name">Activity Name : ชื่อกิจกรรม</label>
                            <input type="text" name="activity_name" id="activity_name" placeholder="กรอกชื่อกิจกรรม" required>
                        </div>
                        <div class="form-group">
                            <label for="activity_date">Activity Date : วันที่เริ่มกิจกรรม ปี ค.ศ </label>
                            <input type="date" name="activity_date" id="activity_date" placeholder="เลือกวันที่เริ่มกิจกรรม" required >
                        </div>
                        <div class="form-group">
                            <label for="activity_description">Activity Description : รายละเอียดกิจกรรม</label>
                            <textarea name="activity_description" id="activity_description" placeholder="กรอกรายละเอียดกิจกรรม" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="activity_byname">Activity By Name : จัดโดย</label>
                            <input type="text" name="activity_byname" id="activity_byname" placeholder="ระบุผู้จัดกิจกรรม" required>
                        </div>
                        <div class="form-group">
                            <label for="activity_category">Activity Category : หมวดหมู่กิจกรรม</label>
                            <select name="activity_category" id="activity_category" required>
                                <?php
                                $categories = $categoryModel->getAllCategories();
                                foreach ($categories as $category) {
                                    echo "<option value=\"{$category['id']}\">{$category['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="activity_cover_image">Activity Cover Image : ภาพปกกิจกรรม</label>
                            <input type="file" name="activity_cover_image" id="activity_cover_image" accept="image/*">
                        </div>
                        <button type="submit" class="submit-btn">สร้างกิจกรรม</button>
                    </form>

                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js"></script>
</body>

</html>