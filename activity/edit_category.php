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
    <title>Edit Category Activity</title>

</head>
<?php
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use App\Model\CategoryActivity;

$category_obj = new CategoryActivity();

if (isset($_GET['result'])) {
    $result = $_GET['result'];
    if ($result === 'success') {
        echo '<div class="alert alert-success mt-3">Category updated successfully.</div>';
    } elseif ($result === 'error') {
        echo '<div class="alert alert-danger mt-3">An error occurred while updating the category.</div>';
    }
}
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $result = $category_obj->deleteCategory($delete_id);
    if ($result) {
        echo '<div class="alert alert-success mt-3">Category deleted successfully.</div>';
    } else {
        echo '<div class="alert alert-danger mt-3">An error occurred while deleting the category.</div>';
    }
}

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
            <h1 class="text-center">Edit Category Activity</h1>
            <!-- รายการหมวดหมู่กิจกรรมทั้งหมด -->
            <h2>All Categories</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // ดึงรายการหมวดหมู่กิจกรรมจากฐานข้อมูลและแสดงผล
                    $categories = $category_obj->getAllCategories();
                    foreach ($categories as $category) {
                        if (isset($category['name'])) { // ตรวจสอบว่า category_name มีใน $category หรือไม่
                            echo '<tr>';
                            echo '<td>' . $category['name'] . '</td>';
                            echo '<td><button onclick="window.location.href=\'edit_category.php?id=' . $category['id'] . '\'" class="custom-btn edit-btn"><i class="fas fa-edit me-2"></i>แก้ไข</button> | <button onclick="window.location.href=\'edit_category.php?delete_id=' . $category['id'] . '\'" class="custom-btn delete-btn"><i class="fas fa-trash me-2"></i> ลบ</button></td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
            <!-- แก้ไขหมวดหมู่กิจกรรม -->
            <?php
            if (isset($_GET['id'])) {
                $category_id = $_GET['id'];
                $category = $category_obj->getCategoryById($category_id);
                if ($category && isset($category['name'])) { // ตรวจสอบว่า name มีใน $category หรือไม่
                    echo '<h2>Edit Category</h2>';
                    echo '<form action="process_edit_category.php" method="post">';
                    echo csrf_field();
                    echo '<input type="hidden" name="category_id" value="' . $category['id'] . '">';
                    echo '<div class="mb-3">';
                    echo '<label for="edit_name" class="form-label">Category Name:</label>';
                    echo '<input type="text" name="edit_name" id="edit_name" class="form-control" value="' . $category['name'] . '" required>';
                    echo '</div>';
                    echo '<button type="submit" class="submit-btn">Save Changes</button>';
                    echo '</form>';
                } else {
                    echo '<div class="alert alert-danger mt-3">Category not found.</div>';
                }
            }
            ?>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
