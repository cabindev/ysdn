<?php
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/ysdn/auth/auth.php";
require $_SERVER['DOCUMENT_ROOT'] . "/ysdn_thailand/vendor/autoload.php";

use App\Model\CategoryActivity;
use App\Helper\Input;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit;
}

csrf_verify();

$category_name = Input::text($_POST["category_name"] ?? '');

if (empty($category_name)) {
    echo "กรุณาระบุชื่อหมวดหมู่";
    exit;
}

$categoryActivity = new CategoryActivity();
if ($categoryActivity->createCategory($category_name)) {
    header("location: create_category.php");
} else {
    echo "ไม่สามารถสร้างหมวดหมู่ได้";
}
