<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/ysdn/auth/csrf.php";
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/vendor/autoload.php";

use App\Model\CategoryActivity;
use App\Helper\Input;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit;
}

csrf_verify();

$category_id       = (int) ($_POST['category_id'] ?? 0);
$new_category_name = Input::text($_POST['edit_name'] ?? '');

if (!$category_id || empty($new_category_name)) {
    header("Location: edit_category.php?result=error");
    exit;
}

$category_obj = new CategoryActivity();
if ($category_obj->updateCategory($category_id, $new_category_name)) {
    header("Location: edit_category.php?result=success");
} else {
    header("Location: edit_category.php?result=error");
}
exit;
