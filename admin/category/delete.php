<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Category.php';
require_once __DIR__ . '/../check_login.php';

$db = new Database();
$conn = $db->connect();
$catObj = new Category($conn);

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($category_id > 0) {
    $catObj->deleteCategory($category_id);
}

header("Location: index.php");
exit;
?>