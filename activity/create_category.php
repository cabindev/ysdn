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
    <title>Create Category-Activity</title>

</head>

<?php
require __DIR__ . "/../vendor/autoload.php";

use App\Model\User;
use App\Model\Activitycms;
use App\Model\CategoryActivity;
// สร้างอ็อบเจกต์ของคลาส User
$user_obj = new User();
$activityModel = new Activitycms();
$categoryModel = new CategoryActivity();

?>

<?php include 'nav_activity.php'; ?>

<body>

    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <?php include 'sidebar.php'; ?>
            </div>

            <!-- Content -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header text-center">
                        <h1>Create New Category Activity</h1>
                    </div>
                    <div class="card-body">
                        <form action="process_create_category.php" enctype="multipart/form-data" method="post">
                        <?= csrf_field() ?>
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark text-center">
                                    <tr>
                                        <th>ชื่อ</th>
                                        <th>ชื่อหมวดหมู่ที่ต้องการ</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><label for="category_name">Category Name:</label></td>
                                        <td>
                                            <input type="text" name="category_name" id="category_name" class="form-control" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="submit" class="submit-btn">Create Category</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                        <table class="table table-bordered table-striped mt-4">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th>ชื่อ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $category_obj = new CategoryActivity();
                                $categories = $category_obj->getAllCategories();
                                foreach ($categories as $category) {
                                    if (isset($category['name'])) {
                                        echo '<tr>';
                                        echo '<td>' . $category['name'] . '</td>';
                                        echo '<td><button onclick="window.location.href=\'edit_category.php?id=' . $category['id'] . '\'" class="custom-btn edit-btn"><i class="fas fa-edit me-2"></i>แก้ไข</button> | <button onclick="window.location.href=\'edit_category.php?delete_id=' . $category['id'] . '\'" class="custom-btn delete-btn"><i class="fas fa-trash me-2"></i> ลบ</button></td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>