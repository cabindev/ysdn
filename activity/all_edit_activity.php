<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit YSDN Activity</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style_activity.css">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-icon/favicon.png">
</head>
<?php
require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use App\Model\Activitycms;

$activityModel = new Activitycms();
// $allActivities = $activityModel->getAllActivities();
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 6;
$activities = $activityModel->getAllActivities($currentPage, $itemsPerPage);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบและอัปเดตข้อมูลในฐานข้อมูล
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    // ... โค้ดสำหรับจัดการไฟล์รูปภาพ ...
    $activityObj->updateActivity($id, $name, $description, $new_image_path);

  
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
            <div class="col-md-9">
                <h1 class="text-center">Edit YSDN Activity</h1>
                <div id="status-alert" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
                    <strong>สถานะอัปเดตเรียบร้อย!</strong> สถานะของกิจกรรมถูกอัปเดตแล้ว.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Byname</th>
                            <th>Coverimage</th>
                            <th>Category</th>
                            <th>Act_status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activities as $activity) : ?>
                            <tr>
                                <td><?= $activity['id'] ?></td>
                                <td><?= $activity['name'] ?></td>
                                <td><?= $activity['date'] ?></td>
                                <td><?= $activity['description'] ?></td>
                                <td><?= $activity['byname'] ?></td>
                                <td><?= $activity['coverimage'] ?></td>
                                <td><?= $activity['category_activity'] ?></td>
                                <td>
                                    <select name="act_status[]" class="form-select act-status-select" data-activity-id="<?= $activity['id'] ?>">
                                        <option value="1" <?php echo ($activity['is_registration_open'] == 1) ? 'selected' : ''; ?>>เปิด</option>
                                        <option value="2" <?php echo ($activity['is_registration_open'] == 2) ? 'selected' : ''; ?>>ปิด</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button onclick="window.location.href='edit_activity.php?id=<?= $activity['id'] ?>'" class="custom-btn edit-btn">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <form action="process_edit_activity.php" method="POST" onsubmit="return confirm('Are you sure?');" class="d-inline-block" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?= $activity['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="custom-btn delete-btn">
                                            <i class="fas fa-trash"></i> ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
</body>
<nav aria-label="Page navigation" class="pagination-container">
    <ul class="pagination justify-content-center">
        <li class="page-item">
            <a class="page-link" href="?page=<?php echo max(1, $currentPage - 1); ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <li class="page-item <?php echo ($currentPage == 1) ? 'active' : ''; ?>">
            <a class="page-link" href="?page=1">1</a>
        </li>
        <li class="page-item <?php echo ($currentPage == 2) ? 'active' : ''; ?>">
            <a class="page-link" href="?page=2">2</a>
        </li>
        <!-- ... -->
        <li class="page-item <?php echo ($currentPage == 3) ? 'active' : ''; ?>">
            <a class="page-link" href="?page=3">3</a>
        </li>
        <!-- ... -->
        <li class="page-item">
            <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(".act-status-select").change(function() {
    const activityId = $(this).data("activity-id");
    const newStatus = $(this).val();

    $.ajax({
        url: "process_edit_activity.php",
        type: "POST",
        data: {
            action: "updateStatus",
            id: activityId,
            newStatus: newStatus
        },
   
    });
});

</script>

</html>